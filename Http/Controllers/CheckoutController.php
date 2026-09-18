<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\ShippingAddress;
use App\Models\Vendor;
use App\Services\NotificationService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function show(Cart $cart)
    {

        $this->authorizeCartItem($cart);

        $cart->load(['product.vendor', 'variant']);

        $user = auth()->user();
        if ($user && ! empty($user->address)) {
            $formattedPhone = $user->phone ? (str_starts_with($user->phone, '+977-') ? $user->phone : '+977-'.$user->phone) : null;
            ShippingAddress::saveAsDefault($user->id, $user->address, $formattedPhone);
        }

        $addresses = ShippingAddress::where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('checkout', [
            'cartItem' => $cart,
            'unitPrice' => $cart->unitPrice(),
            'subtotal' => $cart->subtotal(),
            'user' => auth()->user(),
            'addresses' => $addresses,
        ]);
    }

    public function saveUserInfo(Request $request, Cart $cart)
    {

        $this->authorizeCartItem($cart);

        if ($request->has('phone')) {
            $phone = preg_replace('/[^\d]/', '', $request->input('phone'));
            if (str_starts_with($phone, '977') && strlen($phone) === 13) {
                $phone = substr($phone, 3);
            }
            $request->merge(['phone' => $phone]);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'digits:10'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        try {
            $formattedPhone = '+977-'.$validated['phone'];
            auth()->user()->update([
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            ShippingAddress::saveAsDefault(auth()->id(), $validated['address'], $formattedPhone);

            return redirect()
                ->route('checkout.show', $cart)
                ->with('success', 'Delivery information saved successfully!');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062 && str_contains($e->getMessage(), 'users_phone_unique')) {
                return back()->withErrors(['phone' => 'This phone number is already taken. Please use a different phone number.']);
            }
            throw $e;
        }
    }

    public function store(Request $request, Cart $cart)
    {
        $this->authorizeCartItem($cart);

        if ($request->has('phone')) {
            $phone = preg_replace('/[^\d]/', '', $request->input('phone'));
            if (str_starts_with($phone, '977') && strlen($phone) === 13) {
                $phone = substr($phone, 3);
            }
            $request->merge(['phone' => $phone]);
        }

        // VALIDATION — phone & address are taken directly from the checkout form.
        $data = $request->validate([
            'payment_method' => ['required', 'in:cod,esewa,khalti'],
            'phone' => ['required', 'string', 'digits:10'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $cart->load(['product', 'variant']);

        $product = $cart->product;
        $variant = $cart->variant;

        $availableStock = $variant?->stock ?? $product->stock;

        if ($cart->quantity > $availableStock) {
            return back()->withErrors([
                'quantity' => "Sorry, only {$availableStock} left in stock for {$product->name}.",
            ]);
        }

        $unitPrice = $cart->unitPrice();
        $subtotal = $cart->subtotal();
        $user = auth()->user();

        // ✅ TRANSACTION
        $order = DB::transaction(function () use ($cart, $product, $variant, $data, $unitPrice, $subtotal, $user) {

            $formattedPhone = '+977-'.$data['phone'];

            $user->update([
                'phone' => $data['phone'],
                'address' => $data['address'],
            ]);

            $shippingAddress = ShippingAddress::saveAsDefault($user->id, $data['address'], $formattedPhone);

            // 1. CREATE ORDER
            $order = Order::create([
                'user_id' => $user->id,
                'shipping_address_id' => $shippingAddress->id,
                'total_amount' => $subtotal,
                'discount' => 0,
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
            ]);

            // 2. CREATE ORDER ITEM
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'vendor_id' => $product->vendor_id,
                'quantity' => $cart->quantity,
                'price' => $unitPrice,
                'subtotal' => $subtotal,
                'status' => 'pending',
            ]);

            // 3. DECREASE STOCK
            if ($variant) {
                $variant->decrement('stock', $cart->quantity);
            } else {
                $product->decrement('stock', $cart->quantity);
            }

            // 4. REMOVE CART ITEM
            $cart->delete();

            // 5. CREATE PENDING PAYMENT FOR COD
            // Esewa and Khalti create their own payment rows inside their
            // respective controllers (EsewaPaymentController / KhaltiPaymentController).
            // For COD we create the row here so every order always has a
            // matching payment record with a pending amount.
            if ($data['payment_method'] === 'cod') {
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'gateway' => 'cod',
                    'total_amount' => $subtotal,
                    'status' => 'pending',
                ]);
            }

            return $order;
        });

        // NOTIFY VENDOR(S)
        NotificationService::orderPlaced($order);

        // COD is complete the moment the order is created. Khalti and eSewa
        // still need the customer to actually pay — send them to the
        // gateway's hosted checkout instead of the confirmation page.
        if ($data['payment_method'] === 'khalti') {
            return redirect()->route('khalti.initiate', $order);
        }

        if ($data['payment_method'] === 'esewa') {
            return redirect()->route('esewa.initiate', $order);
        }

        return redirect()
            ->route('order.confirmation', $order)
            ->with('success', 'Order placed successfully!');
    }

    public function confirmation(Order $order)
    {
        abort_if($order->user_id != auth()->id(), 403);

        $order->load(['orderItems.product', 'orderItems.vendor']);

        return view('orderconfirmation', compact('order'));
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk (per-vendor) checkout — combines every cart line the user has
    | from one vendor into a single order with multiple order items.
    |--------------------------------------------------------------------------
    */

    public function showVendor(Vendor $vendor)
    {
        $user = auth()->user();
        if ($user && ! empty($user->address)) {
            $formattedPhone = $user->phone ? (str_starts_with($user->phone, '+977-') ? $user->phone : '+977-'.$user->phone) : null;
            ShippingAddress::saveAsDefault($user->id, $user->address, $formattedPhone);
        }

        $cartItems = $this->vendorCartItems($vendor);
        $addresses = ShippingAddress::where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('checkout-vendor', [
            'vendor' => $vendor,
            'cartItems' => $cartItems,
            'total' => $cartItems->sum(fn (Cart $item) => $item->subtotal()),
            'user' => auth()->user(),
            'addresses' => $addresses,
        ]);
    }

    public function saveVendorUserInfo(Request $request, Vendor $vendor)
    {

        // Make sure this vendor actually has cart items for this user before
        // bothering to save anything.
        $this->vendorCartItems($vendor);

        if ($request->has('phone')) {
            $phone = preg_replace('/[^\d]/', '', $request->input('phone'));
            if (str_starts_with($phone, '977') && strlen($phone) === 13) {
                $phone = substr($phone, 3);
            }
            $request->merge(['phone' => $phone]);
        }

        $validated = $request->validate([
            'phone' => ['required', 'string', 'digits:10'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        try {
            $formattedPhone = '+977-'.$validated['phone'];
            auth()->user()->update([
                'phone' => $validated['phone'],
                'address' => $validated['address'],
            ]);

            ShippingAddress::saveAsDefault(auth()->id(), $validated['address'], $formattedPhone);

            return redirect()
                ->route('checkout.show.vendor', $vendor)
                ->with('success', 'Delivery information saved successfully!');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062 && str_contains($e->getMessage(), 'users_phone_unique')) {
                return back()->withErrors(['phone' => 'This phone number is already taken. Please use a different phone number.']);
            }
            throw $e;
        }
    }

    public function storeVendor(Request $request, Vendor $vendor)
    {
        $cartItems = $this->vendorCartItems($vendor);

        if ($request->has('phone')) {
            $phone = preg_replace('/[^\d]/', '', $request->input('phone'));
            if (str_starts_with($phone, '977') && strlen($phone) === 13) {
                $phone = substr($phone, 3);
            }
            $request->merge(['phone' => $phone]);
        }

        $data = $request->validate([
            'payment_method' => ['required', 'in:cod,esewa,khalti'],
            'phone' => ['required', 'string', 'digits:10'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        $cartItems->load(['product', 'variant']);

        // Validate stock for every line before touching the database.
        foreach ($cartItems as $cartItem) {
            $availableStock = $cartItem->variant?->stock ?? $cartItem->product->stock;

            if ($cartItem->quantity > $availableStock) {
                return back()->withErrors([
                    'quantity' => "Sorry, only {$availableStock} left in stock for {$cartItem->product->name}.",
                ]);
            }
        }

        $total = $cartItems->sum(fn (Cart $item) => $item->subtotal());
        $user = auth()->user();
        try {
            $order = DB::transaction(function () use ($cartItems, $data, $total, $user) {

                $formattedPhone = '+977-'.$data['phone'];

                try {
                    $user->update([
                        'phone' => $data['phone'],
                        'address' => $data['address'],
                    ]);
                } catch (\Exception $e) {
                    $user->update([
                        'address' => $data['address'],
                    ]);
                }

                $shippingAddress = ShippingAddress::saveAsDefault($user->id, $data['address'], $formattedPhone);

                $order = Order::create([
                    'user_id' => $user->id,
                    'shipping_address_id' => $shippingAddress->id,
                    'total_amount' => $total,
                    'discount' => 0,
                    'payment_method' => $data['payment_method'],
                    'status' => 'pending',
                ]);

                foreach ($cartItems as $cartItem) {
                    $product = $cartItem->product;
                    $variant = $cartItem->variant;
                    $unitPrice = $cartItem->unitPrice();
                    $subtotal = $cartItem->subtotal();

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'variant_id' => $variant?->id,
                        'vendor_id' => $product->vendor_id,
                        'quantity' => $cartItem->quantity,
                        'price' => $unitPrice,
                        'subtotal' => $subtotal,
                        'status' => 'pending',
                    ]);

                    if ($variant) {
                        $variant->decrement('stock', $cartItem->quantity);
                    } else {
                        $product->decrement('stock', $cartItem->quantity);
                    }

                    $cartItem->delete();
                }

                // CREATE PENDING PAYMENT FOR COD (vendor bulk checkout)
                // Same reasoning as store() above — Esewa/Khalti handle their
                // own rows; COD needs one created here.
                if ($data['payment_method'] === 'cod') {
                    Payment::create([
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'gateway' => 'cod',
                        'total_amount' => $total,
                        'status' => 'pending',
                    ]);
                }

                return $order;
            });

            // NOTIFY VENDOR(S)
            NotificationService::orderPlaced($order);

            if ($data['payment_method'] === 'khalti') {
                return redirect()->route('khalti.initiate', $order);
            }

            if ($data['payment_method'] === 'esewa') {
                return redirect()->route('esewa.initiate', $order);
            }

            return redirect()
                ->route('order.confirmation', $order)
                ->with('success', 'Order placed successfully!');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062 && str_contains($e->getMessage(), 'users_phone_unique')) {
                return back()->withErrors(['phone' => 'This phone number is already taken. Please use a different phone number.']);
            }
            throw $e;
        }
    }

    /**
     * All of the current user's cart lines for a given vendor. Aborts with
     * a 404 if there aren't any — there's nothing to check out.
     */
    private function vendorCartItems(Vendor $vendor)
    {
        $cartItems = Cart::with(['product.vendor', 'variant'])
            ->where('user_id', auth()->id())
            ->whereHas('product', fn ($q) => $q->where('vendor_id', $vendor->id))
            ->get();

        abort_if($cartItems->isEmpty(), 404);

        return $cartItems;
    }

    private function authorizeCartItem(Cart $cart): void
    {
        abort_if($cart->user_id != auth()->id(), 403);
    }

    private function parseAddressComponents(string $fullAddress): array
    {
        $parts = array_map('trim', explode(',', $fullAddress));

        $province = 'N/A';
        $city = 'N/A';

        if (count($parts) >= 3) {
            $province = $parts[0];
            $city = $parts[1];
        } elseif (count($parts) === 2) {
            $province = $parts[0];
            $city = $parts[1];
        } elseif (count($parts) === 1 && ! empty($parts[0])) {
            $city = $parts[0];
        }

        return [
            'province' => mb_substr($province, 0, 80),
            'city' => mb_substr($city, 0, 80),
            'address' => $fullAddress,
        ];
    }
}

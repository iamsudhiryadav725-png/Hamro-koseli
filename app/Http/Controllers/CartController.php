<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ShippingAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Show the cart page. Items are grouped by vendor purely for display —
     * each item still checks out individually (see CheckoutController).
     */
    public function index()
    {
        $items = Cart::with(['product.vendor', 'product.images', 'variant'])
            ->forUser(auth()->id())
            ->latest()
            ->get();

        $groupedByVendor = $items->groupBy(
            fn (Cart $item) => $item->product->vendor->id ?? 0
        );

        $user = auth()->user();
        if ($user && ! empty($user->address)) {
            $formattedPhone = $user->phone ? (str_starts_with($user->phone, '+977-') ? $user->phone : '+977-'.$user->phone) : null;
            ShippingAddress::saveAsDefault($user->id, $user->address, $formattedPhone);
        }

        $addresses = ShippingAddress::where('user_id', auth()->id())
            ->orderBy('is_default', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('cart', [
            'items' => $items,
            'groupedByVendor' => $groupedByVendor,
            'addresses' => $addresses,
        ]);
    }

    /**
     * Add a product (optionally a specific variant) to the current user's cart.
     * If the same product+variant combo already exists, bump the quantity
     * instead of creating a duplicate row.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'variant_id' => ['nullable', 'exists:product_variants,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $data['quantity'] ?? 1;
        $product = Product::findOrFail($data['product_id']);

        if (! $product->isActive()) {
            $message = 'This product is not currently available.';

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->withErrors(['product' => $message]);
        }

        $variant = null;
        $availableStock = $product->stock;

        if (! empty($data['variant_id'])) {
            $variant = ProductVariant::where('product_id', $product->id)
                ->findOrFail($data['variant_id']);
            $availableStock = $variant->stock;
        }

        // Look up any existing cart row for this exact product+variant first,
        // since the DB's unique index treats multiple NULL variant_id rows
        // as distinct and won't stop us from creating duplicates otherwise.
        $cartItem = Cart::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->where('variant_id', $variant?->id)
            ->first();

        $desiredQuantity = $quantity + ($cartItem->quantity ?? 0);

        if ($desiredQuantity > $availableStock) {
            $message = "Only {$availableStock} left in stock for {$product->name}.";

            return $request->wantsJson()
                ? response()->json(['success' => false, 'message' => $message], 422)
                : back()->withErrors(['quantity' => $message]);
        }

        if ($cartItem) {
            $cartItem->update(['quantity' => $desiredQuantity]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'variant_id' => $variant?->id,
                'quantity' => $quantity,
            ]);
        }

        $message = $product->name.' added to cart.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'cart_count' => (int) Cart::where('user_id', auth()->id())->sum('quantity'),
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update the quantity of a single cart line.
     * Accepts both AJAX (JSON) and regular form submissions.
     */
    public function update(Request $request, Cart $cart): RedirectResponse|JsonResponse
    {
        abort_if($cart->user_id != auth()->id(), 403);

        $data = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $availableStock = $cart->variant?->stock ?? $cart->product->stock;

        if ($data['quantity'] > $availableStock) {
            $message = "Only {$availableStock} left in stock.";

            return $request->wantsJson()
                ? response()->json(['success' => false, 'errors' => ['quantity' => [$message]]], 422)
                : back()->withErrors(['quantity' => $message]);
        }

        $cart->update(['quantity' => $data['quantity']]);

        return $request->wantsJson()
            ? response()->json(['success' => true, 'message' => 'Cart updated.'])
            : back()->with('success', 'Cart updated.');
    }

    /**
     * Remove a single line from the cart.
     */
    public function destroy(Cart $cart): RedirectResponse
    {
        abort_if($cart->user_id != auth()->id(), 403);

        $cart->delete();

        return back()->with('success', 'Item removed from cart.');
    }
}

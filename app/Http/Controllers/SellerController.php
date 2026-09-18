<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\SupportTicket;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    public function login()
    {
        return view('seller.login');
    }

    public function loginSubmit(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('vendor')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::guard('vendor')->user();

            // Block if not vendor or not active
            if ($user->role !== 'vendor' || ! $user->is_active) {
                Auth::guard('vendor')->logout();

                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'You do not have a seller account.',
                    ]);
            }

            // Sync Spatie role if missing
            if (! $user->hasRole('vendor')) {
                $user->assignRole('vendor');
            }

            // Auto-create vendor record if missing
            if (! $user->vendor) {
                $user->vendor()->create([
                    'vendor_name' => $user->name,
                    'owner_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '0000000000',
                    'status' => 'pending',
                ]);
            }

            // Refresh user and vendor relation
            $user->load('vendor');
            $vendor = $user->vendor;

            if (! $vendor) {
                Auth::guard('vendor')->logout();

                return redirect()
                    ->route('seller.login')
                    ->with('error', 'Vendor account not found.');
            }

            if ($vendor->status !== 'active') {
                Auth::guard('vendor')->logout();

                return redirect()
                    ->route('seller.login')
                    ->with('error', 'Your account is pending approval.');
            }

            return redirect()->route('dashboard');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'These credentials do not match our records.',
            ]);
    }

    public function logout(Request $request)
    {
        // Only clear the vendor guard — do NOT invalidate the whole session
        // or the user (web guard) login will be wiped too.
        Auth::guard('vendor')->logout();

        return redirect()->route('seller.login');
    }

    public function seller()
    {
        // If the user is already logged in, redirect them appropriately.
        if (Auth::guard('vendor')->check()) {
            $user = Auth::guard('vendor')->user();

            if ($user->role === 'vendor') {
                return redirect()->route('dashboard')
                    ->with('info', 'You already have a seller account.');
            }

            // Regular user – automatically log them out of the buyer account
            // and let them see the seller registration page.
            Auth::guard('vendor')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }

        return view('seller.register');
    }

    public function dashboard()
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('seller.login')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        $vendorId = $vendor->id;

        // Items that actually count as sales (exclude cancelled/returned lines).
        $soldItems = OrderItem::where('vendor_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned']);

        // ─── Review Statistics ───────────────────────────────
        $productIds = Product::where('vendor_id', $vendorId)->pluck('id');

        $allReviews = Review::whereIn('product_id', $productIds);

        $reviewCount = $allReviews->count();
        $avgRating = $reviewCount > 0 ? round($allReviews->avg('rating'), 2) : 0;

        $stats = [
            'total_sales' => (clone $soldItems)->sum('subtotal'),
            'total_orders' => (clone $soldItems)->select('order_id')->distinct()->count('order_id'),
            'active_products' => Product::where('vendor_id', $vendorId)->where('status', 'active')->count(),
            'avg_rating' => (float) $avgRating,
            'review_count' => $reviewCount,
        ];

        // ─── Sales trend for the last 7 days ───────────────────────────────
        $days = collect(range(6, 0))->map(fn ($daysAgo) => now()->subDays($daysAgo)->startOfDay());

        $dailyTotals = (clone $soldItems)
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(subtotal) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $maxDailyTotal = max(1, $dailyTotals->max() ?? 0);

        $salesTrend = $days->map(function ($date) use ($dailyTotals, $maxDailyTotal) {
            $total = (float) ($dailyTotals[$date->toDateString()] ?? 0);

            return [
                'label' => $date->format('D'),
                'total' => $total,
                'height' => $total > 0 ? max(8, (int) round(($total / $maxDailyTotal) * 140)) : 4,
            ];
        });

        // ─── Recent orders (latest 5 order lines for this vendor) ─────────
        $recentItems = OrderItem::with(['order.user', 'order.payment'])
            ->where('vendor_id', $vendorId)
            ->latest()
            ->take(5)
            ->get();

        $dealEndsAt = Setting::getValue('todays_deal_ends_at')
                    ?? Setting::getValue('date')
                    ?? Setting::getValue('deal_ends_at');
        if (! $dealEndsAt) {
            $dateSetting = Setting::where('key', 'LIKE', '%date%')
                ->orWhere('key', 'LIKE', '%time%')
                ->orWhere('key', 'LIKE', '%_at%')
                ->latest()
                ->first();
            if ($dateSetting) {
                $dealEndsAt = $dateSetting->value;
            }
        }
        if ($dealEndsAt) {
            $dealEndsAt = Carbon::parse($dealEndsAt)->toIso8601String();
        } else {
            $dealEndsAt = now()->endOfDay()->toIso8601String();
        }

        $dealBgImage = Setting::getValue('deal_countdown_bg_image')
                    ?? Setting::getValue('banner_image')
                    ?? Setting::getValue('deal_bg_image')
                    ?? Setting::getValue('todays_deal_bg_image')
                    ?? Setting::getValue('image')
                    ?? Setting::getValue('banner');
        if (! $dealBgImage) {
            $imageSetting = Setting::where('value', 'LIKE', 'settings/%')
                ->orWhere('key', 'LIKE', '%image%')
                ->orWhere('key', 'LIKE', '%bg%')
                ->latest()
                ->first();
            if ($imageSetting) {
                $dealBgImage = $imageSetting->value;
            }
        }
        if ($dealBgImage) {
            $dealBgImage = ltrim(preg_replace('#^storage/#', '', $dealBgImage), '/');
        }

        return view('seller.dashboard', compact('vendor', 'stats', 'salesTrend', 'recentItems', 'dealEndsAt', 'dealBgImage'));
    }

    /**
     * API endpoint: returns sales trend data for a given period.
     * GET /seller-dashboard/sales-trend?period=7|30|90
     */
    public function salesTrendData(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return response()->json(['error' => 'Vendor not found.'], 403);
        }

        $period = (int) $request->query('period', 7);

        // Clamp to allowed values
        if (! in_array($period, [7, 30, 90])) {
            $period = 7;
        }

        $vendorId = $vendor->id;

        $soldItems = OrderItem::where('vendor_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned']);

        $days = collect(range($period - 1, 0))->map(
            fn ($daysAgo) => now()->subDays($daysAgo)->startOfDay()
        );

        $dailyTotals = (clone $soldItems)
            ->where('created_at', '>=', now()->subDays($period - 1)->startOfDay())
            ->selectRaw('DATE(created_at) as day, SUM(subtotal) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $maxDailyTotal = max(1, $dailyTotals->max() ?? 0);

        // For 7 days: show short day name (Mon). For 30/90: show M/d (Jun 5).
        $labelFormat = $period === 7 ? 'D' : 'M j';

        $trend = $days->map(function ($date) use ($dailyTotals, $maxDailyTotal, $labelFormat) {
            $total = (float) ($dailyTotals[$date->toDateString()] ?? 0);

            return [
                'label' => $date->format($labelFormat),
                'date' => $date->toDateString(),
                'total' => $total,
                'height' => $total > 0 ? max(8, (int) round(($total / $maxDailyTotal) * 140)) : 4,
            ];
        });

        return response()->json([
            'period' => $period,
            'trend' => $trend->values(),
        ]);
    }

    public function product_management(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        $vendorId = $vendor->id;

        $query = Product::with(['category', 'images'])
            ->where('vendor_id', $vendorId);

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('stock_status')) {
            match ($request->stock_status) {
                'in_stock' => $query->where('stock', '>', 5),
                'low_stock' => $query->whereBetween('stock', [1, 5]),
                'out_of_stock' => $query->where('stock', 0),
                'draft' => $query->where('status', 'draft'),
                default => null,
            };
        }

        $products = $query->latest()->paginate(10);
        $totalProducts = Product::where('vendor_id', $vendorId)->count();
        $lowStock = Product::where('vendor_id', $vendorId)->whereBetween('stock', [1, 5])->count();
        $outOfStock = Product::where('vendor_id', $vendorId)->where('stock', 0)->count();
        $draftProducts = Product::where('vendor_id', $vendorId)->where('status', 'draft')->count();
        $categories = Category::where('status', 'active')->get();

        return view('seller.product-management', compact(
            'products',
            'totalProducts',
            'lowStock',
            'outOfStock',
            'draftProducts',
            'categories'
        ));
    }

    public function productCreate()
    {
        $categories = Category::where('status', 'active')->get();

        return view('seller.product-create', compact('categories'));
    }

    public function productEdit($id)
    {
        $product = Product::with(['category', 'images', 'variants'])
            ->where('vendor_id', Auth::guard('vendor')->user()->vendor->id)
            ->findOrFail($id);

        $categories = Category::where('status', 'active')->get();

        return view('seller.product-edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('vendor_id', Auth::guard('vendor')->user()->vendor->id)->findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'required|string|max:200',
            'category' => 'required|exists:categories,id',
            'product_type' => 'nullable|string|max:100',
            'description' => 'required|string|max:2000',
            'base_price' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0|max:99',
            'sku' => 'nullable|string|max:100|unique:products,sku,'.$product->id,
            'stock' => 'required|integer|min:0',
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:100',
            'specifications.*.value' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
            'status' => 'required|in:active,draft',
        ]);

        // Update Product
        $product->update([
            'category_id' => $validated['category'],
            'name' => $validated['product_name'],
            'slug' => Str::slug($validated['product_name']).'-'.Str::lower(Str::random(5)),
            'product_type' => $validated['product_type'] ?? null,
            'description' => $validated['description'],
            'specifications' => ! empty($validated['specifications'])
                ? $this->filterSpecs($validated['specifications'])
                : null,
            'price' => $validated['base_price'],
            'discount_price' => ($validated['discount_amount'] ?? 0) > 0
                ? ($validated['base_price'] * (1 - $validated['discount_amount'] / 100))
                : null,
            'stock' => $validated['stock'],
            'sku' => $validated['sku'] ?? $product->sku,
            'status' => $validated['status'],
        ]);

        // Image Handling
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($product->images as $oldImage) {
                Storage::disk('public')->delete($oldImage->path);
                $oldImage->delete();
            }

            $file = $request->file('images')[0];
            $path = $file->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'type' => 'gallery',
                'is_primary' => 1,
            ]);
        }

        return redirect()->route('product-management')
            ->with('success', 'Product updated successfully!');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:200',
            'category' => 'required|exists:categories,id',
            'product_type' => 'nullable|string|max:100',
            'description' => 'required|string|max:2000',
            'base_price' => 'required|numeric|min:0',
            'discounted_price' => 'nullable|numeric|min:0|max:99',
            'sku' => 'required|string|max:100|unique:products,sku',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,draft',
            'specifications' => 'nullable|array',
            'specifications.*.key' => 'nullable|string|max:100',
            'specifications.*.value' => 'nullable|string|max:255',
            'images' => 'nullable|array|max:1',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:10240',
        ]);

        // 1. Create the product
        $product = Product::create([
            'vendor_id' => Auth::guard('vendor')->user()->vendor->id,
            'category_id' => $validated['category'],
            'name' => $validated['product_name'],
            'slug' => Str::slug($validated['product_name']).'-'.Str::lower(Str::random(5)),
            'product_type' => $validated['product_type'] ?? null,
            'description' => $validated['description'],
            'specifications' => ! empty($validated['specifications'])
                ? $this->filterSpecs($validated['specifications'])
                : null,
            'price' => $validated['base_price'],
            'discount_price' => ($validated['discounted_price'] ?? 0) > 0
                ? ($validated['base_price'] * (1 - $validated['discounted_price'] / 100))
                : null,
            'stock' => $validated['stock'],
            'sku' => $validated['sku'],
            'status' => $validated['status'],
        ]);

        // 3. Save images
        if ($request->hasFile('images')) {
            $file = $request->file('images')[0];
            $path = $file->store('products', 'public');

            $product->images()->create([
                'path' => $path,
                'type' => 'gallery',
                'is_primary' => 1,
            ]);
        }

        return redirect()
            ->route('product-management')
            ->with('success', 'Product "'.$product->name.'" created successfully!');
    }

    public function destroy($id)
    {
        $product = Product::where('vendor_id', Auth::guard('vendor')->user()->vendor->id)
            ->findOrFail($id);

        try {
            // Check if this product is linked to any orders
            $orderItemsCount = OrderItem::where('product_id', $product->id)->count();

            if ($orderItemsCount > 0) {
                return redirect()
                    ->route('product-management')
                    ->with('error', "This product cannot be deleted. It is used in {$orderItemsCount} order(s).");
            }

            // Safe to delete
            $product->images()->delete();
            $product->variants()->delete();
            $product->delete();

            return redirect()
                ->route('product-management')
                ->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            // Catch foreign key constraint error
            if ($e instanceof QueryException && $e->getCode() === '23000') {
                return redirect()
                    ->route('product-management')
                    ->with('error', 'This product cannot be deleted because it is linked to existing orders.');
            }

            // Log unexpected errors
            Log::error('Product deletion failed: '.$e->getMessage());

            return redirect()
                ->route('product-management')
                ->with('error', 'Something went wrong. Please try again later.');
        }
    }

    public function order(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        $vendorId = $vendor->id;

        // ─── Tab → order_item status mapping (fulfillment tabs) ───────────
        $statusMap = [
            'new' => ['pending'],
            'cancelled' => ['cancelled', 'returned'],
        ];

        // ─── Tab → payment status mapping (payment tabs) ───────────────────
        $paymentMap = [
            'paid' => ['completed'],
            'pending_payment' => ['pending'],
        ];

        $activeTab = $request->query('status', 'all');

        $applyTabFilter = function ($query) use ($activeTab, $statusMap, $paymentMap) {
            if (isset($statusMap[$activeTab])) {
                $query->whereIn('status', $statusMap[$activeTab]);
            } elseif ($activeTab === 'paid') {
                $query->whereHas('order.payment', fn ($pq) => $pq->whereIn('status', $paymentMap['paid']));
            } elseif ($activeTab === 'pending_payment') {
                // Orders paid via COD (or otherwise missing a payment row) are
                // treated as "payment pending" too, same as the badge shown
                // on the order details page.
                $query->where(function ($pq) use ($paymentMap) {
                    $pq->whereHas('order.payment', fn ($ppq) => $ppq->whereIn('status', $paymentMap['pending_payment']))
                        ->orWhereDoesntHave('order.payment');
                });
            }
        };

        $query = OrderItem::with(['order.user', 'order.payment', 'product'])
            ->where('vendor_id', $vendorId);

        if ($activeTab !== 'all') {
            $applyTabFilter($query);
        }

        if ($request->filled('search')) {
            $search = $request->query('search');

            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                    ->orWhereHas('order.user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $orderItems = $query->latest()->paginate(10)->withQueryString();

        // ─── Counts for the tab badges (unaffected by the active filter) ──
        $baseCount = fn () => OrderItem::where('vendor_id', $vendorId);

        $counts = [
            'all' => $baseCount()->count(),
            'new' => $baseCount()->whereIn('status', $statusMap['new'])->count(),
            'paid' => $baseCount()->whereHas('order.payment', fn ($pq) => $pq->whereIn('status', $paymentMap['paid']))->count(),
            'pending_payment' => $baseCount()->where(function ($pq) use ($paymentMap) {
                $pq->whereHas('order.payment', fn ($ppq) => $ppq->whereIn('status', $paymentMap['pending_payment']))
                    ->orWhereDoesntHave('order.payment');
            })->count(),
            'cancelled' => $baseCount()->whereIn('status', $statusMap['cancelled'])->count(),
        ];

        return view('seller.order', compact('orderItems', 'counts', 'activeTab'));
    }

    public function orderDetails(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        // Every order in this schema is already scoped to a single vendor
        // (checkout groups cart lines by vendor before an Order is created),
        // so we just need to confirm this order actually belongs to them.
        $order = Order::with(['user', 'shippingAddress', 'payment'])
            ->whereHas('orderItems', fn ($q) => $q->where('vendor_id', $vendor->id))
            ->find($request->query('order'));

        if (! $order) {
            abort(404);
        }

        $items = $order->orderItems()
            ->where('vendor_id', $vendor->id)
            ->with(['product.images', 'variant'])
            ->get();

        $subtotal = $items->sum('subtotal');

        return view('seller.order-details', compact('order', 'items', 'subtotal'));
    }

    public function updatePaymentStatus(Request $request, Order $order)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        // Make sure this order actually belongs to the current vendor.
        $belongsToVendor = $order->orderItems()->where('vendor_id', $vendor->id)->exists();

        if (! $belongsToVendor) {
            abort(404);
        }

        $validated = $request->validate([
            'payment_status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment = $order->payment;

        if (! $payment) {
            return redirect()
                ->route('order-details', ['order' => $order->id])
                ->with('error', 'No payment record found for this order.');
        }

        // Vendors mainly need to confirm they have received the money
        // (pending -> paid), but we also allow flagging failed/refunded.
        if ($payment->status !== $validated['payment_status']) {
            if ($validated['payment_status'] === 'completed') {
                $payment->markAsCompleted($payment->transaction_id ?? ('MANUAL-'.Str::upper(Str::random(10))));
            } elseif ($validated['payment_status'] === 'failed') {
                $payment->markAsFailed();
            } elseif ($validated['payment_status'] === 'refunded') {
                $payment->markAsRefunded();
            } else {
                $payment->update(['status' => 'pending']);
            }
        }

        $labels = [
            'pending' => 'Pending',
            'completed' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ];

        return redirect()
            ->route('order-details', ['order' => $order->id])
            ->with('success', 'Payment status updated to '.$labels[$validated['payment_status']].'.');
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        // Make sure this order actually belongs to the current vendor.
        $belongsToVendor = $order->orderItems()->where('vendor_id', $vendor->id)->exists();

        if (! $belongsToVendor) {
            abort(404);
        }

        $validated = $request->validate([
            'order_status' => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        ]);

        if ($order->status !== $validated['order_status']) {
            $order->update(['status' => $validated['order_status']]);
        }

        $labels = [
            'pending' => 'New',
            'confirmed' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        return redirect()
            ->route('order-details', ['order' => $order->id])
            ->with('success', 'Order status updated to '.$labels[$validated['order_status']].'.');
    }

    public function sellerProfile()
    {
        $user = Auth::guard('vendor')->user();
        $vendor = $user?->vendor;

        return view('seller.profile', compact('user', 'vendor'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('vendor')->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'profile_pic' => 'nullable|image|max:2048',
            'vendor_name' => 'required|string|max:100',
            'owner_name' => 'required|string|max:100',
            'vendor_email' => 'nullable|email|max:150',
            'vendor_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->hasFile('profile_pic')) {
            $path = $request->file('profile_pic')->store('profiles', 'public');
            $user->profile_pic = $path;
        }

        $user->save();

        $vendor = $user->vendor;
        if ($vendor) {
            $vendor->vendor_name = $request->vendor_name;
            $vendor->owner_name = $request->owner_name;
            $vendor->email = $request->vendor_email;
            $vendor->phone = $request->vendor_phone;
            $vendor->vendor_address = $request->address;
            $vendor->city = $request->city;
            $vendor->province = $request->province;
            $vendor->save();
        }

        NotificationService::profileUpdated($user, 'profile', true);

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::guard('vendor')->user();

        if (! \Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput()
                ->withFragment('password-section');
        }

        if (\Hash::check($request->new_password, $user->password)) {
            return redirect()->back()
                ->withErrors(['new_password' => 'New password must be different from your current password.'])
                ->withInput()
                ->withFragment('password-section');
        }

        $user->password = \Hash::make($request->new_password);
        $user->save();

        NotificationService::profileUpdated($user, 'password', true);

        return redirect()->back()->with('password_success', 'Password changed successfully.');
    }

    public function sellerReview(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        $vendorId = $vendor->id;

        // Fetch products owned by this vendor
        $productIds = Product::where('vendor_id', $vendorId)->pluck('id');

        $query = Review::with(['user', 'product'])->whereIn('product_id', $productIds);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Search by keyword in comments or product names or user names
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(5);

        // Calculate statistics based on ALL reviews for this vendor's products
        $allReviews = Review::whereIn('product_id', $productIds)->get();
        $totalReviews = $allReviews->count();
        $avgRating = $totalReviews > 0 ? round($allReviews->avg('rating'), 2) : 0;

        // Response rate: percentage of reviews with a reply
        $repliedCount = $allReviews->whereNotNull('reply')->count();
        $responseRate = $totalReviews > 0 ? round(($repliedCount / $totalReviews) * 100) : 0;

        // Pending Replies
        $pendingReplies = $totalReviews - $repliedCount;

        return view('seller.review', compact(
            'reviews',
            'totalReviews',
            'avgRating',
            'responseRate',
            'pendingReplies'
        ));
    }

    public function replyToReview(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

        $vendor = Auth::guard('vendor')->user()->vendor;
        if (! $vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }

        $review = Review::findOrFail($id);

        // Ensure the review belongs to this vendor's products
        if ($review->product->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized action.');
        }

        $review->reply = $request->reply;
        $review->replied_at = now();
        $review->save();

        return redirect()->back()->with('success', 'Reply submitted successfully.');
    }

    public function destroyReview($id)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        if (! $vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }

        $review = Review::findOrFail($id);

        // Ensure the review belongs to this vendor's products
        if ($review->product->vendor_id !== $vendor->id) {
            abort(403, 'Unauthorized action.');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }

    public function sellerPayment(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found. Please contact support.');
        }

        $vendorId = $vendor->id;

        // ─── Base query: vendor's order items that have a completed payment ──
        // We join order_items → orders → payments to find items the vendor
        // has actually been paid for (payment.status = 'completed').
        // Cancelled / returned items are excluded from earnings.
        $paidItemsBase = OrderItem::where('order_items.vendor_id', $vendorId)
            ->whereNotIn('order_items.status', ['cancelled', 'returned'])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'completed');

        // ─── Total Earnings: sum of subtotals from paid, non-cancelled items ─
        $totalEarnings = (clone $paidItemsBase)->sum('order_items.subtotal');

        // ─── Total Payouts (Commission Paid): sum of completed platform fees/commissions paid to admin ─────────
        $totalPayouts = Payout::where('vendor_id', $vendorId)
            ->where('status', 'completed')
            ->where('platform_fee', 0)
            ->sum('amount');

        // ─── Pending Settlement: orders confirmed/shipped but not yet paid ───
        // These are items on orders that exist but payment is still pending.
        $pendingSettlement = OrderItem::where('order_items.vendor_id', $vendorId)
            ->whereNotIn('order_items.status', ['cancelled', 'returned'])
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('payments', 'payments.order_id', '=', 'orders.id')
            ->where('payments.status', 'pending')
            ->sum('order_items.subtotal');

        // ─── Current Balance (Commission Owed to Admin): 3% of total earnings minus total commission paid ───
        $totalCommissionOwed = round($totalEarnings * 0.03, 2);
        $currentBalance = max(0, $totalCommissionOwed - $totalPayouts);

        // ─── Payment transaction history (from the payments table) ───────────
        // Each row here represents a customer payment for an order containing
        // this vendor's items. We show these as the "payout history" since
        // the payouts table may still be empty for new setups.

        $paymentHistoryQuery = Payment::select(
            'payments.id',
            'payments.gateway',
            'payments.total_amount',
            'payments.status',
            'payments.transaction_id',
            'payments.reference_id',
            'payments.paid_at',
            'payments.created_at',
            DB::raw('SUM(order_items.subtotal) as vendor_subtotal'),
            DB::raw('ROUND(SUM(order_items.subtotal) * 0.03, 2) as commission_amount'),
            DB::raw('ROUND(SUM(order_items.subtotal) * 0.97, 2) as net_amount')
        )
            ->join('orders', 'orders.id', '=', 'payments.order_id')
            ->join('order_items', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.vendor_id', $vendorId)
            ->whereNotIn('order_items.status', ['cancelled', 'returned'])
            ->where('payments.status', 'completed')
            ->groupBy(
                'payments.id',
                'payments.gateway',
                'payments.total_amount',
                'payments.status',
                'payments.transaction_id',
                'payments.reference_id',
                'payments.paid_at',
                'payments.created_at'
            )
            ->latest('payments.created_at');

        $paymentHistory = $paymentHistoryQuery->paginate(8)->withQueryString();

        // ─── Payout requests (admin-initiated payouts to the vendor) ─────────
        $payouts = Payout::where('vendor_id', $vendorId)->latest()->get();

        return view('seller.payment', compact(
            'vendor',
            'totalEarnings',
            'totalPayouts',
            'pendingSettlement',
            'currentBalance',
            'paymentHistory',
            'payouts',
        ));
    }

    public function paymentDetails($id)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;

        if (! $vendor) {
            return redirect()->route('dashboard')
                ->with('error', 'Vendor profile not found.');
        }

        // Fetch payment with full order and buyer info
        $payment = Payment::with([
            'order.user',
            'order.shippingAddress',
            'order.orderItems' => function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id)
                    ->with(['product.images', 'variant']);
            },
        ])
            ->whereHas('order.orderItems', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->findOrFail($id);

        $order = $payment->order;
        $items = $order->orderItems; // Only this vendor's items

        $vendorSubtotal = $items->sum('subtotal');
        $commission = round($vendorSubtotal * 0.03, 2);

        // Buyer Payment History Summary
        $buyerPaymentInfo = [
            'total_paid_by_buyer' => $payment->total_amount,
            'payment_method' => $payment->gateway,
            'transaction_id' => $payment->transaction_id ?? $payment->reference_id,
            'paid_at' => $payment->paid_at ?? $payment->created_at,
            'status' => $payment->status,
        ];

        return view('seller.payment-details', compact(
            'payment',
            'order',
            'items',
            'vendorSubtotal',
            'commission',
            'buyerPaymentInfo'
        ));
    }

    public function payCommission(Request $request)
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        if (! $vendor) {
            return redirect()->route('dashboard')->with('error', 'Vendor profile not found.');
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:10|max:100000',
        ]);

        $amount = (float) $validated['amount'];

        // Create Payout Record (transaction_id will be set to Khalti pidx after initiation)
        $payout = Payout::create([
            'vendor_id' => $vendor->id,
            'gross_amount' => $amount,
            'platform_fee' => 0,
            'amount' => $amount,
            'method' => 'Khalti',
            'status' => 'pending',
            'notes' => 'Admin Commission Payment via Khalti',
        ]);

        return $this->initiateKhaltiPayment($payout, $amount);
    }

    private function initiateKhaltiPayment(Payout $payout, float $amount)
    {
        $secretKey = config('services.khalti.secret_key');
        $baseUrl = rtrim(config('services.khalti.base_url', 'https://dev.khalti.com/api/v2'), '/').'/';

        Log::info('Khalti Debug - Config', [
            'secret_key_present' => ! empty($secretKey),
            'base_url' => $baseUrl,
            'env_key' => env('KHALTI_SECRET_KEY') ? 'YES' : 'NO',
            'amount' => $amount,
        ]);

        if (empty($secretKey)) {
            $payout->delete();

            return back()->with('error', 'Khalti secret key is missing.');
        }

        $internalTxnId = $payout->id.'-'.Str::uuid();
        $payout->update(['transaction_id' => $internalTxnId]);  // temporary

        $payload = [
            'return_url' => route('seller.payment.khalti.callback'),
            'website_url' => config('app.url'),
            'amount' => (int) round($amount * 100),
            'purchase_order_id' => $internalTxnId,
            'purchase_order_name' => "Commission #{$payout->id}",
            'customer_info' => [
                'name' => Auth::guard('vendor')->user()->name ?? 'Vendor',
                'email' => Auth::guard('vendor')->user()->email,
                'phone' => Auth::guard('vendor')->user()->phone ?? '9800000000',
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key '.$secretKey,
                'Content-Type' => 'application/json',
            ])
                ->withOptions(['verify' => false])
                ->post($baseUrl.'epayment/initiate/', $payload);

            Log::info('Khalti Raw Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (! empty($data['payment_url']) && ! empty($data['pidx'])) {
                    // ← Save Khalti pidx into transaction_id
                    $payout->update(['transaction_id' => $data['pidx']]);

                    return redirect($data['payment_url']);
                }
            }

            $error = $response->json('detail') ?? $response->body() ?? 'No detail';
            Log::error('Khalti Initiate Failed', ['error' => $error]);
            $payout->update(['status' => 'failed']);

            return back()->with('error', 'Khalti Error: '.$error);
        } catch (\Exception $e) {
            $payout->delete();
            Log::error('Khalti Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()->with('error', 'Something went wrong with Khalti.');
        }
    }

    public function khaltiCallback(Request $request)
    {
        $pidx = $request->query('pidx');
        if (! $pidx) {
            return redirect()->route('seller.payment')
                ->with('error', 'Invalid Khalti payment response.');
        }

        $payout = Payout::where('transaction_id', $pidx)->first();

        if (! $payout) {
            Log::warning('Khalti callback: Payout not found for pidx', ['pidx' => $pidx]);

            return redirect()->route('seller.payment')
                ->with('error', 'Payment record not found.');
        }

        if ($payout->status === 'completed') {
            return redirect()->route('seller.payment')
                ->with('success', 'Commission payment already processed.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Key '.config('services.khalti.secret_key'),
            ])
                ->withOptions(['verify' => false])
                ->post(rtrim(config('services.khalti.base_url'), '/').'/epayment/lookup/', [
                    'pidx' => $pidx,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['status']) && $data['status'] === 'Completed') {
                    $payout->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'transaction_id' => $data['transaction_id'] ?? $pidx,
                    ]);

                    return redirect()->route('seller.payment')
                        ->with('success', 'Commission payment of Rs. '.number_format($payout->amount, 2).' settled successfully.');
                }
            }
        } catch (\Exception $e) {
            Log::error('Khalti lookup exception', ['message' => $e->getMessage()]);
        }

        $payout->update(['status' => 'failed']);

        return redirect()->route('seller.payment')
            ->with('error', 'Commission payment verification failed.');
    }

    public function esewaCallback(Request $request)
    {
        $encoded = $request->query('data');
        if (! $encoded) {
            return redirect()->route('seller.payment')->with('error', 'Invalid eSewa payment response.');
        }

        $decoded = json_decode(base64_decode($encoded), true);
        if (! is_array($decoded) || empty($decoded['transaction_uuid'])) {
            return redirect()->route('seller.payment')->with('error', 'Invalid eSewa response.');
        }

        $payout = Payout::where('transaction_id', $decoded['transaction_uuid'])->first();
        if (! $payout) {
            return redirect()->route('seller.payment')->with('error', 'Payment record not found.');
        }

        if ($payout->status === 'completed') {
            return redirect()->route('seller.payment')->with('success', 'Commission payment already processed.');
        }

        try {
            $config = config('services.esewa');
            $response = Http::get($config['status_url'], [
                'product_code' => $config['product_code'],
                'total_amount' => number_format((float) $payout->amount, 2, '.', ''),
                'transaction_uuid' => $decoded['transaction_uuid'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? null;

                if ($status === 'COMPLETE') {
                    $payout->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'transaction_id' => $data['ref_id'] ?? $decoded['transaction_uuid'],
                    ]);

                    return redirect()->route('seller.payment')->with('success', 'Commission payment of Rs. '.number_format($payout->amount, 2).' settled successfully.');
                }
            }
        } catch (\Exception $e) {
            Log::error('eSewa lookup exception', ['message' => $e->getMessage()]);
        }

        $payout->update(['status' => 'failed']);

        return redirect()->route('seller.payment')->with('error', 'Commission payment failed.');
    }

    public function sellerNotification()
    {
        $user = Auth::guard('vendor')->user();
        $sellerTypes = [
            'order_placed',
            'vendor_order_placed',
            'return_requested',
            'return_approved',
            'vendor_profile_updated',
            'support_ticket_status',
        ];

        $notifications = $user->appNotifications()
            ->whereIn('type', $sellerTypes)
            ->latest()
            ->paginate(10);

        $counts = [
            'all' => $user->appNotifications()->whereIn('type', $sellerTypes)->where('is_read', false)->count(),
            'orders' => $user->appNotifications()->where('is_read', false)->whereIn('type', [
                'order_placed',
                'vendor_order_placed',
                'return_requested',
                'return_approved',
            ])->count(),
            'store' => $user->appNotifications()->where('is_read', false)->whereIn('type', [
                'vendor_profile_updated',
                'support_ticket_status',
            ])->count(),
        ];

        return view('seller.notification', compact('notifications', 'counts'));
    }

    public function markNotificationRead($id)
    {
        $notification = Auth::guard('vendor')->user()->appNotifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead()
    {
        $sellerTypes = [
            'order_placed',
            'vendor_order_placed',
            'return_requested',
            'return_approved',
            'vendor_profile_updated',
            'support_ticket_status',
        ];

        Auth::guard('vendor')->user()->appNotifications()
            ->whereIn('type', $sellerTypes)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    public function sellerSupport()
    {
        return view('seller.support');
    }

    public function createTicket()
    {
        return view('seller.create-ticket');
    }

    public function storeTicket(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string|max:100',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $vendor = Auth::guard('vendor')->user()->vendor;
        if (! $vendor) {
            return redirect()->back()->with('error', 'Vendor profile not found.');
        }

        SupportTicket::create([
            'vendor_id' => $vendor->id,
            'ticket_number' => 'TK-'.mt_rand(10000, 99999),
            'category' => $validated['category'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'Pending',
        ]);

        return redirect()->route('seller-ticket')->with('success', 'Support ticket created successfully!');
    }

    public function sellerTicket()
    {
        $vendor = Auth::guard('vendor')->user()->vendor;
        if (! $vendor) {
            return redirect()->route('dashboard')->with('error', 'Vendor profile not found.');
        }

        $tickets = SupportTicket::where('vendor_id', $vendor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('seller.tickets', compact('tickets'));
    }
    // ─── Helpers ──────────────────────────────────────────────────────────────

    private function filterSpecs(array $specs): array
    {
        return array_values(array_filter($specs, function ($spec) {
            return ! empty($spec['key']) || ! empty($spec['value']);
        }));
    }
}

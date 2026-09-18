<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Models\Wishlist;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        $totalOrders = $user->orders()->count();
        $pendingOrders = $user->orders()->where('status', 'pending')->count();
        $deliveredOrders = $user->orders()->where('status', 'delivered')->count();
        $canceledOrders = $user->orders()->where('status', 'cancelled')->count();

        $recentOrders = $user->orders()->with('orderItems.product.images')->latest()->take(4)->get();

        // ─── TOP DISCOUNTS ────────────────────────────────────────────────────
        // Fetch ALL active discounted products, sorted by highest discount %
        $topDiscounts = Product::with(['images', 'variants', 'category'])
            ->where('status', 'active')
            ->get()
            ->filter(fn ($p) => $p->hasDiscount())
            ->sortByDesc(function ($p) {
                $orig = $p->originalPrice();

                return $orig > 0 ? (($orig - $p->effectivePrice()) / $orig) * 100 : 0;
            })
            ->values();

        // ─── RECOMMENDED FOR YOU ─────────────────────────────────────────────
        // Collect category IDs from 3 sources: Purchase History, Wishlist, Recently Viewed

        // Source 1: Purchase History
        $purchasedProductIds = OrderItem::whereHas('order', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
            ->pluck('product_id')
            ->unique()
            ->toArray();

        $purchasedCategoryIds = Product::whereIn('id', $purchasedProductIds)
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->unique()
            ->toArray();

        // Source 2: Wishlist
        $wishlistProductIds = Wishlist::where('user_id', $user->id)
            ->pluck('product_id')
            ->unique()
            ->toArray();

        $wishlistCategoryIds = Product::whereIn('id', $wishlistProductIds)
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->unique()
            ->toArray();

        // Source 3: Recently Viewed (session)
        $recentlyViewedIds = session()->get('recently_viewed', []);

        $recentlyViewedCategoryIds = Product::whereIn('id', $recentlyViewedIds)
            ->whereNotNull('category_id')
            ->pluck('category_id')
            ->unique()
            ->toArray();

        // Merge all category IDs
        $recommendedCategoryIds = array_unique(array_merge(
            $purchasedCategoryIds,
            $wishlistCategoryIds,
            $recentlyViewedCategoryIds
        ));

        // Exclude products user already bought
        $excludeProductIds = array_unique(array_merge($purchasedProductIds, []));

        // Determine recommendation label for the view
        $recommendationSource = 'Popular Products'; // default fallback label
        if (! empty($purchasedCategoryIds)) {
            $recommendationSource = 'Based on Your Purchases';
        } elseif (! empty($wishlistCategoryIds)) {
            $recommendationSource = 'Based on Your Wishlist';
        } elseif (! empty($recentlyViewedCategoryIds)) {
            $recommendationSource = 'Based on Your Recently Viewed';
        }

        if (! empty($recommendedCategoryIds)) {
            $recommendedProducts = Product::with('images')
                ->where('status', 'active')
                ->whereIn('category_id', $recommendedCategoryIds)
                ->whereNotIn('id', $excludeProductIds)
                ->inRandomOrder()
                ->take(4)
                ->get();

            // Fill up to 4 with popular products if not enough from history
            if ($recommendedProducts->count() < 4) {
                $moreProducts = Product::with('images')
                    ->where('status', 'active')
                    ->whereNotIn('id', $recommendedProducts->pluck('id')->toArray())
                    ->withCount('orderItems')
                    ->orderByDesc('order_items_count')
                    ->take(4 - $recommendedProducts->count())
                    ->get();
                $recommendedProducts = $recommendedProducts->merge($moreProducts);
                if ($recommendedProducts->count() >= 4) {
                    $recommendationSource .= ' & Popular Items';
                }
            }
        } else {
            // No history at all — show most popular products
            $recommendationSource = 'Popular Products';
            $recommendedProducts = Product::with('images')
                ->where('status', 'active')
                ->withCount('orderItems')
                ->orderByDesc('order_items_count')
                ->take(4)
                ->get();
        }

        return view('user.dashboard', compact(
            'user',
            'totalOrders',
            'pendingOrders',
            'deliveredOrders',
            'canceledOrders',
            'recentOrders',
            'recommendedProducts',
            'recommendationSource',
            'topDiscounts'
        ));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $userId = auth()->id();
        $productId = $request->product_id;

        $existing = Wishlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            $existing->delete();
            $inWishlist = false;
            $message = 'Removed from wishlist.';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
            ]);
            $inWishlist = true;
            $message = 'Added to wishlist.';
        }

        return response()->json([
            'success' => true,
            'in_wishlist' => $inWishlist,
            'message' => $message,
        ]);
    }

    public function wishlistItems()
    {
        $items = Wishlist::where('user_id', auth()->id())
            ->with('product.images')
            ->get()
            ->map(function ($w) {
                $product = $w->product;

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->effectivePrice(),
                    'image' => $product->primaryImageUrl(),
                    'desc' => $product->description,
                    'category' => $product->category?->cat_name ?? '',
                    'slug' => $product->slug,
                ];
            });

        return response()->json(['items' => $items]);
    }

    public function orders(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status'); // null = all

        $query = $user->orders()
            ->with(['orderItems.product.images'])
            ->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->paginate(10)->withQueryString();

        // Tab counts
        $counts = [
            'all' => $user->orders()->count(),
            'pending' => $user->orders()->where('status', 'pending')->count(),
            'confirmed' => $user->orders()->where('status', 'confirmed')->count(),
            'shipped' => $user->orders()->where('status', 'shipped')->count(),
            'delivered' => $user->orders()->where('status', 'delivered')->count(),
            'cancelled' => $user->orders()->where('status', 'cancelled')->count(),
        ];

        return view('user.orders', compact('orders', 'counts', 'status'));
    }

    public function orderDetail(Request $request)
    {
        $user = auth()->user();
        $order = $user->orders()
            ->with([
                'orderItems.product.images',
                'orderItems.variant',
                'shippingAddress',
                'payment',
            ])
            ->findOrFail($request->query('order'));

        return view('user.order-details', compact('order'));
    }

    public function cancelOrder(Request $request, $orderId)
    {
        $order = auth()->user()->orders()->findOrFail($orderId);

        if (! $order->isCancellable()) {
            return redirect()
                ->route('order-detail', ['order' => $orderId])
                ->with('error', 'This order can no longer be cancelled.');
        }

        $order->cancel();

        return redirect()
            ->route('order-detail', ['order' => $orderId])
            ->with('success', 'Your order has been cancelled successfully.');
    }

    public function userProfile()
    {
        $user = auth()->user();

        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'digits:10', Rule::unique('users', 'phone')->ignore($user->id)],
            'address' => 'nullable|string|max:255',
            'profile_pic' => 'nullable|image|max:2048',
        ], [
            'phone.digits' => 'Phone number must be exactly 10 digits.',
            'phone.unique' => 'This phone number is already registered by another user.',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address;

        if ($request->hasFile('profile_pic')) {
            // Delete old pic if exists
            if ($user->profile_pic && \Storage::disk('public')->exists($user->profile_pic)) {
                \Storage::disk('public')->delete($user->profile_pic);
            }
            $user->profile_pic = $request->file('profile_pic')->store('profiles', 'public');
        }

        if ($request->boolean('remove_pic') && $user->profile_pic) {
            \Storage::disk('public')->delete($user->profile_pic);
            $user->profile_pic = null;
        }

        $user->save();

        if (! empty($user->address)) {
            $formattedPhone = $user->phone ? (str_starts_with($user->phone, '+977-') ? $user->phone : '+977-'.$user->phone) : null;
            ShippingAddress::saveAsDefault($user->id, $user->address, $formattedPhone);
        }

        NotificationService::profileUpdated($user, 'profile');

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[\^$*.\[\]{}()?\-"!@#%&\/\\,><\':;|_~`+=]/',
            ],
        ], [
            'new_password.regex' => 'Password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'new_password.min' => 'Password must be at least 8 characters.',
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user = auth()->user();

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

        // Change password
        $user->password = \Hash::make($request->new_password);
        $user->save();

        NotificationService::profileUpdated($user, 'password');

        return redirect()->back()
            ->with('password_success', 'Password changed successfully.')
            ->withFragment('password-section');
    }

    public function userNotification(Request $request)
    {
        $user = auth()->user();
        $type = $request->query('type'); // orders | deliveries | account | null = all

        $typeGroups = [
            'orders' => ['order_confirmed', 'order_cancelled'],
            'deliveries' => ['order_shipped', 'order_delivered', 'return_approved'],
            'account' => ['profile_updated'],
        ];

        $allUserTypes = array_merge(...array_values($typeGroups));

        $query = $user->appNotifications()->whereIn('type', $allUserTypes)->latest('created_at');

        if ($type && isset($typeGroups[$type])) {
            $query->whereIn('type', $typeGroups[$type]);
        }

        $notifications = $query->paginate(7)->withQueryString();
        $unreadCount = $user->appNotifications()->whereIn('type', $allUserTypes)->where('is_read', false)->count();

        return view('user.notification', compact('notifications', 'unreadCount', 'type'));
    }

    public function markNotificationRead(Request $request, $id)
    {
        $notification = auth()->user()->appNotifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllNotificationsRead(Request $request)
    {
        $typeGroups = [
            'orders' => ['order_confirmed', 'order_cancelled'],
            'deliveries' => ['order_shipped', 'order_delivered', 'return_requested', 'return_approved'],
            'account' => ['payment_received', 'profile_updated'],
        ];
        $allUserTypes = array_merge(...array_values($typeGroups));

        auth()->user()->appNotifications()
            ->whereIn('type', $allUserTypes)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}

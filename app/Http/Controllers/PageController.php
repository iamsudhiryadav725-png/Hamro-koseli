<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PageController extends Controller
{
    public function home()
    {
        // Fetch approved vendors with their active products
        $vendors = Vendor::where('status', 'approved')
            ->with(['products' => function ($query) {
                $query->where('status', 'active')->limit(8);
            }, 'products.category', 'products.images'])
            ->limit(10)
            ->get();

        // Fallback: Get featured active products if no vendor data
        $featuredProducts = Product::where('status', 'active')
            ->with(['category', 'vendor', 'images', 'variants'])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Fetch 4 random active categories
        $categories = Category::where('status', 'active')
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Fetch top sellers dynamically
        $topSellers = Product::where('status', 'active')
            ->with(['category', 'vendor', 'images', 'variants'])
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->take(4)
            ->get();

        // Fetch today's deals products (limited to 4 for home page)
        $todaysDeals = Product::with(['category', 'vendor', 'images'])
            ->where('status', 'active')
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->whereColumn('discount_price', '<', 'price')
            ->orderByRaw('((price - discount_price) / price) DESC')
            ->limit(4)
            ->get();

        // Fetch trending products — top 3 by number of order items (most sold)
        $trendingProducts = Product::where('status', 'active')
            ->with(['category', 'vendor', 'images', 'variants'])
            ->withCount('orderItems')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->orderByDesc('order_items_count')
            ->take(3)
            ->get();

        return view('welcome', [
            'vendors' => $vendors,
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'topSellers' => $topSellers,
            'todaysDeals' => $todaysDeals,
            'trendingProducts' => $trendingProducts,
        ]);
    }

    public function categories()
    {
        $categories = Category::where('status', 'active')->get();

        return view('categories', compact('categories'));
    }

    // Shared query logic for shop() and viewProduct()
    private function buildShopData(): array
    {
        // The actual price range across all active products — drives the
        // slider's min/max bounds so it's never a stale hardcoded number.
        $priceBounds = Product::where('status', 'active')
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $priceFloor = (int) floor($priceBounds->min_price ?? 0);
        $priceCeil = (int) ceil($priceBounds->max_price ?? 0);

        // Guard against a single-product / all-same-price catalogue, where
        // floor === ceil would collapse the slider to a single point.
        if ($priceCeil <= $priceFloor) {
            $priceCeil = $priceFloor + 100;
        }

        $query = Product::with(['category', 'vendor', 'images', 'variants'])
            ->where('status', 'active');

        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        if (($categorySlug = request('category')) && $categorySlug !== 'all') {
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if (request()->filled('min_price')) {
            $query->where('price', '>=', request('min_price'));
        }
        if (request()->filled('max_price')) {
            $query->where('price', '<=', request('max_price'));
        }

        if (request()->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        switch (request('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'popularity':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            default:
                $query->inRandomOrder();
        }

        return [
            'products' => $query->paginate(12)->withQueryString(),
            'categories' => Category::where('status', 'active')->get(),
            'priceFloor' => $priceFloor,
            'priceCeil' => $priceCeil,
        ];
    }

    public function shop()
    {
        return view('shop', $this->buildShopData());
    }

    public function new_arrival()
    {
        $products = Product::with(['category', 'vendor', 'images'])
            ->where('status', 'active')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('new_arrival', compact('products'));
    }

    public function todays_deals()
    {
        $dealsBase = Product::with(['category', 'vendor', 'images'])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->where('status', 'active')
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->whereColumn('discount_price', '<', 'price');

        if (($categorySlug = request('category')) && $categorySlug !== 'all') {
            $dealsBase->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        switch (request('sort')) {
            case 'price-asc':
                $dealsBase->orderBy('discount_price', 'asc');
                break;
            case 'price-desc':
                $dealsBase->orderBy('discount_price', 'desc');
                break;
            default:
                $dealsBase->orderByRaw('((price - discount_price) / price) DESC');
        }

        $products = $dealsBase->paginate(8)->withQueryString();

        $categories = Product::with('category')
            ->where('status', 'active')
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->whereColumn('discount_price', '<', 'price')
            ->get()
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->values();

        // Pull the top 20 products by discount percentage, then randomly pick 5
        // so Featured Star Deals rotates on every page load while always showing
        // the best-discounted items in the catalogue.
        $featuredDeals = Product::with(['category', 'vendor', 'images'])
            ->where('status', 'active')
            ->whereNotNull('discount_price')
            ->where('discount_price', '>', 0)
            ->whereColumn('discount_price', '<', 'price')
            ->orderByRaw('((price - discount_price) / price) DESC')
            ->take(20)
            ->get()
            ->shuffle()
            ->take(5);

        $trendingProducts = Product::with(['category', 'vendor', 'images'])
            ->withCount('orderItems')
            ->where('status', 'active')
            ->orderByDesc('order_items_count')
            ->take(6)
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
            $dealEndsAt = Carbon::parse($dealEndsAt, config('app.timezone'))->toIso8601String();
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
            // Strip any accidental leading "storage/" prefix or leading slashes
            $dealBgImage = ltrim(preg_replace('#^storage/#', '', $dealBgImage), '/');
        }

        return view('todays-deals', compact('products', 'categories', 'featuredDeals', 'trendingProducts', 'dealEndsAt', 'dealBgImage'));
    }

    public function top_sellers()
    {
        $products = Product::with(['category', 'vendor', 'images', 'variants'])
            ->where('status', 'active')
            ->withCount('orderItems')
            ->orderByDesc('order_items_count')
            ->paginate(12)
            ->withQueryString();

        if ($products->isEmpty()) {
            $products = collect([
                (object) [
                    'id' => 1,
                    'name' => 'Copper Singing Bowl',
                    'price' => 4500.00,
                    'discount_price' => null,
                    'image' => 'images/1st-image.png',
                    'category' => (object) ['cat_name' => 'Metalware'],
                    'vendor' => (object) ['vendor_name' => 'Patan Crafts'],
                    'description' => 'Experience the meditative resonance of ancient Patan copper singing bowls, hand-hammered with care.',
                    'rating' => 5,
                    'reviews_count' => 124,
                    'sales_count' => 842,
                    'tag' => 'Terai Plains',
                    'stock' => 10,
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Thimi Crackle Bowl',
                    'price' => 3500.00,
                    'discount_price' => null,
                    'image' => 'images/2nd-image.png',
                    'category' => (object) ['cat_name' => 'Pottery'],
                    'vendor' => (object) ['vendor_name' => 'Kancha\'s Pottery'],
                    'description' => 'Authentic ceramic crackle bowl handmade by master artisans in Bhaktapur.',
                    'rating' => 4.5,
                    'reviews_count' => 92,
                    'sales_count' => 765,
                    'tag' => 'Bhaktapur',
                    'stock' => 15,
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Patan Floral Lattice',
                    'price' => 2500.00,
                    'discount_price' => null,
                    'image' => 'images/Table.png',
                    'category' => (object) ['cat_name' => 'Woodcraft'],
                    'vendor' => (object) ['vendor_name' => 'Patan Woodcrafts'],
                    'description' => 'Beautifully hand-carved floral lattice wooden wall art panel.',
                    'rating' => 5,
                    'reviews_count' => 56,
                    'sales_count' => 610,
                    'tag' => 'Artisan Made',
                    'stock' => 5,
                ],
                (object) [
                    'id' => 4,
                    'name' => 'Hand-Woven Dhankuta Dhaka',
                    'price' => 2500.00,
                    'discount_price' => 2200.00,
                    'image' => 'images/4th-image.png',
                    'category' => (object) ['cat_name' => 'Textiles'],
                    'vendor' => (object) ['vendor_name' => 'Dhankuta Weavers'],
                    'description' => 'Intricately woven traditional Dhaka fabric, handmade in Dhankuta.',
                    'rating' => 4.5,
                    'reviews_count' => 84,
                    'sales_count' => 590,
                    'tag' => 'Dhankuta',
                    'stock' => 8,
                ],
                (object) [
                    'id' => 5,
                    'name' => 'Himalayan Lokta Journal',
                    'price' => 1500.00,
                    'discount_price' => 1200.00,
                    'image' => 'images/aboutus.jpg',
                    'category' => (object) ['cat_name' => 'Paper'],
                    'vendor' => (object) ['vendor_name' => 'Himalayan Paper St.'],
                    'description' => 'Eco-friendly journal made from traditional hand-pressed Lokta paper in Mustang.',
                    'rating' => 4,
                    'reviews_count' => 45,
                    'sales_count' => 480,
                    'tag' => 'Mustang',
                    'stock' => 20,
                ],
                (object) [
                    'id' => 6,
                    'name' => 'Silver Filigree Pendant',
                    'price' => 4500.00,
                    'discount_price' => null,
                    'image' => 'images/Jewlery and Accessory.png',
                    'category' => (object) ['cat_name' => 'Jewelry'],
                    'vendor' => (object) ['vendor_name' => 'Newar Silversmiths'],
                    'description' => 'Stunning handmade silver filigree pendant crafted in Kathmandu Valley.',
                    'rating' => 5,
                    'reviews_count' => 78,
                    'sales_count' => 450,
                    'tag' => 'Kathmandu Valley',
                    'stock' => 12,
                ],
                (object) [
                    'id' => 7,
                    'name' => 'Bhaktapur Clay Pot',
                    'price' => 1800.00,
                    'discount_price' => 1500.00,
                    'image' => 'images/pot.png',
                    'category' => (object) ['cat_name' => 'Pottery'],
                    'vendor' => (object) ['vendor_name' => 'Praiapati Clay Art'],
                    'description' => 'Traditional clay pot, perfect for cooking or decoration, fired in Bhaktapur.',
                    'rating' => 4.5,
                    'reviews_count' => 110,
                    'sales_count' => 410,
                    'tag' => 'Bhaktapur',
                    'stock' => 18,
                ],
                (object) [
                    'id' => 8,
                    'name' => 'Handwoven Wool Sweater',
                    'price' => 1899.00,
                    'discount_price' => 1299.00,
                    'image' => 'images/Sweaters.png',
                    'category' => (object) ['cat_name' => 'Textiles'],
                    'vendor' => (object) ['vendor_name' => 'The Wool Studio'],
                    'description' => 'Warm and cozy handwoven wool sweater made by artisans in Helambu.',
                    'rating' => 4.5,
                    'reviews_count' => 134,
                    'sales_count' => 380,
                    'tag' => 'Helambu',
                    'stock' => 10,
                ],
            ]);
        }

        return view('top-sellers', compact('products'));
    }

    public function about_us()
    {
        return view('about');
    }

    public function wishlist()
    {
        return view('wishlist');
    }

    public function privacy()
    {
        return view('privacy');
    }

    public function contactus()
    {
        return view('contact-us');
    }

    public function cart()
    {
        return view('cart');
    }

    public function terms_conditions()
    {
        return view('terms-conditions');
    }

    public function return_policy()
    {
        return view('return-policy');
    }

    public function seller_policy()
    {
        return view('seller-policy');
    }

    public function faq()
    {
        return view('faq');
    }

    public function shipping_policy()
    {
        return view('shipping-policy');
    }

    public function viewProduct($slug)
    {
        $product = Product::with(['category', 'vendor', 'images', 'variants', 'reviews.user'])
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->firstOrFail();

        $product->effective_price = method_exists($product, 'effectivePrice') ? $product->effectivePrice() : $product->price;
        $product->original_price = method_exists($product, 'originalPrice') ? $product->originalPrice() : $product->price;
        $product->discount_price = method_exists($product, 'resolvedDiscountPrice') ? $product->resolvedDiscountPrice() : $product->discount_price;
        $product->primary_image_url = method_exists($product, 'primaryImageUrl') ? $product->primaryImageUrl() : asset($product->image);
        $product->category_name = $product->category?->cat_name ?? $product->category?->name ?? 'Crafts';
        $product->vendor_name = $product->vendor?->vendor_name ?? $product->vendor?->business_name ?? $product->vendor?->name ?? 'Local Artisan';

        $recentlyViewed = session()->get('recently_viewed', []);
        $recentlyViewed = array_filter($recentlyViewed, fn ($pid) => $pid != $product->id);
        array_unshift($recentlyViewed, $product->id);
        $recentlyViewed = array_slice($recentlyViewed, 0, 20);
        session()->put('recently_viewed', $recentlyViewed);

        // Fetch related products from same category or vendor
        $relatedProducts = Product::where('status', 'active')
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product) {
                if ($product->category_id) {
                    $q->where('category_id', $product->category_id);
                }
                if ($product->vendor_id) {
                    $q->orWhere('vendor_id', $product->vendor_id);
                }
            })
            ->with(['category', 'vendor', 'images'])
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $avgRating = round($product->reviews()->avg('rating') ?? 5, 1);
        $reviewsCount = $product->reviews()->count();

        $reviews = Review::with('user')
            ->where('product_id', $product->id)
            ->orderBy('created_at', 'desc')
            ->paginate(6);

        $userReview = null;
        $canReview = false;
        if (auth()->check()) {
            $eligibility = $this->checkReviewEligibility($product->id, auth()->id());
            $canReview = $eligibility['eligible'];
            if ($eligibility['existing'] && $eligibility['review']) {
                $userReview = $eligibility['review'];
            }
        }

        return view('product-details', compact('product', 'relatedProducts', 'avgRating', 'reviewsCount', 'reviews', 'userReview', 'canReview'));
    }

    public function getProductReviews(Request $request, $id)
    {
        $perPage = 6;
        $reviews = Review::with('user:id,name')
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'reviews' => $reviews->getCollection()->map(function ($review) {
                return [
                    'id' => $review->id,
                    'user_name' => $review->user?->name ?? 'Anonymous',
                    'rating' => $review->rating,
                    'comment' => $review->comment,
                    'verified_purchase' => $review->verified_purchase,
                    'reply' => $review->reply,
                    'replied_at' => $review->replied_at ? $review->replied_at->format('M j, Y') : null,
                    'date' => $review->created_at->format('M j, Y'),
                ];
            }),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    private function checkReviewEligibility($productId, $userId)
    {
        // Purchase verify check
        $verified = OrderItem::where('product_id', $productId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->whereHas('order', fn ($q) => $q->where('user_id', $userId))
            ->exists();

        if (! $verified) {
            return [
                'eligible' => false,
                'message' => 'You must purchase this product before writing a review.',
            ];
        }

        // Already reviewed check
        $existing = Review::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        return [
            'eligible' => true,
            'verified' => $verified,
            'existing' => $existing ? true : false,
            'review' => $existing ? [
                'rating' => $existing->rating,
                'comment' => $existing->comment,
            ] : null,
        ];
    }

    public function canReviewProduct($id)
    {
        $userId = auth()->id();

        if (! $userId) {
            return response()->json([
                'eligible' => false,
                'message' => 'Please login to write a review.',
            ]);
        }

        return response()->json(
            $this->checkReviewEligibility($id, $userId)
        );
    }

    public function storeProductReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $userId = auth()->id();

        if (! $userId) {
            return response()->json([
                'success' => false,
                'message' => 'Please login first.',
            ], 401);
        }

        $result = $this->checkReviewEligibility($id, $userId);

        if (! $result['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 403);
        }

        // Check if we are updating an existing review or creating a new one
        $review = Review::where('user_id', $userId)
            ->where('product_id', $id)
            ->first();

        if ($review) {
            // Update review
            $review->update([
                'rating' => $request->rating,
                'comment' => $request->comment,
                'verified_purchase' => $result['verified'],
                // Reset vendor reply when user edits their review
                'reply' => null,
                'replied_at' => null,
            ]);
            $message = 'Review updated successfully!';
        } else {
            // Create new review
            $review = Review::create([
                'user_id' => $userId,
                'product_id' => $id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'verified_purchase' => $result['verified'],
            ]);
            $message = 'Review submitted successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'review' => [
                'id' => $review->id,
                'user_name' => auth()->user()->name,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'verified_purchase' => $review->verified_purchase,
                'reply' => $review->reply,
                'replied_at' => $review->replied_at ? $review->replied_at->format('M j, Y') : null,
                'date' => $review->created_at->format('M j, Y'),
            ],
        ]);
    }

    public function sitemap()
    {
        $products = Product::where('status', 'active')->get(['slug', 'updated_at']);
        $categories = Category::where('status', 'active')->get(['slug', 'updated_at']);

        return response()->view('sitemap', compact('products', 'categories'))
            ->header('Content-Type', 'application/xml');
    }

    public function contactusSubmit(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Mail::to(env('ADMIN_EMAIL'))
            ->send(new ContactFormSubmitted(
                firstName: $request->first_name,
                lastName: $request->last_name,
                email: $request->email,
                messageSubject: $request->subject,
                messageBody: $request->message,
            ));

        return back()->with('success', 'Thank you! Your message has been sent. We\'ll get back to you soon.');
    }

}

<x-frontend-layout
    :title="$product->name . ' - Hamro Koseli'"
    :description="Str::limit(strip_tags($product->description ?? 'Handmade product in Nepal'), 160)"
>

<div class="bg-[#F4EAE1] min-h-screen pb-12 pt-4 text-[#3A2A1F]">

    <!-- Breadcrumb Navigation -->
    <div class="container mx-auto px-4 sm:px-6 md:px-12 max-w-7xl mb-6">
        <nav class="text-xs font-semibold text-[#3A2A1F]/70 flex items-center gap-2 flex-wrap">
            <a href="/" class="hover:text-[#C65A3A] transition">Home</a>
            <span class="text-[#3A2A1F]/40">&rsaquo;</span>
            <a href="{{ route('shop') }}" class="hover:text-[#C65A3A] transition">Shop</a>
            <span class="text-[#3A2A1F]/40">&rsaquo;</span>
            @if($product->category)
                <a href="{{ route('shop', ['category' => $product->category->slug ?? $product->category->id]) }}" class="hover:text-[#C65A3A] transition">
                    {{ $product->category_name }}
                </a>
                <span class="text-[#3A2A1F]/40">&rsaquo;</span>
            @endif
            <span class="text-[#C65A3A] font-bold truncate max-w-[200px] sm:max-w-xs">{{ $product->name }}</span>
        </nav>
    </div>

    <!-- Main Container -->
    <div class="container mx-auto px-4 sm:px-6 md:px-12 max-w-7xl space-y-10">

        <!-- Top Product Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 md:p-10 shadow-xl border border-[#ebd7be]/60 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            <!-- Left: Single Product Image Showcase -->
            <div class="lg:col-span-6 space-y-5">
                <!-- Main Image Card -->
                <div class="relative w-full aspect-[4/3] sm:aspect-square rounded-2xl overflow-hidden bg-[#FAF6F0] border border-[#ebd7be]/50 shadow-inner group">
                    <img id="main-product-image" src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                    @if($product->hasDiscount())
                        <span class="absolute top-4 left-4 bg-[#C65A3A] text-white text-xs font-extrabold px-3 py-1 rounded-full shadow-md tracking-wider uppercase">
                            OFFER
                        </span>
                    @endif
                </div>

                <!-- Value Proposition Badges -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-[#ebd7be]/40">
                    <div class="flex items-center gap-3 bg-[#FFF7EF] p-4 rounded-2xl border border-[#ebd7be]/50 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-[#C65A3A]/10 text-[#C65A3A] flex items-center justify-center shrink-0">
                            <i class="fas fa-truck text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-[#1F3D2E] uppercase tracking-wide">Insured Delivery</h4>
                            <p class="text-[11px] text-[#3A2A1F]/60 font-medium">3-5 days across Nepal</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-[#FFF7EF] p-4 rounded-2xl border border-[#ebd7be]/50 shadow-xs">
                        <div class="w-10 h-10 rounded-xl bg-[#C65A3A]/10 text-[#C65A3A] flex items-center justify-center shrink-0">
                            <i class="fas fa-certificate text-lg"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-[#1F3D2E] uppercase tracking-wide">100% Authentic</h4>
                            <p class="text-[11px] text-[#3A2A1F]/60 font-medium">Nepalese Craftsmanship</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Info & Actions -->
            <div class="lg:col-span-6 space-y-6">

                <!-- Category & Stock Status -->
                <div class="flex items-center justify-between gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 bg-[#1F3D2E]/10 text-[#1F3D2E] text-xs font-bold uppercase tracking-wider px-3.5 py-1 rounded-full border border-[#1F3D2E]/20">
                        <i class="fas fa-gem text-[10px] text-[#C65A3A]"></i>
                        {{ $product->category_name }}
                    </span>

                    @if(($product->stock ?? 0) > 0)
                        <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-200 shadow-xs">
                            <i class="fas fa-check-circle mr-1"></i> In Stock ({{ $product->stock }})
                        </span>
                    @else
                        <span class="text-xs font-bold text-red-600 bg-red-50 px-3.5 py-1 rounded-full border border-red-200">
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Product Name -->
                <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#1F3D2E] leading-tight font-serif">
                    {{ $product->name }}
                </h1>

                <!-- Rating Summary -->
                <div class="flex items-center gap-3 text-sm">
                    <div class="flex text-amber-400 text-base gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($avgRating))
                                <i class="fas fa-star"></i>
                            @elseif($i - $avgRating < 1 && $i - $avgRating > 0)
                                <i class="fas fa-star-half-alt"></i>
                            @else
                                <i class="far fa-star text-slate-300"></i>
                            @endif
                        @endfor
                    </div>
                    <a href="#reviews-section" class="text-xs font-bold text-[#C65A3A] hover:underline">
                        {{ number_format($avgRating, 1) }} ({{ $reviewsCount }} customer {{ Str::plural('review', $reviewsCount) }})
                    </a>
                </div>

                <!-- Price Box -->
                <div class="bg-[#FFF7EF] p-5 rounded-2xl border border-[#ebd7be]/60 flex flex-wrap items-baseline gap-3 shadow-xs">
                    <span class="text-3xl font-extrabold text-[#C65A3A]">
                        Rs. {{ number_format($product->effective_price, 2) }}
                    </span>
                    @if($product->hasDiscount())
                        <span class="text-slate-400 text-lg line-through font-medium">
                            Rs. {{ number_format($product->original_price, 2) }}
                        </span>
                        @php
                            $savings = $product->original_price - $product->effective_price;
                            $discountPct = round(($savings / $product->original_price) * 100);
                        @endphp
                        <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-lg">
                            SAVE {{ $discountPct }}%
                        </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="text-sm text-[#3A2A1F]/80 leading-relaxed font-medium modal-product-desc prose max-w-none">
                    {!! Str::contains($product->description, ['<p', '<br', '<div', '<ul', '<ol', '<h', '<b', '<i', '<strong', '<em']) ? $product->description : nl2br(e($product->description)) !!}
                </div>

                <!-- Vendor Info Card -->
                @if($product->vendor)
                    <div class="bg-[#FAF6F0] border border-[#ebd7be]/60 rounded-2xl p-4 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-full bg-[#1F3D2E] text-white flex items-center justify-center font-bold text-lg border border-[#ebd7be]">
                                {{ strtoupper(substr($product->vendor_name, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-[#C65A3A] leading-tight">
                                    {{ $product->vendor_name }}
                                </h3>
                                <p class="text-[11px] text-[#3A2A1F]/60 font-semibold mt-0.5">Verified Artisan Seller on Hamro Koseli</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-bold text-[#1F3D2E] bg-white border border-[#ebd7be] px-3 py-1 rounded-full">
                            Verified Seller
                        </span>
                    </div>
                @endif

                <!-- Quantity & Add to Cart -->
                <div class="space-y-4 pt-2">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-[#1F3D2E]">Quantity</span>
                        <div class="flex items-center border border-[#ebd7be] rounded-full bg-white px-3 py-1.5 gap-4 shadow-xs">
                            <button type="button" onclick="decrementDetailQty()" class="text-[#3A2A1F] hover:text-[#C65A3A] font-bold text-base w-6 h-6 flex items-center justify-center cursor-pointer focus:outline-none">−</button>
                            <input type="number" id="detail-qty-input" value="1" min="1" max="{{ $product->stock ?? 99 }}" class="text-sm font-bold text-[#1F3D2E] w-10 text-center bg-transparent border-none outline-none">
                            <button type="button" onclick="incrementDetailQty()" class="text-[#3A2A1F] hover:text-[#C65A3A] font-bold text-base w-6 h-6 flex items-center justify-center cursor-pointer focus:outline-none">+</button>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <button type="button"
                                id="detail-add-to-cart-btn"
                                class="add-to-cart-btn flex-1 bg-[#C65A3A] hover:bg-[#b04a2c] text-white font-bold py-3.5 px-6 rounded-2xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2 text-sm cursor-pointer"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $product->effective_price }}"
                                data-product-image="{{ $product->primary_image_url }}"
                                data-product-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}"
                                data-product-category="{{ $product->category_name }}"
                                data-product-qty="1">
                            <i class="fas fa-shopping-bag"></i>
                            Add to Cart
                        </button>

                        <button type="button"
                                onclick="handleBuyNow('{{ $product->id }}')"
                                class="flex-1 border-2 border-[#C65A3A] text-[#C65A3A] hover:bg-[#C65A3A] hover:text-white font-bold py-3.5 px-6 rounded-2xl transition duration-200 text-sm flex items-center justify-center gap-2 cursor-pointer">
                            <i class="fas fa-bolt"></i>
                            Buy Now
                        </button>

                        <button type="button"
                                class="wishlist-btn border border-[#ebd7be] text-[#C65A3A] hover:bg-[#FFF7EF] w-12 h-12 rounded-2xl flex items-center justify-center transition shrink-0 cursor-pointer"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-slug="{{ $product->slug }}"
                                data-product-price="{{ $product->effective_price }}"
                                data-product-image="{{ $product->primary_image_url }}"
                                data-product-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}
                                data-product-category="{{ $product->category_name }}">
                            <i class="far fa-heart text-lg"></i>
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- Specifications & Care Section -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 md:p-10 shadow-lg border border-[#ebd7be]/60 space-y-6">
            <h3 class="text-xl font-bold text-[#1F3D2E] font-serif border-b border-[#ebd7be]/50 pb-3 flex items-center gap-2">
                <i class="fas fa-list-alt text-[#C65A3A]"></i>
                Product Specifications
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-bold text-[#1F3D2E]">Category</span>
                        <span class="text-[#3A2A1F]/70">{{ $product->category_name }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-bold text-[#1F3D2E]">Origin</span>
                        <span class="text-[#3A2A1F]/70">Handcrafted in Nepal</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-bold text-[#1F3D2E]">Artisan Guarantee</span>
                        <span class="text-[#3A2A1F]/70">100% Authentic Handmade</span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-bold text-[#1F3D2E]">Stock Code (SKU)</span>
                        <span class="text-[#3A2A1F]/70">{{ $product->sku ?? ('HK-' . $product->id) }}</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-bold text-[#1F3D2E]">Stock Availability</span>
                        <span class="text-[#3A2A1F]/70">{{ $product->stock ?? 1 }} units</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-slate-100">
                        <span class="font-bold text-[#1F3D2E]">Shipping Time</span>
                        <span class="text-[#3A2A1F]/70">3 - 5 Days Standard Shipping</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div id="reviews-section" class="bg-white rounded-3xl p-6 sm:p-8 md:p-10 shadow-lg border border-[#ebd7be]/60 space-y-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-[#ebd7be]/50 pb-5">
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-[#1F3D2E] font-serif flex items-center gap-2">
                        <i class="fas fa-star text-amber-400"></i>
                        Customer Reviews ({{ $reviewsCount }})
                    </h3>
                    <p class="text-xs text-[#3A2A1F]/60 mt-1 font-medium">Ratings and reviews from customers who purchased this item</p>
                </div>

                <button type="button" id="page-toggle-review-btn" onclick="toggleReviewForm()" class="bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-xs font-bold px-5 py-3 rounded-2xl transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    @auth
                        @if($userReview)
                            <i class="fas fa-edit"></i>
                            Edit Review
                        @else
                            <i class="fas fa-pen"></i>
                            Write a Review
                        @endif
                    @else
                        <i class="fas fa-pen"></i>
                        Write a Review
                    @endauth
                </button>
            </div>

            <!-- Review Form -->
            <div id="page-add-review-section" class="hidden bg-[#FFF7EF] border border-[#C65A3A]/30 rounded-3xl p-6 sm:p-8 space-y-5">
                <h4 id="page-review-form-title" class="text-lg font-bold text-[#1F3D2E] flex items-center gap-2">
                    <i class="fas fa-edit text-[#C65A3A]"></i>
                    {{ $userReview ? 'Edit Your Review' : 'Share Your Feedback' }}
                </h4>

                @auth
                    <form id="page-product-review-form" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-[#1F3D2E] uppercase mb-2">Rating</label>
                            <div class="flex gap-2 text-2xl" id="page-star-selector">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setStarRating({{ $i }})" class="page-star-btn text-slate-300 hover:text-amber-400 transition" data-rating="{{ $i }}">
                                        <i class="far fa-star"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" id="page-rating-value" value="{{ $userReview['rating'] ?? '' }}">
                        </div>

                        <div>
                            <label for="page-review-comment" class="block text-xs font-bold text-[#1F3D2E] uppercase mb-2">Review Comment</label>
                            <textarea id="page-review-comment" rows="4" class="w-full rounded-2xl border border-[#ebd7be] p-4 text-sm focus:border-[#C65A3A] outline-none" placeholder="How was the quality of this product?">{{ $userReview['comment'] ?? '' }}</textarea>
                        </div>

                        <button type="submit" id="page-submit-review-btn" class="bg-[#1F3D2E] hover:bg-[#16301f] text-white text-xs font-bold px-6 py-3 rounded-xl transition cursor-pointer">
                            {{ $userReview ? 'Update Review' : 'Submit Review' }}
                        </button>
                    </form>
                @else
                    <div class="text-center py-6 space-y-3">
                        <p class="text-xs text-[#3A2A1F]/70 font-semibold">Please log in to leave a review for this product.</p>
                        <button onclick="openLoginModal(null, 'login')" class="bg-[#1F3D2E] text-white text-xs font-bold px-6 py-2.5 rounded-xl hover:bg-[#16301f] transition">
                            Login Now
                        </button>
                    </div>
                @endauth
            </div>

            <!-- Reviews Listing -->
            @if($reviews && $reviews->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($reviews as $rev)
                        <div class="bg-[#FAF6F0] p-5 rounded-2xl border border-[#ebd7be]/50 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-[#C65A3A] text-white flex items-center justify-center text-xs font-bold">
                                        {{ strtoupper(substr($rev->user?->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-bold text-[#1F3D2E]">{{ $rev->user?->name ?? 'Customer' }}</h5>
                                        <span class="text-[10px] text-slate-400">{{ $rev->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                <div class="flex text-amber-400 text-xs gap-0.5">
                                    @for($s = 1; $s <= 5; $s++)
                                        <i class="{{ $s <= $rev->rating ? 'fas fa-star' : 'far fa-star text-slate-300' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-[#3A2A1F]/80 leading-relaxed">
                                {{ $rev->comment }}
                            </p>
                            @if($rev->reply)
                                <div class="mt-3 ml-2 sm:ml-4 bg-[#E5DCD0]/30 border border-[#ebd7be]/30 rounded-xl p-3 space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[10px] font-bold text-[#C65A3A] flex items-center gap-1">
                                            <i class="fas fa-reply fa-flip-horizontal"></i> Vendor Response
                                        </span>
                                        @if($rev->replied_at)
                                            <span class="text-[8px] text-[#3A2A1F]/50 font-semibold">{{ $rev->replied_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-[#3A2A1F]/80 leading-relaxed font-medium">{{ $rev->reply }}</p>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="pt-4">
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-10 text-slate-400 text-xs font-medium bg-[#FAF6F0] rounded-2xl border border-[#ebd7be]/30">
                    No customer reviews yet. Be the first to share your feedback!
                </div>
            @endif
        </div>

        <!-- Related Products -->
        @if(isset($relatedProducts) && $relatedProducts->count() > 0)
            <div class="space-y-6 pt-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl sm:text-2xl font-bold text-[#1F3D2E] font-serif">
                        You Might Also Like
                    </h3>
                    <a href="{{ route('shop') }}" class="text-xs font-bold text-[#C65A3A] hover:underline">View All Products &rarr;</a>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($relatedProducts as $rel)
                        @php
                            $relImg = $rel->primaryImageUrl();
                            $relPrice = $rel->effectivePrice();
                        @endphp
                        <div class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                            <a href="{{ route('viewdetails', $rel->slug) }}" class="relative w-full aspect-square overflow-hidden bg-slate-100 block">
                                <img src="{{ $relImg }}" alt="{{ $rel->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </a>
                            <div class="p-3 sm:p-4 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-1 truncate">{{ $rel->category?->cat_name ?? 'Crafts' }}</span>
                                    <a href="{{ route('viewdetails', $rel->slug) }}" class="text-xs sm:text-sm font-bold text-[#1F3D2E] hover:text-[#C65A3A] transition line-clamp-1 block mb-2">
                                        {{ $rel->name }}
                                    </a>
                                    <span class="text-[#C65A3A] font-extrabold text-xs sm:text-sm block mb-3">
                                        Rs. {{ number_format($relPrice, 0) }}
                                    </span>
                                </div>
                                <a href="{{ route('viewdetails', $rel->slug) }}" class="w-full text-center bg-[#1F3D2E] hover:bg-[#16301f] text-white text-xs font-bold py-2 rounded-xl transition block">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    function changeMainImage(src, btn) {
        const mainImg = document.getElementById('main-product-image');
        if (mainImg) mainImg.src = src;
        document.querySelectorAll('.thumb-btn').forEach(b => {
            b.classList.remove('border-[#C65A3A]', 'opacity-100');
            b.classList.add('border-transparent', 'opacity-70');
        });
        if (btn) {
            btn.classList.remove('border-transparent', 'opacity-70');
            btn.classList.add('border-[#C65A3A]', 'opacity-100');
        }
    }

    function incrementDetailQty() {
        const input = document.getElementById('detail-qty-input');
        const cartBtn = document.getElementById('detail-add-to-cart-btn');
        if (input) {
            let val = parseInt(input.value) || 1;
            val++;
            input.value = val;
            if (cartBtn) cartBtn.setAttribute('data-product-qty', val);
        }
    }

    function decrementDetailQty() {
        const input = document.getElementById('detail-qty-input');
        const cartBtn = document.getElementById('detail-add-to-cart-btn');
        if (input) {
            let val = parseInt(input.value) || 1;
            if (val > 1) val--;
            input.value = val;
            if (cartBtn) cartBtn.setAttribute('data-product-qty', val);
        }
    }

    document.getElementById('detail-qty-input')?.addEventListener('change', function() {
        let val = parseInt(this.value) || 1;
        if (val < 1) val = 1;
        this.value = val;
        document.getElementById('detail-add-to-cart-btn')?.setAttribute('data-product-qty', val);
    });

    function handleBuyNow(productId) {
        if (!window.isLoggedIn) {
            if (typeof window.openLoginModal === 'function') window.openLoginModal(null, 'login');
            else window.location.href = '/userlogin';
            return;
        }
        const qtyInput = document.getElementById('detail-qty-input');
        const qty = qtyInput ? (parseInt(qtyInput.value) || 1) : 1;

        const activeProduct = {
            id: productId,
            name: "{{ $product->name }}",
            price: {{ $product->effective_price }},
            image: "{{ $product->primary_image_url }}",
            category: "{{ $product->category_name }}"
        };

        if (typeof window.addToCartAsync === 'function') {
            window.addToCartAsync(activeProduct, qty).then(() => {
                window.location.href = '/cart';
            });
        } else if (typeof window.addToCart === 'function') {
            window.addToCart(activeProduct, qty);
            window.location.href = '/cart';
        }
    }

    function toggleReviewForm() {
        const formSec = document.getElementById('page-add-review-section');
        const toggleBtn = document.getElementById('page-toggle-review-btn');
        if (!formSec) return;

        if (!formSec.classList.contains('hidden')) {
            formSec.classList.add('hidden');
            return;
        }

        if (!window.isLoggedIn) {
            formSec.classList.remove('hidden');
            return;
        }

        const originalHtml = toggleBtn ? toggleBtn.innerHTML : '';
        if (toggleBtn) {
            toggleBtn.disabled = true;
            toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
        }

        fetch('/product/{{ $product->id }}/can-review')
            .then(res => res.json())
            .then(data => {
                if (toggleBtn) {
                    toggleBtn.disabled = false;
                    toggleBtn.innerHTML = originalHtml;
                }

                if (!data.eligible) {
                    if (window.showToast) window.showToast(data.message || 'You cannot review this product.', 'error');
                    return;
                }

                applyPageReviewFormState(data.existing, data.review);
                formSec.classList.remove('hidden');
                formSec.scrollIntoView({ behavior: 'smooth', block: 'center' });
            })
            .catch(() => {
                if (toggleBtn) {
                    toggleBtn.disabled = false;
                    toggleBtn.innerHTML = originalHtml;
                }
                if (window.showToast) window.showToast('Something went wrong. Please try again.', 'error');
            });
    }

    function applyPageReviewFormState(isExisting, review) {
        const formTitle = document.getElementById('page-review-form-title');
        const submitBtn = document.getElementById('page-submit-review-btn');
        const toggleBtn = document.getElementById('page-toggle-review-btn');
        const rating = isExisting && review ? review.rating : '';
        const comment = isExisting && review ? (review.comment || '') : '';

        if (formTitle) {
            formTitle.innerHTML = isExisting
                ? '<i class="fas fa-edit text-[#C65A3A]"></i> Edit Your Review'
                : '<i class="fas fa-edit text-[#C65A3A]"></i> Share Your Feedback';
        }
        if (submitBtn) submitBtn.textContent = isExisting ? 'Update Review' : 'Submit Review';
        if (toggleBtn) {
            toggleBtn.innerHTML = isExisting
                ? '<i class="fas fa-edit"></i> Edit Review'
                : '<i class="fas fa-pen"></i> Write a Review';
        }

        const hiddenVal = document.getElementById('page-rating-value');
        const commentEl = document.getElementById('page-review-comment');
        if (hiddenVal) hiddenVal.value = rating;
        if (commentEl) commentEl.value = comment;
        if (rating) setStarRating(parseInt(rating, 10));
        else {
            document.querySelectorAll('.page-star-btn').forEach(btn => {
                const icon = btn.querySelector('i');
                if (icon) icon.className = 'far fa-star text-slate-300';
            });
        }
    }

    function initPageReviewForm() {
        @auth
            @if($userReview)
                applyPageReviewFormState(true, @json($userReview));
            @endif
        @endauth
    }

    initPageReviewForm();

    function setStarRating(rating) {
        const hiddenVal = document.getElementById('page-rating-value');
        if (hiddenVal) hiddenVal.value = rating;

        document.querySelectorAll('.page-star-btn').forEach(btn => {
            const r = parseInt(btn.getAttribute('data-rating'));
            const icon = btn.querySelector('i');
            if (icon) {
                if (r <= rating) {
                    icon.className = 'fas fa-star text-amber-400';
                } else {
                    icon.className = 'far fa-star text-slate-300';
                }
            }
        });
    }

    document.getElementById('page-product-review-form')?.addEventListener('submit', function(e) {
        e.preventDefault();
        const rating = document.getElementById('page-rating-value')?.value;
        const comment = document.getElementById('page-review-comment')?.value;
        const btn = document.getElementById('page-submit-review-btn');

        if (!rating) {
            if (window.showToast) window.showToast('Please select a star rating.', 'error');
            return;
        }

        if (btn) { btn.disabled = true; btn.textContent = 'Submitting...'; }

        fetch('/product/{{ $product->id }}/reviews', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ rating, comment })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = data.success ? 'Update Review' : @json($userReview ? 'Update Review' : 'Submit Review');
            }
            if (data.success) {
                if (window.showToast) window.showToast(data.message || 'Review submitted successfully!', 'success');
                applyPageReviewFormState(true, { rating: parseInt(rating, 10), comment });
                setTimeout(() => window.location.reload(), 1500);
            } else {
                if (window.showToast) window.showToast(data.message || 'Error submitting review.', 'error');
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = @json($userReview ? 'Update Review' : 'Submit Review');
            }
            if (window.showToast) window.showToast('Server error. Please try again.', 'error');
        });
    });
</script>
</x-frontend-layout>

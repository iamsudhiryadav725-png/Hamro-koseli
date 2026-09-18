<x-frontend-layout
    title="Hamro Koseli – Gifts & Surprises Delivered in Nepal"
    description="Hamro Koseli is Nepal's trusted gifting platform. Send gifts, sweets, and surprises to your loved ones across Nepal."
    ogImage="/images/og-images.jpg">


<div class="bg-[#F4EAE1] text-brand-dark leading-relaxed flex flex-col min-h-screen">

    <!-- Main Content -->
    <main class="w-full flex-grow">

        <!-- 1. Hero Welcome Banner -->
        <section class="relative bg-gradient-to-b from-[#1f3d2e] to-transparent text-white pt-12 sm:pt-16 pb-32 sm:pb-40 text-center px-4 sm:px-6">
            <div class="max-w-4xl mx-auto relative z-10">
                <h2 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-3 sm:mb-4 tracking-tight leading-tight">
                    Discover Authentic Artisan Crafts
                </h2>
                <p class="text-emerald-100/90 text-sm sm:text-base md:text-lg mb-6 sm:mb-8 max-w-xl mx-auto px-2">
                    Support local artisans and find unique, handmade treasures
                </p>

                <a href="{{ route('shop') }}" wire:navigate class="bg-[#b55b3d] hover:bg-[#a04f33] text-white font-bold px-6 sm:px-8 py-2.5 sm:py-3 rounded-full shadow-md transition duration-300 inline-block tracking-wide text-sm sm:text-base">
                    Shop Now
                </a>
            </div>
        </section>

        <!-- 2. Featured Products from Vendors Section - 3 COLUMNS ON MOBILE -->
        <section class="max-w-7xl mx-auto -mt-20 sm:-mt-24 px-4 sm:px-6 mb-12 sm:mb-16 relative z-20">
            <div class="mb-3 sm:mb-4 text-left">
                <div class="inline-flex items-center gap-1.5 bg-[#e5b842] text-brand-dark text-[10px] sm:text-xs font-bold uppercase tracking-wider px-3 sm:px-4 py-1 sm:py-1.5 rounded-md shadow-sm">
                    <span>Flash Sale &mdash; Limited Time</span>
                </div>
            </div>

            <!-- Grid: 2 columns on mobile, 4 on desktop -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6">
                @forelse($featuredProducts as $product)
                    @php
                        $imageUrl = $product->primaryImageUrl();
                        $dDiscount = $product->resolvedDiscountPrice();
                        $hasDiscount = !is_null($dDiscount) && $dDiscount > 0 && $dDiscount < $product->price;
                        $discountPrice = $hasDiscount ? $dDiscount : $product->price;
                    @endphp
                    <!-- Product Card -->
                    <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group {{ $loop->index >= 2 ? 'hidden sm:block' : '' }}">
                        <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100 cursor-pointer view-details-btn"
                             data-id="{{ $product->id }}"
                             data-slug="{{ $product->slug }}"
                             data-name="{{ $product->name }}"
                             data-price="{{ intval($discountPrice) }}"
                             data-original-price="{{ intval($product->price) }}"
                             data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                             data-image="{{ $imageUrl }}"
                             data-category="{{ $product->category?->cat_name ?? 'Uncategorized' }}"
                             data-vendor="{{ $product->vendor?->vendor_name ?? 'Unknown' }}"
                             data-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}"
                             data-stock="{{ $product->stock }}">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-10 sm:h-10 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-price="{{ intval($discountPrice) }}"
                                    data-product-image="{{ $imageUrl }}"
                                    data-product-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}"
                                    data-product-category="{{ $product->category?->cat_name ?? 'Uncategorized' }}">
                                <i class="far fa-heart text-[10px] sm:text-lg"></i>
                            </button>
                        </div>
                        <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1 truncate">{{ $product->category?->cat_name ?? 'Uncategorized' }}</span>
                                <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 cursor-pointer view-details-btn"
                                    data-id="{{ $product->id }}"
                                    data-slug="{{ $product->slug }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ intval($discountPrice) }}"
                                    data-original-price="{{ intval($product->price) }}"
                                    data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                    data-image="{{ $imageUrl }}"
                                    data-category="{{ $product->category?->cat_name ?? 'Uncategorized' }}"
                                    data-vendor="{{ $product->vendor?->vendor_name ?? 'Unknown' }}"
                                    data-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}"
                                    data-stock="{{ $product->stock }}">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex flex-wrap items-baseline gap-1 sm:gap-2 mb-2 sm:mb-4">
                                    <span class="text-[#C65A3A] font-bold text-xs sm:text-sm md:text-base block">Rs. {{ number_format($discountPrice, 0) }}</span>
                                    @if($hasDiscount)
                                        <span class="text-slate-400 text-[8px] sm:text-xs line-through font-semibold">Rs. {{ number_format($product->price, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex gap-1 sm:gap-2 mt-auto">
                                <a href="{{ route('viewdetails', $product->slug) }}"
                                   class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                   data-id="{{ $product->id }}"
                                   data-slug="{{ $product->slug }}"
                                   data-name="{{ $product->name }}"
                                   data-price="{{ intval($discountPrice) }}"
                                   data-original-price="{{ intval($product->price) }}"
                                   data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                   data-image="{{ $imageUrl }}"
                                   data-category="{{ $product->category?->cat_name ?? 'Uncategorized' }}"
                                   data-vendor="{{ $product->vendor?->vendor_name ?? 'Unknown' }}"
                                   data-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}"
                                   data-stock="{{ $product->stock }}">
                                    <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                                </a>
                                <button class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        data-product-price="{{ intval($discountPrice) }}"
                                        data-product-image="{{ $imageUrl }}"
                                        data-product-desc="{{ Str::limit(strip_tags($product->description ?? ''), 100) }}"
                                        data-product-category="{{ $product->category?->cat_name ?? 'Uncategorized' }}"
                                        {{ ($product->stock ?? 0) < 1 ? 'disabled' : '' }}>
                                    <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i>
                                    {{ ($product->stock ?? 0) < 1 ? 'Sold Out' : 'Add' }}
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-slate-500">No featured products available at this time.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 3. Shop By Category Section - 3 COLUMNS ON MOBILE -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 mb-12 sm:mb-16">
            <div class="flex items-center justify-between mb-4 sm:mb-6 md:mb-8">
                <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark">Shop by Category</h3>
                <a href="{{ route('categories') }}" wire:navigate class="text-brand-primary hover:text-[#a04f33] font-bold text-xs sm:text-sm flex items-center gap-1 transition-colors hover:underline">
                    <span>See All</span>
                    <i class="fa-solid fa-chevron-right text-[10px] sm:text-xs"></i>
                </a>
            </div>

            <!-- Grid: 3 columns on mobile, 4 on desktop -->
            <div class="grid grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3 md:gap-4 lg:gap-6">
                @forelse($categories as $category)
                    <a href="{{ route('shop', ['category' => $category->slug]) }}" wire:navigate class="bg-[#FDFBF7] rounded-xl sm:rounded-2xl overflow-hidden shadow-sm border border-amber-900/5 hover:shadow-md transition group block {{ $loop->index >= 3 ? 'hidden lg:block' : '' }}">
                        <div class="h-24 xs:h-28 sm:h-36 md:h-44 lg:h-56 overflow-hidden bg-slate-100 relative flex items-center justify-center">
                            @if($category->image)
                                <img src="{{ Storage::disk('public')->url($category->image) }}"
                                     alt="{{ $category->cat_name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-500 relative z-10"
                                     onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 bg-slate-100 p-2 text-center z-0 hidden">
                                    <i class="fa-solid fa-folder-open text-xl sm:text-2xl md:text-3xl mb-1"></i>
                                    <span class="text-[8px] sm:text-[10px] text-slate-400 mt-1">No Image</span>
                                </div>
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-400 bg-slate-100 p-2 text-center">
                                    <i class="fa-solid fa-folder-open text-xl sm:text-2xl md:text-3xl mb-1"></i>
                                    <span class="text-[8px] sm:text-[10px] text-slate-400 mt-1">No Image</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-1.5 sm:p-2 md:p-3 text-center">
                            <h4 class="text-[10px] sm:text-xs md:text-sm lg:text-base font-bold text-brand-dark group-hover:text-brand-primary transition-colors line-clamp-1">{{ $category->cat_name }}</h4>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full text-center py-8">
                        <p class="text-slate-500">No categories available at this time.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- 4. Today's Deals Section - 3 COLUMNS ON MOBILE -->
        <section id="todays-deals" class="max-w-7xl mx-auto px-4 sm:px-6 mb-12 sm:mb-16">
            <div class="bg-[#E5DCD0]/60 border border-[#ebd7be]/40 rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 lg:p-8 shadow-sm">

                <div class="flex items-center justify-between mb-4 sm:mb-5 md:mb-6 lg:mb-8">
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark flex items-center gap-2">
                        <span>Today's Deals</span>
                    </h3>
                    <a href="{{ route('todays-deals') }}" wire:navigate class="text-brand-primary hover:text-[#a04f33] font-bold text-xs sm:text-sm flex items-center gap-1 transition-colors hover:underline">
                        <span>See All</span>
                        <i class="fa-solid fa-chevron-right text-[10px] sm:text-xs"></i>
                    </a>
                </div>

                <!-- Grid: 2 columns on mobile, 4 on desktop -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6">
@forelse($todaysDeals as $product)
    @php
        $price = $product->price;
        $discountPrice = $product->discount_price ?? null;
        $hasDiscount = !is_null($discountPrice) && $discountPrice < $price;
        $displayPrice = $hasDiscount ? $discountPrice : $price;
        $discountPercentage = $hasDiscount ? round((($price - $discountPrice) / $price) * 100) : 0;
    @endphp
    <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
        <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100 cursor-pointer view-details-btn"
             data-id="{{ $product->id }}"
             data-slug="{{ $product->slug }}"
             data-name="{{ $product->name }}"
             data-price="{{ $displayPrice }}"
             data-original-price="{{ $price }}"
             data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
             data-image="{{ $product->primaryImageUrl() }}"
             data-category="{{ $product->category->cat_name ?? '' }}"
             data-vendor="{{ $product->vendor->vendor_name ?? 'Local Artisan' }}"
             data-desc="{{ $product->description }}"
             data-rating="{{ $product->rating ?? 5 }}"
             data-reviews="{{ $product->reviews_count ?? 0 }}"
             data-stock="{{ $product->stock ?? 10 }}">
            <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            
            @if ($hasDiscount)
                <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#C65A3A] text-white text-[7px] xs:text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm z-10">
                    -{{ $discountPercentage }}% OFF
                </span>
            @endif

            <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-10 sm:h-10 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-price="{{ $displayPrice }}"
                    data-product-image="{{ $product->primaryImageUrl() }}"
                    data-product-desc="{{ $product->description }}"
                    data-product-category="{{ $product->category->cat_name ?? '' }}">
                <i class="far fa-heart text-[10px] sm:text-lg"></i>
            </button>
        </div>
        <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
            <div>
                <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1 truncate">{{ $product->category->cat_name ?? '' }}</span>
                <h4 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 cursor-pointer view-details-btn"
                    data-id="{{ $product->id }}"
                    data-slug="{{ $product->slug }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $displayPrice }}"
                    data-original-price="{{ $price }}"
                    data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                    data-image="{{ $product->primaryImageUrl() }}"
                    data-category="{{ $product->category->cat_name ?? '' }}"
                    data-vendor="{{ $product->vendor->vendor_name ?? 'Local Artisan' }}"
                    data-desc="{{ $product->description }}"
                    data-rating="{{ $product->rating ?? 5 }}"
                    data-reviews="{{ $product->reviews_count ?? 0 }}"
                    data-stock="{{ $product->stock ?? 10 }}">
                    {{ $product->name }}
                </h4>
                <div class="flex flex-wrap items-baseline gap-1 sm:gap-2 mb-2 sm:mb-4">
                    <span class="text-[#C65A3A] font-bold text-xs sm:text-sm md:text-base block">Rs {{ number_format($displayPrice, 2) }}</span>
                    @if ($hasDiscount)
                        <span class="text-slate-400 text-[8px] sm:text-xs line-through font-semibold">Rs {{ number_format($price, 2) }}</span>
                    @endif
                </div>
            </div>
            <div class="flex gap-1 sm:gap-2 mt-auto">
                <a href="{{ route('viewdetails', $product->slug) }}"
                   class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                   data-id="{{ $product->id }}"
                   data-slug="{{ $product->slug }}"
                   data-name="{{ $product->name }}"
                   data-price="{{ $displayPrice }}"
                   data-original-price="{{ $price }}"
                   data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                   data-image="{{ $product->primaryImageUrl() }}"
                   data-category="{{ $product->category->cat_name ?? '' }}"
                   data-vendor="{{ $product->vendor->vendor_name ?? 'Local Artisan' }}"
                   data-desc="{{ $product->description }}"
                   data-rating="{{ $product->rating ?? 5 }}"
                   data-reviews="{{ $product->reviews_count ?? 0 }}"
                   data-stock="{{ $product->stock ?? 10 }}">
                    <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                </a>
                <button class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-price="{{ $displayPrice }}"
                        data-product-image="{{ $product->primaryImageUrl() }}"
                        data-product-desc="{{ $product->description }}"
                        data-product-category="{{ $product->category->cat_name ?? '' }}"
                        {{ ($product->stock ?? 0) < 1 ? 'disabled' : '' }}>
                    <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i>
                    {{ ($product->stock ?? 0) < 1 ? 'Sold Out' : 'Add' }}
                </button>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full text-center py-8">
        <p class="text-slate-500">No deals available at the moment.</p>
    </div>
@endforelse
                    {{--
                    <div class="bg-white rounded-xl sm:rounded-2xl overflow-hidden shadow-sm border border-[#ebd7be]/40 relative hover:shadow-md transition group">
                        <span class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-[#e5b842] text-brand-dark text-[8px] sm:text-[9px] md:text-[10px] font-extrabold uppercase px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full z-10 shadow-sm">
                            -20%
                        </span>
                        <div class="relative w-full aspect-[4/5] overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100 cursor-pointer view-details-btn"
                             data-id="105"
                             data-name="Merino Wool Sweater"
                             data-price="1299"
                             data-original-price="1624"
                             data-discount="true"
                             data-image="{{ asset('images/Sweaters.png') }}"
                             data-category="Artisan Weaves"
                             data-vendor="Artisan Weaves"
                             data-desc="High-quality merino wool sweater woven by local weavers."
                             data-rating="4"
                             data-reviews="124"
                             data-stock="20">
                            <img src="{{ asset('images/Sweaters.png') }}" alt="Deal 1" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-9 sm:h-9 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="105"
                                    data-product-name="Merino Wool Sweater"
                                    data-product-price="1299"
                                    data-product-image="{{ asset('images/Sweaters.png') }}"
                                    data-product-desc="High-quality merino wool sweater woven by local weavers."
                                    data-product-category="Artisan Weaves">
                                <i class="far fa-heart text-xs"></i>
                            </button>
                        </div>
                        <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">Artisan Weaves</span>
                                <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 view-details-btn"
                                    data-id="105"
                                    data-name="Merino Wool Sweater"
                                    data-price="1299"
                                    data-original-price="1624"
                                    data-discount="true"
                                    data-image="{{ asset('images/Sweaters.png') }}"
                                    data-category="Artisan Weaves"
                                    data-vendor="Artisan Weaves"
                                    data-desc="High-quality merino wool sweater woven by local weavers."
                                    data-rating="4"
                                    data-reviews="124"
                                    data-stock="20">Merino Wool Sweater</h3>
                                <div class="flex items-center gap-0.5 sm:gap-1 text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] mb-1 sm:mb-2 text-slate-500">
                                    <span class="flex text-amber-500 gap-0.5">
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-regular fa-star text-[6px] sm:text-[8px]"></i>
                                    </span>
                                    <span class="hidden xs:inline">(124)</span>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-100/60">
                                <span class="text-brand-primary font-bold text-xs sm:text-sm md:text-base block mb-2">Rs. 1,299</span>
                                <div class="flex gap-1 sm:gap-2">
                                    <button class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-id="105" data-name="Merino Wool Sweater" data-price="1299" data-original-price="1624"
                                            data-discount="true" data-image="{{ asset('images/Sweaters.png') }}" data-category="Artisan Weaves"
                                            data-vendor="Artisan Weaves" data-desc="High-quality merino wool sweater woven by local weavers." data-stock="20">
                                        <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                                    </button>
                                    <button class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-product-id="105" data-product-name="Merino Wool Sweater" data-product-price="1299"
                                            data-product-image="{{ asset('images/Sweaters.png') }}" data-product-desc="High-quality merino wool sweater." data-product-category="Artisan Weaves">
                                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Deal Card 2 -->
                    <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">
                        <div class="relative w-full aspect-[4/5] overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100 cursor-pointer view-details-btn"
                             data-id="106"
                             data-name="Bamboo Sunglasses"
                             data-price="899"
                             data-original-price="1124"
                             data-discount="true"
                             data-image="{{ asset('images/SunGlass.png') }}"
                             data-category="Eco Eyewear"
                             data-vendor="Eco Eyewear"
                             data-desc="Stylish sunglasses crafted from sustainable natural bamboo wood."
                             data-rating="4"
                             data-reviews="89"
                             data-stock="30">
                            <img src="{{ asset('images/SunGlass.png') }}" alt="Deal 2" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-9 sm:h-9 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="106"
                                    data-product-name="Bamboo Sunglasses"
                                    data-product-price="899"
                                    data-product-image="{{ asset('images/SunGlass.png') }}"
                                    data-product-desc="Stylish sunglasses crafted from sustainable natural bamboo wood."
                                    data-product-category="Eco Eyewear">
                                <i class="far fa-heart text-xs"></i>
                            </button>
                        </div>
                        <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">Eco Eyewear</span>
                                <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 view-details-btn"
                                    data-id="106"
                                    data-name="Bamboo Sunglasses"
                                    data-price="899"
                                    data-original-price="1124"
                                    data-discount="true"
                                    data-image="{{ asset('images/SunGlass.png') }}"
                                    data-category="Eco Eyewear"
                                    data-vendor="Eco Eyewear"
                                    data-desc="Stylish sunglasses crafted from sustainable natural bamboo wood."
                                    data-rating="4"
                                    data-reviews="89"
                                    data-stock="30">Bamboo Sunglasses</h3>
                                <div class="flex items-center gap-0.5 sm:gap-1 text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] mb-1 sm:mb-2 text-slate-500">
                                    <span class="flex text-amber-500 gap-0.5">
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-regular fa-star text-[6px] sm:text-[8px]"></i>
                                    </span>
                                    <span class="hidden xs:inline">(89)</span>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-100/60">
                                <span class="text-brand-primary font-bold text-xs sm:text-sm md:text-base block mb-2">Rs. 899</span>
                                <div class="flex gap-1 sm:gap-2">
                                    <button class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-id="106" data-name="Bamboo Sunglasses" data-price="899" data-original-price="1124"
                                            data-discount="true" data-image="{{ asset('images/SunGlass.png') }}" data-category="Eco Eyewear"
                                            data-vendor="Eco Eyewear" data-desc="Stylish sunglasses crafted from sustainable natural bamboo wood." data-stock="30">
                                        <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                                    </button>
                                    <button class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-product-id="106" data-product-name="Bamboo Sunglasses" data-product-price="899"
                                            data-product-image="{{ asset('images/SunGlass.png') }}" data-product-desc="Stylish sunglasses crafted from sustainable bamboo." data-product-category="Eco Eyewear">
                                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Deal Card 3 -->
                    <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group hidden sm:flex">
                        <div class="relative w-full aspect-[4/5] overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100 cursor-pointer view-details-btn"
                             data-id="107"
                             data-name="Teak Wood Side Table"
                             data-price="8999"
                             data-original-price="11249"
                             data-discount="true"
                             data-image="{{ asset('images/Table.png') }}"
                             data-category="Woodcraft"
                             data-vendor="Woodcraft Nepal"
                             data-desc="Sturdy, hand-crafted teak wood side table with rustic charm."
                             data-rating="4"
                             data-reviews="56"
                             data-stock="8">
                            <img src="{{ asset('images/Table.png') }}" alt="Deal 3" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-9 sm:h-9 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="107"
                                    data-product-name="Teak Wood Side Table"
                                    data-product-price="8999"
                                    data-product-image="{{ asset('images/Table.png') }}"
                                    data-product-desc="Sturdy, hand-crafted teak wood side table with rustic charm."
                                    data-product-category="Woodcraft">
                                <i class="far fa-heart text-xs"></i>
                            </button>
                        </div>
                        <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">Woodcraft</span>
                                <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 view-details-btn"
                                    data-id="107"
                                    data-name="Teak Wood Side Table"
                                    data-price="8999"
                                    data-original-price="11249"
                                    data-discount="true"
                                    data-image="{{ asset('images/Table.png') }}"
                                    data-category="Woodcraft"
                                    data-vendor="Woodcraft Nepal"
                                    data-desc="Sturdy, hand-crafted teak wood side table with rustic charm."
                                    data-rating="4"
                                    data-reviews="56"
                                    data-stock="8">Teak Wood Side Table</h3>
                                <div class="flex items-center gap-0.5 sm:gap-1 text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] mb-1 sm:mb-2 text-slate-500">
                                    <span class="flex text-amber-500 gap-0.5">
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-regular fa-star text-[6px] sm:text-[8px]"></i>
                                    </span>
                                    <span class="hidden xs:inline">(56)</span>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-100/60">
                                <span class="text-brand-primary font-bold text-xs sm:text-sm md:text-base block mb-2">Rs. 8,999</span>
                                <div class="flex gap-1 sm:gap-2">
                                    <button class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-id="107" data-name="Teak Wood Side Table" data-price="8999" data-original-price="11249"
                                            data-discount="true" data-image="{{ asset('images/Table.png') }}" data-category="Woodcraft"
                                            data-vendor="Woodcraft Nepal" data-desc="Sturdy, hand-crafted teak wood side table with rustic charm." data-stock="8">
                                        <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                                    </button>
                                    <button class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-product-id="107" data-product-name="Teak Wood Side Table" data-product-price="8999"
                                            data-product-image="{{ asset('images/Table.png') }}" data-product-desc="Sturdy, hand-crafted teak wood side table with rustic charm." data-product-category="Woodcraft">
                                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>                    <!-- Deal Card 4 -->
                    <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group hidden sm:flex">
                        <div class="relative w-full aspect-[4/5] overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100 cursor-pointer view-details-btn"
                             data-id="108"
                             data-name="Ceramic Bowl Set"
                             data-price="3499"
                             data-original-price="4374"
                             data-discount="true"
                             data-image="{{ asset('images/Pottery.png') }}"
                             data-category="Clay Studio"
                             data-vendor="Clay Studio Nepal"
                             data-desc="Set of handmade ceramic bowls, clay-fired and glazed by local potters."
                             data-rating="4"
                             data-reviews="203"
                             data-stock="12">
                            <img src="{{ asset('images/Pottery.png') }}" alt="Deal 4" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-9 sm:h-9 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="108"
                                    data-product-name="Ceramic Bowl Set"
                                    data-product-price="3499"
                                    data-product-image="{{ asset('images/Pottery.png') }}"
                                    data-product-desc="Set of handmade ceramic bowls, clay-fired and glazed by local potters."
                                    data-product-category="Clay Studio">
                                <i class="far fa-heart text-xs"></i>
                            </button>
                        </div>
                        <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                            <div>
                                <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">Clay Studio</span>
                                <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 view-details-btn"
                                    data-id="108"
                                    data-name="Ceramic Bowl Set"
                                    data-price="3499"
                                    data-original-price="4374"
                                    data-discount="true"
                                    data-image="{{ asset('images/Pottery.png') }}"
                                    data-category="Clay Studio"
                                    data-vendor="Clay Studio Nepal"
                                    data-desc="Set of handmade ceramic bowls, clay-fired and glazed by local potters."
                                    data-rating="4"
                                    data-reviews="203"
                                    data-stock="12">Ceramic Bowl Set</h3>
                                <div class="flex items-center gap-0.5 sm:gap-1 text-[8px] sm:text-[9px] md:text-[10px] lg:text-[11px] mb-1 sm:mb-2 text-slate-500">
                                    <span class="flex text-amber-500 gap-0.5">
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-solid fa-star text-[6px] sm:text-[8px]"></i>
                                        <i class="fa-regular fa-star text-[6px] sm:text-[8px]"></i>
                                    </span>
                                    <span class="hidden xs:inline">(203)</span>
                                </div>
                            </div>
                            <div class="mt-2 pt-2 border-t border-slate-100/60">
                                <span class="text-brand-primary font-bold text-xs sm:text-sm md:text-base block mb-2">Rs. 3,499</span>
                                <div class="flex gap-1 sm:gap-2">
                                    <button class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-id="108" data-name="Ceramic Bowl Set" data-price="3499" data-original-price="4374"
                                            data-discount="true" data-image="{{ asset('images/Pottery.png') }}" data-category="Clay Studio"
                                            data-vendor="Clay Studio Nepal" data-desc="Set of handmade ceramic bowls, clay-fired and glazed by local potters." data-stock="12">
                                        <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                                    </button>
                                    <button class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-product-id="108" data-product-name="Ceramic Bowl Set" data-product-price="3499"
                                            data-product-image="{{ asset('images/Pottery.png') }}" data-product-desc="Handmade ceramic bowls by local potters." data-product-category="Clay Studio">
                                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
--}}
                </div>
            </div>
        </section>

        <!-- 5. Trending Now Section -->
        <section id="trending-now" class="max-w-7xl mx-auto px-4 sm:px-6 mb-12 sm:mb-16">
            <div class="bg-[#E5DCD0]/60 border border-[#ebd7be]/40 rounded-2xl sm:rounded-3xl p-4 sm:p-6 md:p-8 shadow-sm">

                <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark flex items-center gap-2 mb-6">
                    <span>Trending Now</span>
                </h3>

                <!-- Grid: 3 columns on desktop, 1 on mobile -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    @forelse($trendingProducts as $product)
                        @php
                            $imageUrl      = $product->primaryImageUrl();
                            $effectivePrice = $product->effectivePrice();
                            $originalPrice  = $product->originalPrice();
                            $categoryName   = $product->category?->cat_name ?? $product->category?->name ?? 'Crafts';
                            $vendorName     = $product->vendor?->vendor_name ?? $product->vendor?->name ?? 'Local Artisan';
                            $avgRating      = $product->reviews_avg_rating ? round($product->reviews_avg_rating, 1) : null;
                            $soldCount      = $product->order_items_count ?? 0;
                            $soldLabel      = $soldCount >= 1000
                                ? number_format($soldCount / 1000, 1) . 'k'
                                : $soldCount;
                        @endphp
                        <div class="flex items-center gap-3 sm:gap-4 bg-white p-3 sm:p-4 rounded-xl sm:rounded-2xl shadow-sm border border-[#ebd7be]/40 group transition">
                            <div class="w-12 h-12 sm:w-14 sm:h-14 md:w-16 md:h-16 rounded-lg sm:rounded-xl overflow-hidden shrink-0 bg-slate-100 cursor-pointer view-details-btn"
                                 data-id="{{ $product->id }}"
                                 data-slug="{{ $product->slug }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $effectivePrice }}"
                                 data-original-price="{{ $originalPrice }}"
                                 data-image="{{ $imageUrl }}"
                                 data-category="{{ $categoryName }}"
                                 data-vendor="{{ $vendorName }}"
                                 data-desc="{{ $product->description }}"
                                 data-rating="{{ $avgRating ?? 0 }}"
                                 data-reviews="{{ $product->reviews_count ?? 0 }}"
                                 data-stock="{{ $product->stock }}">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            </div>
                            <div class="flex-grow">
                                <h4 class="text-xs sm:text-sm md:text-base font-bold text-brand-dark group-hover:text-brand-primary transition-colors line-clamp-1 cursor-pointer view-details-btn"
                                    data-id="{{ $product->id }}"
                                    data-slug="{{ $product->slug }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $effectivePrice }}"
                                    data-original-price="{{ $originalPrice }}"
                                    data-image="{{ $imageUrl }}"
                                    data-category="{{ $categoryName }}"
                                    data-vendor="{{ $vendorName }}"
                                    data-desc="{{ $product->description }}"
                                    data-rating="{{ $avgRating ?? 0 }}"
                                    data-reviews="{{ $product->reviews_count ?? 0 }}"
                                    data-stock="{{ $product->stock }}">{{ $product->name }}</h4>
                                <span class="text-brand-primary font-bold text-xs sm:text-sm">Rs. {{ number_format($effectivePrice) }}</span>
                                <div class="flex items-center gap-1 text-[9px] sm:text-xs text-amber-500 font-semibold mt-0.5">
                                    <i class="fa-solid fa-star"></i>
                                    <span>{{ $avgRating ?? '—' }}</span>
                                    <span class="text-slate-400 ml-0.5 sm:ml-1 hidden xs:inline">({{ $soldLabel }} sold)</span>
                                </div>
                            </div>
                            <button class="add-to-cart-btn shrink-0 bg-[#b55b3d] hover:bg-[#a04f33] text-white text-[9px] sm:text-xs font-semibold px-2 py-1 sm:px-3 sm:py-1.5 rounded-lg transition"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-price="{{ $effectivePrice }}"
                                    data-product-image="{{ $imageUrl }}"
                                    data-product-desc="{{ $product->description }}"
                                    data-product-category="{{ $categoryName }}">
                                Add
                            </button>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-slate-400">
                            No trending products available yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- 6. Top Sellers Section - 3 COLUMNS ON MOBILE -->
        <section id="top-sellers" class="max-w-7xl mx-auto px-4 sm:px-6 mb-12 sm:mb-20">
            <div class="bg-[#E5DCD0]/60 border border-[#ebd7be]/40 rounded-2xl sm:rounded-3xl p-3 sm:p-4 md:p-6 lg:p-10 shadow-sm">

                <div class="flex items-center justify-between mb-4 sm:mb-5 md:mb-6 lg:mb-8">
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark flex items-center gap-2">
                        <span>Top Sellers</span>
                    </h3>
                    <a href="{{ route('top-sellers') }}" wire:navigate class="text-brand-primary hover:text-[#a04f33] font-bold text-xs sm:text-sm flex items-center gap-1 transition-colors hover:underline">
                        <span>See All</span>
                        <i class="fa-solid fa-chevron-right text-[10px] sm:text-xs"></i>
                    </a>
                </div>

                <!-- Grid: 2 columns on mobile, 4 on desktop -->
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6">
                    @forelse($topSellers as $index => $product)
                        @php
                            $rank = $index + 1;
                            $rankBg = match(true) {
                                $rank === 1 => 'bg-yellow-400 text-yellow-900',
                                $rank === 2 => 'bg-slate-300 text-slate-800',
                                $rank === 3 => 'bg-amber-600 text-white',
                                default     => 'bg-[#1F3D2E] text-white',
                            };
                            $imageUrl = $product->primaryImageUrl();
                            $dDiscount = $product->resolvedDiscountPrice();
                            $hasDiscount = !is_null($dDiscount) && $dDiscount > 0 && $dDiscount < $product->price;
                            $discountPrice = $hasDiscount ? $dDiscount : $product->price;
                        @endphp
                        <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group {{ $index >= 2 ? 'hidden sm:block' : '' }}">
                            <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl cursor-pointer view-details-btn"
                                 data-id="{{ $product->id }}"
                                 data-slug="{{ $product->slug }}"
                                 data-name="{{ $product->name }}"
                                 data-price="{{ $discountPrice }}"
                                 data-original-price="{{ $product->price }}"
                                 data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                 data-discount-price="{{ $dDiscount ?? '' }}"
                                 data-image="{{ $imageUrl }}"
                                 data-category="{{ $product->category?->cat_name ?? 'Crafts' }}"
                                 data-vendor="{{ $product->vendor->business_name ?? $product->vendor->name ?? '' }}"
                                 data-desc="{{ $product->description }}"
                                 data-rating="{{ $product->rating ?? 5 }}"
                                 data-reviews="{{ $product->reviews_count ?? 24 }}"
                                 data-stock="{{ $product->stock ?? 10 }}">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                                <span class="absolute top-2 left-2 sm:top-3 sm:left-3 {{ $rankBg }} text-[7px] xs:text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full z-10 shadow">
                                    #{{ $rank }} {{ $rank <= 3 ? 'Best Seller' : 'Seller' }}
                                </span>

                                <button
                                    class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-10 sm:h-10 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-price="{{ $discountPrice }}"
                                    data-product-image="{{ $imageUrl }}"
                                    data-product-desc="{{ $product->description }}"
                                    data-product-category="{{ $product->category?->cat_name }}">
                                    <i class="far fa-heart text-[10px] sm:text-lg"></i>
                                </button>
                            </div>
                            <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                                <div>
                                    <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">
                                        {{ $product->category?->cat_name ?? 'General' }}
                                    </span>
                                    <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1 cursor-pointer view-details-btn"
                                        data-id="{{ $product->id }}"
                                        data-slug="{{ $product->slug }}"
                                        data-name="{{ $product->name }}"
                                        data-price="{{ $discountPrice }}"
                                        data-original-price="{{ $product->price }}"
                                        data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                        data-discount-price="{{ $dDiscount ?? '' }}"
                                        data-image="{{ $imageUrl }}"
                                        data-category="{{ $product->category?->cat_name ?? 'Crafts' }}"
                                        data-vendor="{{ $product->vendor->business_name ?? $product->vendor->name ?? '' }}"
                                        data-desc="{{ $product->description }}"
                                        data-rating="{{ $product->rating ?? 5 }}"
                                        data-reviews="{{ $product->reviews_count ?? 24 }}"
                                        data-stock="{{ $product->stock ?? 10 }}">
                                        {{ $product->name }}
                                    </h3>
                                    <div class="flex flex-wrap items-baseline gap-1 sm:gap-2 mb-2 sm:mb-4">
                                        <span class="text-[#C65A3A] font-bold text-xs sm:text-sm md:text-base">
                                            Rs {{ number_format($discountPrice, 2) }}
                                        </span>
                                        @if($hasDiscount)
                                            <span class="text-slate-400 text-[8px] sm:text-xs line-through font-semibold">
                                                Rs {{ number_format($product->price, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex gap-1 sm:gap-2 mt-auto">
                                    <a href="{{ route('viewdetails', $product->slug) }}"
                                       class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                       data-id="{{ $product->id }}"
                                       data-slug="{{ $product->slug }}"
                                       data-name="{{ $product->name }}"
                                       data-price="{{ $discountPrice }}"
                                       data-original-price="{{ $product->price }}"
                                       data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                       data-discount-price="{{ $dDiscount ?? '' }}"
                                       data-image="{{ $imageUrl }}"
                                       data-category="{{ $product->category?->cat_name ?? 'Crafts' }}"
                                       data-vendor="{{ $product->vendor->business_name ?? $product->vendor->name ?? '' }}"
                                       data-desc="{{ $product->description }}"
                                       data-rating="{{ $product->rating ?? 5 }}"
                                       data-reviews="{{ $product->reviews_count ?? 24 }}"
                                       data-stock="{{ $product->stock ?? 10 }}">
                                        <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i>
                                        Details
                                    </a>
                                    <button
                                        type="button"
                                        class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
                                        data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        {{ ($product->stock ?? 0) < 1 ? 'disabled' : '' }}>
                                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i>
                                        {{ ($product->stock ?? 0) < 1 ? 'Sold Out' : 'Add' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <p class="text-slate-500">No top sellers available at this time.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

    </main>
</div>
</x-frontend-layout>
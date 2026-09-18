<x-frontend-layout title="Today's Deals - Hamro Koseli"
    description="Check out today's limited-time offers on authentic Nepali crafts and handmade treasures."
    ogImage="/images/og-images.jpg">

    <main class="bg-[#f2eae1] min-h-screen">

        <!-- Hero Section -->
        @php
            $bgImageUrl = null;
            if (isset($dealBgImage) && $dealBgImage) {
                if (str_starts_with($dealBgImage, 'http://') || str_starts_with($dealBgImage, 'https://')) {
                    $bgImageUrl = $dealBgImage;
                } else {
                    $cleanPath = ltrim(preg_replace('#^storage/#', '', $dealBgImage), '/');
                    $bgImageUrl = asset('storage/' . $cleanPath);
                }
            }

            $heroBgStyle = $bgImageUrl
                ? "background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('" . $bgImageUrl . "'); background-size: cover; background-position: center;"
                : "background-image: url('" . asset('images/Potteqry.png') . "'); background-size: cover; background-position: center;";
        @endphp
        <section style="{{ $heroBgStyle }}" class="text-white py-16 px-4 md:px-8 lg:px-16">


            <div class="max-w-7xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <span
                        class="inline-block bg-white bg-opacity-20 text-black text-xs font-bold px-3 py-1 rounded-full mb-4">LIMITED
                        TIME OFFER</span>
                    <h1
                        class="text-[30px] md:text-[24px] font-bold leading-[38px] md:leading-[30px] tracking-[-0.02em] mb-4 font-['Plus_Jakarta_Sans']">
                        Authentic Nepali Heritage</h1>
                    <p class="text-white text-opacity-90 text-base leading-6 mb-6">Experience the pinnacle of Nepalese
                        craftsmanship with our exclusive artisanal collection.</p>

                    <!-- Countdown Timer -->
                    <div id="deal-countdown" data-ends-at="{{ $dealEndsAt }}"
                        class="flex gap-6 mb-8 font-['Plus_Jakarta_Sans']">
                        <div class="text-center">
                            <div id="countdown-days" class="text-3xl font-bold">00</div>
                            <div class="text-xs font-semibold text-white text-opacity-80 uppercase">
                                Days
                            </div>
                        </div>

                        <span class="text-2xl font-bold">:</span>

                        <div class="text-center">
                            <div id="countdown-hours" class="text-3xl font-bold">00</div>
                            <div class="text-xs font-semibold text-white text-opacity-80 uppercase">
                                Hours
                            </div>
                        </div>

                        <span class="text-2xl font-bold">:</span>

                        <div class="text-center">
                            <div id="countdown-minutes" class="text-3xl font-bold">00</div>
                            <div class="text-xs font-semibold text-white text-opacity-80 uppercase">
                                Mins
                            </div>
                        </div>

                        <span class="text-2xl font-bold">:</span>

                        <div class="text-center">
                            <div id="countdown-seconds" class="text-3xl font-bold">00</div>
                            <div class="text-xs font-semibold text-white text-opacity-80 uppercase">
                                Secs
                            </div>
                        </div>
                    </div>


                    <button
                        class="bg-white text-[#d93537] font-bold px-8 py-3 rounded-full hover:bg-opacity-90 transition font-['Plus_Jakarta_Sans']">Shop
                        The Drop</button>
                </div>

                <div class="flex justify-center">
                    {{-- <img src="{{ asset('images/Pottery.png') }}" alt="Authentic Nepali Heritage"
                        class="max-w-md rounded-2xl shadow-lg"> --}}
                </div>
            </div>
        </section>

        <!-- Lightning Deals Section -->
        <section class="py-12 px-4 md:px-8 lg:px-16">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-[#181c1e] font-['Plus_Jakarta_Sans']">Alert Today's Deals
                        </h2>
                    </div>
                </div>

                <!-- Filter Controls -->
                <div class="bg-white rounded-lg border border-[#e0e3e5] p-6 mb-8">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                        <!-- Category Filter -->
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-[#181c1e] block mb-3">Filter by
                                Category</span>
                            <div class="flex flex-wrap gap-2" id="category-filters">
                                <button data-category="all"
                                    class="filter-pill px-4 py-2 border border-[#b51822] text-[#181c1e] text-xs font-bold rounded-full hover:bg-[#b51822] hover:text-white transition active">
                                    All Categories
                                </button>
                                @if (isset($categories))
                                    @foreach ($categories as $cat)
                                        @if (is_object($cat) && isset($cat->cat_name))
                                            <button data-category="{{ strtolower($cat->cat_name) }}"
                                                class="filter-pill px-4 py-2 border border-[#e0e3e5] text-[#181c1e] text-xs font-bold rounded-full hover:border-[#b51822] transition">
                                                {{ $cat->cat_name }}
                                            </button>
                                        @elseif(is_string($cat))
                                            <button data-category="{{ strtolower($cat) }}"
                                                class="filter-pill px-4 py-2 border border-[#e0e3e5] text-[#181c1e] text-xs font-bold rounded-full hover:border-[#b51822] transition">
                                                {{ $cat }}
                                            </button>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold uppercase tracking-wider text-[#181c1e]">Sort By:</span>
                            <select id="sort-select"
                                class="border border-[#e0e3e5] rounded-lg px-4 py-2 text-sm font-semibold text-[#181c1e] focus:outline-none focus:ring-2 focus:ring-[#b51822]">
                                <option value="discount">Biggest Discount</option>
                                <option value="price-asc">Price: Low to High</option>
                                <option value="price-desc">Price: High to Low</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6"
                id="product-grid">
                @if (isset($products) && $products->count() > 0)
                    @foreach ($products as $product)
                        @php
                            $imageUrl = $product->primaryImageUrl();
                            $price = $product->price;
                            $dDiscount = $product->resolvedDiscountPrice();
                            $hasDiscount = !is_null($dDiscount) && $dDiscount > 0 && $dDiscount < $price;
                            $displayPrice = $hasDiscount ? $dDiscount : $price;
                            $discountPct = $hasDiscount ? round((($price - $dDiscount) / $price) * 100) : 0;
                            $catName = $product->category?->cat_name ?? 'Uncategorized';
                            $vendorName = $product->vendor?->vendor_name ?? 'Local Artisan';
                            $desc = Str::limit(strip_tags($product->description ?? ''), 100);
                            $stock = $product->stock ?? 0;
                            $avgRating = round($product->reviews_avg_rating ?? 5);
                            $reviewCount = $product->reviews_count ?? 0;
                        @endphp
                        <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group"
                            data-id="{{ $product->id }}" data-slug="{{ $product->slug ?? '' }}"
                            data-category="{{ strtolower($catName) }}" data-discount="{{ $discountPct }}"
                            data-price="{{ intval($displayPrice) }}">

                            {{-- Image area --}}
                            <div class="h-36 xs:h-40 sm:h-44 md:h-48 lg:h-56 overflow-hidden bg-slate-100 relative cursor-pointer view-details-btn"
                                data-id="{{ $product->id }}" data-slug="{{ $product->slug ?? '' }}"
                                data-name="{{ $product->name }}" data-price="{{ intval($displayPrice) }}"
                                data-original-price="{{ intval($price) }}"
                                data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                data-discount-price="{{ intval($displayPrice) }}" data-image="{{ $imageUrl }}"
                                data-category="{{ $catName }}" data-vendor="{{ $vendorName }}"
                                data-desc="{{ $desc }}" data-rating="{{ $avgRating }}"
                                data-reviews="{{ $reviewCount }}" data-stock="{{ $stock }}">
                                <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                                {{-- Discount badge --}}
                                @if ($hasDiscount)
                                    <span
                                        class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-[#e5b842] text-brand-dark text-[8px] sm:text-[9px] md:text-[10px] font-extrabold uppercase px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full z-10 shadow-sm">
                                        -{{ $discountPct }}% OFF
                                    </span>
                                @endif

                                {{-- Wishlist button --}}
                                <button
                                    class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-9 sm:h-9 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                    data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}"
                                    data-product-price="{{ intval($displayPrice) }}"
                                    data-product-image="{{ $imageUrl }}" data-product-desc="{{ $desc }}"
                                    data-product-category="{{ $catName }}">
                                    <i class="far fa-heart text-[11px] sm:text-sm"></i>
                                </button>
                            </div>

                            {{-- Card body --}}
                            <div class="p-2 sm:p-3 md:p-4 flex flex-col flex-grow">
                                <h4
                                    class="text-slate-500 font-semibold text-[8px] sm:text-[9px] md:text-[10px] uppercase tracking-wider mb-0.5 sm:mb-1 truncate">
                                    {{ $catName }}
                                </h4>
                                <h3 class="text-[10px] sm:text-xs md:text-sm lg:text-base font-bold text-brand-dark mb-1 sm:mb-2 line-clamp-2 cursor-pointer hover:text-brand-primary transition-colors view-details-btn"
                                    data-id="{{ $product->id }}" data-slug="{{ $product->slug ?? '' }}"
                                    data-name="{{ $product->name }}" data-price="{{ intval($displayPrice) }}"
                                    data-original-price="{{ intval($price) }}"
                                    data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                    data-discount-price="{{ intval($displayPrice) }}"
                                    data-image="{{ $imageUrl }}" data-category="{{ $catName }}"
                                    data-vendor="{{ $vendorName }}" data-desc="{{ $desc }}"
                                    data-rating="{{ $avgRating }}" data-reviews="{{ $reviewCount }}"
                                    data-stock="{{ $stock }}">
                                    {{ $product->name }}
                                </h3>

                                <div class="mt-auto pt-2 border-t border-slate-100/60">
                                    <div class="flex items-baseline gap-1.5 mb-1.5">
                                        <span class="text-brand-primary font-bold text-[10px] sm:text-xs md:text-sm">
                                            Rs. {{ number_format($displayPrice, 0) }}
                                        </span>
                                        @if ($hasDiscount)
                                            <span class="text-slate-400 text-[8px] sm:text-[9px] line-through">
                                                Rs. {{ number_format($price, 0) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex gap-1 sm:gap-2">
                                        <a href="{{ route('viewdetails', $product->slug) }}"
                                            class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                            data-id="{{ $product->id }}" data-slug="{{ $product->slug ?? '' }}"
                                            data-name="{{ $product->name }}"
                                            data-price="{{ intval($displayPrice) }}"
                                            data-original-price="{{ intval($price) }}"
                                            data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                                            data-discount-price="{{ intval($displayPrice) }}"
                                            data-image="{{ $imageUrl }}" data-category="{{ $catName }}"
                                            data-vendor="{{ $vendorName }}" data-desc="{{ $desc }}"
                                            data-rating="{{ $avgRating }}" data-reviews="{{ $reviewCount }}"
                                            data-stock="{{ $stock }}">
                                            <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i> Details
                                        </a>
                                        <button
                                            class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
                                            data-product-id="{{ $product->id }}"
                                            data-product-name="{{ $product->name }}"
                                            data-product-price="{{ intval($displayPrice) }}"
                                            data-product-image="{{ $imageUrl }}"
                                            data-product-desc="{{ $desc }}"
                                            data-product-category="{{ $catName }}"
                                            {{ $stock < 1 ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i>
                                            {{ $stock < 1 ? 'Sold Out' : 'Add' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-12">
                        <p class="text-slate-500 text-lg">No products available</p>
                    </div>
                @endif
            </div>
            </div>
        </section>

        <!-- Featured Star Deal Carousel Section -->
        <section class="bg-[#2d3133] text-white py-16 px-4 md:px-8 lg:px-16">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">⭐</span>
                        <h2 class="text-2xl font-bold font-['Plus_Jakarta_Sans']">Featured Star Deals</h2>
                    </div>
                    <div class="flex gap-3">
                        <button id="featured-prev"
                            class="p-2 border border-white rounded-lg hover:bg-white hover:text-[#2d3133] transition">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button id="featured-next"
                            class="p-2 border border-white rounded-lg hover:bg-white hover:text-[#2d3133] transition">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Carousel Container -->
                <div class="relative overflow-hidden">
                    <div id="featured-carousel" class="flex gap-6 transition-transform duration-500 ease-out">
                        @if (isset($featuredDeals) && $featuredDeals->count() > 0)
                            @foreach ($featuredDeals as $fProduct)
                                @php
                                    $fImageUrl = $fProduct->primaryImageUrl();
                                    $fPrice = (float) $fProduct->price;
                                    $fDiscountPrice = $fProduct->resolvedDiscountPrice();
                                    $fHasDiscount =
                                        !is_null($fDiscountPrice) && $fDiscountPrice > 0 && $fDiscountPrice < $fPrice;
                                    $fDisplayPrice = $fHasDiscount ? $fDiscountPrice : $fPrice;
                                    $fDiscountPct = $fHasDiscount
                                        ? round((($fPrice - $fDiscountPrice) / $fPrice) * 100)
                                        : 0;
                                    $fCatName = $fProduct->category?->cat_name ?? 'Crafts';
                                    $fVendorName = $fProduct->vendor?->vendor_name ?? 'Local Artisan';
                                    $fDesc = Str::limit(strip_tags($fProduct->description ?? ''), 160);
                                    $fStock = $fProduct->stock ?? 0;
                                @endphp
                                <div class="featured-card flex-shrink-0 w-full animate-fade-in">
                                    <div
                                        class="grid md:grid-cols-2 gap-8 items-center bg-[#1a1a1a] rounded-2xl p-8 min-h-[380px] w-full">
                                        <div class="flex justify-center items-center w-full h-[250px] md:h-[300px]">
                                            <img src="{{ $fImageUrl }}" alt="{{ $fProduct->name }}"
                                                class="w-full h-full object-cover rounded-xl shadow-md max-w-sm">
                                        </div>
                                        <div
                                            class="flex flex-col justify-between h-full min-h-[250px] md:min-h-[300px]">
                                            <div>
                                                <div class="flex items-center gap-2 mb-3">
                                                    @if ($fHasDiscount)
                                                        <span
                                                            class="inline-block bg-[#d4a017] text-black text-xs font-bold px-3 py-1 rounded-full">-{{ $fDiscountPct }}%
                                                            OFF</span>
                                                    @endif
                                                </div>
                                                <p
                                                    class="text-xs font-semibold uppercase tracking-wider text-white/50 mb-1">
                                                    {{ $fCatName }} &middot; {{ $fVendorName }}</p>
                                                <h3 class="text-2xl font-bold mb-3 font-['Plus_Jakarta_Sans']">
                                                    {{ $fProduct->name }}</h3>
                                                <p class="text-white text-opacity-80 text-sm leading-6 mb-4">
                                                    {{ $fDesc }}</p>
                                            </div>
                                            <div>
                                                @if ($fHasDiscount)
                                                    <p class="text-sm text-white text-opacity-70 mb-1">Regularly Rs.
                                                        {{ number_format($fPrice, 0) }}</p>
                                                @endif
                                                <p class="text-4xl font-bold mb-4 font-['Plus_Jakarta_Sans']">Rs.
                                                    {{ number_format($fDisplayPrice, 0) }}</p>
                                                <div class="flex gap-3">
                                                    <button type="button"
                                                        class="add-to-cart-btn bg-[#d4a017] hover:bg-[#b38a0a] text-black font-bold px-6 py-2 rounded-full transition flex items-center gap-2 font-['Plus_Jakarta_Sans'] text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                                                        data-product-id="{{ $fProduct->id }}"
                                                        data-product-name="{{ $fProduct->name }}"
                                                        data-product-price="{{ intval($fDisplayPrice) }}"
                                                        data-product-image="{{ $fImageUrl }}"
                                                        data-product-desc="{{ $fDesc }}"
                                                        data-product-category="{{ $fCatName }}"
                                                        {{ $fStock < 1 ? 'disabled' : '' }}>
                                                        {{ $fStock < 1 ? 'Sold Out' : 'Buy Now' }} <span>→</span>
                                                    </button>
                                                    <button type="button"
                                                        class="view-details-btn border border-white text-white font-bold px-6 py-2 rounded-full hover:bg-white hover:text-[#2d3133] transition text-sm"
                                                        data-id="{{ $fProduct->id }}"
                                                        data-slug="{{ $fProduct->slug ?? '' }}"
                                                        data-name="{{ $fProduct->name }}"
                                                        data-price="{{ intval($fDisplayPrice) }}"
                                                        data-original-price="{{ intval($fPrice) }}"
                                                        data-discount="{{ $fHasDiscount ? 'true' : 'false' }}"
                                                        data-discount-price="{{ intval($fDisplayPrice) }}"
                                                        data-image="{{ $fImageUrl }}"
                                                        data-category="{{ $fCatName }}"
                                                        data-vendor="{{ $fVendorName }}"
                                                        data-desc="{{ $fDesc }}" data-rating="5"
                                                        data-reviews="0" data-stock="{{ $fStock }}">
                                                        Details
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="w-full text-center py-16 text-white/50">
                                No featured deals available at the moment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Trending Now Section - Updated with consistent card sizes and button dimensions -->
        <section class="py-12 px-4 md:px-8 lg:px-16">
            <div class="max-w-7xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-[#181c1e] flex items-center gap-2 font-['Plus_Jakarta_Sans']">
                        <span class="text-2xl">📈</span> Trending Now
                    </h2>
                    <div class="flex gap-3">
                        <button id="trending-prev"
                            class="p-2 border border-[#e0e3e5] rounded-lg hover:bg-[#ebeef0] transition">
                            <i class="fas fa-chevron-left text-[#181c1e]"></i>
                        </button>
                        <button id="trending-next"
                            class="p-2 border border-[#e0e3e5] rounded-lg hover:bg-[#ebeef0] transition">
                            <i class="fas fa-chevron-right text-[#181c1e]"></i>
                        </button>
                    </div>
                </div>

                <!-- Trending Carousel - Now using the same card structure as Alert Today's Deals -->
                <div class="relative overflow-hidden">
                    <div id="trending-carousel"
                        class="flex gap-3 sm:gap-4 md:gap-6 transition-transform duration-500 ease-out">
                        @if (isset($trendingProducts) && $trendingProducts->count() > 0)
                            @foreach ($trendingProducts as $product)
                                @php
                                    $tImgUrl = $product->primaryImageUrl();
                                    $tPrice = (float) $product->price;
                                    $tDDiscount = $product->resolvedDiscountPrice();
                                    $tHasDiscount = !is_null($tDDiscount) && $tDDiscount > 0 && $tDDiscount < $tPrice;
                                    $tDisplayPrice = $tHasDiscount ? $tDDiscount : $tPrice;
                                    $tDiscountPct = $tHasDiscount
                                        ? round((($tPrice - $tDDiscount) / $tPrice) * 100)
                                        : 0;
                                    $tCatName = $product->category?->cat_name ?? 'Crafts';
                                    $tVendorName = $product->vendor?->vendor_name ?? 'Local Artisan';
                                    $tDesc = Str::limit(strip_tags($product->description ?? ''), 100);
                                    $tStock = $product->stock ?? 0;
                                    $tAvgRating = round($product->reviews_avg_rating ?? 5);
                                    $tReviewCount = $product->reviews_count ?? 0;
                                @endphp
                                <div class="trending-card flex-shrink-0 w-1/2 md:w-1/3 lg:w-1/4">
                                    <!-- Same card structure as Alert Today's Deals -->
                                    <div class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group h-full"
                                        data-id="{{ $product->id }}" data-slug="{{ $product->slug ?? '' }}"
                                        data-category="{{ strtolower($tCatName) }}"
                                        data-discount="{{ $tDiscountPct }}"
                                        data-price="{{ intval($tDisplayPrice) }}">

                                        {{-- Image area - Same dimensions as Alert Today's Deals --}}
                                        <div class="h-36 xs:h-40 sm:h-44 md:h-48 lg:h-56 overflow-hidden bg-slate-100 relative cursor-pointer view-details-btn"
                                            data-id="{{ $product->id }}" data-slug="{{ $product->slug ?? '' }}"
                                            data-name="{{ $product->name }}"
                                            data-price="{{ intval($tDisplayPrice) }}"
                                            data-original-price="{{ intval($tPrice) }}"
                                            data-discount="{{ $tHasDiscount ? 'true' : 'false' }}"
                                            data-discount-price="{{ intval($tDisplayPrice) }}"
                                            data-image="{{ $tImgUrl }}" data-category="{{ $tCatName }}"
                                            data-vendor="{{ $tVendorName }}" data-desc="{{ $tDesc }}"
                                            data-rating="{{ $tAvgRating }}" data-reviews="{{ $tReviewCount }}"
                                            data-stock="{{ $tStock }}">
                                            <img src="{{ $tImgUrl }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                                            {{-- Discount badge --}}
                                            @if ($tHasDiscount)
                                                <span
                                                    class="absolute top-1 left-1 sm:top-2 sm:left-2 bg-[#e5b842] text-brand-dark text-[8px] sm:text-[9px] md:text-[10px] font-extrabold uppercase px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-full z-10 shadow-sm">
                                                    -{{ $tDiscountPct }}% OFF
                                                </span>
                                            @endif

                                            {{-- Wishlist button --}}
                                            <button
                                                class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-9 sm:h-9 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                                data-product-id="{{ $product->id }}"
                                                data-product-name="{{ $product->name }}"
                                                data-product-price="{{ intval($tDisplayPrice) }}"
                                                data-product-image="{{ $tImgUrl }}"
                                                data-product-desc="{{ $tDesc }}"
                                                data-product-category="{{ $tCatName }}">
                                                <i class="far fa-heart text-[11px] sm:text-sm"></i>
                                            </button>
                                        </div>

                                        {{-- Card body - Same as Alert Today's Deals --}}
                                        <div class="p-2 sm:p-3 md:p-4 flex flex-col flex-grow">
                                            <h4
                                                class="text-slate-500 font-semibold text-[8px] sm:text-[9px] md:text-[10px] uppercase tracking-wider mb-0.5 sm:mb-1 truncate">
                                                {{ $tCatName }}
                                            </h4>
                                            <h3 class="text-[10px] sm:text-xs md:text-sm lg:text-base font-bold text-brand-dark mb-1 sm:mb-2 line-clamp-2 cursor-pointer hover:text-brand-primary transition-colors view-details-btn"
                                                data-id="{{ $product->id }}"
                                                data-slug="{{ $product->slug ?? '' }}"
                                                data-name="{{ $product->name }}"
                                                data-price="{{ intval($tDisplayPrice) }}"
                                                data-original-price="{{ intval($tPrice) }}"
                                                data-discount="{{ $tHasDiscount ? 'true' : 'false' }}"
                                                data-discount-price="{{ intval($tDisplayPrice) }}"
                                                data-image="{{ $tImgUrl }}"
                                                data-category="{{ $tCatName }}"
                                                data-vendor="{{ $tVendorName }}" data-desc="{{ $tDesc }}"
                                                data-rating="{{ $tAvgRating }}"
                                                data-reviews="{{ $tReviewCount }}"
                                                data-stock="{{ $tStock }}">
                                                {{ $product->name }}
                                            </h3>

                                            <div class="mt-auto pt-2 border-t border-slate-100/60">
                                                <div class="flex items-baseline gap-1.5 mb-1.5">
                                                    <span
                                                        class="text-brand-primary font-bold text-[10px] sm:text-xs md:text-sm">
                                                        Rs. {{ number_format($tDisplayPrice, 0) }}
                                                    </span>
                                                    @if ($tHasDiscount)
                                                        <span
                                                            class="text-slate-400 text-[8px] sm:text-[9px] line-through">
                                                            Rs. {{ number_format($tPrice, 0) }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex gap-1 sm:gap-2">
                                                    <a href="{{ route('viewdetails', $product->slug) }}"
                                                        class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                                                        data-id="{{ $product->id }}"
                                                        data-slug="{{ $product->slug ?? '' }}"
                                                        data-name="{{ $product->name }}"
                                                        data-price="{{ intval($tDisplayPrice) }}"
                                                        data-original-price="{{ intval($tPrice) }}"
                                                        data-discount="{{ $tHasDiscount ? 'true' : 'false' }}"
                                                        data-discount-price="{{ intval($tDisplayPrice) }}"
                                                        data-image="{{ $tImgUrl }}"
                                                        data-category="{{ $tCatName }}"
                                                        data-vendor="{{ $tVendorName }}"
                                                        data-desc="{{ $tDesc }}"
                                                        data-rating="{{ $tAvgRating }}"
                                                        data-reviews="{{ $tReviewCount }}"
                                                        data-stock="{{ $tStock }}">
                                                        <i class="fa-solid fa-circle-info text-[8px] sm:text-xs"></i>
                                                        Details
                                                    </a>
                                                    <button
                                                        class="add-to-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300 disabled:opacity-60 disabled:cursor-not-allowed"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}"
                                                        data-product-price="{{ intval($tDisplayPrice) }}"
                                                        data-product-image="{{ $tImgUrl }}"
                                                        data-product-desc="{{ $tDesc }}"
                                                        data-product-category="{{ $tCatName }}"
                                                        {{ $tStock < 1 ? 'disabled' : '' }}>
                                                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i>
                                                        {{ $tStock < 1 ? 'Sold Out' : 'Add' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="w-full text-center py-8 text-slate-500">
                                No trending products at the moment.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>

    </main>

    @vite('resources/js/today-deals.js')

    {{-- DEBUG: remove once image and countdown are confirmed working --}}
    @if (config('app.debug'))
        <script>
            console.log('[TodaysDeals] dealEndsAt =', @json($dealEndsAt));
            console.log('[TodaysDeals] dealBgImage =', @json($dealBgImage));
            console.log('[TodaysDeals] bgImageUrl =',
                '{{ isset($dealBgImage) && $dealBgImage ? asset('storage/' . $dealBgImage) : 'none' }}');
        </script>
    @endif

</x-frontend-layout>

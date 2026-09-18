<x-frontend-layout title="Shop - Hamro Koseli"
 description="Discover authentic Nepali crafts and handmade treasures. Shop our curated collection of unique products, supporting local artisans and preserving cultural heritage."
    ogImage="/images/og-images.jpg">


    <style>
        /* Hide browser number-input spinner arrows */
        .qty-val-input::-webkit-outer-spin-button,
        .qty-val-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .qty-val-input { -moz-appearance: textfield; }

        /* Floating sidebar styles */
        .sidebar-sticky {
            position: sticky;
            top: 20px;
            align-self: flex-start;
        }

        /* Ensure the sidebar content doesn't overflow viewport */
        .sidebar-sticky > div {
            max-height: calc(100vh - 40px);
            overflow-y: auto;
        }

        /* Custom scrollbar for floating sidebar */
        .sidebar-sticky > div::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-sticky > div::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-sticky > div::-webkit-scrollbar-thumb {
            background: #C65A3A;
            border-radius: 10px;
        }

        /* For mobile, remove sticky behavior */
        @media (max-width: 768px) {
            .sidebar-sticky {
                position: relative;
                top: 0;
                align-self: auto;
            }
            .sidebar-sticky > div {
                max-height: none;
                overflow-y: visible;
            }
        }
    </style>
    <div class="bg-[#F4EAE1] text-[#3A2A1F] min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- ==================== MAIN LAYOUT (now a single GET form) ==================== --}}
            <form id="filter-form" method="GET" action="{{ route('shop') }}"
                  class="grid grid-cols-1 md:grid-cols-12 gap-4 sm:gap-8 items-start">

                {{-- ===== LEFT SIDEBAR ===== --}}
                <aside class="md:col-span-4 lg:col-span-3 col-span-full sidebar-sticky">
                    <div class="bg-[#FFF7EF] rounded-3xl p-6 border border-[#ebd7be]/50 shadow-sm">

                        {{-- Header --}}
                        <div class="flex items-center justify-between pb-4 border-b border-[#ebd7be]/60 mb-5">
                            <button id="filter-mobile-toggle" type="button" class="flex items-center gap-2 text-left focus:outline-none flex-grow md:pointer-events-none">
                                <h2 class="text-xl font-bold text-[#C65A3A]">Filters</h2>
                                <i id="filter-toggle-icon" class="fas fa-chevron-down text-[#C65A3A] transition-transform duration-300 md:hidden"></i>
                            </button>
                            <button id="reset-filters" type="button"
                                class="text-xs font-bold text-[#C65A3A] hover:text-[#b04a2c] flex items-center gap-1.5 transition-colors shrink-0">
                                <i class="fas fa-rotate-right text-[10px]"></i> Reset
                            </button>
                        </div>

                        <div id="filter-body" class="hidden md:block mt-5 space-y-5">

                            {{-- Search --}}
                            <div class="mb-5">
                                <div class="relative">
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        placeholder="Search products..."
                                        class="w-full bg-[#ebd7be]/20 border border-[#ebd7be]/60 rounded-xl pl-4 pr-10 py-2.5 text-sm text-[#1F3D2E] focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/30">
                                    <button type="submit"
                                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-[#C65A3A]">
                                        <i class="fas fa-magnifying-glass text-sm"></i>
                                    </button>
                                </div>
                            </div>



                            {{-- Categories (now built from the real Category table) --}}
                            <div class="pt-5 border-t border-[#ebd7be]/40 mb-5">
                                <button type="button" class="w-full flex items-center justify-between text-left focus:outline-none py-1">
                                    <span
                                        class="text-xs font-bold uppercase tracking-wider text-[#1F3D2E]">Categories</span>
                                    <i class="fas fa-minus text-[10px] text-[#C65A3A]"></i>
                                </button>
                                <div class="mt-4 space-y-3">
                                    <label class="flex items-center justify-between cursor-pointer group">
                                        <span
                                            class="flex items-center gap-2.5 text-sm text-[#3A2A1F]/80 group-hover:text-[#3A2A1F] transition-colors">
                                            <input type="radio" name="category" value="all"
                                                {{ request('category', 'all') === 'all' ? 'checked' : '' }}
                                                class="category-radio w-4 h-4 border-[#ebd7be] accent-[#C65A3A] focus:ring-0 bg-[#FFF7EF]">
                                            All Categories
                                        </span>
                                    </label>

                                    @foreach($categories as $cat)
                                        <label class="flex items-center justify-between cursor-pointer group">
                                            <span
                                                class="flex items-center gap-2.5 text-sm text-[#3A2A1F]/80 group-hover:text-[#3A2A1F] transition-colors">
                                                <input type="radio" name="category" value="{{ $cat->slug }}"
                                                    {{ request('category') === $cat->slug ? 'checked' : '' }}
                                                    class="category-radio w-4 h-4 border-[#ebd7be] accent-[#C65A3A] focus:ring-0 bg-[#FFF7EF]">
                                                {{ $cat->cat_name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Price Range --}}
                            <div class="pt-5 border-t border-[#ebd7be]/40 mb-5">
                                <button type="button" class="w-full flex items-center justify-between text-left focus:outline-none py-1">
                                    <span class="text-xs font-bold uppercase tracking-wider text-[#1F3D2E]">Price
                                        Range</span>
                                    <i class="fas fa-minus text-[10px] text-[#C65A3A]"></i>
                                </button>
                                <div class="mt-4 px-1">
                                    {{-- Dual range slider (UI only — drives the two number inputs below) --}}
                                    <div class="relative w-full h-1.5 mt-3 mb-6">
                                        <div class="absolute h-full w-full bg-[#ebd7be]/50 rounded-full"></div>
                                        <div id="slider-track-accent" class="absolute h-full bg-[#C65A3A] rounded-full"
                                            style="left:0%;right:0%;"></div>
                                        <input type="range" id="price-min" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
                                            value="{{ request('min_price', $priceFloor) }}"
                                            class="range-slider-input">
                                        <input type="range" id="price-max" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
                                            value="{{ request('max_price', $priceCeil) }}"
                                            class="range-slider-input">
                                    </div>
                                    {{-- Min / Max inputs — these are the ones actually submitted --}}
                                    <div
                                        class="flex items-center justify-between gap-2 text-xs font-semibold text-[#3A2A1F]/70">
                                        <div
                                            class="flex items-center bg-[#ebd7be]/20 rounded-xl border border-[#ebd7be]/60 px-3 py-2 w-[45%]">
                                            <span class="mr-1 text-[#3A2A1F]/60">Rs.</span>
                                            <input type="number" id="input-min" name="min_price"
                                                value="{{ request('min_price', $priceFloor) }}" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
                                                class="w-full bg-transparent border-none p-0 text-[#1F3D2E] font-bold focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </div>
                                        <span class="text-[#3A2A1F]/50 font-bold">TO</span>
                                        <div
                                            class="flex items-center bg-[#ebd7be]/20 rounded-xl border border-[#ebd7be]/60 px-3 py-2 w-[45%]">
                                            <span class="mr-1 text-[#3A2A1F]/60">Rs.</span>
                                            <input type="number" id="input-max" name="max_price"
                                                value="{{ request('max_price', $priceCeil) }}" min="{{ $priceFloor }}" max="{{ $priceCeil }}"
                                                class="w-full bg-transparent border-none p-0 text-[#1F3D2E] font-bold focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>

                    </div>
                </aside>
                {{-- END LEFT SIDEBAR --}}

                {{-- ===== RIGHT: PRODUCT GRID ===== --}}
                <main class="md:col-span-8 lg:col-span-9">

                    {{-- ==================== PAGE HEADER ==================== --}}
                    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-end justify-between gap-3 sm:gap-4">
                        <div>
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-[#1F3D2E] tracking-tight">Shop
                                Authentic Crafts</h1>
                            <p class="text-[#3A2A1F]/70 text-sm mt-1">Discover hand-crafted pieces from Nepal's finest
                                master craftspeople.</p>
                        </div>
                        <div class="flex items-center gap-2 self-start sm:self-auto">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#3A2A1F]/60">Sort
                                by:</span>
                            <div class="relative inline-block">
                                <select id="sort-select" name="sort"
                                    class="appearance-none bg-[#FFF7EF] border border-[#ebd7be] rounded-full px-5 py-2.5 pr-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#1F3D2E]/25 text-[#1F3D2E] cursor-pointer shadow-sm">
                                    <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="popularity" {{ request('sort') === 'popularity' ? 'selected' : '' }}>Popularity</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#1F3D2E]/70">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-6">

    @forelse($products as $product)
        <div
            data-category="{{ $product->category?->slug ?? 'uncategorized' }}"
            class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">

            <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl">

                <img
                    src="{{ $product->primaryImageUrl() }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                @if($product->hasDiscount())
                    @php $discPct = $product->getDiscountPercentage(); @endphp
                    <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#C65A3A] text-white text-[7px] xs:text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm z-10">
                        -{{ $discPct }}% OFF
                    </span>
                @endif

                <button
                    class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-10 sm:h-10 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                    data-product-id="{{ $product->id }}"
                    data-product-name="{{ $product->name }}"
                    data-product-price="{{ $product->effectivePrice() }}"
                    data-product-image="{{ $product->primaryImageUrl() }}"
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

                    <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1">
                        {{ $product->name }}
                    </h3>

                    <span class="text-[#C65A3A] font-bold text-xs sm:text-sm md:text-base block mb-2 sm:mb-4">
                        Rs {{ number_format($product->effectivePrice(), 2) }}
                    </span>
                </div>

                    <div class="flex gap-1 sm:gap-2 mt-auto">
                        <a href="{{ route('viewdetails', $product->slug) }}"
                           class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                           data-id="{{ $product->id }}"
                           data-slug="{{ $product->slug }}"
                           data-name="{{ $product->name }}"
                           data-price="{{ $product->effectivePrice() }}"
                           data-original-price="{{ $product->originalPrice() }}"
                           data-discount="{{ $product->hasDiscount() ? 'true' : 'false' }}"
                           data-discount-price="{{ $product->resolvedDiscountPrice() ?? '' }}"
                           data-image="{{ $product->primaryImageUrl() }}"
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
        <div class="col-span-3 text-center py-12">
            No products found.
        </div>
    @endforelse

</div>

                    {{-- ==================== PAGINATION (always shown) ==================== --}}
                    <div class="flex items-center justify-center gap-3 mt-12 pb-6">

                        {{-- Previous --}}
                        @if($products->onFirstPage())
                            <span
                                class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </span>
                        @else
                            <a href="{{ $products->previousPageUrl() }}" wire:navigate
                                class="w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm">
                                <i class="fas fa-chevron-left text-xs"></i>
                            </a>
                        @endif

                        {{-- Page numbers (windowed around the current page) --}}
                        <div class="flex items-center gap-1">
                            @php
                                $start = max(1, $products->currentPage() - 2);
                                $end = min($products->lastPage(), $products->currentPage() + 2);
                            @endphp

                            @if($start > 1)
                                <a href="{{ $products->url(1) }}" wire:navigate
                                    class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">1</a>
                                @if($start > 2)
                                    <span class="text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none">...</span>
                                @endif
                            @endif

                            @for($page = $start; $page <= $end; $page++)
                                @if($page == $products->currentPage())
                                    <a href="{{ $products->url($page) }}" wire:navigate
                                        class="w-10 h-10 flex flex-col items-center justify-center text-sm font-bold text-[#1F3D2E] relative">
                                        <span>{{ $page }}</span>
                                        <span class="absolute bottom-1 w-5 h-0.5 bg-[#1F3D2E] rounded-full"></span>
                                    </a>
                                @else
                                    <a href="{{ $products->url($page) }}" wire:navigate
                                        class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">{{ $page }}</a>
                                  @endif
                              @endfor

                            @if($end < $products->lastPage())
                                @if($end < $products->lastPage() - 1)
                                    <span class="text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none">...</span>
                                @endif
                                <a href="{{ $products->url($products->lastPage()) }}" wire:navigate
                                    class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">{{ $products->lastPage() }}</a>
                            @endif
                        </div>

                        {{-- Next --}}
                        @if($products->hasMorePages())
                            <a href="{{ $products->nextPageUrl() }}" wire:navigate
                                class="w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </a>
                        @else
                            <span
                                class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </span>
                        @endif
                    </div>

                </main>
                {{-- END RIGHT PRODUCT GRID --}}

            </form>{{-- end filter form / main layout grid --}}
        </div>
    </div>




    {{-- ==================== FILTER UI BEHAVIOUR JS ==================== --}}
    <script>
        (function () {
            const filterForm = document.getElementById('filter-form');

            // Mobile filters toggle
            const filterToggle = document.getElementById('filter-mobile-toggle');
            const filterBody = document.getElementById('filter-body');
            const filterToggleIcon = document.getElementById('filter-toggle-icon');

            if (filterToggle && filterBody) {
                filterToggle.addEventListener('click', function () {
                    const isHidden = filterBody.classList.contains('hidden');
                    if (isHidden) {
                        filterBody.classList.remove('hidden');
                        if (filterToggleIcon) {
                            filterToggleIcon.classList.remove('fa-chevron-down');
                            filterToggleIcon.classList.add('fa-chevron-up');
                        }
                    } else {
                        filterBody.classList.add('hidden');
                        if (filterToggleIcon) {
                            filterToggleIcon.classList.remove('fa-chevron-up');
                            filterToggleIcon.classList.add('fa-chevron-down');
                        }
                    }
                });
            }

            // Accordion toggles for filter subsections
            const subsectionButtons = document.querySelectorAll('#filter-body > div > button[type="button"]');
            subsectionButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const content = btn.nextElementSibling;
                    if (!content) return;

                    const icon = btn.querySelector('i.fas');
                    const isCollapsed = content.classList.contains('hidden');

                    if (isCollapsed) {
                        content.classList.remove('hidden');
                        if (icon) {
                            icon.classList.remove('fa-plus');
                            icon.classList.add('fa-minus');
                        }
                    } else {
                        content.classList.add('hidden');
                        if (icon) {
                            icon.classList.remove('fa-minus');
                            icon.classList.add('fa-plus');
                        }
                    }
                });
            });

            // Price range slider selectors
            const minSlider = document.getElementById('price-min');
            const maxSlider = document.getElementById('price-max');
            const minInput = document.getElementById('input-min');
            const maxInput = document.getElementById('input-max');
            const trackAccent = document.getElementById('slider-track-accent');

            const sliderFloor = parseInt(minSlider.min);
            const sliderCeil = parseInt(maxSlider.max);
            const sliderRange = Math.max(1, sliderCeil - sliderFloor);
            // Keep the two handles from overlapping, but don't let the gap
            // swallow a narrow price range (e.g. a shop where everything
            // costs roughly the same).
            const MIN_GAP = Math.min(100, Math.max(1, Math.floor(sliderRange * 0.05)));

            function updateTrack(minVal, maxVal) {
                const minPct = ((minVal - sliderFloor) / sliderRange) * 100;
                const maxPct = 100 - ((maxVal - sliderFloor) / sliderRange) * 100;
                trackAccent.style.left = minPct + '%';
                trackAccent.style.right = maxPct + '%';
            }

            function onSliderChange() {
                let minVal = parseInt(minSlider.value);
                let maxVal = parseInt(maxSlider.value);
                if (maxVal - minVal < MIN_GAP) {
                    if (document.activeElement === minSlider) {
                        minVal = maxVal - MIN_GAP;
                        minSlider.value = minVal;
                    } else {
                        maxVal = minVal + MIN_GAP;
                        maxSlider.value = maxVal;
                    }
                }
                minInput.value = minVal;
                maxInput.value = maxVal;
                updateTrack(minVal, maxVal);
            }

            function onInputChange() {
                const sliderMin = parseInt(minSlider.min);
                const sliderMax = parseInt(maxSlider.max);
                let minVal = Math.max(sliderMin, parseInt(minInput.value) || sliderMin);
                let maxVal = Math.min(sliderMax, parseInt(maxInput.value) || sliderMax);
                if (maxVal - minVal < MIN_GAP) {
                    if (document.activeElement === minInput) {
                        minVal = Math.max(sliderMin, maxVal - MIN_GAP);
                    } else {
                        maxVal = Math.min(sliderMax, minVal + MIN_GAP);
                    }
                }
                minSlider.value = minVal;
                maxSlider.value = maxVal;
                minInput.value = minVal;
                maxInput.value = maxVal;
                updateTrack(minVal, maxVal);
            }

            minSlider.addEventListener('input', onSliderChange);
            maxSlider.addEventListener('input', onSliderChange);
            minInput.addEventListener('change', onInputChange);
            maxInput.addEventListener('change', onInputChange);

            // Initialise slider accent on load
            updateTrack(parseInt(minSlider.value), parseInt(maxSlider.value));

            // Submit the form once the user lets go of either price handle
            minSlider.addEventListener('change', () => filterForm.submit());
            maxSlider.addEventListener('change', () => filterForm.submit());

            // Debounce the number inputs so we don't submit on every keystroke
            let priceTimeout;
            [minInput, maxInput].forEach(el => {
                el.addEventListener('input', () => {
                    clearTimeout(priceTimeout);
                    priceTimeout = setTimeout(() => filterForm.submit(), 600);
                });
            });

            // Collection Pills — visual only for now (no backing data field yet)
            const pills = document.querySelectorAll('.collection-pill');
            pills.forEach(pill => {
                pill.addEventListener('click', () => {
                    pills.forEach(p => {
                        p.classList.remove('bg-[#C65A3A]', 'text-white', 'shadow-sm', 'hover:bg-[#b04a2c]');
                        p.classList.add('bg-transparent', 'text-[#1F3D2E]', 'hover:bg-[#C65A3A]/10');
                    });
                    pill.classList.remove('bg-transparent', 'text-[#1F3D2E]', 'hover:bg-[#C65A3A]/10');
                    pill.classList.add('bg-[#C65A3A]', 'text-white', 'shadow-sm', 'hover:bg-[#b04a2c]');
                });
            });

            // Category radios + sort + in-stock toggle auto-submit the form
            document.querySelectorAll('.category-radio').forEach(radio => {
                radio.addEventListener('change', () => filterForm.submit());
            });

            document.getElementById('sort-select').addEventListener('change', () => filterForm.submit());

            // Reset Button — just navigate back to the bare shop URL
            document.getElementById('reset-filters').addEventListener('click', function () {
                window.location.href = "{{ route('shop') }}";
            });
        })();
    </script>

    @if(isset($activeProduct))
    <script>
        window.activeProductOnLoad = @json($activeProduct);
    </script>
@endif
</x-frontend-layout>

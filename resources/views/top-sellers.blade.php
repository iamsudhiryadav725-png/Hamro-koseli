<x-frontend-layout title="Top Selling Creations - Hamro Koseli"
    description="Discover the most popular and best-selling Nepali crafts and handmade treasures, curated for you."
    ogImage="/images/og-images.jpg">

<div class="bg-[#F4EAE1] text-[#3A2A1F] min-h-screen py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-[#1F3D2E] tracking-tight">🏆 Top Selling Creations</h1>
        </div>

        <!-- Filter Controls -->
        <div class="bg-[#FFF7EF] rounded-3xl p-6 sm:p-8 border border-[#ebd7be]/40 shadow-sm mb-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-[#3A2A1F]/60 block mb-3">Filter by Category</span>
                    <div class="flex flex-wrap gap-2.5" id="category-filters">
                        <button data-category="all" class="filter-pill active px-4 py-2 border border-[#C65A3A]/30 text-[#1F3D2E] text-xs font-bold rounded-full hover:bg-[#C65A3A]/10 transition cursor-pointer">
                            All Categories
                        </button>
                        @php
                            $categories = $products->pluck('category.cat_name')->unique()->filter()->values();
                        @endphp
                        @foreach($categories as $cat)
                            <button data-category="{{ strtolower($cat) }}" class="filter-pill px-4 py-2 border border-[#C65A3A]/30 text-[#1F3D2E] text-xs font-bold rounded-full hover:bg-[#C65A3A]/10 transition cursor-pointer">
                                {{ ucwords($cat) }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center gap-3 self-start lg:self-auto shrink-0">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#3A2A1F]/60">Sort By:</span>
                    <div class="relative">
                        <select id="sort-select" class="appearance-none bg-[#FFF7EF] border border-[#ebd7be] rounded-full px-5 py-2.5 pr-10 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-[#1F3D2E]/25 text-[#1F3D2E] cursor-pointer shadow-sm">
                            <option value="rank">Popularity (Rank)</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-[#1F3D2E]/70">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6" id="product-grid">
            @foreach($products as $index => $product)
                @php
                    $rank = $index + 1;
                    $rankBg = match(true) {
                        $rank === 1 => 'bg-yellow-400 text-yellow-900',
                        $rank === 2 => 'bg-slate-300 text-slate-800',
                        $rank === 3 => 'bg-amber-600 text-white',
                        default     => 'bg-[#1F3D2E] text-white',
                    };

                    $imageUrl = method_exists($product, 'primaryImageUrl')
                        ? $product->primaryImageUrl()
                        : ($product->image ? asset($product->image) : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PC9zdmc+');

                    $price         = (float) $product->price;
                    $discountPrice = method_exists($product, 'resolvedDiscountPrice')
                        ? $product->resolvedDiscountPrice()
                        : ($product->discount_price ?? null);
                    $hasDiscount  = !is_null($discountPrice) && $discountPrice < $price;
                    $displayPrice = $hasDiscount ? $discountPrice : $price;

                    $catName    = $product->category->cat_name ?? 'Crafts';
                    $vendorName = $product->vendor->vendor_name ?? 'Local Artisan';
                    $rating     = $product->rating ?? 5;
                    $reviews    = $product->reviews_count ?? 0;
                    $stock      = $product->stock ?? 10;
                @endphp

                <div class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group"
                     data-id="{{ $product->id }}"
                               data-slug="{{ $product->slug }}"
                     data-name="{{ $product->name }}"
                     data-price="{{ $displayPrice }}"
                     data-category="{{ strtolower($catName) }}"
                     data-rank="{{ $rank }}">

                    <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl">
                        <img src="{{ $imageUrl }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PC9zdmc+'">

                        <span class="absolute top-2 left-2 sm:top-3 sm:left-3 {{ $rankBg }} text-[7px] xs:text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wider px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full z-10 shadow">
                            #{{ $rank }} {{ $rank <= 3 ? 'Best Seller' : 'Seller' }}
                        </span>

                        <button class="wishlist-btn absolute bottom-2 right-2 sm:bottom-4 sm:right-4 bg-white/90 hover:bg-white text-[#C65A3A] hover:text-[#b04a2c] w-7 h-7 sm:w-10 sm:h-10 rounded-full shadow-md transition-all flex items-center justify-center z-10 focus:outline-none"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}"
                                data-product-price="{{ $displayPrice }}"
                                data-product-image="{{ $imageUrl }}"
                                data-product-desc="{{ $product->description }}"
                                data-product-category="{{ $catName }}"
                                data-product-tag="{{ $product->tag ?? '' }}">
                            <i class="far fa-heart text-[10px] sm:text-lg"></i>
                        </button>
                    </div>

                    <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                        <div>
                            <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">
                                {{ $catName }}
                            </span>

                            <h3 class="text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1">
                                {{ $product->name }}
                            </h3>

                            <div class="flex items-baseline gap-1 sm:gap-2 mb-2 sm:mb-4">
                                <span class="text-[#C65A3A] font-bold text-xs sm:text-sm md:text-base">
                                    Rs {{ number_format($displayPrice, 2) }}
                                </span>
                                @if($hasDiscount)
                                    <span class="text-slate-400 text-[8px] sm:text-xs line-through font-semibold">
                                        Rs {{ number_format($price, 2) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- View Details triggers modal -->
                        <div class="flex gap-1 sm:gap-2 mt-auto">
                            <a href="{{ route('viewdetails', $product->slug) }}"
                               class="view-details-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300"
                               data-id="{{ $product->id }}"
                               data-slug="{{ $product->slug }}"
                               data-name="{{ $product->name }}"
                               data-price="{{ $displayPrice }}"
                               data-original-price="{{ $price }}"
                               data-discount="{{ $hasDiscount ? 'true' : 'false' }}"
                               data-discount-price="{{ $discountPrice ?? '' }}"
                               data-image="{{ $imageUrl }}"
                               data-category="{{ $catName }}"
                               data-vendor="{{ $vendorName }}"
                               data-desc="{{ $product->description }}"
                               data-rating="{{ $rating }}"
                               data-reviews="{{ $reviews }}"
                               data-stock="{{ $stock }}">
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
            @endforeach
        </div>

        {{-- ==================== PAGINATION (always shown) ==================== --}}
        @if(method_exists($products, 'currentPage'))
        <div class="flex items-center justify-center gap-3 mt-12 pb-6">

            {{-- Previous --}}
            @if($products->onFirstPage())
                <span class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                </span>
            @else
                <a href="{{ $products->previousPageUrl() }}" wire:navigate
                    class="w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
            @endif

            {{-- Page numbers --}}
            <div class="flex items-center gap-1">
                @php
                    $start = max(1, $products->currentPage() - 2);
                    $end = min($products->lastPage(), $products->currentPage() + 2);
                @endphp

                @if($start > 1)
                    <a href="{{ $products->url(1) }}" wire:navigate class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">1</a>
                    @if($start > 2)
                        <span class="text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none">...</span>
                    @endif
                @endif

                @for($page = $start; $page <= $end; $page++)
                    @if($page == $products->currentPage())
                        <a href="{{ $products->url($page) }}" wire:navigate class="w-10 h-10 flex flex-col items-center justify-center text-sm font-bold text-[#1F3D2E] relative">
                            <span>{{ $page }}</span>
                            <span class="absolute bottom-1 w-5 h-0.5 bg-[#1F3D2E] rounded-full"></span>
                        </a>
                    @else
                        <a href="{{ $products->url($page) }}" wire:navigate class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">{{ $page }}</a>
                    @endif
                @endfor

                @if($end < $products->lastPage())
                    @if($end < $products->lastPage() - 1)
                        <span class="text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none">...</span>
                    @endif
                    <a href="{{ $products->url($products->lastPage()) }}" wire:navigate class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">{{ $products->lastPage() }}</a>
                @endif
            </div>

            {{-- Next --}}
            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" wire:navigate
                    class="w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            @else
                <span class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                </span>
            @endif
        </div>
        @endif

    </div>
</div>



<style>
    .filter-pill.active { background-color: rgba(198,90,58,0.12); border-color: #C65A3A; }
</style>

<script>
(function () {

    // ── Filter ──────────────────────────────────────────────
    const pills = document.querySelectorAll('.filter-pill');
    const cards = document.querySelectorAll('.product-card');
    const grid  = document.getElementById('product-grid');

    pills.forEach(pill => {
        pill.addEventListener('click', function () {
            pills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            const cat = this.dataset.category;
            cards.forEach(card => {
                card.style.display = (cat === 'all' || card.dataset.category === cat) ? '' : 'none';
            });
        });
    });

    // ── Sort ────────────────────────────────────────────────
    document.getElementById('sort-select').addEventListener('change', function () {
        const criteria = this.value;
        Array.from(cards)
            .sort((a, b) => {
                if (criteria === 'rank')       return +a.dataset.rank  - +b.dataset.rank;
                if (criteria === 'price-asc')  return +a.dataset.price - +b.dataset.price;
                if (criteria === 'price-desc') return +b.dataset.price - +a.dataset.price;
                return 0;
            })
            .forEach(card => grid.appendChild(card));
    });


    {{-- ==================== ADD TO CART (AJAX) ==================== --}}
    // ── Toast helper ────────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        const existing = document.getElementById('shop-cart-toast');
        if (existing) existing.remove();

        const colours = {
            success : 'bg-[#1F3D2E] text-white',
            error   : 'bg-red-600 text-white',
            warning : 'bg-amber-500 text-white',
            info    : 'bg-[#C65A3A] text-white',
        };

        const icons = {
            success : 'fa-circle-check',
            error   : 'fa-circle-xmark',
            warning : 'fa-triangle-exclamation',
            info    : 'fa-circle-info',
        };

        const toast = document.createElement('div');
        toast.id = 'shop-cart-toast';
        toast.className = [
            'fixed bottom-6 right-6 z-[9999] flex items-center gap-3',
            'px-5 py-3.5 rounded-2xl shadow-xl text-sm font-semibold',
            'translate-y-4 opacity-0 transition-all duration-300',
            colours[type] ?? colours.success,
        ].join(' ');

        toast.innerHTML = `
            <i class="fas ${icons[type] ?? icons.success} text-base"></i>
            <span>${message}</span>
        `;

        document.body.appendChild(toast);

        // Animate in
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            });
        });

        // Animate out after 3 s
        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // ── Update cart badge in the navbar (if one exists) ─────────────────
    function updateCartBadge(count) {
        document.querySelectorAll('[data-cart-count]').forEach(el => {
            el.textContent = count;
            el.classList.toggle('hidden', count === 0);
        });
    }

    // ── Wire up every "Add to Cart" button ──────────────────────────────
    (function () {

        document.querySelectorAll('.add-to-cart-btn').forEach(function (btn) {
            btn.addEventListener('click', async function () {
                const productId   = btn.dataset.productId;
                const productName = btn.dataset.productName;

                // Prevent double-clicks while the request is in flight
                btn.disabled = true;
                const originalHtml = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Adding…';

                try {
                    const response = await fetch('{{ route('cart.add') }}', {
                        method  : 'POST',
                        headers : {
                            'Content-Type' : 'application/json',
                            'Accept'       : 'application/json',
                            // Laravel CSRF token -must be present in the page meta tag
                            'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                        },
                        body: JSON.stringify({
                            product_id : productId,
                            quantity   : 1,
                        }),
                    });

                    const json = await response.json();

                    if (response.status === 401) {
                        // Not logged in -send to login page
                        showToast('Please log in to add items to your cart.', 'warning');
                        setTimeout(() => {
                            window.location.href = '{{ route('userlogin') }}';
                        }, 1500);
                        return;
                    }

                    if (json.success) {
                        showToast(`${productName} added to cart!`, 'success');
                        updateCartBadge(json.cart_count ?? 0);

                        // Brief visual feedback on the button
                        btn.innerHTML = '<i class="fas fa-check text-xs"></i> Added!';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.disabled  = false;
                        }, 1500);
                    } else {
                        showToast(json.message ?? 'Could not add to cart.', 'error');
                        btn.innerHTML = originalHtml;
                        btn.disabled  = false;
                    }

                } catch (err) {
                    console.error('Add-to-cart error:', err);
                    showToast('Something went wrong. Please try again.', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled  = false;
                }
            });
        });

    })();
})();
</script>

</x-frontend-layout>

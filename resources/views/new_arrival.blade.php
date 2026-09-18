<x-frontend-layout title="New Arrivals - Hamro Koseli">
    <div class="bg-[#FFF7EF] text-[#3A2A1F] min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <!-- Section Header -->
            <div class="mb-10">
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-serif font-extrabold text-[#1F3D2E] tracking-tight mb-3 sm:mb-4">
                    New Arrivals
                </h1>
                <p class="text-[#3A2A1F]/70 text-sm md:text-base leading-relaxed">
                    The newest handmade pieces added to Hamro Koseli.
                </p>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-6">

                @forelse ($products as $product)
                    <div
                        data-category="{{ $product->category?->slug ?? 'uncategorized' }}"
                        class="product-card bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group">

                        <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl bg-slate-100">
                            <img src="{{ $product->primaryImageUrl() }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500">

                            <span class="absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#C65A3A] text-white text-[7px] xs:text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm z-10">
                                New !!!
                            </span>


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

                            <div class="flex flex-row gap-1 sm:gap-2 mt-auto">
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
                                   data-reviews="{{ $product->reviews_count ?? 0 }}"
                                   data-stock="{{ $product->stock ?? 0 }}">
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
                    <div class="col-span-4 text-center py-12">
                        <p class="text-[#3A2A1F]/60">No products found.</p>
                    </div>
                @endforelse

            </div>

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
                        $end   = min($products->lastPage(), $products->currentPage() + 2);
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
                    <span class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </span>
                @endif
            </div>

        </div>
    </div>
</x-frontend-layout>

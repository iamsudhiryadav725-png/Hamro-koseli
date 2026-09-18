<x-frontend-layout title="My Wishlist - Hamro Koseli">
    <div class="bg-[#F4EAE1] text-[#3A2A1F] min-h-screen py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">
            
            <!-- Page Title Section -->
            <div class="mb-10 sm:mb-12">
                <h1 class="text-2xl sm:text-3.5xl sm:text-4xl md:text-5xl font-extrabold text-[#1F3D2E] tracking-tight mb-2 sm:mb-3">
                    My Wishlist
                </h1>
                <p class="text-[#3A2A1F]/70 text-sm sm:text-base font-medium max-w-xl">
                    A curated collection of your favorite Nepali treasures.
                </p>
            </div>

            {{--
            ========================================================================
            BACKEND DEVELOPERS NOTICE:
            ------------------------------------------------------------------------
            You can easily swap this dynamic frontend/localStorage simulation with 
            Laravel Blade rendering by using a loop like this:

            @if(isset($wishlistItems) && count($wishlistItems) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 sm:gap-8">
                    @foreach($wishlistItems as $item)
                        <div class="bg-white rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group relative">
                            <div class="relative w-full aspect-[4/3] overflow-hidden">
                                <img src="{{ asset($item->product->image_path) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @if($item->product->tag)
                                    <span class="absolute bottom-3 left-3 bg-[#1F3D2E]/80 text-[#FFF7EF] text-[10px] font-semibold px-2.5 py-1 rounded-full shadow-sm backdrop-blur-xs">
                                        {{ $item->product->tag }}
                                    </span>
                                @endif
                                <form action="{{ route('wishlist.remove', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="absolute top-3 right-3 bg-white hover:bg-red-50 text-[#C65A3A] hover:text-red-600 rounded-full w-9 h-9 flex items-center justify-center shadow-md transition hover:scale-105 active:scale-95 cursor-pointer">
                                        <i class="fa-regular fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </div>
                            <div class="p-5 flex-grow flex flex-col justify-between">
                                <div class="mb-4">
                                    <h3 class="text-base sm:text-lg font-bold text-[#1F3D2E] mb-1.5 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1">
                                        {{ $item->product->name }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[#3A2A1F]/70 line-clamp-2 leading-relaxed">
                                        {{ strip_tags($item->product->description) }}
                                    </p>
                                </div>
                                <div class="mt-auto space-y-4">
                                    <span class="text-[#C65A3A] font-extrabold text-base sm:text-lg block">रू {{ number_format($item->product->price) }}</span>
                                    <form action="{{ route('cart.add') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                        <button type="submit" class="w-full flex items-center justify-center gap-2 bg-[#b55b3d] hover:bg-[#a04f33] text-white text-sm font-bold py-3 px-4 rounded-xl shadow-sm hover:shadow transition duration-300">
                                            <i class="fa-solid fa-cart-shopping text-xs"></i> Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-20 bg-white/40 rounded-3xl border border-[#ebd7be]/20 shadow-sm max-w-xl mx-auto mt-8 px-6">
                    <div class="w-16 h-16 bg-[#ebd7be]/30 rounded-full flex items-center justify-center mx-auto mb-5 text-[#C65A3A]">
                        <i class="fa-regular fa-heart text-2xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-[#1F3D2E] mb-2">Your wishlist is empty</h2>
                    <p class="text-sm text-[#3A2A1F]/70 max-w-sm mx-auto mb-8 font-medium">
                        Explore our collection and add your favorite Nepalese handicrafts to your wishlist.
                    </p>
                    <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white font-bold px-8 py-3 rounded-full shadow-md hover:shadow transition duration-300 text-sm">
                        Shop Now
                    </a>
                </div>
            @endif
            ========================================================================
            --}}

            <!-- Active Frontend Wishlist Grid -->
            <div id="wishlist-grid-container">
                <!-- Javascript will render products grid here -->
                <div class="flex items-center justify-center py-20">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#C65A3A]"></div>
                </div>
            </div>

        </div>
    </div>

    <!-- Template for rendering Wishlist Items dynamically -->
    <template id="wishlist-card-template">
        <div class="bg-white rounded-2xl sm:rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm hover:shadow-md transition duration-300 flex flex-col group relative">
            <div class="relative w-full aspect-square overflow-hidden rounded-t-2xl sm:rounded-t-3xl">
                <img src="" alt="" class="wishlist-img w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <span class="wishlist-tag absolute top-2 left-2 sm:top-3 sm:left-3 bg-[#C65A3A] text-white text-[7px] xs:text-[9px] sm:text-[10px] font-bold px-1.5 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow-sm z-10 hidden">
                    Tag
                </span>
            </div>
            <div class="p-3 sm:p-5 flex-grow flex flex-col justify-between">
                <div>
                    <span class="text-[8px] sm:text-[10px] font-bold uppercase tracking-wider text-[#3A2A1F]/50 block mb-0.5 sm:mb-1">
                        Crafts
                    </span>
                    <h3 class="wishlist-title text-xs sm:text-sm md:text-base lg:text-lg font-bold text-[#1F3D2E] mb-1 sm:mb-2 leading-tight group-hover:text-[#C65A3A] transition-colors line-clamp-1">
                        Product Name
                    </h3>
                    <p class="wishlist-desc hidden">
                        Product description goes here.
                    </p>
                    <span class="wishlist-price text-[#C65A3A] font-bold text-xs sm:text-sm md:text-base block mb-2 sm:mb-4">रू Price</span>
                </div>
                <div class="flex gap-1 sm:gap-2 mt-auto">
                    <button type="button" class="wishlist-delete-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#1F3D2E] hover:bg-[#16301f] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300" title="Remove from wishlist">
                        <i class="fa-solid fa-trash-can text-[8px] sm:text-xs"></i>
                        Remove
                    </button>
                    <button type="button" class="wishlist-add-cart-btn flex-grow flex items-center justify-center gap-1 sm:gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-[8px] sm:text-xs md:text-sm font-semibold py-1.5 px-1 sm:py-3 sm:px-3 rounded-lg sm:rounded-xl shadow-sm hover:shadow transition duration-300">
                        <i class="fa-solid fa-cart-plus text-[8px] sm:text-xs"></i>
                        Add
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Template for Empty State -->
    <template id="wishlist-empty-template">
        <div class="text-center py-20 bg-white/40 rounded-3xl border border-[#ebd7be]/20 shadow-sm max-w-xl mx-auto mt-8 px-6">
            <div class="w-16 h-16 bg-[#ebd7be]/30 rounded-full flex items-center justify-center mx-auto mb-5 text-[#C65A3A]">
                <i class="fa-regular fa-heart text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold text-[#1F3D2E] mb-2">Your wishlist is empty</h2>
            <p class="text-sm text-[#3A2A1F]/70 max-w-sm mx-auto mb-8 font-medium">
                Explore our collection and add your favorite Nepalese handicrafts to your wishlist.
            </p>
            <a href="{{ route('shop') }}" wire:navigate class="inline-flex items-center gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white font-bold px-8 py-3 rounded-full shadow-md hover:shadow transition duration-300 text-sm">
                Shop Now
            </a>
        </div>
    </template>
</x-frontend-layout>

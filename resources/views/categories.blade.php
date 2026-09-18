<x-frontend-layout title="Categories - Hamro Koseli"
    description="Explore the rich heritage of Nepal through our curated collection of masterfully crafted artifacts, each telling a story of ancient traditions and skilled hands."
    ogImage="/images/og-images.jpg">
    <div class="bg-[#FFF7EF] text-[#3A2A1F] min-h-screen py-8 sm:py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            {{-- ==================== HERO SECTION ==================== --}}
            <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16">
                <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold text-[#1F3D2E] tracking-tight mb-4">Discover Our
                    Categories</h1>
                <p class="text-[#3A2A1F]/70 text-sm md:text-base leading-relaxed">
                    Explore the soul of Nepal through our curated collection of masterfully crafted artifacts, each
                    telling a story of ancient traditions and skilled hands.
                </p>
                <div class="mt-8 max-w-xl mx-auto relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-[#3A2A1F]/50">
                        <i class="fas fa-search text-sm"></i>
                    </span>
                    <input type="text" id="category-search" placeholder="Explore our categories..."
                        class="w-full bg-white border border-[#ebd7be]/80 rounded-full py-4 pl-12 pr-6 text-sm focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/25 text-[#1F3D2E] placeholder-[#3A2A1F]/40 shadow-sm transition-all duration-300">
                </div>
            </div>

            {{-- ==================== SPOTLIGHT CRAFT SECTION ==================== --}}
            <div class="relative rounded-3xl overflow-hidden mb-16 border border-[#ebd7be]/40 shadow-sm group">
                <!-- Background Image -->
                <div class="absolute inset-0 w-full h-full bg-[#1F3D2E]">
                    <img src="{{ asset('images/Categories.jpg') }}" alt="Bhaktapur Pottery"
                        class="w-full h-full object-cover opacity-80 group-hover:scale-[1.02] transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-r from-black/85 via-black/40 to-transparent"></div>
                </div>

                <!-- Content Container -->
                <div
                    class="relative z-10 max-w-2xl py-8 px-4 sm:px-12 md:py-20 md:px-16 flex flex-col items-start justify-center min-h-[240px] sm:min-h-[360px]">
                    <span
                        class="bg-[#C65A3A] text-white text-[10px] font-bold tracking-widest uppercase px-3.5 py-1.5 rounded-full shadow-sm mb-4">Our Heritage</span>
                    <h2 class="text-xl sm:text-3xl md:text-4xl font-bold text-white tracking-tight mb-3">Handcrafted Treasures of Nepal</h2>
                    <p class="text-white/80 text-sm md:text-base leading-relaxed mb-8">
                        From vibrant paintings to intricate wood carvings, traditional textiles to authentic spices —
                        explore the rich diversity of Nepali craftsmanship and culture.
                    </p>
                    <a href="{{ route('shop', ['category' => 'pottery']) }}" wire:navigate
                        class="flex items-center gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-sm font-semibold py-3.5 px-6 rounded-xl shadow-sm hover:shadow transition duration-300">
                        Explore Collection <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

            {{-- ==================== BROWSE BY CATEGORY SECTION ==================== --}}
            <div class="mb-16">
                <div class="flex items-end justify-between mb-8 pb-3 border-b border-[#ebd7be]/60">
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold text-[#1F3D2E] inline-block relative">
                            Browse by Category
                            <span class="absolute bottom-[-13px] left-0 w-full h-[3px] bg-[#C65A3A]"></span>
                        </h2>
                    </div>
                    <span class="text-xs font-semibold text-[#3A2A1F]/60" id="category-counter">Showing
                        {{ $categories->count() }} Heritage Categories</span>
                </div>

                <!-- Categories Grid -->
                <div class="grid grid-cols-2 md:grid-cols-12 gap-3 sm:gap-6">
                    @foreach ($categories as $category)
                        @php
                            $remainder = $loop->index;
                            $colSpanClass = '';
                            $heightClass = '';
                            $titleSizeClass = '';

                            if ($remainder === 0) {
                                $colSpanClass = 'md:col-span-7';
                                $heightClass = 'h-44 sm:h-72 sm:h-80 md:h-[350px]';
                                $titleSizeClass = 'text-lg md:text-2xl';
                            } elseif ($remainder === 1) {
                                $colSpanClass = 'md:col-span-5';
                                $heightClass = 'h-44 sm:h-72 sm:h-80 md:h-[350px]';
                                $titleSizeClass = 'text-lg md:text-2xl';
                            } else {
                                $colSpanClass = 'col-span-1 md:col-span-4';
                                $heightClass = 'h-40 sm:h-64 sm:h-72 md:h-[280px]';
                                $titleSizeClass = 'text-base md:text-xl';
                            }
                        @endphp

                        <div
                            class="{{ $colSpanClass }} {{ $heightClass }} relative rounded-3xl overflow-hidden border border-[#ebd7be]/40 shadow-sm group cursor-pointer category-card">
                            <a href="{{ route('shop', ['category' => $category->slug]) }}" wire:navigate class="block w-full h-full">
                                <img src="{{ $category->image ? asset('storage/' . $category->image) : asset('images/pot.png') }}"
                                    alt="{{ $category->cat_name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/25 to-transparent">
                                </div>
                                <div class="absolute bottom-6 left-6 text-white z-10">
                                    <h3 class="{{ $titleSizeClass }} font-bold">{{ $category->cat_name }}</h3>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ==================== BECOME A SELLER BANNER ==================== --}}
            <div
                class="bg-[#1F3D2E] rounded-3xl border border-[#ebd7be]/20 p-8 sm:p-12 md:py-16 md:px-16 shadow-md relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 blur-2xl"></div>
                <div
                    class="absolute left-1/3 bottom-0 w-96 h-96 bg-white/5 rounded-full -ml-48 -mb-48 blur-3xl pointer-events-none">
                </div>

                <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 sm:gap-8">
                    <div class="max-w-2xl">
                        <h3 class="text-2xl md:text-3xl font-bold text-white mb-3">Are you a guardian of heritage?</h3>
                        <p class="text-white/80 text-sm md:text-base leading-relaxed">
                            We are looking for authentic artisans and vendors to join our marketplace. Share your craft
                            with the world and preserve the legacy of local craftsmanship.
                        </p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('seller') }}"
                            class="inline-block bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-sm font-semibold py-3.5 px-8 rounded-xl shadow hover:shadow-md transition duration-300">
                            Become a Seller
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ==================== SEARCH SCRIPT ==================== --}}
    <script>
        (function() {
            const searchInput = document.getElementById('category-search');
            const categoryCards = document.querySelectorAll('.category-card');
            const counter = document.getElementById('category-counter');

            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const query = e.target.value.toLowerCase().trim();
                    let visibleCount = 0;

                    categoryCards.forEach(card => {
                        const categoryName = card.querySelector('h3').textContent.toLowerCase();
                        if (categoryName.includes(query)) {
                            card.style.display = '';
                            visibleCount++;
                        } else {
                            card.style.display = 'none';
                        }
                    });

                    if (counter) {
                        counter.textContent = `Showing ${visibleCount} Heritage Categories`;
                    }
                });
            }
        })();
    </script>
</x-frontend-layout>

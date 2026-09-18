<x-frontend-layout title="about - Hamro Koseli"
    description="Shop handmade gift boxes and hampers for every occasion. Delivered across Nepal."
    ogImage="/images/og-images.jpg"
>

<section class="bg-[#F4EAE1] text-brand-dark leading-relaxed">

    <!-- Hero Section -->
    <section
        class="relative h-[45vh] sm:h-[65vh] min-h-[280px] sm:min-h-[480px] w-full flex items-center justify-start bg-cover bg-center overflow-hidden"
        style="background-image: url('{{ asset('images/1st-image.png') }}');">

        <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-transparent"></div>

        <div class="container mx-auto px-4 sm:px-6 md:px-12 max-w-6xl relative z-10 text-white">
            <span class="text-[#e5b842] text-xs font-bold uppercase tracking-[0.25em] block mb-3">
                The Heritage of Nepal
            </span>

            <h1 class="text-2xl sm:text-4xl md:text-6xl font-bold leading-[1.15] max-w-3xl mb-4 sm:mb-6">
                Crafting a bridge between tradition and the modern world.
            </h1>

            <p class="text-gray-300 text-sm md:text-base max-w-xl leading-relaxed font-light">
                Empowering local artisans by showcasing their unique handmade creations, preserving rich cultural heritage, and supporting sustainable livelihoods.
            </p>
        </div>
    </section>

    <!-- Story Section -->
    <section class="container mx-auto px-4 sm:px-6 md:px-12 max-w-6xl py-10 sm:py-20 space-y-12 sm:space-y-24">

        <!-- Row 1 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <div class="space-y-6">
                <h2 class="text-3xl md:text-4xl font-bold text-brand-dark">
                    Our Heritage
                </h2>

                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    For generations, the artisans of Nepal have crafted beautiful works of art...
                </p>

                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    Every product is selected with great care and authenticity.
                </p>
            </div>

            <div class="relative group">
                <div class="absolute -inset-1 bg-brand-primary/10 rounded-2xl blur-sm"></div>

                <img src="{{ asset('images/2nd-image.png') }}"
                    class="relative w-full h-[220px] sm:h-[320px] object-cover rounded-2xl shadow-lg"
                    alt="Traditional Weaving Loom">
            </div>

        </div>

        <!-- Row 2 -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <div class="relative group order-last md:order-first">
                <div class="absolute -inset-1 bg-brand-primary/10 rounded-2xl blur-sm"></div>

                <img src="{{ asset('images/pot.png') }}"
                    class="relative w-full h-[220px] sm:h-[320px] object-cover rounded-2xl shadow-lg"
                    alt="Handmade Clay Pots">
            </div>

            <div class="bg-[#E0D5C5] rounded-2xl p-5 sm:p-8 md:p-10 shadow-md space-y-4 sm:space-y-6">
                <h2 class="text-2xl md:text-3xl font-bold text-brand-dark">
                    The Artisan's Spirit
                </h2>

                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    Every piece tells a story of dedication, passion, and skill.
                </p>

                <p class="text-gray-600 text-sm md:text-base leading-relaxed">
                    We support over 500 family-run workshops.
                </p>
            </div>

        </div>

    </section>

    <!-- Empowering Section -->
    <section class="container mx-auto px-6 md:px-12 max-w-6xl py-12">

        <div class="bg-[#E0D5C5] rounded-3xl p-5 sm:p-8 md:p-12 shadow-lg grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12 items-center">

            <!-- Image -->
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative w-full max-w-[340px]">

                    <div class="absolute bottom-[-15px] right-[-15px] w-full h-full bg-[#5c4a43] rounded-2xl"></div>

                    <img src="{{ asset('images/4th-image.png') }}"
                        class="relative z-10 w-full h-[260px] sm:h-[380px] object-cover rounded-2xl shadow-md"
                        alt="Artisan">
                </div>
            </div>

            <!-- Content -->
            <div class="lg:col-span-7 space-y-6">

                <h2 class="text-3xl font-bold text-brand-primary">
                    Empowering Artisans
                </h2>

                <p class="text-gray-600 text-sm md:text-base">
                    We support local communities and promote sustainable crafting traditions.
                </p>

                <ul class="space-y-4">

                    <li class="flex items-start gap-4">
                        <span class="w-6 h-6 rounded-full bg-[#fceae4] flex items-center justify-center text-brand-primary">
                            <i class="fa-solid fa-check text-sm"></i>
                        </span>
                        <p class="text-gray-700 text-sm font-medium">Direct-to-consumer access.</p>
                    </li>

                    <li class="flex items-start gap-4">
                        <span class="w-6 h-6 rounded-full bg-[#fceae4] flex items-center justify-center text-brand-primary">
                            <i class="fa-solid fa-check text-sm"></i>
                        </span>
                        <p class="text-gray-700 text-sm font-medium">Digital literacy training.</p>
                    </li>

                    <li class="flex items-start gap-4">
                        <span class="w-6 h-6 rounded-full bg-[#fceae4] flex items-center justify-center text-brand-primary">
                            <i class="fa-solid fa-check text-sm"></i>
                        </span>
                        <p class="text-gray-700 text-sm font-medium">Financial support tools.</p>
                    </li>

                </ul>

            </div>

        </div>

    </section>

</section>

</x-frontend-layout>
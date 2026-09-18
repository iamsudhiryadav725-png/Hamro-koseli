<footer class="bg-[#1E2A44] text-slate-300 py-8 md:py-16 mt-auto">
    <div class="container mx-auto px-4 sm:px-6 max-w-7xl">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-6 md:gap-8 lg:gap-12">

            <!-- Column 1: Brand Info -->
            <div class="col-span-2 sm:col-span-3 md:col-span-1 space-y-4">
                <h3 class="text-3xl font-bold text-[#b55b3d] tracking-tight">HamroKoseli</h3>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Preserving Nepal's rich artistic heritage by connecting local master craftspeople with global
                    connoisseurs of authenticity.
                </p>
            </div>

            <!-- Columns 2, 3, 4: Quick Link + Support + Legal (side-by-side grid) -->
            <div
                class="grid grid-cols-2 sm:grid-cols-3 md:contents gap-6 sm:gap-10 col-span-2 sm:col-span-3 md:col-span-3">

                <!-- Column 2: Navigation Links -->
                <div class="space-y-4">
                    <h4 class="text-s font-bold uppercase tracking-widest text-[#b55b3d]">QUICK LINK</h4>
                    <ul class="flex flex-col space-y-2.5 text-sm">
                        <li><a href="{{ url('/') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Home</a></li>
                        <li><a href="{{ url('shop') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Shop</a></li>
                        <li><a href="{{ url('categories') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Categories</a></li>
                        <li><a href="{{ url('about-us') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">About Us</a></li>
                    </ul>
                </div>

                <!-- Column 3: Support Links -->
                <div class="space-y-4">
                    <h4 class="text-s font-bold uppercase tracking-widest text-[#b55b3d]">SUPPORT</h4>
                    <ul class="flex flex-col space-y-2.5 text-sm">
                        <li><a href="{{ url('FAQ') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">FAQs</a></li>
                        <li><a href="{{ url('shipping-info') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Shipping Info</a></li>
                        <li><a href="{{ url('contact-us') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Contact Us</a></li>
                    </ul>
                </div>

                <!-- Column 4: Legal -->
                <div class="space-y-4">
                    <h4 class="text-s font-bold uppercase tracking-widest text-[#b55b3d]">LEGAL</h4>
                    <ul class="flex flex-col space-y-2.5 text-sm">
                        <li><a href="{{ url('privacypolicy') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ url('terms_&_conditions') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ url('return_&_refund') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Return & Refund Policy</a>
                        </li>
                        <li><a href="{{ url('seller_policy') }}" wire:navigate.hover
                                class="text-slate-300 hover:text-white transition-colors">Seller Policy</a></li>
                    </ul>
                </div>

            </div>

            <!-- Column 5: Sell with us -->
            <div class="col-span-2 sm:col-span-3 md:col-span-1 space-y-4">
                <h4 class="text-s font-bold uppercase tracking-widest text-[#b55b3d]">SELL WITH US</h4>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Reach customers across Nepal and beyond. Join our platform to showcase your authentic Nepalese
                    crafts to a global audience.
                </p>
                <div class="pt-1">
                    <a href="{{ route('seller.login') }}"
                        class="text-[#b55b3d] px-3 py-0.5 rounded-xl hover:rounded-xl hover:bg-[#a04e33] hover:text-[#fff7ef] text-sm font-semibold underline underline-offset-4 transition-colors">Become
                        a Seller <i class="fa-solid fa-arrow-right pl-2"></i></a>
                </div>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div
            class="flex flex-col md:flex-row items-center justify-between gap-3 md:gap-6 mt-8 md:mt-16 pt-6 md:pt-8 border-t border-slate-700/30">
            <p class="text-xs text-slate-400 text-center md:text-left">
                &copy; 2026 HamroKoseli. Preserving Nepalese heritage through authentic craftsmanship.
            </p>
            {{-- <div class="flex items-center space-x-6">
                <a href="#" class="text-[#b55b3d] hover:text-[#a04e33] transition-colors text-lg">
                    <i class="fa-solid fa-globe"></i>
                </a>
                <a href="#" class="text-[#b55b3d] hover:text-[#a04e33] transition-colors text-lg">
                    <i class="fa-solid fa-share-nodes"></i>
                </a>
                <a href="#" class="text-[#b55b3d] hover:text-[#a04e33] transition-colors text-lg">
                    <i class="fa-brands fa-instagram"></i>
                </a>
            </div> --}}
        </div>
    </div>
</footer>

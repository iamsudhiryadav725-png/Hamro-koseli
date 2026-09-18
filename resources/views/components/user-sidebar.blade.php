<style>
    .nav-link {
        color: var(--text-light);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        color: var(--text-color);
        transform: translateX(2px);
    }

    .overflow-y-auto::-webkit-scrollbar,
    .overflow-auto::-webkit-scrollbar {
        display: none;
    }

    .overflow-y-auto,
    .overflow-auto {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
</style>
<aside id="sidebar"
    class="fixed top-0 left-0 z-50 h-dvh w-72 bg-[#1E2A44] -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col ">
    <!-- Brand / Logo Section -->
    <div class="px-6 pb-2 border-b border-white/10">
        <div class="flex items-center gap-3 mt-3.5">
            <img src="{{ asset('images/logo.png') }}" alt="HamroKoseli Logo" class="w-10 h-10 bg-white rounded-full object-cover">

            <div class="transition-all duration-300 hover:translate-x-1">
                <h1 class="text-2xl font-bold tracking-tight text-brand-cream font-sans">
                    HamroKoseli</h1>
                <p class="text-xs text-brand-cream font-medium mt-0.5">User Portal</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav
        class="navbar flex-1 px-4 py-4 space-y-1.5 overflow-y-auto scroll-smooth">
        <!-- Dashboard -->
        <a href="{{ route('Userdashboard') }}" wire:navigate
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl  hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('Userdashboard') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="layout-dashboard"
                class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Dashboard</span>
        </a>

        <!-- Orders -->
        <a href="{{ route('User-orders') }}" wire:navigate
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('User-orders') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="shopping-cart" class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Orders</span>
        </a>

        <!-- profile -->
        <a href="{{ route('user-profile') }}" wire:navigate
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('user-profile') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="user-round" class="w-5 transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Profile</span>
        </a>

        <!-- notification -->
        <a href="{{ route('user-notification') }}" wire:navigate
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('user-notification') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="bell" class="w-5 transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Notification</span>
        </a>
    </nav>

    <div class=" px-4 pb-8 mt-auto border-t border-white/10 pt-4 space-y-4">
        <!-- buy now -->
        <a href="{{ route('shop') }}" wire:navigate
            class="flex items-center gap-4 px-4 py-3 rounded-xl bg-brand-primary  hover:bg-[#B14E32] text-brand-cream  transition-all duration-300 active:scale-[0.98] group">
            <i data-lucide="shopping-bag"
                class="w-5 text-brand-cream text-lg transition-transform duration-300 group-hover:rotate-9"></i>
            <span class="font-semibold group-hover:translate-x-1 transition">Back to Shop Now</span>
        </a>
    </div>
</aside>

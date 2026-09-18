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
            <img src="{{ asset('images/logo.png') }}" alt="HamroKoseli Logo"
                class="w-10 h-10 bg-white rounded-full object-cover">

            <div class="transition-all duration-300 hover:translate-x-1">
                <h1 class="text-2xl font-bold tracking-tight text-[#FFF7EF] font-sans">
                    HamroKoseli</h1>
                <p class="text-xs text-[#FFF7EF] font-medium mt-0.5">Vendor Panel</p>
            </div>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="navbar flex-1 px-4 py-4 space-y-1.5 overflow-y-auto scroll-smooth">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl  hover:bg-[#D4A017] group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('dashboard') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="layout-dashboard"
                class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Dashboard</span>
        </a>

        <!-- Products -->
        <a href="{{ route('product-management') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl  hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('product-management') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="package" class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Products</span>
        </a>

        <!-- Orders -->
        <a href="{{ route('order') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl  hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('order') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="shopping-cart"
                class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Orders</span>
        </a>

        <!-- Payments -->
        <a href="{{ route('seller.payment') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl  hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('seller.payment') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="wallet"
                class="fas fa-wallet w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Payments</span>
        </a>

        <!-- Reviews -->
        <a href="{{ route('seller.review') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017] group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('seller.review') ? 'bg-(--hover-color)' : '' }}">

            <i data-lucide="star" class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Reviews</span>
        </a>

        <!-- support -->
        <a href="{{ route('seller-support') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017] group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('seller-support') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="headset" class="w-5 transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Support</span>
        </a>

        <!-- profile -->
        <a href="{{ route('seller.profile') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017]  group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('seller.profile') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="user-round" class="w-5 transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Profile</span>
        </a>

        <!-- notification -->
        <a href="{{ route('seller-notification') }}"
            class="nav-link flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017] group transition-all duration-300 ease-out active:scale-[0.98]
            {{ Request::routeIs('seller-notification') ? 'bg-(--hover-color)' : '' }}">
            <i data-lucide="bell" class="w-5 transition-transform duration-300 group-hover:scale-110"></i>
            <span class="font-medium transition-all duration-300 group-hover:translate-x-1">Notification</span>
        </a>
    </nav>

    <div class=" px-4 pb-8 mt-auto border-t border-white/10 pt-4 space-y-4">
        <!-- Add New Product (shown as prominent call to action) -->
        <a href="{{ route('product-create') }}"
            class="flex items-center gap-4 px-4 py-3 rounded-xl bg-[#C65A3A]  hover:bg-[#B14E32] text-[#FFF7EF]  transition-all duration-300 active:scale-[0.98] group">
            <i data-lucide="circle-plus"
                class="w-5 text-[#FFF7EF] text-lg transition-transform duration-300 group-hover:rotate-90"></i>
            <span class="font-semibold group-hover:translate-x-1 transition">Add New Product</span>
        </a>

        <!-- Logout button section with proper spacing and hover effect (original design improved) -->
        <div class="navbar">
            <form method="POST" action="{{ route('seller.logout') }}">
                @csrf
                <button type="submit"
                    class="nav-link w-full flex items-center gap-4 px-4 py-3 rounded-xl hover:bg-[#D4A017] hover:text-[#3A2A1F] transition-all duration-300 active:scale-[0.98]">
                    <i data-lucide="log-out"
                        class="w-5 text-lg transition-transform duration-300 group-hover:scale-110"></i>
                    <span class="font-medium group-hover:translate-x-1 transition">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

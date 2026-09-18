<header class="sticky top-0 z-30 w-full bg-[#1E2A44] border-b border-(--primary-color)/15 shadow-sm backdrop-blur-md">
    <div class="flex items-center gap-3 w-full px-4 md:px-8 py-3 justify-between">

        <button id="menu-btn" class="md:hidden text-(--text-light) mr-2">
            <i data-lucide="menu" class="text-xl"></i>
        </button>

        <!-- Title -->
        <div class="flex-1 md:flex-none">
            <h1 class="text-xl md:text-2xl font-semibold text-[#FFF7EF] tracking-tight">
                {{ $title ?? 'Dashboard' }}
            </h1>
        </div>

        {{-- Right Icons --}}
        <div class="flex items-center gap-3">
            @php
                $sellerTypes = [
                    'order_placed',
                    'vendor_order_placed',
                    'vendor_payment_received',
                    'vendor_profile_updated',
                    'support_ticket_status',
                ];
                $hasUnreadSeller = Auth::user()
                    ? Auth::user()->appNotifications()->whereIn('type', $sellerTypes)->where('is_read', false)->exists()
                    : false;
            @endphp
            {{-- Notification Bell --}}
            <a href="{{ route('seller-notification') }}"
                class="relative p-3 text-(--text-color) cursor-pointer hover:scale-110 transition-transform">
                @if ($hasUnreadSeller)
                    <i data-lucide="bell" class="w-5 fill-current text-(--text-light)"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-[#1E2A44]"></span>
                @else
                    <i data-lucide="bell" class="w-5 text-(--text-light)"></i>
                @endif
            </a>

            {{-- Profile --}}
            <a href="{{ route('seller.profile') }}"
            class="flex items-center gap-3 pl-4 border-l border-white/10">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name ?? 'User' }}</p>
                </div>
                <div
                    class="w-9 h-9  text-[#1E2A44] rounded-2xl flex items-center justify-center font-semibold shadow overflow-hidden">

                    @if (Auth::user()->profile_pic)
                        <img src="{{ asset('storage/' . Auth::user()->profile_pic) }}" alt="Profile"
                            class="w-full h-full object-cover">
                    @else
                        <span
                            class="text-sm font-bold text-(--text-color)">{{ strtoupper(substr(Auth::user()->name ?? 'V', 0, 1)) }}</span>
                    @endif

                </div>

            </a>
        </div>
    </div>
</header>


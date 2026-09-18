<!-- Topbar -->
<header class="sticky top-0 z-30 bg-[#1E2A44] backdrop-blur-lg border-b border-white/10 shadow-sm">
    <div class="flex items-center justify-between px-4 md:px-8 py-3.5">

        <!-- Mobile Menu Button -->
        <button id="menu-btn" class="md:hidden text-[#FFF7EF] p-2 -ml-2">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <!-- Title -->
        <div class="flex-1 md:flex-none">
            <h1 class="text-xl md:text-2xl font-semibold text-[#FFF7EF] tracking-tight">
                {{ $title ?? 'Dashboard' }}
            </h1>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-2 md:gap-4">

            @php
                $allUserTypes = [
                    'order_confirmed',
                    'order_shipped',
                    'order_delivered',
                    'order_cancelled',
                    'return_approved',
                    'profile_updated',
                ];
                $hasUnreadUser = Auth::user()
                    ? Auth::user()
                        ->appNotifications()
                        ->whereIn('type', $allUserTypes)
                        ->where('is_read', false)
                        ->exists()
                    : false;
            @endphp
            <!-- Notification -->
            <a href="{{ route('user-notification') }}"
                class="relative p-3 text-(--text-color) cursor-pointer hover:scale-110 transition-transform">
                @if ($hasUnreadUser)
                    <i data-lucide="bell" class="w-5 fill-current text-(--text-light)"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-[#1E2A44]"></span>
                @else
                    <i data-lucide="bell" class="w-5 h-5 text-(--text-light)"></i>
                @endif
            </a>

            <!-- Profile -->
            <a href="{{ route('user-profile') }}"
            class="flex items-center gap-3 pl-4 border-l border-white/10">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name ?? 'User' }}</p>
                </div>
                <div
                    class="w-9 h-9 text-[#1E2A44] rounded-2xl flex items-center justify-center font-semibold shadow overflow-hidden">
                    @if (Auth::user()->profile_pic)
                        <img src="{{ asset('storage/' . Auth::user()->profile_pic) }}" alt="Profile"
                            class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    @endif
                </div>
            </a>
        </div>
    </div>
</header>

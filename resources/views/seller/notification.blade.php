<x-seller_layout title="Notification">
    <div class="space-y-10">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between items-start gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-(--text-color)">Notifications</h1>
                <p class="text-sm text-(--text-color)/70 mt-1">Stay updated with your orders and store activities.</p>
            </div>
            @if($notifications->isNotEmpty())
                <button onclick="markAllAsRead()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-(--text-dark) bg-(--text-light) border border-(--text-color)/20 rounded-2xl hover:border-(--secondary-color) transition-colors">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Mark all as read
                </button>
            @endif
        </div>

        {{-- Tabs --}}
        <div class="relative flex flex-nowrap bg-(--card-bg) rounded-3xl p-1 shadow-sm overflow-x-auto scrollbar-hide mb-8">
            @foreach(['all' => 'All', 'orders' => 'Orders', 'store' => 'Store'] as $key => $label)
                <button onclick="switchTab('{{ $key }}')" data-tab="{{ $key }}"
                    class="tab-button relative z-10 px-5 py-3 sm:px-6 sm:py-3.5 rounded-3xl font-medium text-sm transition-all duration-200 whitespace-nowrap
                        {{ $key === 'all' ? 'bg-(--secondary-color) text-white' : 'text-(--text-dark)' }}">
                    {{ $label }}
                    @if(($counts[$key] ?? 0) > 0)
                        <span id="badge-{{ $key }}" class="ml-1 text-xs bg-white/25 rounded-full px-1.5 py-0.5">{{ $counts[$key] }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Cards --}}
        <div class="space-y-4" id="notification-list">
            @forelse($notifications as $notification)
                @php
                    $d = json_decode($notification->message, true) ?? [];

                    $isOrder = in_array($notification->type, ['order_placed','order_confirmed','order_shipped','order_delivered','order_cancelled','return_requested','return_approved','vendor_order_placed']);
                    $tab     = $isOrder ? 'orders' : 'store';

                    $icon = match($notification->type) {
                        'order_placed'         => 'archive',
                        'vendor_order_placed'  => 'archive',
                        'order_confirmed'      => 'package-check',
                        'order_shipped'        => 'truck',
                        'order_delivered'      => 'circle-check',
                        'order_cancelled'      => 'x-circle',
                        'return_requested'     => 'undo-2',
                        'return_approved'      => 'refresh-ccw',
                        default                => 'bell',
                    };
                @endphp

                <div class="notification-card bg-(--card-bg) rounded-2xl shadow-sm border transition-all duration-200
                        {{ $notification->is_read ? 'border-(--text-color)/10 opacity-60' : 'border-(--secondary-color)/40' }}
                        {{ $isOrder ? 'cursor-pointer hover:border-(--secondary-color)' : '' }}"
                    data-id="{{ $notification->id }}"
                    data-tab="{{ $tab }}"
                    @if($isOrder) onclick="if(!event.target.closest('button') && !event.target.closest('a')) window.location.href='{{ route('order') }}'" @endif>

                    <div class="p-5">
                        {{-- Top row --}}
                        <div class="flex items-start justify-between gap-3 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-(--card-dark) flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="{{ $icon }}" class="w-4 h-4 text-(--secondary-color)"></i>
                                </div>
                                <div>
                                    <span class="font-semibold text-(--text-color)">{{ $notification->title }}</span>
                                    @if(!$notification->is_read)
                                        <span class="inline-block w-1.5 h-1.5 bg-(--secondary-color) rounded-full ml-1.5 align-middle"></span>
                                    @endif
                                    <p class="text-xs text-(--text-color)/50 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            @if(!$notification->is_read)
                                <button onclick="markAsRead({{ $notification->id }}, this)"
                                    class="text-xs text-(--text-color)/40 hover:text-(--secondary-color) transition-colors whitespace-nowrap flex-shrink-0">
                                    Mark read
                                </button>
                            @endif
                        </div>

                        @if(!empty($d) && isset($d['order_ref']))
                            {{-- Order detail rows — data pulled straight from the order at checkout --}}
                            <div class="rounded-xl bg-(--card-dark)/40 divide-y divide-(--text-color)/10 text-sm mb-4">

                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-(--text-color)/50">Order</span>
                                    <span class="font-semibold text-(--text-color)">{{ $d['order_ref'] }}</span>
                                </div>

                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-(--text-color)/50">Customer</span>
                                    <span class="font-medium text-(--text-color)">{{ $d['customer_name'] }}</span>
                                </div>

                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-(--text-color)/50">Payment</span>
                                    <span class="font-medium text-(--text-color)">{{ $d['payment_method'] }}</span>
                                </div>

                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-(--text-color)/50">Amount</span>
                                    <span class="font-semibold text-(--text-color)">{{ $d['amount'] }}</span>
                                </div>

                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-(--text-color)/50">Items</span>
                                    <span class="text-(--text-color)/80 text-right max-w-[60%] truncate">
                                        {{ $d['quantity'] }} × {{ $d['products'] }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between px-4 py-2.5">
                                    <span class="text-(--text-color)/50">Placed</span>
                                    <span class="text-(--text-color)/70">{{ $d['placed_at'] }}</span>
                                </div>

                            </div>

                            <a href="{{ route('order') }}"
                                class="inline-flex items-center gap-2 px-5 py-2 bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-xl text-sm font-medium transition-colors">
                                View Order
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                            </a>

                        @else
                            {{-- Plain text fallback for store notifications --}}
                            <p class="text-sm text-(--text-color)/80 mb-3">{{ $notification->message }}</p>
                        @endif
                    </div>
                </div>

            @empty
                <div class="text-center py-24 text-(--text-color)/30">
                    <i data-lucide="bell-off" class="w-12 h-12 mx-auto mb-4"></i>
                    <p class="text-lg font-medium">No notifications yet</p>
                    <p class="text-sm mt-1">You're all caught up!</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($notifications->hasPages())
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-(--text-dark)/50">
                    Showing <span class="font-medium text-(--text-dark)">{{ $notifications->firstItem() }}–{{ $notifications->lastItem() }}</span>
                    of <span class="font-medium text-(--text-dark)">{{ $notifications->total() }}</span>
                </p>
                <div class="flex items-center gap-2">
                    @if ($notifications->onFirstPage())
                        <button disabled class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-2xl opacity-40 cursor-not-allowed">
                            <i data-lucide="chevron-left" class="w-3 h-3"></i>
                        </button>
                    @else
                        <a href="{{ $notifications->previousPageUrl() }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white transition">
                            <i data-lucide="chevron-left" class="w-3 h-3"></i>
                        </a>
                    @endif

                    @foreach ($notifications->getUrlRange(max(1, $notifications->currentPage() - 2), min($notifications->lastPage(), $notifications->currentPage() + 2)) as $page => $url)
                        @if ($page == $notifications->currentPage())
                            <button class="w-10 h-10 bg-[#1F3D2E] text-white rounded-2xl font-medium text-sm">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white transition">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($notifications->hasMorePages())
                        <a href="{{ $notifications->nextPageUrl() }}" class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white transition">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    @else
                        <button disabled class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-2xl opacity-40 cursor-not-allowed">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab-button').forEach(btn => {
                const on = btn.dataset.tab === tab;
                btn.classList.toggle('bg-(--secondary-color)', on);
                btn.classList.toggle('text-white', on);
                btn.classList.toggle('text-(--text-dark)', !on);
            });
            document.querySelectorAll('.notification-card').forEach(card => {
                card.style.display = (tab === 'all' || card.dataset.tab === tab) ? '' : 'none';
            });
        }

        function markAsRead(id, btn) {
            fetch(`/seller-notification/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(() => {
                const card = btn.closest('.notification-card');
                const tab = card.dataset.tab;

                card.classList.add('opacity-60', 'border-(--text-color)/10');
                card.classList.remove('border-(--secondary-color)/40');
                card.querySelector('span.bg-\\(--secondary-color\\).rounded-full')?.remove();
                btn.remove();

                // Decrement badges
                ['all', tab].forEach(key => {
                    const badge = document.getElementById('badge-' + key);
                    if(badge) {
                        let count = parseInt(badge.innerText) - 1;
                        if(count <= 0) badge.remove();
                        else badge.innerText = count;
                    }
                });
            });
        }

        function markAllAsRead() {
            fetch('/seller-notification/read-all', {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(() => location.reload());
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
</x-seller_layout>

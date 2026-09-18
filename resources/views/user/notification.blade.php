<x-user-layout title="Notifications">
    <div class="space-y-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-(--text-color)">Notifications</h1>
                <p class="text-sm text-(--text-color)/70 mt-1">Stay updated with your orders, deliveries, and account.</p>
            </div>

            <div id="mark-all-container">
                @if ($unreadCount > 0)
                    <form method="POST" action="{{ route('user.notifications.markAllRead') }}" id="mark-all-form">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-(--text-dark) bg-(--text-light) border border-(--text-color)/20 rounded-2xl hover:bg-(--card-dark) transition">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Mark all as read
                            <span id="unread-badge" class="bg-(--secondary-color) text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if (session('success'))
            <script>
                (function() {
                    if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                })();
            </script>
        @endif

        <!-- Tabs -->
        @php
            $tabs = ['all' => 'All', 'orders' => 'Orders', 'deliveries' => 'Deliveries', 'account' => 'Account'];
            $active = $type ?? 'all';
        @endphp

        <div class="flex flex-wrap border-b border-(--secondary-color)/20">
            @foreach ($tabs as $key => $label)
                <a href="{{ route('user-notification', $key ? ['type' => $key] : []) }}" wire:navigate
                    class="flex-1 sm:flex-none px-4 sm:px-8 py-3 sm:py-4 text-sm font-semibold border-b-2 transition
                        {{ $active === $key ? 'text-(--secondary-color) border-(--secondary-color)' : 'text-(--text-color) border-transparent hover:text-(--secondary-color)' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Notification List -->
        <div class="space-y-4" id="notification-list">
            @forelse ($notifications as $notification)
                @php
                    $meta = $iconMap[$notification->type] ?? ['icon' => 'bell', 'bg' => 'bg-(--card-dark)', 'color' => 'text-(--text-color)'];
                @endphp

                <div id="notif-{{ $notification->id }}"
                    class="notification-card bg-(--card-bg) rounded-2xl p-6 shadow-sm border transition-all duration-300
                        {{ $notification->is_read
                            ? 'border-(--text-color)/10 opacity-60'
                            : 'border-(--secondary-color)/30 bg-orange-50/30' }}
                        hover:shadow-md flex gap-5"
                    data-id="{{ $notification->id }}"
                    data-read="{{ $notification->is_read ? '1' : '0' }}">

                    <!-- Icon -->
                    <div class="w-12 h-12 {{ $meta['bg'] }} rounded-2xl flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $meta['icon'] }}" class="w-5 h-5 {{ $meta['color'] }}"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-1">
                            <h3 class="font-semibold text-(--text-color) flex items-center gap-2">
                                {{ $notification->title }}
                                @if (!$notification->is_read)
                                    <span class="unread-dot w-2 h-2 bg-(--secondary-color) rounded-full shrink-0"></span>
                                @endif
                            </h3>
                            <span class="text-xs text-(--text-color)/60 shrink-0">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>

                        <p class="text-(--text-color)/80 mt-1 text-sm">{{ $notification->message }}</p>

                        <!-- Actions -->
                        <div class="flex items-center gap-3 mt-4">
                             @if (in_array($notification->type, ['order_placed','order_confirmed','order_shipped','order_delivered','order_cancelled']))
                                <a href="{{ route('User-orders') }}" wire:navigate class="px-4 py-2 bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-xl text-xs font-medium transition">
                                    View Orders
                                </a>
                            @elseif (in_array($notification->type, ['return_requested','return_approved']))
                                <a href="{{ route('User-orders') }}" wire:navigate class="px-4 py-2 bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-xl text-xs font-medium transition">
                                    View Returns
                                </a>
                            @endif

                            @if (!$notification->is_read)
                                <button onclick="markRead({{ $notification->id }}, this)"
                                    class="mark-read-btn text-xs text-(--text-color)/50 hover:text-(--secondary-color) transition">
                                    Mark as read
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-(--card-bg) rounded-2xl p-16 text-center border border-(--text-color)/10">
                    <i data-lucide="bell-off" class="w-12 h-12 mx-auto text-(--text-color)/30 mb-4"></i>
                    <p class="text-(--text-color)/60 font-medium">
                        No {{ $active !== 'all' ? $active : '' }} notifications yet.
                    </p>
                </div>
            @endforelse
        </div>

        <!-- Pagination (unchanged) -->
        @if ($notifications->hasPages())
            {{-- Your existing pagination code --}}
        @endif
    </div>

    <script>
        function markRead(id, btn) {
            const card = document.getElementById('notif-' + id);
            if (!card) return;

            fetch(`/user-notification/${id}/read`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    'Accept': 'application/json',
                }
            })
            .then(r => r.ok ? r.json() : Promise.reject())
            .then(() => {
                // Update card style like seller
                card.classList.remove('border-(--secondary-color)/30', 'bg-orange-50/30');
                card.classList.add('border-(--text-color)/10', 'opacity-60');

                // Remove unread dot
                card.querySelector('.unread-dot')?.remove();

                // Remove mark as read button
                btn?.remove();

                // Decrease unread count
                const badge = document.getElementById('unread-badge');
                if (badge) {
                    let count = parseInt(badge.textContent) - 1;
                    if (count <= 0) {
                        document.getElementById('mark-all-container').innerHTML = '';
                    } else {
                        badge.textContent = count;
                    }
                }
            })
            .catch(() => {
                alert('Something went wrong. Please try again.');
            });
        }

        // Optional: Handle Mark All as Read with better UX
        const markAllForm = document.getElementById('mark-all-form');
        if (markAllForm) {
            markAllForm.addEventListener('submit', function(e) {
                // You can add loading state here if you want
            });
        }

        (function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        })();
    </script>
</x-user-layout>

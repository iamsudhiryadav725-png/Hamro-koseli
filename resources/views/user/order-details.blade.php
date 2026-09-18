<x-user-layout title="Order Details">
    <div class="space-y-10">

        <a href="{{ route('User-orders') }}" wire:navigate
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-(--text-dark) bg-(--text-light) border border-(--text-color)/20 rounded-2xl">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Back to Orders
        </a>

        @if (session('success'))
            <script>
                (function() {
                    if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                })();
            </script>
        @endif
        @if (session('error'))
            <script>
                (function() {
                    if (window.showToast) window.showToast("{{ session('error') }}", 'error');
                })();
            </script>
        @endif

        @php
            $statusSteps = ['pending', 'confirmed', 'shipped', 'delivered'];
            $currentStep = array_search($order->status, $statusSteps);
            $isCancelled = $order->status === 'cancelled';

            $statusBadgeColors = [
                'pending' => 'bg-yellow-400/80',
                'confirmed' => 'bg-blue-400/80',
                'shipped' => 'bg-green-500/80',
                'delivered' => 'bg-green-500/80',
                'cancelled' => 'bg-red-400/80',
            ];
            $badgeColor = $statusBadgeColors[$order->status] ?? 'bg-gray-400/80';
        @endphp

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-(--text-color) inline-flex items-center">
                    Order Details
                    <span
                        class="ml-3 px-4 py-1 text-sm font-medium {{ $badgeColor }} text-white rounded-full capitalize">
                        {{ $order->status }}
                    </span>
                </h1>
                <div class="flex items-center gap-3 text-sm mt-1">
                    <span
                        class="font-semibold text-(--secondary-color)/70">#HK-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="text-(--text-color)/50">•</span>
                    <span class="text-(--text-dark)/50">Placed on {{ $order->created_at->format('M d, Y') }} at
                        {{ $order->created_at->format('h:i A') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Left Side -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Order Tracking -->
                <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                    <h2 class="text-xl font-semibold mb-6">Order Tracking</h2>

                    @if ($isCancelled)
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-5 h-5 text-red-500"></i>
                            </div>
                            <p class="text-red-700">This order has been cancelled.</p>
                        </div>
                    @else
                        @php
                            $steps = [
                                ['label' => 'Order Placed', 'icon' => 'shopping-bag', 'key' => 'pending'],
                                ['label' => 'Processing', 'icon' => 'package', 'key' => 'confirmed'],
                                ['label' => 'Shipped', 'icon' => 'truck', 'key' => 'shipped'],
                                ['label' => 'Delivered', 'icon' => 'house', 'key' => 'delivered'],
                            ];
                        @endphp

                        <div class="flex flex-col lg:flex-row lg:items-start">
                            @foreach ($steps as $i => $step)
                                @php $done = $currentStep !== false && $currentStep >= $i; @endphp

                                <div class="flex lg:flex-col items-center text-center">
                                    <div
                                        class="w-12 h-12 {{ $done ? 'bg-[#3b5e4c]' : 'bg-gray-100' }} rounded-full flex items-center justify-center">
                                        <i data-lucide="{{ $step['icon'] }}"
                                            class="w-5 h-5 {{ $done ? 'text-[#fafbfa]' : 'text-gray-400' }}"></i>
                                    </div>
                                    <div class="ml-4 lg:ml-0 lg:mt-2">
                                        <p class="text-sm font-medium {{ $done ? '' : 'text-gray-400' }}">
                                            {{ $step['label'] }}</p>
                                        @if ($done)
                                            @php
                                                $time = match ($step['key']) {
                                                    'pending' => $order->created_at,
                                                    default => $order->updated_at,
                                                };
                                            @endphp
                                            <p class="text-xs text-(--text-dark)/55">
                                                {{ $time->format('M d, Y') }}<br>
                                                {{ $time->format('h:i A') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                @if (!$loop->last)
                                    @php $lineColor = ($currentStep !== false && $currentStep > $i) ? 'bg-[#3b5e4c]' : 'bg-gray-300'; @endphp
                                    <div class="w-0.5 h-10 {{ $lineColor }} ml-6 my-2 lg:hidden"></div>
                                    <div class="hidden lg:block flex-1 h-0.5 {{ $lineColor }} mt-6 mx-4"></div>
                                @endif
                            @endforeach
                        </div>

                        @php
                            $statusMessages = [
                                'pending' => [
                                    'color' => 'yellow',
                                    'icon' => 'clock',
                                    'msg' => 'Your order has been placed and is awaiting confirmation.',
                                ],
                                'confirmed' => [
                                    'color' => 'blue',
                                    'icon' => 'package',
                                    'msg' => 'Your order has been confirmed and is being prepared.',
                                ],
                                'shipped' => [
                                    'color' => 'green',
                                    'icon' => 'truck',
                                    'msg' =>
                                        'Your order is on the way! It has been shipped and will be delivered soon.',
                                ],
                                'delivered' => [
                                    'color' => 'green',
                                    'icon' => 'check',
                                    'msg' => 'Your order has been delivered. Enjoy your purchase!',
                                ],
                            ];
                            $msg = $statusMessages[$order->status] ?? null;
                        @endphp

                        @if ($msg)
                            <div
                                class="mt-6 bg-{{ $msg['color'] }}-50 border border-{{ $msg['color'] }}-200 rounded-xl p-4 flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-{{ $msg['color'] }}-100 flex items-center justify-center">
                                    <i data-lucide="{{ $msg['icon'] }}"
                                        class="w-5 h-5 text-{{ $msg['color'] }}-600"></i>
                                </div>
                                <p class="text-{{ $msg['color'] }}-700">{{ $msg['msg'] }}</p>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Product Details -->
                <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                    <h2 class="text-xl font-semibold mb-4">Product Details</h2>
                    <div class="space-y-4">
                        @foreach ($order->orderItems as $item)
                            @php
                                $img = $item->product?->images->first()?->url ?? $item->product?->image;
                            @endphp
                            <div class="flex gap-4 pb-4 border-b border-(--text-color)/10 last:border-0 last:pb-0">
                                @if ($img)
                                    <img src="{{ asset('storage/' . $img) }}" alt="{{ $item->product?->name }}"
                                        class="w-20 h-20 object-cover rounded-xl shrink-0">
                                @else
                                    <div
                                        class="w-20 h-20 rounded-xl bg-(--card-dark) flex items-center justify-center shrink-0">
                                        <i data-lucide="package" class="w-7 h-7 text-(--text-color)/30"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="font-semibold text-lg">{{ $item->product?->name ?? 'Product' }}</h3>
                                    @if ($item->variant)
                                        <p class="text-sm text-gray-500 mt-0.5">{{ $item->variant->name ?? '' }}</p>
                                    @endif
                                    <p class="text-(--secondary-color) font-semibold mt-2">Rs.
                                        {{ number_format($item->price, 2) }}</p>
                                    <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-(--text-color) shrink-0">Rs.
                                    {{ number_format($item->subtotal, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Delivery Address -->
                @if ($order->shippingAddress)
                    <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <h2 class="font-semibold text-xl mb-4">Delivery Address</h2>
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center shrink-0">
                                <i data-lucide="map-pin" class="w-5 h-5 text-(--secondary-color)"></i>
                            </div>
                            <div>
                                <p class="font-medium">{{ $order->shippingAddress->full_name ?? $order->user->name }}
                                </p>
                                <p class="text-gray-600">{{ $order->shippingAddress->address_line1 ?? '' }}</p>
                                @if (!empty($order->shippingAddress->address_line2))
                                    <p class="text-gray-600">{{ $order->shippingAddress->address_line2 }}</p>
                                @endif
                                <p class="text-gray-600">
                                    {{ $order->shippingAddress->city ?? '' }}{{ !empty($order->shippingAddress->state) ? ', ' . $order->shippingAddress->state : '' }}
                                </p>
                                @if (!empty($order->shippingAddress->phone))
                                    <div class="flex items-center gap-2 mt-3 text-gray-600">
                                        <i data-lucide="phone" class="w-4 h-4 text-(--text-dark)"></i>
                                        <span>{{ $order->shippingAddress->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="space-y-6">

                <!-- Order Summary -->
                <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                    <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Item Total</span>
                            <span>Rs. {{ number_format($order->orderItems->sum('subtotal'), 2) }}</span>
                        </div>
                        @if ($order->discount > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>- Rs. {{ number_format($order->discount, 2) }}</span>
                            </div>
                        @endif
                        <hr>
                        <div class="flex justify-between font-semibold text-lg">
                            <span>Total</span>
                            <span class="text-(--secondary-color)">Rs.
                                {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                    <h2 class="text-lg font-semibold mb-4">Payment Details</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method</span>
                            <span class="uppercase">{{ $order->payment_method ?? 'N/A' }}</span>
                        </div>
                        @if ($order->payment)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Status</span>
                                @php $paid = $order->payment->status === 'completed'; @endphp
                                <span
                                    class="{{ $paid ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }} px-3 py-0.5 rounded-full text-xs font-medium capitalize">
                                    {{ $order->payment->status }} {{ $paid ? '✓' : '' }}
                                </span>
                            </div>
                            @if ($order->payment->transaction_id ?? $order->transaction_id)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Transaction ID</span>
                                    <span
                                        class="font-mono text-xs">{{ $order->payment->transaction_id ?? $order->transaction_id }}</span>
                                </div>
                            @endif
                            @if ($order->payment->paid_at ?? null)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Paid On</span>
                                    <span>{{ \Carbon\Carbon::parse($order->payment->paid_at)->format('M d, Y g:i A') }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Return / Cancel -->
                <!-- Return / Cancel -->
                @if ($order->status === 'pending')
                    <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-200">
                        <h2 class="text-lg font-semibold mb-3">Cancel Order</h2>
                        <p class="text-gray-600 text-sm mb-5">You can cancel this order only while it is pending.</p>

                        <button onclick="showCancelModal()"
                            class="w-full border border-red-400 text-red-500 hover:bg-red-50 py-3.5 rounded-xl font-medium transition flex items-center justify-center gap-2">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            Cancel Order
                        </button>
                    </div>
                @endif
            </div>
        </div>
        <!-- Cancel Confirmation Modal -->
        <div id="cancelModal"
            class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white dark:bg-(--card-bg) rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                    </div>
                </div>

                <h3 class="text-2xl font-semibold text-center mb-2">Cancel Order?</h3>
                <p class="text-center text-gray-600 dark:text-gray-400 mb-8">
                    Are you sure you want to cancel this order?<br>
                </p>

                <div class="flex gap-3">
                    <button onclick="hideCancelModal()"
                        class="flex-1 py-4 text-base font-medium border border-gray-300 dark:border-gray-600 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        No, Keep Order
                    </button>

                    <form id="cancelForm" method="POST" action="{{ route('order.cancel', $order->id) }}"
                        class="flex-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="w-full py-4 text-base font-medium bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-2xl transition flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                            Yes, Cancel Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
            document.getElementById('cancelModal').classList.add('flex');
        }

        function hideCancelModal() {
            const modal = document.getElementById('cancelModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('cancelModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideCancelModal();
            }
        });

        // Optional: Close with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('cancelModal');
                if (modal && !modal.classList.contains('hidden')) {
                    hideCancelModal();
                }
            }
        });
    </script>
</x-user-layout>

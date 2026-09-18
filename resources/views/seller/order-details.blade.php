<x-seller_layout title="Order Details" searchPlaceholder="Search by Order ID or Customer...">

    @php
        $customer = $order->user;
        $initials = $customer
            ? \Illuminate\Support\Str::upper(
                \Illuminate\Support\Str::substr(
                    collect(explode(' ', trim($customer->name)))
                        ->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))
                        ->join(''),
                    0,
                    2
                )
            )
            : '??';

        $payment = $order->payment;

        $orderStatusOptions = [
            'pending' => 'New',
            'confirmed' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
        ];

        $paymentStatusOptions = [
            'pending' => 'Pending',
            'completed' => 'Paid',
            'failed' => 'Failed',
            'refunded' => 'Refunded',
        ];

        $paymentStatus = $payment->status ?? 'pending';

        $paymentBadge = match ($paymentStatus) {
            'completed' => ['label' => 'Paid', 'icon' => 'check-circle-2', 'class' => 'bg-(--primary-color)/10 text-(--primary-color) ring-1 ring-(--primary-color)/25'],
            'refunded' => ['label' => 'Refunded', 'icon' => 'rotate-ccw', 'class' => 'bg-(--secondary-color)/10 text-(--secondary-color) ring-1 ring-(--secondary-color)/25'],
            'failed' => ['label' => 'Failed', 'icon' => 'x-circle', 'class' => 'bg-red-500/10 text-red-600 ring-1 ring-red-500/25'],
            default => ['label' => 'Pending', 'icon' => 'clock', 'class' => 'bg-(--hover-color)/15 text-(--hover-color) ring-1 ring-(--hover-color)/30'],
        };

        $orderStatusMeta = match ($order->status) {
            'pending' => ['label' => 'New', 'class' => 'bg-(--hover-color)/15 text-(--hover-color) ring-1 ring-(--hover-color)/30'],
            'confirmed' => ['label' => 'Processing', 'class' => 'bg-(--secondary-color)/10 text-(--secondary-color) ring-1 ring-(--secondary-color)/25'],
            'shipped' => ['label' => 'Shipped', 'class' => 'bg-(--primary-color)/10 text-(--primary-color) ring-1 ring-(--primary-color)/25'],
            'delivered' => ['label' => 'Delivered', 'class' => 'bg-(--primary-color)/10 text-(--primary-color) ring-1 ring-(--primary-color)/25'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-(--text-dark)/10 text-(--text-dark)/60 ring-1 ring-(--text-dark)/15'],
            default => ['label' => ucfirst($order->status), 'class' => 'bg-(--text-dark)/10 text-(--text-dark)/60 ring-1 ring-(--text-dark)/15'],
        };
    @endphp

    <div class="space-y-8">

        <!-- Back link -->
        <a href="{{ route('order') }}"
            class="inline-flex items-center gap-2 text-sm font-medium text-(--text-color)/70 hover:text-(--primary-color) transition-colors duration-200 group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform duration-200"></i>
            Back to Orders
        </a>

        <!-- Flash messages -->
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                });
            </script>
        @endif

        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.showToast) window.showToast("{{ session('error') }}", 'error');
                });
            </script>
        @endif

        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-(--text-dark)">Order #HK-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-sm text-(--text-color)/60 mt-1.5 flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    Placed on {{ $order->created_at?->format('F j, Y \a\t g:i A') }}
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold {{ $orderStatusMeta['class'] }}">
                    <i data-lucide="package" class="w-3.5 h-3.5"></i>
                    {{ $orderStatusMeta['label'] }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-xs font-semibold {{ $paymentBadge['class'] }}">
                    <i data-lucide="{{ $paymentBadge['icon'] }}" class="w-3.5 h-3.5"></i>
                    {{ $paymentBadge['label'] }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            <!-- Payment & Status card -->
            <div class="xl:col-span-5 bg-(--card-bg) rounded-3xl shadow-sm hover:shadow-md border border-(--text-color)/10 p-6 lg:p-8 transition-all duration-300">
                <h2 class="text-lg font-serif font-semibold mb-6 flex items-center gap-2 text-(--text-dark)">
                    <span class="w-9 h-9 rounded-xl bg-(--primary-color)/10 flex items-center justify-center">
                        <i data-lucide="credit-card" class="w-5 h-5 text-(--primary-color)"></i>
                    </span>
                    Payment &amp; Status
                </h2>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Order status -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-(--text-color)/50 mb-2">Order Status</label>
                            <form id="orderStatusForm"
                                action="{{ route('order-details.order-status', ['order' => $order->id]) }}"
                                method="POST">
                                @csrf
                                <div class="relative">
                                    <select id="orderStatus" name="order_status"
                                        onchange="document.getElementById('orderStatusForm').submit()"
                                        class="w-full bg-(--bg-color) border border-(--text-color)/15 focus:outline-none focus:ring-2 focus:ring-(--primary-color)/40 focus:border-(--primary-color) rounded-2xl pl-5 pr-11 py-3.5 text-sm font-medium text-(--text-dark) appearance-none transition-all cursor-pointer">
                                        @foreach ($orderStatusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-(--text-color)/50">
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Payment status -->
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wide text-(--text-color)/50 mb-2">Payment Status</label>
                            <form id="paymentStatusForm"
                                action="{{ route('order-details.payment-status', ['order' => $order->id]) }}"
                                method="POST">
                                @csrf
                                <div class="relative">
                                    <select id="paymentStatus" name="payment_status"
                                        @if (! $payment) disabled title="No payment record found for this order." @endif
                                        onchange="document.getElementById('paymentStatusForm').submit()"
                                        class="w-full bg-(--bg-color) border border-(--text-color)/15 focus:outline-none focus:ring-2 focus:ring-(--primary-color)/40 focus:border-(--primary-color) rounded-2xl pl-5 pr-11 py-3.5 text-sm font-medium text-(--text-dark) appearance-none transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed">
                                        @foreach ($paymentStatusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($paymentStatus === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-(--text-color)/50">
                                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Status Helpers -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            @if ($order->status === 'pending')
                                <p class="text-(--hover-color) flex items-start gap-1.5">
                                    <i data-lucide="lightbulb" class="w-3.5 h-3.5 mt-0.5 shrink-0 text-(--hover-color)"></i>
                                    New order waiting for processing confirmation.
                                </p>
                            @elseif ($order->status === 'confirmed')
                                <p class="text-(--secondary-color) flex items-start gap-1.5">
                                    <i data-lucide="info" class="w-3.5 h-3.5 mt-0.5 shrink-0"></i>
                                    Order confirmed and currently processing.
                                </p>
                            @elseif ($order->status === 'shipped')
                                <p class="text-(--primary-color) flex items-start gap-1.5">
                                    <i data-lucide="truck" class="w-3.5 h-3.5 mt-0.5 shrink-0"></i>
                                    Order shipped. On the way to delivery address.
                                </p>
                            @elseif ($order->status === 'delivered')
                                <p class="text-(--primary-color) flex items-start gap-1.5">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 mt-0.5 shrink-0"></i>
                                    Delivered. Transaction finished successfully.
                                </p>
                            @elseif ($order->status === 'cancelled')
                                <p class="text-red-500 flex items-start gap-1.5">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5 mt-0.5 shrink-0"></i>
                                    Order has been cancelled.
                                </p>
                            @endif
                        </div>
                        <div>
                            @if ($paymentStatus === 'pending' && $payment)
                                <p class="text-(--text-color)/60 flex items-start gap-1.5">
                                    <i data-lucide="lightbulb" class="w-3.5 h-3.5 mt-0.5 shrink-0 text-(--hover-color)"></i>
                                    Received payment? Switch to "Paid" to confirm it.
                                </p>
                            @elseif ($paymentStatus === 'completed')
                                <p class="text-(--primary-color) flex items-center gap-1.5">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    Payment confirmed — order is fully paid.
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Payment meta -->
                    <div class="grid grid-cols-2 gap-5 text-sm pt-2 border-t border-(--text-color)/10">
                        <div class="pt-5">
                            <p class="text-(--text-color)/50 text-xs uppercase tracking-wide">Transaction ID</p>
                            <p class="font-semibold mt-1.5 break-all text-(--text-dark)">{{ $payment?->transaction_id ?? $order->transaction_id ?? '—' }}</p>
                        </div>
                        <div class="pt-5">
                            <p class="text-(--text-color)/50 text-xs uppercase tracking-wide">Payment Date</p>
                            <p class="font-semibold mt-1.5 text-(--text-dark)">
                                @if ($payment?->paid_at)
                                    {{ $payment->paid_at->format('M j, Y') }} · {{ $payment->paid_at->format('g:i A') }}
                                @else
                                    Not paid yet
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-(--text-color)/50 text-xs uppercase tracking-wide">Payment Method</p>
                            <p class="font-semibold mt-1.5 text-(--text-dark)">{{ ucfirst($payment?->gateway ?? $order->payment_method ?? '—') }}</p>
                        </div>
                        <div>
                            <p class="text-(--text-color)/50 text-xs uppercase tracking-wide">Reference ID</p>
                            <p class="font-semibold mt-1.5 break-all text-(--text-dark)">{{ $payment?->reference_id ?? '—' }}</p>
                        </div>
                    </div>

                    <div class="bg-(--brand-dark,#1F3D2E) rounded-2xl p-5 text-sm flex gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-(--text-light) mt-0.5 shrink-0"></i>
                        <p class="text-(--text-light)/90">Both order status and payment status update the moment you change them — no need to click Save.</p>
                    </div>
                </div>
            </div>

            <!-- Customer & Shipping -->
            <div class="xl:col-span-7 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="bg-(--card-bg) rounded-3xl shadow-sm hover:shadow-md border border-(--text-color)/10 p-6 lg:p-8 transition-all duration-200">
                    <h2 class="text-lg font-serif font-semibold mb-6 flex items-center gap-2 text-(--text-dark)">
                        <span class="w-9 h-9 rounded-xl bg-(--secondary-color)/10 flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-(--secondary-color)"></i>
                        </span>
                        Customer
                    </h2>
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 bg-(--primary-color)/10 text-(--primary-color) rounded-2xl flex items-center justify-center text-base font-bold shrink-0">
                            {{ $initials }}
                        </div>
                        <div class="space-y-3.5 min-w-0 flex-1">
                            <p class="text-base font-semibold text-(--text-dark)">{{ $customer->name ?? 'Unknown Customer' }}</p>
                            <div class="space-y-2.5 text-sm">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i data-lucide="mail" class="w-4 h-4 text-(--text-color)/40 shrink-0"></i>
                                    @if ($customer?->email)
                                        <a href="mailto:{{ $customer->email }}"
                                            class="hover:text-(--primary-color) text-(--text-color) transition break-all">{{ $customer->email }}</a>
                                    @else
                                        <span class="text-(--text-color)/50">—</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i data-lucide="phone" class="w-4 h-4 text-(--text-color)/40 shrink-0"></i>
                                    @if ($customer?->phone)
                                        <a href="tel:{{ $customer->phone }}" class="hover:text-(--primary-color) text-(--text-color) transition break-all">{{ $customer->phone }}</a>
                                    @else
                                        <span class="text-(--text-color)/50">—</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-(--text-color)/40 shrink-0"></i>
                                    <span class="break-words text-(--text-color)">{{ $order->shippingAddress?->city ?? '—' }}{{ $order->shippingAddress?->country ? ', '.$order->shippingAddress->country : '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-(--card-bg) rounded-3xl shadow-sm hover:shadow-md border border-(--text-color)/10 p-6 lg:p-8 transition-all duration-200">
                    <h2 class="text-lg font-serif font-semibold mb-6 flex items-center gap-2 text-(--text-dark)">
                        <span class="w-9 h-9 rounded-xl bg-(--hover-color)/15 flex items-center justify-center">
                            <i data-lucide="truck" class="w-5 h-5 text-(--hover-color)"></i>
                        </span>
                        Shipping Address
                    </h2>
                    @if ($order->shippingAddress)
                        <div class="space-y-1.5 text-sm leading-relaxed text-(--text-color)">
                            <p>{{ $order->shippingAddress->address }}</p>
                            <p>{{ $order->shippingAddress->city }}, {{ $order->shippingAddress->province }}</p>
                            <p>{{ $order->shippingAddress->country }}</p>
                            <p class="pt-3 font-semibold text-(--text-dark) flex items-center gap-2">
                                <i data-lucide="phone" class="w-4 h-4 text-(--text-color)/40"></i>
                                {{ $order->shippingAddress->phone }}
                            </p>
                        </div>
                    @else
                        <p class="text-(--text-color)/50 text-sm">No shipping address on file for this order.</p>
                    @endif
                </div>
            </div>

            <!-- Order Items -->
            <div class="xl:col-span-12 bg-(--card-bg) rounded-3xl shadow-sm hover:shadow-md border border-(--text-color)/10 overflow-hidden transition-all duration-300">
                <div class="px-6 lg:px-8 py-5 border-b border-(--text-color)/10 bg-(--card-dark) flex items-center gap-2">
                    <i data-lucide="shopping-bag" class="w-5 h-5 text-(--primary-color)"></i>
                    <h2 class="text-lg font-serif font-semibold text-(--text-dark)">Order Items</h2>
                    <span class="ml-auto text-xs font-medium text-(--text-color)/50">{{ $items->count() }} item{{ $items->count() === 1 ? '' : 's' }}</span>
                </div>
                <div class="divide-y divide-(--text-color)/10">
                    @forelse ($items as $item)
                        <div class="p-6 lg:p-8 flex flex-col sm:flex-row sm:items-center gap-5">
                           <img
                                src="{{ $item->product?->primaryImageUrl() ?? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PC9zdmc+' }}"
                                class="w-20 h-20 object-cover rounded-2xl bg-(--card-dark)"
                                alt="{{ $item->product?->name ?? 'product' }}"
                            >
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-base text-(--text-dark)">{{ $item->product?->name ?? 'Product removed' }}</p>
                                <p class="text-(--text-color)/60 text-sm mt-1">
                                    SKU: {{ $item->variant?->sku ?? $item->product?->sku ?? '—' }}
                                    @if ($item->variant && ($item->variant->size || $item->variant->color))
                                        &nbsp;•&nbsp;{{ collect([$item->variant->size, $item->variant->color])->filter()->join(' / ') }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right sm:text-center sm:w-32">
                                <p class="font-semibold text-(--text-dark)">Rs. {{ number_format($item->price, 2) }}</p>
                                <p class="text-sm text-(--text-color)/60">Qty: {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right font-bold text-lg sm:w-32 text-(--primary-color)">Rs. {{ number_format($item->subtotal, 2) }}</div>
                        </div>
                    @empty
                        <div class="p-6 lg:p-8 text-center text-(--text-color)/50">No items found for this order.</div>
                    @endforelse
                </div>
            </div>

            <!-- Totals -->
            <div class="xl:col-span-5">
                <div class="bg-(--card-bg) rounded-3xl shadow-sm hover:shadow-md border border-(--text-color)/10 p-6 lg:p-8">
                    <h2 class="text-lg font-serif font-semibold mb-6 flex items-center gap-2 text-(--text-dark)">
                        <span class="w-9 h-9 rounded-xl bg-(--primary-color)/10 flex items-center justify-center">
                            <i data-lucide="receipt" class="w-5 h-5 text-(--primary-color)"></i>
                        </span>
                        Order Totals
                    </h2>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between">
                            <span class="text-(--text-color)/60">Items Subtotal</span>
                            <span class="font-medium text-(--text-dark)">Rs. {{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-(--text-color)/60">Discount</span>
                            <span class="text-(--primary-color) font-medium">- Rs. {{ number_format($order->discount, 2) }}</span>
                        </div>
                        <div class="h-px bg-(--text-color)/15 my-2"></div>
                        <div class="flex justify-between text-lg font-bold text-(--text-dark)">
                            <span>Order Total</span>
                            <span>Rs. {{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <p class="text-xs text-(--text-color)/45 pt-1">Order Total reflects the full order as paid by the
                            customer; Items Subtotal reflects only the items in this order.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-seller_layout>

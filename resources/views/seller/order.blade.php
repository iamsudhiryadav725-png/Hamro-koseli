<x-seller_layout title="Order Management" searchPlaceholder="Search by Order ID or Customer...">
    <div class="space-y-10">
        <!-- Header Section with fade-in -->
        <div class="mb-6 animate-fadeIn">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-(--text-color)">Manage Orders</h1>
                    <p class="text-sm text-(--text-color)/70 mt-1">Review and update processing for your customer
                        transactions.</p>
                </div>

                <!-- Action Buttons with hover effects -->
                <div class="flex flex-wrap gap-3">
                    <button onclick="bulkPrint()"
                        class="group flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-(--secondary-color) text-(--text-light)/95 rounded-2xl text-sm font-medium hover:bg-[#B94E31] hover:shadow-lg active:scale-95 transition-all duration-200 shadow-md">
                        <i data-lucide="printer"
                            class=" w-5 h-5 group-hover:-rotate-3 transition-transform duration-200"></i>
                        <span>Bulk Print</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div
            class="relative flex flex-nowrap bg-(--card-bg) rounded-3xl p-1 shadow-sm overflow-x-auto scrollbar-hide mb-8 animate-slideUp">
            @php
                $tabs = [
                    'all' => 'All Orders',
                    'new' => 'New',
                    'paid' => 'Paid',
                    'pending_payment' => 'Pending',
                    'cancelled' => 'Cancelled',
                ];
            @endphp
            @foreach ($tabs as $key => $label)
                <a href="{{ route('order', array_filter(['status' => $key === 'all' ? null : $key, 'search' => request('search')])) }}"
                    class="tab-btn relative z-10 px-5 py-3 sm:px-6 sm:py-3.5 rounded-3xl font-medium text-sm transition-all duration-200 whitespace-nowrap {{ $activeTab === $key ? 'bg-(--secondary-color) text-(--text-light)' : 'text-(--text-dark)' }}">
                    {{ $label }} ({{ number_format($counts[$key]) }})
                </a>
            @endforeach
        </div>

        <!-- Table -->
        <div
            class="bg-(--card-bg) rounded-2xl shadow-md overflow-hidden border border-(--text-color)/20 mb-8 transition-all duration-300 hover:shadow-lg">
            <div class="responsive-table-wrapper overflow-x-auto">
                <table class="w-full md:min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-(--card-dark)">
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Order ID</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Customer Name</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Date</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Payment</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Order Status</th>
                            <th
                                class="text-right py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--text-color)/10 text-sm" id="ordersTableBody">
                        @forelse ($orderItems as $item)
                            @php
                                $customer = $item->order?->user;
                                $initials = $customer
                                    ? collect(explode(' ', trim($customer->name)))
                                        ->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))
                                        ->join('')
                                    : '??';
                                $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($initials, 0, 2));

                                $paymentStatus = $item->order?->payment?->status ?? 'pending';
                                $paymentBadge = match ($paymentStatus) {
                                    'completed' => ['label' => 'Paid', 'class' => 'bg-(--card-dark) text-(--primary-color)/85'],
                                    'refunded' => ['label' => 'Refunded', 'class' => 'bg-(--secondary-color)/20 text-(--secondary-color)'],
                                    'failed' => ['label' => 'Failed', 'class' => 'bg-(--secondary-color)/20 text-(--secondary-color)'],
                                    default => ['label' => 'Pending', 'class' => 'bg-(--hover-color)/50 text-(--secondary-color)'],
                                };

                                $statusMeta = match ($item->status) {
                                    'pending' => ['label' => 'New', 'dot' => 'bg-(--hover-color)'],
                                    'confirmed' => ['label' => 'Processing', 'dot' => 'bg-(--secondary-color)'],
                                    'shipped' => ['label' => 'Shipped', 'dot' => 'bg-(--primary-color)'],
                                    'delivered' => ['label' => 'Delivered', 'dot' => 'bg-(--primary-color)'],
                                    'cancelled' => ['label' => 'Cancelled', 'dot' => 'bg-(--text-dark)/40'],
                                    'returned' => ['label' => 'Returned', 'dot' => 'bg-(--text-dark)/40'],
                                    default => ['label' => ucfirst($item->status), 'dot' => 'bg-(--text-dark)/40'],
                                };
                            @endphp
                            <tr class="hover:bg-(--card-dark)/10 transition-all duration-200 cursor-pointer">
                                <td
                                    class="px-4 md:px-6 lg:px-8 py-5 text-(--text-color) text-sm font-medium transition-colors">
                                    #HK-{{ str_pad($item->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 md:px-6 lg:px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-12 h-8 sm:w-13 sm:h-9 bg-(--card-dark) rounded-full flex items-center justify-center font-semibold text-xs sm:text-sm transition-all duration-200">
                                            {{ $initials }}</div>
                                        <div class="font-medium text-(--text-color)">
                                            {{ $customer->name ?? 'Unknown Customer' }}</div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-(--text-color) text-sm">
                                    {{ $item->created_at?->format('M j, Y') }}</td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 font-semibold text-(--text-color)">
                                    Rs. {{ number_format($item->subtotal, 2) }}</td>
                                <td class="px-4 md:px-6 lg:px-8 py-5">
                                    <span
                                        class="inline-block px-3 py-1.5 text-xs font-medium {{ $paymentBadge['class'] }} rounded-full">{{ $paymentBadge['label'] }}</span>
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 {{ $statusMeta['dot'] }} rounded-full"></span>
                                        <span class="font-medium text-(--text-color)">{{ $statusMeta['label'] }}</span>
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-right">
                                    <a href="{{ route('order-details', ['order' => $item->order_id, 'item' => $item->id]) }}"
                                        class="text-(--secondary-color) hover:text-(--hover-color) font-medium flex items-center gap-1 ml-auto text-sm transition-all duration-200 group">
                                        <span class="hover:underline">View Details</span>
                                        <i data-lucide="arrow-right" class="w-6 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 md:px-6 lg:px-8 py-16 text-center text-(--text-color)/60">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="package-search" class="w-10 h-10 text-(--text-color)/30"></i>
                                        <p class="font-medium">No orders found</p>
                                        <p class="text-sm">
                                            @if (request('search'))
                                                No results match "{{ request('search') }}". Try a different search.
                                            @else
                                                Orders for this filter will show up here once customers start
                                                buying.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if ($orderItems->total() > 0)
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 animate-slideUp">
                <p class="text-sm text-(--text-dark)/50">
                    Showing <span class="font-medium text-(--text-dark)">{{ $orderItems->firstItem() }}–{{ $orderItems->lastItem() }}</span>
                    of <span class="font-medium text-(--text-dark)">{{ number_format($orderItems->total()) }}</span> orders
                </p>
                <div class="flex items-center gap-2 flex-wrap justify-center">
                    <a href="{{ $orderItems->previousPageUrl() ?? '#' }}"
                        class="pagination-btn w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white hover:border-[#1F3D2E] active:scale-95 transition-all duration-200 text-gray-700 {{ $orderItems->onFirstPage() ? 'pointer-events-none opacity-40' : '' }}">
                        <i data-lucide="chevron-left" class="w-3 h-3"></i>
                    </a>

                    @foreach ($orderItems->getUrlRange(max(1, $orderItems->currentPage() - 2), min($orderItems->lastPage(), $orderItems->currentPage() + 2)) as $page => $url)
                        <a href="{{ $url }}"
                            class="pagination-btn w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-2xl font-medium text-sm transition-all duration-200 {{ $page === $orderItems->currentPage() ? 'bg-[#1F3D2E] text-white hover:bg-[#2a5040] hover:scale-105' : 'border border-gray-300 hover:bg-[#1F3D2E] hover:text-white hover:border-[#1F3D2E] active:scale-95 text-gray-700' }}">{{ $page }}</a>
                    @endforeach

                    <a href="{{ $orderItems->nextPageUrl() ?? '#' }}"
                        class="pagination-btn w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white hover:border-[#1F3D2E] active:scale-95 transition-all duration-200 text-gray-700 {{ ! $orderItems->hasMorePages() ? 'pointer-events-none opacity-40' : '' }}">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Wires the shared topbar search box to this page's search query param.
        window.sellerSearchHandler = function(value) {
            const url = new URL(window.location.href);
            if (value) {
                url.searchParams.set('search', value);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        };

        function exportCSV() {
            const params = new URLSearchParams(window.location.search);
            window.location.href = "{{ route('order') }}?" + params.toString() + (params.toString() ? '&' : '') + 'export=csv';
        }

        function bulkPrint() {
            window.print();
        }
    </script>

    <style>
        .tab-btn {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .responsive-table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</x-seller_layout>

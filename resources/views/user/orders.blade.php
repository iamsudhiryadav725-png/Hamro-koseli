<x-user-layout title="Orders">
    <div class="space-y-10">
        <!-- Header -->
        <div class="mb-6 animate-fadeIn">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-(--text-color)">My Orders</h1>
                    <p class="text-sm text-(--text-color)/70 mt-1">Track and manage your thoughtful gifts in one place.</p>
                </div>
            </div>
        </div>

        <!-- Status Tabs -->
        @php
            $tabs = [
                'all'       => 'All Orders',
                'pending'   => 'Pending',
                'confirmed' => 'Confirmed',
                'shipped'   => 'Shipped',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled',
            ];
            $active = $status ?? 'all';
        @endphp

        <div class="relative flex flex-nowrap bg-(--card-bg) rounded-3xl p-1 shadow-sm overflow-x-auto scrollbar-hide mb-8 animate-slideUp">
            @foreach ($tabs as $key => $label)
                @if ($counts[$key] > 0 || $key === 'all')
                    <a href="{{ route('User-orders', ['status' => $key === 'all' ? null : $key]) }}" wire:navigate
                        class="relative z-10 px-5 py-3 sm:px-6 sm:py-3.5 rounded-3xl font-medium text-sm transition-all duration-200 whitespace-nowrap
                            {{ $active === $key ? 'bg-(--secondary-color) text-(--text-light)' : 'text-(--text-dark) hover:bg-(--card-dark)' }}">
                        {{ $label }}
                        <span class="ml-1 text-xs opacity-70">({{ $counts[$key] }})</span>
                    </a>
                @endif
            @endforeach
        </div>

        <!-- Orders Table -->
        <div class="bg-(--card-bg) rounded-2xl shadow-md overflow-hidden border border-(--text-color)/20 mb-8 transition-all duration-300 hover:shadow-lg">
            <div class="responsive-table-wrapper overflow-x-auto">
                <table class="w-full md:min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-(--card-dark)">
                            <th class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">Order ID</th>
                            <th class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">Product</th>
                            <th class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">Date</th>
                            <th class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">QTY</th>
                            <th class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">Total</th>
                            <th class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">Status</th>
                            <th class="text-right py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--text-color)/10 text-sm">
                        @forelse ($orders as $order)
                            @php
                                $firstItem    = $order->orderItems->first();
                                $product      = $firstItem?->product;
                                $productImage = $product?->images->first()?->url ?? $product?->image;
                                $totalQty     = $order->orderItems->sum('quantity');
                                $extraCount   = $order->orderItems->count() - 1;

                                $statusColors = [
                                    'pending'   => 'bg-yellow-400',
                                    'confirmed' => 'bg-blue-400',
                                    'shipped'   => 'bg-(--primary-color)',
                                    'delivered' => 'bg-green-500',
                                    'cancelled' => 'bg-(--text-dark)/40',
                                ];
                                $dot = $statusColors[$order->status] ?? 'bg-gray-400';
                            @endphp
                            <tr class="hover:bg-(--card-dark)/10 transition-all duration-200 cursor-pointer">
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-(--text-color) text-sm font-medium">
                                    #HK-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5">
                                    <div class="flex items-center gap-3">
                                        @if ($productImage)
                                            <img src="{{ asset('storage/' . $productImage) }}"
                                                alt="{{ $product?->name }}"
                                                class="w-10 h-10 object-cover rounded-xl">
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-(--card-dark) flex items-center justify-center">
                                                <i data-lucide="package" class="w-5 h-5 text-(--text-color)/40"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-(--text-color) line-clamp-1">
                                                {{ $product?->name ?? 'Product' }}
                                            </p>
                                            @if ($extraCount > 0)
                                                <p class="text-xs text-(--text-color)/50">+{{ $extraCount }} more item{{ $extraCount > 1 ? 's' : '' }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-(--text-color) text-sm">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 font-semibold text-(--text-color)">
                                    {{ $totalQty }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 font-semibold text-(--text-color)">
                                    Rs. {{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 {{ $dot }} rounded-full"></span>
                                        <span class="font-medium text-(--text-color) capitalize">{{ $order->status }}</span>
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-right">
                                    <a href="{{ route('order-detail', ['order' => $order->id]) }}" wire:navigate
                                        class="text-(--secondary-color) hover:text-(--hover-color) font-medium flex items-center justify-end gap-1 text-sm transition-all duration-200 group">
                                        <span class="hover:underline">View Details</span>
                                        <i data-lucide="arrow-right" class="w-6 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-16 text-center text-(--text-color)/60">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="inbox" class="w-10 h-10 opacity-40"></i>
                                        <p class="text-sm font-medium">No orders found{{ $active !== 'all' ? ' with status "' . $active . '"' : '' }}.</p>
                                        <a href="{{ route('home') }}" wire:navigate class="text-(--secondary-color) hover:underline text-sm">Start shopping</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if ($orders->hasPages())
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-8 animate-slideUp">
                <p class="text-sm text-(--text-dark)/50">
                    Showing <span class="font-medium text-(--text-dark)">{{ $orders->firstItem() }}–{{ $orders->lastItem() }}</span>
                    of <span class="font-medium text-(--text-dark)">{{ $orders->total() }}</span> orders
                </p>
                <div class="flex items-center gap-2 flex-wrap justify-center">
                    @if ($orders->onFirstPage())
                        <button disabled class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl opacity-40 cursor-not-allowed">
                            <i data-lucide="chevron-left" class="w-3 h-3"></i>
                        </button>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" wire:navigate class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white hover:border-[#1F3D2E] transition-all duration-200">
                            <i data-lucide="chevron-left" class="w-3 h-3"></i>
                        </a>
                    @endif

                    @foreach ($orders->getUrlRange(max(1, $orders->currentPage() - 2), min($orders->lastPage(), $orders->currentPage() + 2)) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <button class="w-9 h-9 sm:w-10 sm:h-10 bg-[#1F3D2E] text-white rounded-2xl font-medium text-sm">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" wire:navigate class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white hover:border-[#1F3D2E] transition-all duration-200">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" wire:navigate class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl hover:bg-[#1F3D2E] hover:text-white hover:border-[#1F3D2E] transition-all duration-200">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    @else
                        <button disabled class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center border border-gray-300 rounded-2xl opacity-40 cursor-not-allowed">
                            <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .responsive-table-wrapper { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    </style>
</x-user-layout>
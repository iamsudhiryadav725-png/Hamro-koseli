<x-user-layout>
    <div class="space-y-10">

        <!-- Greeting -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-(--text-color) flex items-center gap-3">
                Welcome back, {{ $user->name ?? 'User' }}!
            </h1>
            <p class="text-sm text-(--text-color) mt-1">Manage your orders and account in one place.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- Total Orders -->
            <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 border border-(--text-color)/20">
                <div class="flex items-center justify-between">
                    <div class="bg-(--primary-color)/10 p-3 rounded-xl">
                        <i data-lucide="shopping-cart" class="text-[#0A1410]"></i>
                    </div>
                    <span class="text-xs font-medium px-3 py-1 bg-(--primary-color)/20 text-[#0A1410] rounded-full">Total</span>
                </div>
                <div class="mt-6">
                    <p class="text-3xl font-extrabold text-(--text-dark) font-sans!">{{ $totalOrders }}</p>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest mt-2">Total Orders</p>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 border border-(--text-color)/20">
                <div class="flex items-center justify-between">
                    <div class="bg-(--hover-color)/10 p-3 rounded-xl">
                        <i data-lucide="clock" class="text-(--hover-color)"></i>
                    </div>
                    <span class="text-xs font-medium px-3 py-1 bg-(--hover-color)/20 text-amber-700 rounded-full">Pending</span>
                </div>
                <div class="mt-6">
                    <p class="text-3xl font-extrabold text-(--text-dark) font-sans!">{{ $pendingOrders }}</p>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest mt-2">Pending Orders</p>
                </div>
            </div>

            <!-- Delivered Orders -->
            <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 border border-(--text-color)/20">
                <div class="flex items-center justify-between">
                    <div class="bg-[#E8EEEB] p-3 rounded-xl">
                        <i data-lucide="check" class="text-[#1F3D2E]"></i>
                    </div>
                    <span class="text-xs font-medium px-3 py-1 bg-[#E8EEEB] text-[#1F3D2E] rounded-full">Delivered</span>
                </div>
                <div class="mt-6">
                    <p class="text-3xl font-extrabold text-(--text-dark) font-sans!">{{ $deliveredOrders }}</p>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest mt-2">Delivered Orders</p>
                </div>
            </div>

            <!-- Cancelled Orders -->
            <div class="bg-(--card-bg) rounded-2xl p-6 shadow-sm hover:shadow-lg transition-all duration-300 border border-(--text-color)/20">
                <div class="flex items-center justify-between">
                    <div class="bg-(--secondary-color)/10 p-3 rounded-xl">
                        <i data-lucide="square-slash" class="text-(--secondary-color)"></i>
                    </div>
                    <span class="text-xs font-medium px-3 py-1 bg-(--secondary-color)/20 text-rose-800 rounded-full">Canceled</span>
                </div>
                <div class="mt-6">
                    <p class="text-3xl font-extrabold text-(--text-dark) font-sans!">{{ $canceledOrders }}</p>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest mt-2">Canceled Orders</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Recent Orders -->
            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-semibold text-(--text-color)">Recent Orders</h3>
                    <a href="{{ route('User-orders') }}" wire:navigate class="text-sm text-center text-(--secondary-color) flex items-center gap-1.5 transition">
                        <span class="hover:underline">View All Orders</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="space-y-4 bg-(--card-bg) rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                    @forelse($recentOrders as $order)
                        <div class="group flex items-center gap-4 p-4 hover:bg-(--card-dark)/10 rounded-2xl transition">
                            @php
                                $firstItem = $order->orderItems->first();
                                $productImage = $firstItem && $firstItem->product ? $firstItem->product->primaryImageUrl() : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTAwIiBoZWlnaHQ9IjEwMCIgdmlld0JveD0iMCAwIDEwMCAxMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PC9zdmc+';
                                $productName = $firstItem && $firstItem->product ? $firstItem->product->name : 'Unknown Product';
                                if($order->orderItems->count() > 1) {
                                    $productName .= ' + ' . ($order->orderItems->count() - 1) . ' more items';
                                }
                            @endphp
                            <img src="{{ $productImage }}" alt="{{ $productName }}"
                                class="w-16 h-16 object-cover group-hover:scale-105 transition-transform duration-500 rounded-xl ">
                            <div class="flex-1">
                                <p class="font-medium text-(--text-color)">{{ $productName }}</p>
                                <p class="text-sm text-(--text-color)/80">Order ID: #{{ $order->transaction_id ?? $order->id }} • {{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                @if($order->status == 'pending')
                                    <span class="px-4 py-1 text-xs font-medium bg-(--hover-color)/20 text-amber-700 rounded-full">Pending</span>
                                @elseif($order->status == 'delivered')
                                    <span class="px-4 py-1 text-xs font-medium bg-[#E8EEEB] text-[#1F3D2E] rounded-full">Delivered</span>
                                @elseif($order->status == 'cancelled')
                                    <span class="px-4 py-1 text-xs font-medium bg-(--secondary-color)/20 text-rose-800 rounded-full">Canceled</span>
                                @else
                                    <span class="px-4 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">{{ ucfirst($order->status) }}</span>
                                @endif
                                <p class="font-semibold mt-2">Rs. {{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-(--text-color)/80 py-4">No recent orders found.</p>
                    @endforelse
                </div>
            </div>
            <!-- Top Discounts Section -->
            <div>
                <div class="flex justify-center items-center mb-5">
                    <h2 class="text-xl font-semibold text-(--text-color) flex items-center gap-1.5"><i data-lucide="flame" class="w-6 h-6 text-orange-500 fill-orange-500"></i> Top Discounts</h2>
                </div>

                <div
                    class="bg-(--card-bg) rounded-3xl p-6 shadow-sm hover:shadow-lg transition-all duration-300  max-h-120 overflow-y-auto">
                    <div class="space-y-5">
                        @forelse($topDiscounts as $product)
                            <div class="flex flex-col items-start gap-5 p-4 hover:bg-(--card-dark)/7 rounded-2xl transition-all group">
                                <div class="flex flex-row lg:flex-col gap-7 w-full">
                                    <div class="relative overflow-hidden rounded-2xl w-full">
                                        <img src="{{ $product->primaryImageUrl() }}"
                                            class="w-full h-32 object-cover group-hover:scale-105 transition duration-500"
                                            alt="{{ $product->name }}">
                                        @php
                                            $original = $product->originalPrice();
                                            $discounted = $product->effectivePrice();
                                            $percent = $original > 0 ? round((($original - $discounted) / $original) * 100) : 0;
                                        @endphp
                                        @if($percent > 0)
                                            <div class="absolute top-2 right-2 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-xl">
                                                {{ $percent }}% OFF
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <a href="{{ route('viewdetails', $product->slug) }}" wire:navigate class="font-medium text-(--text-dark) group-hover:text-(--primary-color) transition line-clamp-1">
                                            {{ $product->name }}
                                        </a>
                                        <p class="text-sm text-(--text-color)/80 line-clamp-1">{{ $product->category ? $product->category->name : 'Special Offer' }}</p>
                                        <div class="flex items-center gap-3 mt-2">
                                            <span class="text-lg font-bold text-[#1F3D2E]">Rs. {{ number_format($product->effectivePrice(), 2) }}</span>
                                            @if($product->effectivePrice() < $product->originalPrice())
                                                <span class="text-sm text-gray-400 line-through">Rs. {{ number_format($product->originalPrice(), 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="w-full">
                                    <a href="{{ route('viewdetails', $product->slug) }}" wire:navigate class="block text-center bg-(--primary-color)/90 text-white w-full px-6 py-3 rounded-2xl text-sm font-medium hover:bg-(--primary-color) transition">
                                        Grab Deal
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-(--text-color)/80 py-4">No top discounts available right now.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended For You -->
        <div class="mt-12">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-(--text-color)">Recommended For You</h2>
                    <p class="text-xs text-(--text-color)/60 mt-0.5 flex items-center gap-1">
                        <i data-lucide="sparkles" class="w-3 h-3"></i>
                        {{ $recommendationSource }}
                    </p>
                </div>
                <a href="{{ route('shop') }}" wire:navigate class="text-sm text-center text-(--secondary-color) hover:underline flex items-center gap-1">
                    Browse All <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @forelse($recommendedProducts as $product)
                    <div class="bg-(--card-bg) rounded-3xl overflow-hidden group shadow-sm hover:shadow-lg transition-all duration-300">
                        <div class="relative overflow-hidden">
                            <a href="{{ route('viewdetails', $product->slug) }}" wire:navigate>
                                <img src="{{ $product->primaryImageUrl() }}"
                                    class="w-full h-48 object-cover group-hover:scale-105 transition duration-500"
                                    alt="{{ $product->name }}">
                            </a>
                            @if($product->hasDiscount())
                                @php
                                    $orig = $product->originalPrice();
                                    $eff  = $product->effectivePrice();
                                    $pct  = $orig > 0 ? round((($orig - $eff) / $orig) * 100) : 0;
                                @endphp
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-xl">
                                    {{ $pct }}% OFF
                                </span>
                            @endif
                            <button class="absolute top-3 right-3 text-[#C65A3A] hover:text-[#b04a2c] transition-colors text-lg sm:text-xl drop-shadow focus:outline-none">
                                <i data-lucide="heart" class="w-5 h-5"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <a href="{{ route('viewdetails', $product->slug) }}" wire:navigate class="font-medium text-sm line-clamp-2 hover:text-(--primary-color) transition">
                                {{ $product->name }}
                            </a>
                            <div class="flex items-center gap-2 mt-2">
                                <p class="text-(--primary-color) font-semibold">Rs. {{ number_format($product->effectivePrice(), 2) }}</p>
                                @if($product->hasDiscount())
                                    <p class="text-xs text-gray-400 line-through">Rs. {{ number_format($product->originalPrice(), 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <p class="text-center text-(--text-color)/80 py-4">No recommended products at the moment.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-user-layout>

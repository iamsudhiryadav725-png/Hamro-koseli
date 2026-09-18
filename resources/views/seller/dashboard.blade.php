<x-seller_layout title="Dashboard" searchPlaceholder="Search orders, products...">
    <div class="space-y-10">

        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-(--text-color)">Welcome back, {{ $vendor->owner_name ?? $vendor->vendor_name ?? 'Vendor' }}!</h1>
            <p class="text-sm text-(--text-color) mt-1">Here's what's happening with your store today.</p>
        </div>

        <!-- Stats Cards Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Sales Card -->
            <div
                class="card group border-b-2 border-b-(--primary-color) p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex justify-between items-start">
                    <!-- Icon -->
                    <div
                        class="w-10 h-10 bg-(--primary-color)/10 rounded-2xl flex items-center justify-center text-(--primary-color) text-2xl group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="chart-no-axes-combined"></i>
                    </div>
                </div>

                <!-- Label & Value -->
                <div class="mt-6">
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Total Sales</p>
                    <h2 class="text-3xl font-extrabold text-(--text-dark) mt-1 font-sans!">Rs. {{ number_format($stats['total_sales'], 0) }}</h2>
                </div>
            </div>

            <!-- Total Orders -->
            <div
                class="card group border-b-2 border-b-(--primary-color) p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex justify-between items-start">
                    <div
                        class="w-10 h-10 bg-(--primary-color)/10 rounded-2xl flex items-center justify-center text-(--text-color) text-2xl
                        group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="shopping-cart"></i>
                    </div>

                </div>
                <div class="mt-6">
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Total Orders</p>
                    <h2 class="text-3xl font-extrabold text-(--text-dark) mt-1 font-sans!">{{ number_format($stats['total_orders']) }}</h2>
                </div>
            </div>


            <!-- Active Products -->
            <div
                class="card group border-b-2 border-b-(--primary-color) p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex justify-between items-start ">
                    <div
                        class="w-10 h-10 rounded-2xl bg-(--card-dark) flex items-center justify-center text-(--secondary-color) text-2xl group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="package"></i>
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Active Products</p>
                    <h2 class="text-3xl font-extrabold text-(--text-dark) mt-1 font-sans!">{{ number_format($stats['active_products']) }}</h2>
                </div>

            </div>

            <!-- Avg Rating -->
            @php
                $ratingRounded = (int) round($stats['avg_rating']);
            @endphp
            <div
                class="card group border-b-2 border-b-(--primary-color) p-6 shadow-sm hover:shadow-lg transition-all duration-300">
                <div class="flex items-start justify-between">
                    <div
                        class="w-11 h-11 rounded-2xl bg-(--card-dark) flex items-center justify-center text-(--hover-color) text-2xl group-hover:scale-105 transition-transform duration-300">
                        <i data-lucide="star" class="fill-current text-(--hover-color)"></i>
                    </div>
                    <div class="text-xs flex flex-row items-center gap-1 mt-4 text-(--hover-color)">
                        @for ($i = 1; $i <= 5; $i++)
                            <i data-lucide="star" class="w-4 h-4 {{ $i <= $ratingRounded ? 'fill-current' : '' }} text-(--hover-color)"></i>
                        @endfor
                    </div>
                </div>
                <div class="mt-6">
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Avg Rating</p>
                    <h2 class="text-3xl font-extrabold text-(--text-dark) mt-1 font-sans!">{{ number_format($stats['avg_rating'], 2) }} <span
                            class="text-xl text-(--text-color)/70 font-sans!">/ 5.0</span></h2>
                    <p class="text-xs text-(--text-color)/50 mt-1">{{ number_format($stats['review_count']) }} review{{ $stats['review_count'] === 1 ? '' : 's' }}</p>
                </div>

            </div>
        </div>

        <!-- Sales Trend & Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Sales Trend Chart (Dynamic) -->
            <div class="card-dark lg:col-span-2 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                    <h3 class="text-xl font-semibold text-(--text-color)">Sales Trend</h3>

                    {{-- Period selector --}}
                    <div class="flex items-center gap-1 bg-(--card-dark) rounded-xl p-1">
                        @foreach ([7 => '7D'] as $days => $label)
                            <button
                                data-period="{{ $days }}"
                                class="sales-trend-btn px-3 py-1 text-xs font-semibold rounded-lg transition-all duration-200
                                    {{ $days === 7 ? 'bg-(--primary-color) text-white shadow' : 'text-(--text-color)/60 hover:text-(--text-dark)' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Chart bars --}}
                <div id="sales-trend-chart" class="flex items-end justify-between gap-1 h-52 relative">

                    {{-- Tooltip (inside relative container so absolute coords align) --}}
                    <div id="sales-tooltip"
                        class="hidden absolute z-10 bg-(--card-dark) border border-(--primary-color)/20 text-(--text-dark) text-xs font-medium px-3 py-1.5 rounded-lg shadow-lg pointer-events-none whitespace-nowrap">
                    </div>
                    {{-- Skeleton bars shown while first load --}}
                    @foreach ($salesTrend as $day)
                        <div class="flex-1 flex flex-col items-center gap-2 group cursor-pointer sales-bar-wrap">
                            <div class="chart-bar w-full bg-(--card-dark) rounded-t-xl hover:bg-(--primary-color) transition-all duration-300 group-hover:scale-y-105 origin-bottom"
                                style="height: {{ $day['height'] }}px;"
                                data-total="{{ $day['total'] }}"
                                data-label="{{ $day['label'] }}">
                            </div>
                            <span class="text-xs font-medium text-(--text-color)/70 group-hover:text-(--text-dark) truncate max-w-full">{{ $day['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div id="sales-trend-footer" class="mt-5 text-center text-xs text-(--text-color)/50 flex items-center justify-center gap-2">
                    <i data-lucide="chart-no-axes-column-increasing" class="w-4 h-4"></i>
                    <span id="sales-trend-label">Last 7 days performance</span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card-dark p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <h3 class="text-xl font-semibold text-(--text-color) mb-5">Quick Actions</h3>
                <div class="space-y-4">
                    <a href="{{ route('product-create') }}"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-(--card-dark)/40 hover:bg-(--card-dark)/70 transition-all duration-300 group">
                        <div
                            class="w-11 h-11 rounded-2xl bg-(--card-dark) flex items-center justify-center text-(--secondary-color) text-2xl group-hover:scale-110 transition-transform">
                            <i data-lucide="circle-plus"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-(--text-dark) group-hover:translate-x-1 transition">Add New
                                Product</p>
                            <p class="text-sm text-(--text-color)">List a new item in your catalog</p>
                        </div>
                    </a>

                    <a href="{{ route('order') }}"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-(--card-dark)/40 hover:bg-(--card-dark)/70 transition-all duration-300 group">
                        <div
                            class="w-11 h-11 rounded-2xl bg-(--card-dark) flex items-center justify-center text-(--secondary-color) text-2xl group-hover:scale-110 transition-transform">
                            <i data-lucide="shopping-cart"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-(--text-dark) group-hover:translate-x-1 transition">Manage
                                Orders</p>
                            <p class="text-sm text-(--text-color)">Review and update your orders</p>
                        </div>
                    </a>

                    <a href="{{ route('seller-support') }}"
                        class="flex items-center gap-4 p-4 rounded-2xl bg-(--card-dark)/40 hover:bg-(--card-dark)/70 transition-all duration-300 group">
                        <div
                            class="w-11 h-11 rounded-2xl bg-(--card-dark) flex items-center justify-center text-(--secondary-color) text-2xl group-hover:scale-110 transition-transform">
                            <i data-lucide="headset"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-(--text-dark) group-hover:translate-x-1 transition">Contact
                                Support</p>
                            <p class="text-sm text-(--text-color)">Need help with your store?</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="card bg-(--card-bg) rounded-2xl shadow-sm border border-(--card-dark) overflow-hidden">
            <div class="px-6 py-5 border-b border-(--card-dark) flex justify-between items-center">
                <h3 class="font-semibold text-(--text-color) flex items-center gap-2">
                    <i data-lucide="history" class="w-5 h-5"></i>
                    Recent Orders
                </h3>
                <a href="{{ route('order') }}" class="text-sm text-(--secondary-color) flex items-center gap-1.5 transition">
                    <span class="hover:underline">View All</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-212.5 table-auto">
                    <thead class="bg-(--card-dark)">
                        <tr class="text-xs uppercase tracking-widest font-medium text-(--text-color)/70">
                            <th class="px-6 py-4 text-left">Order ID</th>
                            <th class="px-6 py-4 text-left">Customer</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Total</th>
                            <th class="px-6 py-4 text-left">Payment</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--card-dark)/50 text-sm">
                        @forelse ($recentItems as $item)
                            @php
                                $customer = $item->order?->user;
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

                                $paymentStatus = $item->order?->payment?->status ?? 'pending';
                                $paymentBadge = match ($paymentStatus) {
                                    'completed' => ['label' => 'Paid', 'class' => 'bg-(--card-dark) text-(--primary-color)/85'],
                                    'refunded' => ['label' => 'Refunded', 'class' => 'bg-(--secondary-color)/20 text-(--secondary-color)'],
                                    'failed' => ['label' => 'Failed', 'class' => 'bg-(--secondary-color)/20 text-(--secondary-color)'],
                                    default => ['label' => 'Pending', 'class' => 'bg-(--hover-color)/50 text-(--secondary-color)'],
                                };

                                $statusMeta = match ($item->status) {
                                    'pending' => ['label' => 'New', 'class' => 'bg-(--hover-color)/50 text-(--secondary-color)'],
                                    'confirmed' => ['label' => 'Processing', 'class' => 'bg-(--secondary-color)/20 text-(--secondary-color)'],
                                    'shipped' => ['label' => 'Shipped', 'class' => 'bg-(--secondary-color)/20 text-(--text-dark)'],
                                    'delivered' => ['label' => 'Delivered', 'class' => 'bg-(--card-dark) text-(--primary-color)/85'],
                                    'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-(--text-dark)/10 text-(--text-dark)/60'],
                                    'returned' => ['label' => 'Returned', 'class' => 'bg-(--text-dark)/10 text-(--text-dark)/60'],
                                    default => ['label' => ucfirst($item->status), 'class' => 'bg-(--text-dark)/10 text-(--text-dark)/60'],
                                };
                            @endphp
                            <tr class="hover:bg-(--card-dark)/10 transition-all duration-200 cursor-pointer">
                                <td class="px-6 py-5 whitespace-nowrap font-medium text-(--text-color)">
                                    #HK-{{ str_pad($item->order_id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-(--card-dark) flex items-center justify-center text-xs font-bold text-(--text-color)">
                                            {{ $initials }}
                                        </div>
                                        {{ $customer->name ?? 'Unknown Customer' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-(--text-color)">{{ $item->created_at?->format('M j, Y') }}</td>
                                <td class="px-6 py-5 whitespace-nowrap font-medium text-(--text-color)">Rs. {{ number_format($item->subtotal, 2) }}</td>
                                <td class="px-6 py-5">
                                    <span
                                        class="px-4 py-1.5 text-xs font-medium rounded-full {{ $paymentBadge['class'] }}">{{ $paymentBadge['label'] }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="px-4 py-1.5 text-xs font-medium rounded-full {{ $statusMeta['class'] }}">{{ $statusMeta['label'] }}</span>
                                </td>
                                <td class="px-6 py-5">
                                    <a href="{{ route('order-details', ['order' => $item->order_id]) }}"
                                        class="text-(--secondary-color) hover:text-(--hover-color) transition flex items-center gap-1">
                                        <i data-lucide="square-pen" class="w-4 h-4"></i> Update
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center text-(--text-color)/60">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="package-search" class="w-10 h-10 text-(--text-color)/30"></i>
                                        <p class="font-medium">No orders yet</p>
                                        <p class="text-sm">Orders from your customers will show up here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        // ── Sales Trend Dynamic Chart ──────────────────────────────────────────
        (function () {
            const chart      = document.getElementById('sales-trend-chart');
            const tooltip    = document.getElementById('sales-tooltip');
            const trendLabel = document.getElementById('sales-trend-label');
            const buttons    = document.querySelectorAll('.sales-trend-btn');

            if (!chart) return;

            const apiUrl     = '{{ route("dashboard.sales-trend") }}';
            let currentPeriod = 7;

            const periodLabels = {
                7:  'Last 7 days performance',
                30: 'Last 30 days performance',
                90: 'Last 90 days performance',
            };

            // ── Render bars from API response data ──────────────────────────────
            function renderChart(trend) {
                if (!trend || trend.length === 0) {
                    chart.innerHTML = '<p class="text-center w-full text-(--text-color)/50 text-sm self-center">No sales data for this period.</p>';
                    return;
                }

                const maxTotal  = Math.max(1, ...trend.map(d => d.total));
                const maxHeight = 140;

                chart.innerHTML = trend.map(day => {
                    const height = day.total > 0
                        ? Math.max(8, Math.round((day.total / maxTotal) * maxHeight))
                        : 4;

                    const formattedTotal = new Intl.NumberFormat('en-IN', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }).format(day.total);

                    return `
                        <div class="flex-1 flex flex-col items-center gap-2 group cursor-pointer sales-bar-wrap"
                             data-total="${day.total}" data-label="${day.label}" data-date="${day.date}">
                            <div class="chart-bar w-full bg-(--card-dark) rounded-t-xl hover:bg-(--primary-color)
                                        transition-all duration-300 group-hover:scale-y-105 origin-bottom"
                                 style="height: ${height}px;"
                                 data-total="${day.total}"
                                 data-formatted="Rs. ${formattedTotal}"
                                 data-label="${day.label}">
                            </div>
                            <span class="text-xs font-medium text-(--text-color)/70 group-hover:text-(--text-dark) truncate max-w-full">
                                ${day.label}
                            </span>
                        </div>`;
                }).join('');

                attachTooltips();
            }

            // ── Show/hide tooltip on bar hover ───────────────────────────────────
            function attachTooltips() {
                chart.querySelectorAll('.chart-bar').forEach(bar => {
                    bar.addEventListener('mouseenter', function () {
                        const label     = this.dataset.label;
                        const formatted = this.dataset.formatted;
                        tooltip.textContent = `${label}: ${formatted}`;
                        tooltip.classList.remove('hidden');
                        positionTooltip(this);
                    });

                    bar.addEventListener('mouseleave', function () {
                        tooltip.classList.add('hidden');
                    });
                });
            }

            function positionTooltip(bar) {
                const chartRect   = chart.getBoundingClientRect();
                const barRect     = bar.getBoundingClientRect();
                const tooltipGap  = 8; // px gap between tooltip bottom and bar top

                // Centre tooltip over the bar horizontally
                const barCentreX  = barRect.left - chartRect.left + barRect.width / 2;
                // Place tooltip just above the bar's top edge
                const barTopY     = barRect.top - chartRect.top;

                tooltip.style.left      = '0px';
                tooltip.style.top       = '0px';
                tooltip.style.visibility = 'hidden';
                tooltip.classList.remove('hidden');

                // Read rendered tooltip width after making it visible
                const tooltipWidth  = tooltip.offsetWidth;
                const tooltipHeight = tooltip.offsetHeight;

                const left = Math.max(0, Math.min(
                    barCentreX - tooltipWidth / 2,
                    chartRect.width - tooltipWidth
                ));
                const top  = barTopY - tooltipHeight - tooltipGap;

                tooltip.style.left       = `${left}px`;
                tooltip.style.top        = `${top}px`;
                tooltip.style.visibility = 'visible';
            }

            // ── Fetch data from API and refresh chart ─────────────────────────────
            function loadTrend(period) {
                currentPeriod = period;

                // Visual loading state
                chart.querySelectorAll('.chart-bar').forEach(b => {
                    b.style.opacity = '0.4';
                });

                fetch(`${apiUrl}?period=${period}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                .then(res => {
                    if (!res.ok) throw new Error('Network error');
                    return res.json();
                })
                .then(data => {
                    renderChart(data.trend);
                    if (trendLabel) trendLabel.textContent = periodLabels[period] ?? `Last ${period} days performance`;
                })
                .catch(() => {
                    chart.innerHTML = '<p class="text-center w-full text-(--text-color)/50 text-sm self-center">Failed to load data. Please refresh.</p>';
                });
            }

            // ── Period button click handlers ──────────────────────────────────────
            buttons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const period = parseInt(this.dataset.period, 10);
                    if (period === currentPeriod) return;

                    // Update active button styles
                    buttons.forEach(b => {
                        b.classList.remove('bg-(--primary-color)', 'text-white', 'shadow');
                        b.classList.add('text-(--text-color)/60');
                    });
                    this.classList.add('bg-(--primary-color)', 'text-white', 'shadow');
                    this.classList.remove('text-(--text-color)/60');

                    loadTrend(period);
                });
            });

            // Attach tooltips to the initially server-rendered bars
            attachTooltips();

            // Pre-populate data-formatted on server-rendered bars
            chart.querySelectorAll('.chart-bar').forEach(bar => {
                const total = parseFloat(bar.dataset.total ?? 0);
                bar.dataset.formatted = 'Rs. ' + new Intl.NumberFormat('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }).format(total);
            });
        })();
        // ── End Sales Trend ────────────────────────────────────────────────────

        document.addEventListener('DOMContentLoaded', function () {
            const countdownEl = document.getElementById('seller-deal-countdown');
            if (countdownEl) {
                const dealEndsAt = countdownEl.getAttribute('data-ends-at');
                if (dealEndsAt) {
                    const endTime = new Date(dealEndsAt.replace(' ', 'T')).getTime();

                    function updateSellerCountdown() {
                        const now = new Date().getTime();
                        const distance = endTime - now;

                        if (distance <= 0) {
                            document.getElementById('seller-hours').textContent = "00";
                            document.getElementById('seller-minutes').textContent = "00";
                            document.getElementById('seller-seconds').textContent = "00";
                            return;
                        }

                        const hours = Math.floor(distance / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        document.getElementById('seller-hours').textContent = String(hours).padStart(2, "0");
                        document.getElementById('seller-minutes').textContent = String(minutes).padStart(2, "0");
                        document.getElementById('seller-seconds').textContent = String(seconds).padStart(2, "0");
                    }

                    updateSellerCountdown();
                    setInterval(updateSellerCountdown, 1000);
                }
            }
        });
    </script>
</x-seller_layout>
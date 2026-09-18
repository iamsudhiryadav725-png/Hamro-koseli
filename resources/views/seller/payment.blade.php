<x-seller_layout title="payment Management" searchPlaceholder="Search by Orders , products or analytics...">
    <!-- Main Content -->
    <div class="space-y-10">
        <!-- Session Notifications -->
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
        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl animate-fadeIn">
                <div class="flex items-center gap-3 mb-2">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600"></i>
                    <span class="font-semibold text-sm">Please correct the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-sm pl-2 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-6 animate-fadeIn">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-(--text-color)">Financial Overview</h1>
                    <p class="text-sm text-(--text-color)/70 mt-1">Manage your direct earnings and track the 3% admin
                        commission fee.</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($currentBalance < 10)
                        <button id="requestPayoutBtn" disabled title="Minimum payment amount is Rs. 10"
                            class="group flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-gray-300 text-gray-500 rounded-2xl text-sm font-medium cursor-not-allowed opacity-60">
                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                            <span>Pay Commission</span>
                        </button>
                    @else
                        <button id="requestPayoutBtn" onclick="openPayoutModal()"
                            class="group flex items-center gap-2 px-4 py-2.5 sm:px-5 sm:py-3 bg-(--secondary-color) text-(--text-light)/95 rounded-2xl text-sm font-medium hover:bg-[#B94E31] hover:shadow-lg active:scale-95 transition-all duration-200 shadow-md">
                            <i data-lucide="credit-card"
                                class="w-5 h-5 group-hover:-rotate-3 transition-transform duration-200"></i>
                            <span>Pay Commission</span>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-12">

            <!-- Owed to Admin -->
            <div
                class="relative overflow-hidden bg-[#FFF7EF] rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 rounded-xl bg-(--primary-color)/10 flex items-center justify-center mb-3 shrink-0">
                        <i data-lucide="wallet-minimal" class="w-5 h-5 text-(--primary-color)"></i>
                    </div>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest leading-tight">Owed to
                        Admin</p>
                </div>
                <p class="text-[26px] lg:text-3xl font-extrabold text-(--text-dark) mt-3 leading-none break-all">
                    Rs.{{ number_format($currentBalance, 2) }}
                </p>
                <p class="text-xs text-(--text-color)/70 mt-1">3% commission due</p>
                <p class="text-xs text-(--text-color)/70 mt-1">
                    (A minimum payment of Rs. 10 is required to pay the admin.)
                </p>
            </div>

            <!-- Total Sales -->
            <div
                class="relative overflow-hidden bg-[#FFF7EF] rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 rounded-xl bg-(--secondary-color)/10 flex items-center justify-center mb-3 shrink-0">
                        <i data-lucide="wallet-cards" class="w-5 h-5 text-(--secondary-color)"></i>
                    </div>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest leading-tight">Total
                        Sales</p>
                </div>
                <p class="text-[26px] lg:text-3xl font-extrabold text-(--text-dark) mt-3 leading-none break-all">
                    Rs.{{ number_format($totalEarnings, 2) }}
                </p>
            </div>

            <!-- Commission Paid -->
            <div
                class="relative overflow-hidden bg-[#FFF7EF] rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 rounded-xl bg-(--hover-color)/10 flex items-center justify-center mb-3 shrink-0">
                        <i data-lucide="banknote-check" class="w-5 h-5 text-(--hover-color)"></i>
                    </div>
                    <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest leading-tight">
                        Commission Paid</p>
                </div>
                <p class="text-[26px] lg:text-3xl font-extrabold text-(--text-dark) mt-3 leading-none break-all">
                    Rs.{{ number_format($totalPayouts, 2) }}
                </p>
            </div>

            <!-- Pending Payment -->
            <div
                class="relative overflow-hidden bg-emerald-800 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center mb-3 shrink-0">
                        <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                    </div>
                    <p class="text-sm font-medium text-white uppercase tracking-widest leading-tight">Pending Payment
                    </p>
                </div>
                <p class="text-[26px] lg:text-3xl font-extrabold text-white mt-3 leading-none break-all">
                    Rs.{{ number_format($pendingSettlement, 2) }}
                </p>
                <p class="text-xs text-white/80 mt-1">Awaiting buyer payment confirmation</p>
            </div>
        </div>

        <!-- Payment / Earnings History -->
        <div
            class="bg-(--card-bg) rounded-2xl shadow-md overflow-hidden border border-(--text-color)/20 mb-8 transition-all duration-300 hover:shadow-lg">
            <div class="px-6 py-5 border-b border-(--card-dark)">
                <h2 class="text-lg md:text-2xl font-semibold text-(--text-color)">Earnings History</h2>
            </div>

            <div class="responsive-table-wrapper overflow-x-auto">
                <table class="w-full md:min-w-full">
                    <thead>
                        <tr class="border-b border-gray-100 bg-(--card-dark)">
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Transaction ID</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Date</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Gateway</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Your Earnings</th>
                            <th
                                class="text-left py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Status</th>
                            <th
                                class="text-right py-4 px-4 md:px-6 lg:px-8 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--text-color)/10 text-sm">
                        @forelse ($paymentHistory as $payment)
                            <tr class="hover:bg-(--card-dark)/10 transition-all duration-200 cursor-pointer">
                                <td
                                    class="px-4 md:px-6 lg:px-8 py-5 text-(--text-color) text-sm font-medium transition-colors">
                                    {{ $payment->transaction_id ?? ($payment->reference_id ? 'REF-' . $payment->reference_id : 'TXN-' . str_pad($payment->id, 6, '0', STR_PAD_LEFT)) }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-(--text-color) text-sm">
                                    {{ ($payment->paid_at ?? $payment->created_at)->format('M d, Y') }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 font-medium text-(--text-color) capitalize">
                                    {{ strtoupper($payment->gateway) }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 font-semibold text-(--text-color)">
                                    Rs.{{ number_format($payment->net_amount, 2) }}
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5">
                                    <span
                                        class="inline-block px-3 py-1.5 text-xs font-medium bg-(--card-dark) text-(--primary-color)/85 rounded-full">
                                        RECEIVED
                                    </span>
                                </td>
                                <td class="px-4 md:px-6 lg:px-8 py-5 text-right">
                                    <a href="{{ route('payment-details', $payment->id) }}"
                                        class="text-(--secondary-color) hover:text-(--hover-color) font-medium flex items-center gap-1 ml-auto text-sm transition-all duration-200 group">
                                        <span class="hover:underline">View Details</span>
                                        <i data-lucide="arrow-right" class="w-6 h-4"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-12 text-center text-(--text-color)/60">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="inbox" class="w-10 h-10 opacity-40"></i>
                                        <p class="text-sm font-medium">No completed payments found for the selected
                                            period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div
                class="px-6 py-5 bg-(--card-dark) border-t border-(--text-color)/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">

                <p class="text-sm text-(--text-color)/70">
                    Showing <span
                        class="font-medium">{{ $paymentHistory->firstItem() ?? 0 }}–{{ $paymentHistory->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-medium">{{ $paymentHistory->total() }}</span> transactions
                </p>

                <div class="flex items-center gap-2">
                    @if ($paymentHistory->onFirstPage())
                        <button disabled
                            class="px-4 py-2 text-sm rounded-xl border border-(--text-color)/30 opacity-50 cursor-not-allowed">Previous</button>
                    @else
                        <a href="{{ $paymentHistory->previousPageUrl() }}"
                            class="px-4 py-2 text-sm rounded-xl border border-(--text-color) hover:bg-(--primary-color) hover:text-white transition">Previous</a>
                    @endif

                    @foreach ($paymentHistory->getUrlRange(max(1, $paymentHistory->currentPage() - 2), min($paymentHistory->lastPage(), $paymentHistory->currentPage() + 2)) as $page => $url)
                        @if ($page == $paymentHistory->currentPage())
                            <button
                                class="w-10 h-10 rounded-xl bg-(--primary-color) text-white font-semibold border border-(--primary-color)">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}"
                                class="w-10 h-10 rounded-xl border border-(--text-color) hover:bg-(--primary-color) hover:text-white transition flex items-center justify-center">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if ($paymentHistory->hasMorePages())
                        <a href="{{ $paymentHistory->nextPageUrl() }}"
                            class="px-4 py-2 text-sm rounded-xl border border-(--text-color) hover:bg-(--primary-color) hover:text-white transition">Next</a>
                    @else
                        <button disabled
                            class="px-4 py-2 text-sm rounded-xl border border-(--text-color)/30 opacity-50 cursor-not-allowed">Next</button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Alerts -->
            <div class="bg-(--card-bg) rounded-3xl p-6 shadow-sm">
                <h3 class="font-semibold mb-4">ACTIVE ALERTS</h3>
                <div class="space-y-4">
                    @if ($pendingSettlement > 0)
                        <div class="bg-amber-50 border border-amber-200 p-4 rounded-2xl">
                            <p class="font-medium text-amber-700">Pending Payment</p>
                            <p class="text-sm text-amber-600 mt-1">
                                Rs.{{ number_format($pendingSettlement, 2) }} is awaiting buyer payment.
                            </p>
                        </div>
                    @endif
                    <div class="bg-(--primary-color)/30 border-(--text-color)/10 p-4 rounded-2xl">
                        <p class="font-medium text-(--text-dark)">Account Active</p>
                        <p class="text-sm text-(--text-color) mt-1">Your vendor account is active and in good standing.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Need Help -->
            <div class="bg-emerald-800 text-white rounded-3xl p-6 shadow-sm">
                <h3 class="font-semibold mb-2">NEED HELP?</h3>
                <p class="text-emerald-100 text-base mb-6">Reach out to our team for account and settlement queries.
                </p>
                <a href="{{ route('create-ticket') }}"
                    class="bg-white text-emerald-800 px-6 py-3 rounded-2xl font-medium hover:bg-emerald-100 transition">
                    Create Ticket
                </a>
            </div>
        </div>
    </div>

    <!-- Pay Commission Modal -->
    <div id="payoutModal"
        class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm items-center justify-center p-4">

        <div class="bg-[#FFFCF8] w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b flex justify-between items-start">
                <div class="flex gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-(--primary-color)/20 flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6 text-(--primary-color)"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-(--text-color)">Pay Admin Commission</h2>
                        <p class="text-sm text-(--text-color)/70">Settle your outstanding admin commission fee</p>
                    </div>
                </div>
                <button onclick="closePayoutModal()"
                    class="w-10 h-10 rounded-full text-2xl text-gray-600 hover:text-gray-700 transition">&times;</button>
            </div>

            <form action="{{ route('seller.pay-commission') }}" method="POST"
                class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                @csrf

                <!-- Balance Card -->
                <div class="rounded-3xl bg-(--card-dark)/50 p-4 border border-(--text-color)/20">
                    <p class="text-sm text-(--text-color)/90">Total Commission Due</p>
                    <h3 class="text-3xl font-bold text-(--secondary-color) mt-1">
                        Rs.{{ number_format($currentBalance, 2) }}
                    </h3>
                    <div
                        class="mt-3 inline-flex items-center gap-2 bg-[#FFFCF8] px-3 py-1 rounded-full text-(--primary-color) text-sm">
                        <i data-lucide="info" class="w-4 h-4"></i>
                        3% fee of direct sales earnings
                    </div>
                </div>

                <!-- Input Amount -->
                <div>
                    <label for="commissionAmount" class="block text-sm font-medium text-(--text-dark) mb-2">Amount to
                        Pay (Rs.)</label>
                    <input type="number" step="0.01" min="10" max="{{ $currentBalance }}" name="amount"
                        id="commissionAmount" value="{{ round($currentBalance, 2) }}"
                        class="w-full bg-(--card-dark) border border-(--text-color)/20 rounded-2xl px-4 py-3.5 focus:outline-none focus:border-(--secondary-color) text-lg font-semibold"
                        required>
                    <p class="text-[11px] text-(--text-color)/60 mt-1">Minimum payment: Rs. 10. Max: Rs.
                        {{ number_format($currentBalance, 2) }}</p>
                </div>

                <!-- Payment Gateway -->
                <div>
                    <label class="block text-sm font-medium text-(--text-dark) mb-2">Payment Gateway</label>

                    <div
                        class="border-2 border-green-500 rounded-2xl p-5 bg-green-50/50 flex items-center justify-between transition-all">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-green-600 flex items-center justify-center text-white text-3xl font-bold shadow-inner">
                                K
                            </div>
                            <div>
                                <span class="font-semibold text-lg text-green-800">Khalti</span>
                                <p class="text-sm text-green-700">Secure &amp; Instant Digital Payment</p>
                            </div>
                        </div>

                        <div class="text-right">
                            <span
                                class="inline-flex items-center gap-1 text-xs bg-green-600 text-white font-medium px-3 py-1.5 rounded-full">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                SELECTED
                            </span>
                        </div>
                    </div>

                    <!-- Hidden field for backend -->
                    <input type="hidden" name="gateway" value="khalti">
                </div>

                <!-- Footer / Action Buttons -->
                <div class="border-t pt-4 flex gap-3">
                    <button type="button" onclick="closePayoutModal()"
                        class="flex-1 py-3.5 rounded-2xl border border-(--text-color)/20 text-gray-700 font-medium hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 py-3.5 rounded-2xl bg-(--secondary-color) text-white font-semibold hover:bg-[#B94E31] transition">
                        Proceed to Pay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPayoutModal() {
            const modal = document.getElementById('payoutModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closePayoutModal() {
            const modal = document.getElementById('payoutModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</x-seller_layout>

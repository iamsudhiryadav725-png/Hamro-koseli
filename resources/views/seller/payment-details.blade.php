<x-seller_layout title="Payment Details" searchPlaceholder="Search...">
    <div class="space-y-10">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between items-start gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-(--text-color)">Payment Details</h1>
                <p class="text-sm text-(--text-color)/70 mt-1">Buyer Payment History & Commission Breakdown</p>
            </div>
            <a href="{{ route('seller.payment') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-(--text-dark) bg-(--text-light) border border-(--text-color)/20 rounded-2xl">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                Back to Payments
            </a>
        </div>

        <!-- Buyer Payment Summary -->
        <div class="bg-(--card-bg) border border-(--text-color)/20 rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
                <i data-lucide="credit-card"></i> Buyer Payment Information
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-xs text-(--text-color)/70">Buyer Name</p>
                    <p class="font-semibold">{{ $order->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-(--text-color)/70">Total Paid by Buyer</p>
                    <p class="font-semibold text-lg">Rs. {{ number_format($buyerPaymentInfo['total_paid_by_buyer'], 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-(--text-color)/70">Payment Method</p>
                    <p class="font-medium capitalize">{{ $buyerPaymentInfo['payment_method'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-(--text-color)/70">Transaction ID</p>
                    <p class="font-mono text-sm">{{ $buyerPaymentInfo['transaction_id'] }}</p>
                </div>
            </div>
        </div>

        <!-- Commission Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <div class="lg:col-span-7">
                <div class="bg-(--card-bg) border border-gray-200 rounded-2xl p-6">
                    <h2 class="text-lg font-semibold mb-5">Your Earnings Breakdown</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between py-3 border-b">
                            <span>Your Sales (Subtotal)</span>
                            <span class="font-semibold">Rs. {{ number_format($vendorSubtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-3 text-(--secondary-color)">
                            <span>Platform Commission (3%)</span>
                            <span>Rs. {{ number_format($commission, 2) }}</span>
                        </div>
                        <div class="flex justify-between py-4 text-lg font-bold border-t">
                            <span>Net Amount After Commission</span>
                            <span class="text-(--primary-color)">Rs. {{ number_format($vendorSubtotal - $commission, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-(--card-bg) border border-gray-200 rounded-2xl p-6 h-full">
                    <h2 class="text-lg font-semibold mb-4">Payment Status</h2>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-2xl flex items-center justify-center">
                            <i data-lucide="check-circle"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-green-600">PAYMENT RECEIVED</p>
                            <p class="text-sm">{{ $buyerPaymentInfo['paid_at']->format('d M, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vendor Items Sold in this Order -->
        <div class="mt-8">
            <h2 class="text-lg font-semibold mb-4">Items Sold in this Order</h2>
            <div class="bg-(--card-bg) border border-(--text-color)/20 rounded-2xl overflow-hidden">
                <table class="w-full">
                    <thead class="bg-(--card-dark)">
                        <tr class="text-xs uppercase tracking-widest text-(--text-color)/70">
                            <th class="text-left py-4 px-6">Product</th>
                            <th class="text-left py-4 px-6">Qty</th>
                            <th class="text-right py-4 px-6">Price</th>
                            <th class="text-right py-4 px-6">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($items as $item)
                        <tr>
                            <td class="py-4 px-6">
                                <div class="font-medium">{{ $item->product->name }}</div>
                            </td>
                            <td class="py-4 px-6">{{ $item->quantity }}</td>
                            <td class="py-4 px-6 text-right">Rs. {{ number_format($item->price, 2) }}</td>
                            <td class="py-4 px-6 text-right font-semibold">Rs. {{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end mt-10">
            <a href="{{ route('seller.payment') }}"
               class="px-8 py-3 bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-xl font-semibold">
                Close
            </a>
        </div>
    </div>
</x-seller_layout>

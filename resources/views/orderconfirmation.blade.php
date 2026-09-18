<x-frontend-layout title="Order Confirmation - Hamro Koseli">
    <div class="bg-[#F4EAE1] text-[#3A2A1F] min-h-screen py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl mx-auto text-center">

            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 text-green-600">
                <i class="fas fa-check text-2xl"></i>
            </div>

            <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1F3D2E] tracking-tight mb-3">
                Order Placed!
            </h1>
            <p class="text-[#3A2A1F]/70 text-sm font-semibold mb-10">
                Order #{{ $order->id }} has been sent to
                {{ $order->orderItems->first()?->vendor?->vendor_name ?? 'the artisan' }}.
            </p>

            <div class="bg-white rounded-3xl p-6 border border-[#ebd7be]/40 shadow-sm text-left space-y-4">
                @foreach ($order->orderItems as $orderItem)
                    <div class="flex items-center justify-between border-b border-[#ebd7be]/30 pb-4 last:border-0 last:pb-0">
                        <div>
                            <p class="font-bold text-[#1F3D2E] text-sm">{{ $orderItem->product->name }}</p>
                            <p class="text-xs text-[#3A2A1F]/60 font-semibold">Qty: {{ $orderItem->quantity }}</p>
                        </div>
                        <span class="text-[#C65A3A] font-extrabold text-sm">रू {{ number_format($orderItem->subtotal, 2) }}</span>
                    </div>
                @endforeach

                <div class="flex items-center justify-between pt-2">
                    <span class="font-extrabold text-[#1F3D2E]">Total</span>
                    <span class="text-[#C65A3A] font-extrabold text-lg">रू {{ number_format($order->total_amount, 2) }}</span>
                </div>

                <div class="text-xs text-[#3A2A1F]/60 font-semibold pt-2 space-y-1">
                    <p>Payment: {{ strtoupper($order->payment_method) }}</p>
                    <p>Shipping to: {{ $order->shippingAddress->full_address }}</p>
                    <p>Status: <span class="text-[#1F3D2E] font-bold">{{ ucfirst($order->status) }}</span></p>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-center gap-6">
                <a href="{{ route('cart') }}" class="text-[#C65A3A] hover:text-[#b04a2c] font-bold text-sm transition">
                    <i class="fas fa-arrow-left"></i> Back to Cart
                </a>
                <a href="{{ route('shop') }}" class="bg-[#1F3D2E] hover:bg-[#13261d] text-white font-bold text-sm px-6 py-3 rounded-xl transition">
                    Continue Shopping
                </a>
            </div>

        </div>
    </div>
</x-frontend-layout>
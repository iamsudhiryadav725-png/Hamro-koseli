<x-frontend-layout title="Checkout - Hamro Koseli">
    <div class="bg-[#F4EAE1] text-[#3A2A1F] min-h-screen py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">

            <div class="mb-8">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1F3D2E] tracking-tight mb-2">
                    Checkout
                </h1>
                <p class="text-[#3A2A1F]/70 text-sm font-semibold">
                    Sold by <span
                        class="text-[#C65A3A] font-bold">{{ $cartItem->product->vendor->vendor_name ?? 'Local Artisan' }}</span>
                    &mdash; this order is placed separately from the rest of your cart.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Item summary -->
            <div class="bg-white rounded-3xl p-5 border border-[#ebd7be]/40 shadow-xs flex items-center gap-4 mb-8">
                <div class="w-20 h-20 rounded-2xl overflow-hidden border border-[#ebd7be]/30 shrink-0 bg-white">
                    <img src="{{ $cartItem->product->primaryImageUrl() }}" alt="{{ $cartItem->product->name }}"
                        class="w-full h-full object-cover">
                </div>
                <div class="flex-grow">
                    <h3 class="font-bold text-[#1F3D2E]">{{ $cartItem->product->name }}</h3>
                    @if ($cartItem->variant)
                        <p class="text-xs text-[#3A2A1F]/70 font-semibold">
                            {{ collect([$cartItem->variant->size, $cartItem->variant->color])->filter()->implode(' / ') }}
                        </p>
                    @endif
                    <p class="text-xs text-[#3A2A1F]/70 font-semibold">Qty: {{ $cartItem->quantity }} &times; रू
                        {{ number_format($unitPrice, 2) }}</p>
                </div>
                <span class="text-[#C65A3A] font-extrabold text-lg">रू {{ number_format($subtotal, 2) }}</span>
            </div>

            <form action="{{ route('checkout.store', $cartItem) }}" method="POST" class="space-y-8">
                @csrf

                <!-- Shipping address -->
                <div class="bg-white rounded-3xl p-6 border border-[#ebd7be]/40 shadow-sm">
                    <h2 class="text-lg font-bold text-[#1F3D2E] mb-4">Shipping Address</h2>
                    <p class="text-xs text-[#3A2A1F]/60 font-semibold mb-4">
                        Pulled from your account -update it here if anything's changed.
                    </p>

                    <div class="space-y-4">
                        <div>
                            <label for="phone" class="block text-xs font-bold text-[#1F3D2E] mb-1.5">Phone
                                Number</label>
                            <input type="tel" id="phone" name="phone" required maxlength="15"
                                value="{{ old('phone', $user->phone) }}" placeholder="+977-9800000000"
                                class="w-full px-4 py-3 rounded-xl border border-[#ebd7be] bg-[#FFF7EF] text-sm font-semibold text-[#3A2A1F] focus:outline-none focus:ring-2 focus:ring-[#1F3D2E]/25">
                        </div>
                        <div>
                            <label for="address" class="block text-xs font-bold text-[#1F3D2E] mb-1.5">Delivery
                                Address</label>
                            @if (($addresses && $addresses->count() > 0) || !empty($user->address))
                                <select id="address_select" onchange="triggerSelectChange(this)"
                                    class="w-full px-4 py-3 rounded-xl border border-[#ebd7be] bg-[#FFF7EF] text-sm font-semibold text-[#3A2A1F] focus:outline-none focus:ring-2 focus:ring-[#1F3D2E]/25 mb-3 cursor-pointer">
                                    @if ($addresses && $addresses->count() > 0)
                                        @foreach ($addresses as $addr)
                                            <option value="{{ $addr->address }}" data-phone="{{ $addr->phone }}"
                                                {{ $loop->first ? 'selected' : '' }}>
                                                {{ $addr->address }}
                                            </option>
                                        @endforeach
                                        @if (!empty($user->address) && !$addresses->contains('address', $user->address))
                                            <option value="{{ $user->address }}" data-phone="{{ $user->phone }}">
                                                {{ $user->address }}
                                            </option>
                                        @endif
                                    @elseif(!empty($user->address))
                                        <option value="{{ $user->address }}" data-phone="{{ $user->phone }}"
                                            selected>
                                            {{ $user->address }}
                                        </option>
                                    @endif
                                    <option value="new"
                                        {{ $addresses->isEmpty() && empty($user->address) ? 'selected' : '' }}>--
                                        Enter a New Address --</option>
                                </select>
                            @endif
                            <input type="hidden" id="address" name="address"
                                value="{{ old('address', $user->address) }}">

                            <x-cascading-address-dropdowns targetInputId="address" selectIdPrefix="checkout"
                                :hidden="($addresses && $addresses->count() > 0) || !empty($user->address)" />
                        </div>
                    </div>
                </div>

                <!-- Payment method -->
                <div class="bg-white rounded-3xl p-6 border border-[#ebd7be]/40 shadow-sm">
                    <h2 class="text-lg font-bold text-[#1F3D2E] mb-4">Payment Method</h2>
                    <div class="space-y-3">
                        <label
                            class="flex items-center gap-3 p-4 border border-[#ebd7be] rounded-2xl cursor-pointer hover:border-[#C65A3A] transition">
                            <input type="radio" name="payment_method" value="cod" checked required
                                class="accent-[#1F3D2E]">
                            <span class="text-sm font-semibold text-[#3A2A1F]">Cash on Delivery</span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-4 border border-[#ebd7be] rounded-2xl cursor-pointer hover:border-[#C65A3A] transition">
                            <input type="radio" name="payment_method" value="esewa" class="accent-[#1F3D2E]">
                            <span class="text-sm font-semibold text-[#3A2A1F]">eSewa</span>
                        </label>
                        <label
                            class="flex items-center gap-3 p-4 border border-[#ebd7be] rounded-2xl cursor-pointer hover:border-[#C65A3A] transition">
                            <input type="radio" name="payment_method" value="khalti" class="accent-[#1F3D2E]">
                            <span class="text-sm font-semibold text-[#3A2A1F]">Khalti</span>
                        </label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-[#C65A3A] hover:bg-[#b04a2c] disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-3.5 px-4 rounded-xl shadow-md transition duration-300">
                    Place Order &mdash; Rs. {{ number_format($subtotal, 2) }} <i
                        class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>

            <div class="mt-6">
                <a href="{{ route('cart') }}"
                    class="inline-flex items-center gap-2 text-[#C65A3A] hover:text-[#b04a2c] font-bold text-sm transition duration-300">
                    <i class="fas fa-arrow-left"></i> Back to Cart
                </a>
            </div>

        </div>
    </div>

    <script>
        function formatPhoneWithPrefix(phone) {
            if (!phone) return '+977-';
            let digits = phone.toString().replace(/\D/g, '');
            if (digits.startsWith('977')) {
                digits = digits.substring(3);
            }
            if (digits.length > 10) {
                digits = digits.substring(0, 10);
            }
            return digits ? '+977-' + digits : '+977-';
        }

        function triggerSelectChange(sel) {
            const phoneInput = document.getElementById('phone');
            const addressInput = document.getElementById('address');
            const userPhone = '{{ $user->phone ?? '' }}';
            if (!sel || sel.selectedIndex === -1 || !phoneInput || !addressInput) return;
            const selected = sel.options[sel.selectedIndex];
            if (!selected) return;

            const cascadingWrapper = document.getElementById('checkout_cascading_address_wrapper');
            if (selected.value === 'new') {
                addressInput.value = '';
                const currentPhone = phoneInput.value.trim();
                if (!currentPhone || currentPhone === '+977-') {
                    if (userPhone) {
                        phoneInput.value = formatPhoneWithPrefix(userPhone);
                    } else {
                        phoneInput.value = '+977-';
                    }
                }
                phoneInput.readOnly = false;
                if (cascadingWrapper) {
                    cascadingWrapper.classList.remove('hidden');
                    const provinceSel = document.getElementById('checkout_provinceSelect');
                    if (provinceSel) {
                        provinceSel.value = '';
                        if (typeof onNepalProvinceChange === 'function') {
                            onNepalProvinceChange('checkout', '', 'address');
                        }
                    }
                }
            } else {
                addressInput.value = selected.value;
                if (selected.dataset && selected.dataset.phone) {
                    phoneInput.value = formatPhoneWithPrefix(selected.dataset.phone);
                } else if (userPhone && (!phoneInput.value || phoneInput.value === '+977-')) {
                    phoneInput.value = formatPhoneWithPrefix(userPhone);
                }
                phoneInput.readOnly = false;
                if (cascadingWrapper) cascadingWrapper.classList.add('hidden');
            }
        }

        function initCheckoutScripts() {
            const select = document.getElementById('address_select');
            const phoneInput = document.getElementById('phone');
            const addressInput = document.getElementById('address');
            const userPhone = '{{ $user->phone ?? '' }}';

            if (phoneInput) {
                if (!phoneInput.value || phoneInput.value === '+977' || phoneInput.value === '+977-') {
                    phoneInput.value = userPhone ? formatPhoneWithPrefix(userPhone) : '+977-';
                }
            }

            if (select) {
                triggerSelectChange(select);
            }

            const form = document.querySelector('form');
            if (form) {
                form.removeEventListener('submit', handleFormSubmit);
                form.addEventListener('submit', handleFormSubmit);
            }
        }

        function handleFormSubmit(e) {
            const phoneInput = document.getElementById('phone');
            const addressInput = document.getElementById('address');
            const select = document.getElementById('address_select');

            const phone = phoneInput ? phoneInput.value.trim() : '';
            if (!/^\+977-\d{10}$/.test(phone)) {
                e.preventDefault();
                if (phoneInput) phoneInput.focus();
                if (typeof window.showToastPopup === 'function') {
                    window.showToastPopup(
                        'Phone number must start with +977- followed by 10 digits.', 'error');
                } else {
                    alert('Phone number must start with +977- followed by 10 digits.');
                }
                return;
            }

            if (select && select.value !== 'new') {
                if (addressInput) addressInput.value = select.value;
            }

            if (!addressInput || !addressInput.value.trim()) {
                e.preventDefault();
                if (typeof window.showToastPopup === 'function') {
                    window.showToastPopup(
                        'Please select Province, District, and City for your delivery address.',
                        'error');
                } else {
                    alert('Please select Province, District, and City for your delivery address.');
                }
                return;
            }
        }

        document.addEventListener('DOMContentLoaded', initCheckoutScripts);
        document.addEventListener('livewire:navigated', initCheckoutScripts);
    </script>
    <x-validation-toast />
</x-frontend-layout>

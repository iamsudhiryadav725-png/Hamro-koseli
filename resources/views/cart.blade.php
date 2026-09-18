<x-frontend-layout title="Cart - Hamro Koseli">
    <div class="bg-[#F4EAE1] text-[#3A2A1F] min-h-screen py-10 sm:py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <!-- Title -->
            <div class="mb-8 sm:mb-10">
                <h1 class="text-2xl sm:text-3xl sm:text-4xl font-extrabold text-[#1F3D2E] mb-2">Your Handpicked Pieces
                </h1>
                <p class="text-[#3A2A1F]/70 font-semibold">Items are grouped by seller — checkout one product or the
                    whole box at once.</p>
            </div>

            <!-- Flash -->
            @if (session('success'))
                <script>
                    (function() {
                        if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                    })();
                </script>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($items->isEmpty())
                <!-- Empty state -->
                <div class="text-center py-24 bg-white/50 rounded-3xl border border-[#ebd7be]/40 shadow-sm">
                    <div class="text-6xl mb-4">🛒</div>
                    <h2 class="text-2xl font-bold text-[#1F3D2E] mb-2">Your cart is empty</h2>
                    <p class="text-[#3A2A1F]/60 mb-6">Looks like you haven't added anything yet.</p>
                    <a href="{{ route('shop') }}" wire:navigate
                        class="inline-block bg-[#C65A3A] hover:bg-[#b04a2c] text-white font-bold px-8 py-3 rounded-2xl transition shadow-md">
                        Start Exploring
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                    <!-- LEFT: Cart Items -->
                    <div class="lg:col-span-8 space-y-6">

                        @foreach ($groupedByVendor as $vendorId => $vendorItems)
                            @php
                                $vendor = $vendorItems->first()->product->vendor;
                                $vendorName = $vendor->vendor_name ?? ($vendor->name ?? 'Local Artisan');
                                $vendorTotal = $vendorItems->sum(fn($i) => $i->subtotal());
                            @endphp

                            <div
                                class="bg-white rounded-3xl border border-[#ebd7be]/60 shadow-sm overflow-hidden vendor-group">

                                <!-- Vendor header -->
                                <div
                                    class="flex flex-wrap items-center justify-between gap-2 px-4 sm:px-5 py-3 sm:py-4 bg-[#FFF7EF] border-b border-[#ebd7be]/60">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-store text-[#C65A3A]"></i>
                                        <h2 class="font-bold text-[#1F3D2E] uppercase text-sm tracking-wide">
                                            {{ $vendorName }}</h2>
                                        <span class="text-[10px] font-bold text-[#3A2A1F]/50">
                                            ({{ $vendorItems->count() }} item{{ $vendorItems->count() > 1 ? 's' : '' }})
                                        </span>
                                    </div>

                                    @if ($vendorId && $vendorItems->count() > 1)
                                        <button
                                            onclick="openCheckoutModal('{{ route('checkout.save-user-info.vendor', $vendorId) }}')"
                                            class="bg-[#C65A3A] hover:bg-[#b04a2c] text-white text-xs font-bold px-4 py-2 rounded-xl transition whitespace-nowrap">
                                            Checkout All &mdash;
                                            <span class="vendor-total" data-vendor="{{ $vendorId }}">रू
                                                {{ number_format($vendorTotal, 2) }}</span>
                                        </button>
                                    @endif
                                </div>

                                <!-- Items -->
                                <div class="divide-y divide-[#ebd7be]/30">
                                    @foreach ($vendorItems as $item)
                                        <div class="cart-item flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5"
                                            data-cart-id="{{ $item->id }}"
                                            data-unit-price="{{ $item->product->resolvedDiscountPrice() ?? $item->product->price }}"
                                            data-vendor="{{ $vendorId }}">

                                            <!-- Product info -->
                                            <div class="flex gap-3 items-center flex-1 min-w-0">
                                                <img src="{{ $item->product->primaryImageUrl() }}"
                                                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover flex-shrink-0 border border-[#ebd7be]/40 shadow-sm"
                                                    alt="{{ $item->product->name }}">
                                                <div class="min-w-0">
                                                    <h3 class="font-bold text-[#1F3D2E] truncate">
                                                        {{ $item->product->name }}</h3>
                                                    @if ($item->variant)
                                                        <p class="text-xs text-[#3A2A1F]/50 mt-0.5">
                                                            {{ collect([$item->variant->size, $item->variant->color])->filter()->implode(' / ') }}
                                                        </p>
                                                    @endif
                                                    <p class="text-xs text-[#3A2A1F]/50 mt-0.5">
                                                        रू
                                                        {{ number_format($item->product->resolvedDiscountPrice() ?? $item->product->price, 2) }}
                                                        each
                                                    </p>
                                                    <p class="item-subtotal font-bold text-[#C65A3A] text-sm mt-1">
                                                        रू {{ number_format($item->subtotal(), 2) }}
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Controls -->
                                            <div class="flex items-center gap-3 flex-shrink-0">

                                                <!-- Quantity control -->
                                                <div
                                                    class="flex items-center border border-[#ebd7be] rounded-full bg-[#FFF7EF] px-2 py-1 gap-2 shadow-sm">
                                                    <button type="button"
                                                        class="qty-minus w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#C65A3A]/10 text-[#3A2A1F] hover:text-[#C65A3A] font-bold text-base transition focus:outline-none cursor-pointer"
                                                        aria-label="Decrease quantity">
                                                        −
                                                    </button>
                                                    <span
                                                        class="qty-display font-bold text-[#1F3D2E] w-6 text-center text-sm select-none">{{ $item->quantity }}</span>
                                                    <button type="button"
                                                        class="qty-plus w-7 h-7 flex items-center justify-center rounded-full hover:bg-[#C65A3A]/10 text-[#3A2A1F] hover:text-[#C65A3A] font-bold text-base transition focus:outline-none cursor-pointer"
                                                        aria-label="Increase quantity">
                                                        +
                                                    </button>
                                                </div>

                                                <!-- Checkout single -->
                                                <button
                                                    onclick="openCheckoutModal('{{ route('checkout.save-user-info', $item->id) }}')"
                                                    class="bg-[#1F3D2E] hover:bg-[#13261d] text-white px-3 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap shadow-sm">
                                                    Checkout
                                                </button>

                                                <!-- Remove -->
                                                <form action="{{ route('cart.remove', $item) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-400 hover:text-red-600 transition text-xs font-bold p-2 rounded-xl hover:bg-red-50"
                                                        title="Remove item">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach

                    </div>

                    <!-- RIGHT: Summary -->
                    <div class="lg:col-span-4">
                        <div
                            class="bg-white p-5 sm:p-6 rounded-3xl border border-[#ebd7be]/60 shadow-sm lg:sticky lg:top-6">

                            <h2 class="font-bold text-lg text-[#1F3D2E] mb-5 pb-4 border-b border-[#ebd7be]/40">
                                Order Summary
                            </h2>

                            <div class="space-y-3 text-sm mb-5">
                                <div class="flex justify-between">
                                    <span class="text-[#3A2A1F]/70 font-medium">Items</span>
                                    <span id="summary-count"
                                        class="font-bold text-[#1F3D2E]">{{ $items->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#3A2A1F]/70 font-medium">Subtotal</span>
                                    <span id="summary-subtotal" class="font-bold text-[#1F3D2E]">
                                        रू {{ number_format($items->sum(fn($i) => $i->subtotal()), 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-[#3A2A1F]/70 font-medium">Delivery</span>
                                    <span class="font-bold text-emerald-600">Free</span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center py-4 border-t border-[#ebd7be]/40">
                                <span class="font-extrabold text-[#1F3D2E]">Total</span>
                                <span id="summary-total" class="font-extrabold text-[#C65A3A] text-xl">
                                    रू {{ number_format($items->sum(fn($i) => $i->subtotal()), 2) }}
                                </span>
                            </div>

                            <p class="text-[10px] text-[#3A2A1F]/40 mt-2 text-center font-medium">
                                Taxes included · Free delivery across Nepal
                            </p>
                        </div>
                    </div>

                </div>

            @endif

        </div>
    </div>

    <!-- ============= CHECKOUT MODAL ============= -->
    <div id="checkoutModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-[99999]">

        <div class="bg-white w-full max-w-lg rounded-3xl shadow-xl mx-4 overflow-hidden relative">

            <div class="p-6 border-b border-[#ebd7be]/40">
                <h2 class="text-xl font-bold text-[#1F3D2E]">Delivery Information</h2>
                <p class="text-sm text-gray-500 mt-1">Enter phone and delivery address</p>
            </div>

            <form id="checkoutForm" method="POST">
                @csrf

                <!-- Modal Validation Error Message Popup -->
                <div id="modalValidationErrorContainer" class="px-6 pt-4 hidden">
                    <div id="modalValidationErrorCard"
                        class="bg-red-600 text-white px-5 py-3.5 rounded-2xl shadow-lg border border-red-400/30 flex items-start justify-between gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                        <div class="flex items-center gap-3">
                            <span class="text-lg">⚠️</span>
                            <span id="modalValidationErrorMessage" class="text-xs font-bold leading-snug"></span>
                        </div>
                        <button type="button" onclick="hideDeliveryError()"
                            class="text-white/80 hover:text-white font-bold text-sm leading-none ml-2">
                            &times;
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-[#1F3D2E] uppercase tracking-wider mb-1.5">Phone
                            Number</label>
                        <input type="tel" name="phone" id="phoneInput" placeholder="+977-9800000000"
                            maxlength="15"
                            class="w-full border border-[#ebd7be] rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/30 transition text-sm font-semibold text-[#3A2A1F]"
                            required>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#1F3D2E] uppercase tracking-wider mb-1.5">Delivery
                            Address</label>
                        <select id="modalAddressSelect" onchange="triggerSelectChange(this)"
                            class="w-full border border-[#ebd7be] rounded-xl px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/30 transition text-sm font-semibold text-[#3A2A1F] mb-3 cursor-pointer">
                            @if ($addresses && $addresses->count() > 0)
                                @foreach ($addresses as $addr)
                                    <option value="{{ $addr->address }}" data-phone="{{ $addr->phone }}"
                                        {{ $loop->first ? 'selected' : '' }}>
                                        {{ $addr->address }}
                                    </option>
                                @endforeach
                                @if (!empty(auth()->user()->address) && !$addresses->contains('address', auth()->user()->address))
                                    <option value="{{ auth()->user()->address }}"
                                        data-phone="{{ auth()->user()->phone }}">
                                        {{ auth()->user()->address }}
                                    </option>
                                @endif
                            @elseif(!empty(auth()->user()->address))
                                <option value="{{ auth()->user()->address }}"
                                    data-phone="{{ auth()->user()->phone }}" selected>
                                    {{ auth()->user()->address }}
                                </option>
                            @endif
                            <option value="new"
                                {{ $addresses->isEmpty() && empty(auth()->user()->address) ? 'selected' : '' }}>--
                                Enter a New Address --</option>
                        </select>
                        <input type="hidden" name="address" id="addressInput">

                        <x-cascading-address-dropdowns targetInputId="addressInput" selectIdPrefix="cart"
                            :hidden="($addresses && $addresses->count() > 0) || !empty(auth()->user()->address)" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 p-6 border-t border-[#ebd7be]/40">
                    <button type="button" onclick="closeCheckoutModal()"
                        class="px-4 py-2 border border-[#ebd7be] rounded-xl text-sm font-semibold hover:bg-[#FFF7EF] transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-[#1F3D2E] hover:bg-[#13261d] text-white px-6 py-2 rounded-xl text-sm font-bold transition shadow-sm">
                        Continue →
                    </button>
                </div>
            </form>

        </div>
    </div>



    <!-- ============= SCRIPTS ============= -->
    <script>
        // ── Checkout modal ────────────────────────────────────────────────
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

        function showDeliveryError(message) {
            const container = document.getElementById('modalValidationErrorContainer');
            const messageEl = document.getElementById('modalValidationErrorMessage');
            if (container && messageEl) {
                messageEl.textContent = message;
                container.classList.remove('hidden');
            }
        }

        function hideDeliveryError() {
            const container = document.getElementById('modalValidationErrorContainer');
            if (container) {
                container.classList.add('hidden');
            }
        }

        function triggerSelectChange(select) {
            hideDeliveryError();
            const phoneInput = document.getElementById('phoneInput');
            const addressInput = document.getElementById('addressInput');
            const cascadingWrapper = document.getElementById('cart_cascading_address_wrapper');
            if (!phoneInput || !addressInput || !select) return;

            if (select.selectedIndex === -1) return;
            const selected = select.options[select.selectedIndex];
            if (!selected) return;

            const userPhone = '{{ auth()->user()->phone ?? '' }}';

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
                    const provinceSel = document.getElementById('cart_provinceSelect');
                    if (provinceSel) {
                        provinceSel.value = '';
                        if (typeof onNepalProvinceChange === 'function') {
                            onNepalProvinceChange('cart', '', 'addressInput');
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
                if (cascadingWrapper) {
                    cascadingWrapper.classList.add('hidden');
                }
            }
        }

        function openCheckoutModal(url) {
            hideDeliveryError();
            document.getElementById('checkoutForm').action = url;
            const modal = document.getElementById('checkoutModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            const select = document.getElementById('modalAddressSelect');
            const phoneInput = document.getElementById('phoneInput');
            const userPhone = '{{ auth()->user()->phone ?? '' }}';

            if (phoneInput && (!phoneInput.value || phoneInput.value === '+977-')) {
                if (userPhone) {
                    phoneInput.value = formatPhoneWithPrefix(userPhone);
                } else {
                    phoneInput.value = '+977-';
                }
            }

            if (select) {
                triggerSelectChange(select);
            }
        }

        function closeCheckoutModal() {
            hideDeliveryError();
            const modal = document.getElementById('checkoutModal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }

        function initCartCheckoutScripts() {
            const modalEl = document.getElementById('checkoutModal');
            if (modalEl) {
                modalEl.removeEventListener('click', modalClickOutsideHandler);
                modalEl.addEventListener('click', modalClickOutsideHandler);
            }

            const select = document.getElementById('modalAddressSelect');
            if (select) {
                triggerSelectChange(select);
            }

            const phoneInput = document.getElementById('phoneInput');
            if (phoneInput) {
                if (!phoneInput.value || phoneInput.value === '+977') {
                    const userPhone = '{{ auth()->user()->phone ?? '' }}';
                    phoneInput.value = userPhone ? formatPhoneWithPrefix(userPhone) : '+977-';
                }
            }

            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
                checkoutForm.removeEventListener('submit', handleCheckoutSubmit);
                checkoutForm.addEventListener('submit', handleCheckoutSubmit);
            }
        }

        function modalClickOutsideHandler(e) {
            if (e.target === this) closeCheckoutModal();
        }

        function handleCheckoutSubmit(e) {
            hideDeliveryError();
            const phoneInput = document.getElementById('phoneInput');
            const phone = phoneInput ? phoneInput.value.trim() : '';
            if (!/^\+977-\d{10}$/.test(phone)) {
                e.preventDefault();
                if (phoneInput) phoneInput.focus();
                showDeliveryError('Phone number must start with +977- followed by 10 digits.');
                return;
            }

            const addressInput = document.getElementById('addressInput');
            const selectEl = document.getElementById('modalAddressSelect');
            let addressVal = '';
            if (selectEl && selectEl.value !== 'new') {
                addressVal = selectEl.value;
                if (addressInput) addressInput.value = addressVal;
            } else if (addressInput) {
                addressVal = addressInput.value.trim();
            }

            if (!addressVal) {
                e.preventDefault();
                showDeliveryError('Please select Province, District, and City for your delivery address.');
                return;
            }
        }

        document.addEventListener('DOMContentLoaded', initCartCheckoutScripts);
        document.addEventListener('livewire:navigated', initCartCheckoutScripts);

        // ── Quantity controls ─────────────────────────────────────────────
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        // Debounce map: cartId → timeout id
        const debounceMap = {};

        function formatRs(amount) {
            return 'रू ' + amount.toLocaleString('en-IN', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function recalcTotals() {
            let grandTotal = 0;
            let totalItems = 0;
            document.querySelectorAll('.cart-item').forEach(row => {
                const unitPrice = parseFloat(row.dataset.unitPrice) || 0;
                const qty = parseInt(row.querySelector('.qty-display').textContent) || 1;
                const subtotal = unitPrice * qty;
                row.querySelector('.item-subtotal').textContent = formatRs(subtotal);
                grandTotal += subtotal;
                totalItems += qty;
            });

            const summarySubtotal = document.getElementById('summary-subtotal');
            const summaryTotal = document.getElementById('summary-total');
            if (summarySubtotal) summarySubtotal.textContent = formatRs(grandTotal);
            if (summaryTotal) summaryTotal.textContent = formatRs(grandTotal);

            // Update cart badge dynamically
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.textContent = totalItems;
                if (totalItems > 0) {
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }

            // Update each vendor-checkout-all button total
            const vendorTotals = {};
            document.querySelectorAll('.cart-item').forEach(row => {
                const v = row.dataset.vendor;
                const up = parseFloat(row.dataset.unitPrice) || 0;
                const q = parseInt(row.querySelector('.qty-display').textContent) || 1;
                vendorTotals[v] = (vendorTotals[v] || 0) + up * q;
            });
            document.querySelectorAll('.vendor-total').forEach(el => {
                const v = el.dataset.vendor;
                if (vendorTotals[v] !== undefined) {
                    el.textContent = formatRs(vendorTotals[v]);
                }
            });
        }

        function persistQuantity(cartId, qty) {
            clearTimeout(debounceMap[cartId]);
            debounceMap[cartId] = setTimeout(async () => {
                try {
                    const res = await fetch(`/cart/${cartId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            quantity: qty
                        }),
                    });

                    if (!res.ok) {
                        const json = await res.json().catch(() => ({}));
                        const msg = json?.errors?.quantity?.[0] ?? json?.message ??
                            'Could not update quantity.';
                        showCartToast(msg, 'error');
                    }
                } catch {
                    showCartToast('Network error. Quantity not saved.', 'error');
                }
            }, 400); // 400 ms debounce
        }

        function showCartToast(message, type = 'success') {
            const existing = document.getElementById('cart-qty-toast');
            if (existing) existing.remove();

            const colours = {
                success: 'bg-[#1F3D2E] text-white',
                error: 'bg-red-600 text-white',
                warning: 'bg-amber-500 text-white',
            };
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
            };

            const toast = document.createElement('div');
            toast.id = 'cart-qty-toast';
            toast.className = [
                'fixed bottom-6 right-6 z-[9999] flex items-center gap-3',
                'px-5 py-3.5 rounded-2xl shadow-xl text-sm font-semibold',
                'translate-y-4 opacity-0 transition-all duration-300',
                colours[type] ?? colours.success,
            ].join(' ');
            toast.innerHTML = `<i class="fas ${icons[type] ?? icons.success} text-base"></i><span>${message}</span>`;
            document.body.appendChild(toast);

            requestAnimationFrame(() => requestAnimationFrame(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
                toast.classList.add('translate-y-0', 'opacity-100');
            }));
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 2800);
        }

        // Wire up all qty +/- buttons
        document.querySelectorAll('.cart-item').forEach(row => {
            const cartId = row.dataset.cartId;
            const unitPrice = parseFloat(row.dataset.unitPrice) || 0;
            const display = row.querySelector('.qty-display');
            const plusBtn = row.querySelector('.qty-plus');
            const minusBtn = row.querySelector('.qty-minus');

            plusBtn.addEventListener('click', () => {
                let qty = parseInt(display.textContent) || 1;
                qty += 1;
                display.textContent = qty;
                recalcTotals();
                persistQuantity(cartId, qty);
            });

            minusBtn.addEventListener('click', () => {
                let qty = parseInt(display.textContent) || 1;
                if (qty <= 1) return; // cannot go below 1; use Remove button to delete
                qty -= 1;
                display.textContent = qty;
                recalcTotals();
                persistQuantity(cartId, qty);
            });
        });
    </script>
    <x-validation-toast />
</x-frontend-layout>

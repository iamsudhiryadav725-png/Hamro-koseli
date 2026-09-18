<x-frontend-layout>
    <div class="max-w-4xl mx-auto px-6 py-12">
        <!-- Hero Title -->
        <div class="text-center mb-10">
            <h1 class="text-3xl sm:text-4xl md:text-4xl lg:text-5xl font-bold mb-3 sm:mb-4 tracking-tight leading-tight">
                Join our Artisan Community
            </h1>
            <p class="text-[#3A2A1F] text-sm sm:text-base md:text-lg mb-6 sm:mb-8 max-w-xl mx-auto px-2">
                Start selling your art and handmade creations on HamroKoseli
            </p>
        </div>

        <!-- Form Container -->
        <div class="bg-(--card-dark) rounded-3xl shadow-sm overflow-hidden">
            <form method="POST" action="{{ route('vendor.register') }}" id="sellerForm" class="p-8 md:p-12">
                @csrf

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Artist Profile -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-9 h-9 bg-[#E5DCD0]/60 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark">Artist Profile</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Full Name → users.name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-brand-dark mb-2">Full Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="Enter your full name" required>
                        </div>

                        <!-- Shop Name → vendors.vendor_name -->
                        <div>
                            <label for="vendor_name" class="block text-sm font-medium text-brand-dark mb-2">Shop Name</label>
                            <input type="text" id="vendor_name" name="vendor_name" value="{{ old('vendor_name') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="Enter your shop name" required>
                        </div>

                        <!-- User Email → users.email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-brand-dark mb-2">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ old('email') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="e.g. example@gmail.com" required>
                        </div>

                        <!-- User Phone → users.phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-brand-dark mb-2">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="e.g. 98XXXXXXXX" required>
                        </div>

                        <!-- Address → vendors.vendor_address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-brand-dark mb-2">Address</label>
                            <input type="text" id="address" name="address" value="{{ old('address') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="Street / Tole / Area">
                        </div>

                        <!-- City → vendors.city -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-brand-dark mb-2">City</label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="e.g. Kathmandu">
                        </div>

                        <!-- Province → vendors.province -->
                        <div>
                            <label for="province" class="block text-sm font-medium text-brand-dark mb-2">Province</label>
                            <input type="text" id="province" name="province" value="{{ old('province') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="e.g. Bagmati">
                        </div>
                    </div>
                </div>

                <!-- Shop Details -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-9 h-9 bg-[#E5DCD0]/60 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fa-solid fa-store"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark">Shop Details</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Owner Name → vendors.owner_name -->
                        <div>
                            <label for="owner_name" class="block text-sm font-medium text-brand-dark mb-2">Owner Name</label>
                            <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="Legal owner full name" required>
                        </div>

                        <!-- Vendor Email → vendors.email -->
                        <div>
                            <label for="vendor_email" class="block text-sm font-medium text-brand-dark mb-2">Shop Email</label>
                            <input type="email" id="vendor_email" name="vendor_email" value="{{ old('vendor_email') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="shop@example.com" required>
                        </div>

                        <!-- Vendor Phone → vendors.phone -->
                        <div>
                            <label for="vendor_phone" class="block text-sm font-medium text-brand-dark mb-2">Shop Phone</label>
                            <input type="tel" id="vendor_phone" name="vendor_phone" value="{{ old('vendor_phone') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="98XXXXXXXX" required>
                        </div>

                        <!-- PAN Number → vendors.pan_number -->
                        <div>
                            <label for="pan_number" class="block text-sm font-medium text-brand-dark mb-2">
                                PAN Number <span class="text-(--text-dark)/60 font-normal">(Optional)</span>
                            </label>
                            <input type="text" id="pan_number" name="pan_number" value="{{ old('pan_number') }}"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="PAN / VAT number">
                        </div>
                    </div>
                </div>

                <!-- Password -->
                <div class="mb-12">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-9 h-9 bg-[#E5DCD0]/60 rounded-2xl flex items-center justify-center text-xl">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <h2 class="text-xl sm:text-2xl md:text-3xl font-bold text-brand-dark">Set Password</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-brand-dark mb-2">Password</label>
                            <input type="password" id="password" name="password"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="Min. 8 characters" required>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-brand-dark mb-2">Confirm Password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full px-6 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl focus:outline-none focus:border-(--secondary-color) transition duration-200"
                                placeholder="Repeat your password" required>
                        </div>
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-start gap-3 mb-10">
                    <input type="checkbox" id="terms"
                        class="mt-1 w-5 h-5 accent-(--secondary-color) cursor-pointer">
                    <label for="terms" class="text-sm leading-relaxed text-(--text-color)/90">
                        I agree to the <a href="{{ route('terms&conditions') }}" class="text-(--text-dark) hover:underline">Terms and Conditions</a>
                        and understand that HamroKoseli supports fair trade for local artisans.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full bg-(--secondary-color)/95 hover:bg-(--secondary-color) text-(--text-light) font-semibold text-lg py-5 rounded-3xl transition-all duration-300 shadow-lg flex items-center justify-center gap-3">
                    <span>Register as an Artist</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                <div class="text-center mt-6">
                    <p class="text-sm text-(--text-color)/70">
                        Already have an account?
                        <a href="{{ route('seller.login') }}" class="text-(--text-dark) font-medium hover:underline">Login</a>
                    </p>
                </div>
            </form>
        </div>

        <!-- Trust Badges -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
            <div class="text-center">
                <div class="w-14 h-14 mx-auto bg-[#E5DCD0]/60 rounded-2xl shadow flex items-center justify-center text-4xl mb-4">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <p class="font-semibold text-(--text-dark)">Easy Shipping</p>
                <p class="text-sm text-(--text-color) mt-2">We handle the logistics so you can focus on creating.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 mx-auto bg-[#E5DCD0]/60 rounded-2xl shadow flex items-center justify-center text-4xl mb-4">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <p class="font-semibold text-(--text-dark)">Secure Payments</p>
                <p class="text-sm text-(--text-color) mt-2">Direct bank transfers for every piece you sell.</p>
            </div>
            <div class="text-center">
                <div class="w-14 h-14 mx-auto bg-[#E5DCD0]/60 rounded-2xl shadow flex items-center justify-center text-4xl mb-4">
                    <i class="fa-solid fa-users"></i>
                </div>
                <p class="font-semibold text-(--text-dark)">Seller Community</p>
                <p class="text-sm text-(--text-color) mt-2">Access workshops and networking with other Nepalese artists.</p>
            </div>
        </div>
    </div>

    <!-- Bottom Banner -->
    <div class="mt-20 mb-20">
        <div class="relative max-w-6xl mx-auto h-100.5 overflow-hidden rounded-3xl shadow-xl">
            <img src="{{ asset('images/craft.png') }}" alt="Artisan crafts" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/40"></div>
            <div class="absolute bottom-10 left-5 md:bottom-14 md:left-14 max-w-xl">
                <p class="text-(--hover-color) text-xs md:text-sm tracking-[0.3em] uppercase mb-3">
                    Every Creation Has a Story
                </p>
                <h2 class="text-(--text-light) text-3xl md:text-5xl font-bold leading-tight mb-4">
                    Tell Yours on HamroKoseli
                </h2>
                <p class="text-(--text-light)/90 text-sm md:text-base leading-relaxed">
                    Share authentic handmade products, connect with customers,
                    and let your craftsmanship reach homes across Nepal.
                </p>
            </div>
        </div>
    </div>

    <script>
        const sellerForm = document.getElementById('sellerForm');

        // ── Helpers ─────────────────────────────────────────────────────────
        const phoneRegex    = /^\d{10}$/;
        const pwUpperRegex  = /[A-Z]/;
        const pwLowerRegex  = /[a-z]/;
        const pwDigitRegex  = /[0-9]/;
        const pwSpecialRegex = /[\^$*.\[\]{}()?\-"!@#%&\/\\,><':;|_~`+=]/;

        // ── Restrict phone inputs to digits only, max 10 ────────────────────
        ['phone', 'vendor_phone'].forEach(function(id) {
            const input = document.getElementById(id);
            if (!input) return;
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length > 10) value = value.substring(0, 10);
                this.value = value;
            });
        });

        // ── Submit validation ────────────────────────────────────────────────
        sellerForm.addEventListener('submit', function(e) {
            const name         = document.getElementById('name').value.trim();
            const vendorName   = document.getElementById('vendor_name').value.trim();
            const email        = document.getElementById('email').value.trim();
            const phone        = document.getElementById('phone').value.trim();
            const ownerName    = document.getElementById('owner_name').value.trim();
            const vendorEmail  = document.getElementById('vendor_email').value.trim();
            const vendorPhone  = document.getElementById('vendor_phone').value.trim();
            const password     = document.getElementById('password').value;
            const passwordConf = document.getElementById('password_confirmation').value;
            const termsChecked = document.getElementById('terms').checked;

            let errors = [];

            // Required text fields
            if (!name)       errors.push('Full Name is required.');
            if (!vendorName) errors.push('Shop Name is required.');
            if (!email || !email.includes('@')) errors.push('A valid Email Address is required.');
            if (!ownerName)  errors.push('Owner Name is required.');
            if (!vendorEmail || !vendorEmail.includes('@')) errors.push('A valid Shop Email is required.');

            // Phone — digits only, exactly 10
            if (!phone) {
                errors.push('Phone Number is required.');
            } else if (!/^\d+$/.test(phone)) {
                errors.push('Phone Number must contain numbers only (no letters or special characters).');
            } else if (phone.length !== 10) {
                errors.push('Phone Number must be exactly 10 digits.');
            }

            if (!vendorPhone) {
                errors.push('Shop Phone is required.');
            } else if (!/^\d+$/.test(vendorPhone)) {
                errors.push('Shop Phone must contain numbers only (no letters or special characters).');
            } else if (vendorPhone.length !== 10) {
                errors.push('Shop Phone must be exactly 10 digits.');
            }

            // Password complexity
            if (!password) {
                errors.push('Password is required.');
            } else {
                if (password.length < 8)            errors.push('Password must be at least 8 characters.');
                if (!pwUpperRegex.test(password))   errors.push('Password must contain at least one uppercase letter (A–Z).');
                if (!pwLowerRegex.test(password))   errors.push('Password must contain at least one lowercase letter (a–z).');
                if (!pwDigitRegex.test(password))   errors.push('Password must contain at least one number (0–9).');
                if (!pwSpecialRegex.test(password)) errors.push('Password must contain at least one special character (e.g. ! @ # $ % ^ & *).');
            }

            if (!passwordConf) {
                errors.push('Confirm Password cannot be empty.');
            } else if (password !== passwordConf) {
                errors.push('Passwords do not match.');
            }

            if (!termsChecked) errors.push('You must agree to the Terms and Conditions.');

            if (errors.length > 0) {
                e.preventDefault();
                alert(errors.join('\n'));
            }
        });
    </script>
</x-frontend-layout>

<x-seller_layout title="Profile">
    <div class="space-y-10">
        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-(--text-color)">Account Settings</h1>
            <p class="text-sm text-(--text-color)/70 mt-1">Manage your profile information </p>
        </div>
        <!-- Flash Messages -->
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                });
            </script>
        @endif
        @if (session('password_success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.showToast) window.showToast("{{ session('password_success') }}", 'success');
                });
            </script>
        @endif
        @if ($errors->any())
            @php
                $profileErrors = collect($errors->messages())
                    ->except(['current_password', 'new_password', 'new_password_confirmation'])
                    ->flatten();
            @endphp
            @if ($profileErrors->isNotEmpty())
                <div
                    class="flex items-start gap-3 px-5 py-4 rounded-2xl bg-red-500/10 border border-red-500/25 text-red-600 text-sm font-medium">
                    <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 mt-0.5"></i>
                    <ul class="list-disc pl-4 space-y-1">
                        @foreach ($profileErrors as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif
        <div class="space-y-6">
            <form action="{{ route('seller.profile.update') }}" method="POST" enctype="multipart/form-data"
                id="profileForm">
                @csrf
                <!-- Profile Information -->
                <div class="bg-(--card-bg) rounded-2xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                    <div class="flex items-start gap-6">
                        <!-- Profile Picture -->
                        <div id="profileContainer" class="relative group cursor-pointer">
                            <img id="profilePreview"
                                src="{{ $user->profile_pic ? asset('storage/' . $user->profile_pic) : 'https://api.iconify.design/lucide/user.svg?color=%236b7280' }}"
                                alt="Profile"
                                class="w-24 h-24 rounded-full object-cover border border-(--text-color)/10 shadow bg-(--card-dark)">

                            <!-- Hover Overlay -->
                            <div
                                class="absolute inset-0 bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition flex items-center justify-center">
                                <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                            </div>

                            <!-- Delete Button (hidden by default) -->
                            <button type="button" id="deleteBtn"
                                class="absolute -top-1 -right-1 bg-red-500 hover:bg-red-600 text-white p-1 rounded-full transition hidden">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>

                            <!-- Hidden File Input -->
                            <input type="file" name="profile_pic" id="profileImage" accept="image/*" class="hidden">
                        </div>

                        <!-- Form Fields -->
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-brand-dark mb-2">
                                    Full Name <span class="text-(--secondary-color)">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                                    placeholder="Enter your name"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-brand-dark mb-1">
                                    Email Address <span class="text-(--secondary-color)">*</span>
                                </label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                    placeholder="e.g. example22@gmail.com"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-brand-dark mb-1">
                                    Phone Number <span class="text-(--secondary-color)">*</span>
                                </label>
                                <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                    placeholder="e.g. 1234567890"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-brand-dark mb-2">Address <span
                                        class="text-(--secondary-color)">*</span></label>
                                <input type="text" name="address"
                                    value="{{ old('address', $vendor?->vendor_address) }}"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-brand-dark mb-2">City <span
                                        class="text-(--secondary-color)">*</span></label>
                                <input type="text" name="city" value="{{ old('city', $vendor?->city) }}"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-brand-dark mb-2">Province <span
                                        class="text-(--secondary-color)">*</span></label>
                                <input type="text" name="province" value="{{ old('province', $vendor?->province) }}"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shop Details -->
                <div class="bg-(--card-bg) rounded-2xl shadow-sm p-6 mt-6">
                    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                        <i data-lucide="store"></i>
                        Shop Details
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Shop Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="vendor_name"
                                value="{{ old('vendor_name', $vendor?->vendor_name) }}" required
                                class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Owner Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="owner_name"
                                value="{{ old('owner_name', $vendor?->owner_name) }}" required
                                class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Shop Email <span
                                    class="text-(--secondary-color)">*</span></label>
                            <input type="email" name="vendor_email"
                                value="{{ old('vendor_email', $vendor?->email) }}"
                                class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-2">Shop Phone <span
                                    class="text-(--secondary-color)">*</span></label>
                            <input type="tel" name="vendor_phone"
                                value="{{ old('vendor_phone', $vendor?->phone) }}"
                                class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color)">
                        </div>

                    </div>
                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mt-8">

                        <button type="submit" id="saveProfileBtn"
                            class="order-1 sm:order-2 inline-flex items-center justify-center gap-2 px-6 py-3.5 bg-(--secondary-color) hover:bg-[#B94E31] text-(--text-light) rounded-2xl font-medium transition">
                            Save &nbsp; Profile
                        </button>
                        <a href="#"
                            class="order-2 sm:order-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 border border-(--secondary-color) text-(--text-color)! hover:bg-[#FFFAF5] bg-(--text-light) rounded-2xl font-medium transition">
                            Discard &nbsp; Changes
                        </a>

                    </div>
                </div>
            </form>
            <!-- Security & Privacy -->
            <form action="{{ route('seller.profile.password') }}" method="POST" id="passwordUpdateForm">
                @csrf
                <div id="password-section"
                    class="bg-(--card-bg) rounded-2xl shadow-sm p-6 hover:shadow-md transition-all duration-300">
                    <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                        <i data-lucide="lock-keyhole"></i>
                        Security & Privacy
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Current Password -->
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-1">Current Password</label>
                            <div class="relative">
                                <input type="password" id="currentPassword" name="current_password"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color) pr-12
                                    {{ $errors->has('current_password') ? 'ring-1 ring-red-400' : '' }}">
                                <button type="button" onclick="togglePassword('currentPassword', this)"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-(--text-color)/60 hover:text-(--text-color) transition">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-1">New Password</label>
                            <div class="relative">
                                <input type="password" id="newPassword" name="new_password"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color) pr-12
                                {{ $errors->has('new_password') ? 'ring-1 ring-red-400 border-red-400' : '' }}">

                                <button type="button" onclick="togglePassword('newPassword', this)"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-(--text-color)/60 hover:text-(--text-color) transition">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                            @error('new_password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-sm font-medium text-brand-dark mb-1">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="new_password_confirmation"
                                    class="w-full bg-(--card-dark) rounded-xl px-4 py-3 focus:outline-none focus:ring-1 focus:ring-(--secondary-color) pr-12
                                    {{ $errors->has('new_password_confirmation') ? 'ring-1 ring-red-400 border-red-400' : '' }}">
                                <button type="button" onclick="togglePassword('confirmPassword', this)"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-(--text-color)/60 hover:text-(--text-color) transition">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="submit" id="savePasswordBtn"
                            class="inline-flex items-center gap-2 px-8 py-3.5 bg-(--secondary-color) hover:bg-[#B94E31] text-(--text-light) rounded-2xl font-semibold transition">
                            Save &nbsp; Password
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @vite('resources/js/seller-profile.js')
</x-seller_layout>

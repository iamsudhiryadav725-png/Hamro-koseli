<x-frontend-layout title="Contact Us - Hamro Koseli">
    <section class="bg-[#FFF7EF] min-h-screen">

        <!-- Hero -->
        <div class="bg-[#1F3D2E] ">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-20 text-white">

                <span class="text-[#d4a017] uppercase tracking-widest text-sm font-semibold">
                    Contact Hamro Koseli
                </span>

                <h1 class="mt-4 text-2xl sm:text-4xl md:text-6xl font-serif">
                    We'd Love to Hear From You
                </h1>

                <p class="mt-4 sm:mt-6 text-sm sm:text-lg text-gray-300 max-w-2xl">
                    Whether you're a customer, artisan, food producer, or future
                    seller, our team is ready to help.
                </p>

            </div>
        </div>

        <!-- Contact Cards -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 sm:py-16">

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">

                <div class="bg-white p-8 rounded-2xl shadow-sm">
                    <h3 class="text-xl font-semibold text-[#3A2A1F]">
                        Email Us
                    </h3>

                    <p class="mt-3 text-[#8E8376]">
                        hamrokoseli06@gmail.com
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm">
                    <h3 class="text-xl font-semibold text-[#3A2A1F]">
                        Call Us
                    </h3>

                    <p class="mt-3 text-[#8E8376]">
                        +977-9872273268
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-sm">
                    <h3 class="text-xl font-semibold text-[#3A2A1F]">
                        Visit Us
                    </h3>

                    <p class="mt-3 text-[#8E8376]">
                        Kathmandu, Nepal
                    </p>
                </div>

            </div>

        </div>

        <!-- Contact Form -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 pb-10 sm:pb-20">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 sm:gap-12">

                <!-- Left -->
                <div>

                    <span class="text-[#C65A3A] font-semibold">
                        GET IN TOUCH
                    </span>

                    <h2 class="text-2xl sm:text-4xl font-serif mt-3 text-[#3A2A1F]">
                        Send us a Message
                    </h2>

                    <p class="mt-4 text-[#8E8376] leading-relaxed">
                        Have questions about products, orders, artisan partnerships,
                        or becoming a seller? Fill out the form and we'll get back
                        to you as soon as possible.
                    </p>

                </div>

                <!-- Right -->
                <form method="POST" action="{{ route('contact-us.submit') }}"
                    class="bg-white rounded-3xl shadow-sm p-5 sm:p-8">

                    @csrf

                    {{-- Success Message --}}
                    @if (session('success'))
                        <script>
                            (function() {
                                if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                            })();
                        </script>
                    @endif

                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">

                        <div>
                            <label class="block mb-2 font-medium">
                                First Name <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#C65A3A] @error('first_name') border-red-400 @enderror"
                                placeholder="Hari">
                        </div>

                        <div>
                            <label class="block mb-2 font-medium">
                                Last Name <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#C65A3A] @error('last_name') border-red-400 @enderror"
                                placeholder="Prasad">
                        </div>

                    </div>

                    <div class="mt-5">
                        <label class="block mb-2 font-medium">
                            Email <span class="text-red-500">*</span>
                        </label>

                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#C65A3A] @error('email') border-red-400 @enderror"
                            placeholder="hari@example.com">
                    </div>

                    <div class="mt-5">
                        <label class="block mb-2 font-medium">
                            Subject <span class="text-red-500">*</span>
                        </label>

                        <input type="text" name="subject" value="{{ old('subject') }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#C65A3A] @error('subject') border-red-400 @enderror"
                            placeholder="Question about my order...">
                    </div>

                    <div class="mt-5">
                        <label class="block mb-2 font-medium">
                            Message <span class="text-red-500">*</span>
                        </label>

                        <textarea rows="6" name="message"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-[#C65A3A] @error('message') border-red-400 @enderror"
                            placeholder="Write your message here...">{{ old('message') }}</textarea>
                    </div>

                    <button type="submit"
                        class="mt-6 bg-[#C65A3A] hover:bg-[#b34f31] transition text-white px-8 py-4 rounded-xl font-medium">
                        Send Message
                    </button>

                </form>

            </div>

        </div>

        <!-- Seller CTA -->
        <div class="bg-[#1F3D2E] text-white">

            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-20 text-center">

                <span class="text-[#D4A017] uppercase tracking-widest text-sm">
                    Join Our Community
                </span>

                <h2 class="text-2xl sm:text-4xl font-serif mt-4">
                    Are You a Local Artisan or Food Producer?
                </h2>

                <p class="max-w-2xl mx-auto mt-6 text-gray-300">
                    Sell your handmade crafts and authentic local products
                    through Hamro Koseli and reach customers across Nepal.
                </p>

                <div class="mt-8">
                    <a href="{{ route('seller') }}"
                        class="inline-block bg-[#C65A3A] hover:bg-[#B14F32] text-white px-8 py-4 rounded-2xl text-lg font-semibold">
                        Become a Seller
                    </a>
                </div>

            </div>

        </div>

        <!-- FAQ Preview -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 sm:py-20">

            <h2 class="text-2xl sm:text-4xl font-serif text-center text-[#3A2A1F]">
                Frequently Asked Questions
            </h2>

            <div class="mt-8 sm:mt-12 grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">

                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-semibold">
                        How long does delivery take?
                    </h3>

                    <p class="mt-3 text-[#8E8376]">
                        Most orders arrive within 2–7 business days.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-semibold">
                        How can I become a seller?
                    </h3>

                    <p class="mt-3 text-[#8E8376]">
                        Complete seller verification and submit your products.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm">
                    <h3 class="font-semibold">
                        What payment methods are accepted?
                    </h3>

                    <p class="mt-3 text-[#8E8376]">
                        eSewa, Khalti, and COD.
                    </p>
                </div>

            </div>

        </div>

    </section>
</x-frontend-layout>

<x-seller_layout title="Support">
    <div class="space-y-10">
        <!-- Header -->
        <div class="text-center mt-10 mb-12">
            <h1 class="text-3xl font-bold text-(--text-color)">How can we help you today?</h1>
            <p class="text-(--text-color)/70 mt-3 text-base">
                Choose the category that best matches your question and we'll get back to you quickly.
            </p>
        </div>

        <!-- Support Cards -->
        <div class="grid md:grid-cols-2 gap-8">
            <!-- General Support -->
            <div
                class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 hover:shadow-md transition-all duration-300 overflow-hidden group">
                <div class="p-10">
                    <div
                        class="w-20 h-20 mx-auto bg-(--secondary-color)/10 rounded-full flex items-center justify-center mb-6 group-hover:scale-102 transition-transform">
                        <span> <i data-lucide="circle-question-mark"
                                class="w-10 h-10 text-(--secondary-color) "></i></span>
                    </div>
                    <h3 class="text-xl font-semibold text-center text-(--text-color)">General Support</h3>
                    <p class="text-(--text-color) text-center mt-4 leading-relaxed">
                        Questions about orders, payments, payouts, product listings,
                        store policies, or other general inquiries.
                    </p>
                </div>
                <div class="border-t border-(--text-dark)/20 bg-(--card-bg)/70 py-5 px-8">
                    <a href="{{ route('create-ticket') }}"
                        class="w-full flex items-center justify-center gap-2 text-(--secondary-color)/90 font-medium hover:text-(--secondary-color) hover:underline underline-offset-4 transition-colors cursor-pointer">
                        Create Ticket
                        <span> <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </span>
                    </a>
                </div>
            </div>

            <!-- Technical Support -->
            <div
                class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 hover:shadow-md transition-all duration-300 overflow-hidden group">
                <div class="p-10">
                    <div
                        class="w-20 h-20 mx-auto bg-(--primary-color)/10 rounded-full flex items-center justify-center mb-6 group-hover:scale-102 transition-transform">
                        <span> <i data-lucide="settings" class="w-10 h-10 text-(--primary-color) "></i></span>
                    </div>
                    <h3 class="text-2xl font-semibold text-center text(--text-color)0">Technical Support</h3>
                    <p class="text-(--text-color) text-center mt-4 leading-relaxed">
                        Login issues, dashboard errors, product upload problems,
                        payment gateway issues, or any technical difficulties.
                    </p>
                </div>
                <div class="border-t border-(--text-dark)/20 bg-(--card-bg)/70 py-5 px-8">
                    <a href="{{ route('create-ticket') }}"
                        class="w-full flex items-center justify-center gap-2 text-(--secondary-color)/90 font-medium hover:text-(--secondary-color) hover:underline underline-offset-4 transition-colors cursor-pointer">
                        Create Ticket
                        <span> <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- View Existing Tickets Button -->
        <div class="flex justify-center mt-12">
            <a href="{{ route('seller-ticket') }}"
             class="flex items-center gap-3 bg-(--secondary-color) hover:bg-[#B94E31]  text-white font-semibold text-base px-5 py-2 rounded-2xl shadow-sm hover:shadow-md transition-all active:scale-95">
                <span><i data-lucide="menu" class="w-4 h-4"></i></span>
                View My Existing Tickets
            </a>
        </div>
    </div>

    <script>
        function createTicket(type) {
            if (type === 'general') {
                alert("Opening General Support Ticket Form...");
            } else {
                alert("Opening Technical Support Ticket Form...");
            }
            // Redirect to ticket creation page or open modal
        }

        function viewExistingTickets() {
            alert("Showing your existing support tickets...");
            // Redirect to tickets list page
        }
    </script>
</x-seller_layout>

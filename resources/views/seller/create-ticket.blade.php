<x-seller_layout title="Create Ticket">
    <div class="space-y-10">
        <!-- Back Button -->
        <a href="{{ route('seller-support') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-(--text-dark) bg-(--text-light) border border-(--text-color)/20 rounded-2xl">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Back to Support
        </a>

        <div class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 p-8 md:p-10">
            <h1 class="text-3xl font-semibold text-(--text-color) text-center">Create New Ticket</h1>
            <p class="text-(--text-color)/70 mt-3 text-base text-center">Describe your issue -we'll respond as soon as
                possible.</p>

            <form id="ticketForm" class="mt-8 space-y-8" method="POST" action="{{ route('store-ticket') }}">
                @csrf

                <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-brand-dark mb-2">Category <span
                            class="text-(--secondary-color)">*</span></label>
                    <select id="category" name="category" required
                        class="bg-(--card-dark) w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-(--secondary-color) transition-colors">
                        <option value="">Select Category</option>
                        <option value="General Support" {{ old('category') == 'General Support' ? 'selected' : '' }}>General Support</option>
                        <option value="Technical Support" {{ old('category') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                        <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-brand-dark mb-2">Subject <span
                            class="text-(--secondary-color)">*</span></label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}"
                        class="bg-(--card-dark) w-full px-5 py-4 border border-gray-200 rounded-2xl focus:outline-none focus:border-(--secondary-color) transition-colors"
                        placeholder="e.g. Payment not received this week" required>
                    @error('subject')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-brand-dark mb-2">Description <span
                            class="text-(--secondary-color)">*</span></label>
                    <textarea id="description" name="description" rows="8"
                        class="bg-(--card-dark) w-full px-5 py-4 border border-gray-200 rounded-3xl focus:outline-none focus:border-(--secondary-color) transition-colors resize-y"
                        placeholder="Please explain your issue in detail..." required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end mt-8">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-8 py-3.5 bg-(--secondary-color) hover:bg-[#B94E31] text-(--text-light) rounded-2xl font-semibold transition">
                        Submit Ticket
                    </button>
                </div>
            </form>
            <div class="mt-8 border-t border-gray-100 pt-6 text-center">
                <p class="text-(--text-color)/60">
                    You can view and track your tickets in the
                    <a href="{{ route('seller-ticket') }}" class="text-(--secondary-color) hover:underline font-medium">
                        My Tickets
                    </a>
                    section.
                </p>
            </div>
        </div>
    </div>


</x-seller_layout>

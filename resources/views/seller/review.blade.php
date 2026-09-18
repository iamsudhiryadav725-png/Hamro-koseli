<x-seller_layout title="Review" searchPlaceholder="Search orders, reviews...">
    <div class="space-y-10">
        {{-- Success and Error Flash Alerts --}}
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

        <!-- Header -->
        <div>
            <h1 class="text-3xl font-bold text-(--text-color)">Customer Reviews</h1>
            <p class="text-sm text-(--text-color) mt-1">Monitor and respond to customer feedback to maintain your store
                rating.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Total Reviews -->
            <div
                class="card group border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-40">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium uppercase tracking-wider text-(--text-color)">
                        Total Reviews
                    </span>
                    <div
                        class="w-10 h-10 rounded-lg bg-(--primary-color)/10 flex items-center justify-center group-hover:scale-102 transition-transform duration-300">
                        <i data-lucide="message-square" class="text-(--primary-color)"></i>
                    </div>
                </div>

                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-(--text-dark)">{{ number_format($totalReviews) }}</span>
                </div>

            </div>

            <!-- Average Rating -->
            <div
                class="card group border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-40">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium uppercase tracking-wider text-(--text-color)">
                        Average Rating
                    </span>
                    <div
                        class="w-10 h-10 bg-(--hover-color)/20 rounded-lg flex items-center justify-center group-hover:scale-102 transition-transform duration-300">
                        <i data-lucide="star" class="text-(--hover-color)"></i>
                    </div>
                </div>

                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-(--text-dark)">{{ number_format($avgRating, 2) }}</span>
                    <span class="text-xl text-(--text-color)/70">/5</span>
                </div>

                <div class="flex items-center gap-1 mt-2">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= $avgRating)
                            <i data-lucide="star" class="w-4 h-4 fill-current text-(--hover-color)"></i>
                        @elseif ($i - $avgRating < 1)
                            <i data-lucide="star-half" class="w-4 h-4 fill-current text-(--hover-color)"></i>
                        @else
                            <i data-lucide="star" class="w-4 h-4 text-gray-300"></i>
                        @endif
                    @endfor
                </div>
            </div>



            <!-- Response Rate -->
            <div
                class="card group border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-40">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium uppercase tracking-wider text-(--text-color)">
                        Response Rate
                    </span>
                    <div
                        class="w-10 h-10 rounded-lg bg-(--text-dark)/20 flex items-center justify-center group-hover:scale-102 transition-transform duration-300">
                        <i data-lucide="clock" class="text-(--text-dark)"></i>
                    </div>
                </div>

                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-(--text-color)">{{ $responseRate }}%</span>
                </div>

                <div class="w-full bg-[#FFFCF8] rounded-full h-2 mt-3">
                    <div class="bg-[#C65A3A] h-2 rounded-full" style="width: {{ $responseRate }}%"></div>
                </div>
            </div>

            <!-- Pending Replies -->
            <div
                class="card group border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 h-40">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium uppercase tracking-wider text-(--text-color)">
                        Pending Replies
                    </span>
                    <div
                        class="w-10 h-10 rounded-lg bg-(--secondary-color)/20 flex items-center justify-center group-hover:scale-102 transition-transform duration-300">
                        <i data-lucide="inbox" class="text-[#C65A3A]"></i>
                    </div>
                </div>

                <div class="mt-3">
                    <span class="text-3xl font-extrabold text-(--text-dark)">{{ number_format($pendingReplies) }}</span>
                </div>


            </div>

        </div>
        <!-- Filter + Search -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div class="flex gap-1 bg-(--card-bg) p-1 rounded-2xl w-full md:w-fit overflow-x-auto whitespace-nowrap">
                <a href="{{ route('seller.review', array_filter(['rating' => null, 'search' => request('search')])) }}"
                    class="filter-btn shrink-0 px-5 py-2 rounded-xl font-medium text-sm {{ !request('rating') ? 'bg-[#1F3D2E] text-white' : 'text-(--text-color) hover:bg-gray-100' }}">All</a>
                <a href="{{ route('seller.review', array_filter(['rating' => 5, 'search' => request('search')])) }}"
                    class="filter-btn shrink-0 px-5 py-2 rounded-xl font-medium text-sm {{ request('rating') == 5 ? 'bg-[#1F3D2E] text-white' : 'text-(--text-color) hover:bg-gray-100' }}">5
                    Stars <i data-lucide="star"
                        class="inline ml-1 mb-1 w-4 h-4 fill-current text-(--hover-color)"></i></a>
                <a href="{{ route('seller.review', array_filter(['rating' => 4, 'search' => request('search')])) }}"
                    class="filter-btn shrink-0 px-5 py-2 rounded-xl font-medium text-sm {{ request('rating') == 4 ? 'bg-[#1F3D2E] text-white' : 'text-(--text-color) hover:bg-gray-100' }}">4
                    Stars <i data-lucide="star"
                        class="inline ml-1 mb-1 w-4 h-4 fill-current text-(--hover-color)"></i></a>
                <a href="{{ route('seller.review', array_filter(['rating' => 3, 'search' => request('search')])) }}"
                    class="filter-btn shrink-0 px-5 py-2 rounded-xl font-medium text-sm {{ request('rating') == 3 ? 'bg-[#1F3D2E] text-white' : 'text-(--text-color) hover:bg-gray-100' }}">3
                    Stars <i data-lucide="star"
                        class="inline ml-1 mb-1 w-4 h-4 fill-current text-(--hover-color)"></i></a>
            </div>

            <div class="relative w-full md:w-80">
                <form action="{{ route('seller.review') }}" method="GET" class="w-full">
                    @if (request('rating'))
                        <input type="hidden" name="rating" value="{{ request('rating') }}">
                    @endif
                    <input type="text" name="search" id="searchInput" value="{{ request('search') }}"
                        placeholder="Search by product or keyword..."
                        class="w-full pl-11 pr-12 py-3 bg-(--card-bg) border border-[#EDE4D4] rounded-2xl shadow-sm focus:outline-none focus:border-(--secondary-color)">
                    <button type="submit"
                        class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-(--secondary-color)">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="space-y-5" id="reviewsContainer">
            @forelse ($reviews as $review)
                @php
                    $customer = $review->user;
                    $initials = $customer
                        ? collect(explode(' ', trim($customer->name)))
                            ->map(fn($part) => \Illuminate\Support\Str::substr($part, 0, 1))
                            ->join('')
                        : '??';
                    $initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($initials, 0, 2));
                @endphp
                <div
                    class="review-card bg-[#FFF7EF] rounded-xl p-5 border border-[#efe3d5] hover:shadow-md transition-all duration-300">
                    <div class="flex flex-wrap justify-between items-start gap-3">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-[#1F3D2E]/10 flex items-center justify-center text-(--text-color) font-bold">
                                {{ $initials }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-[#2d2a24] text-lg">
                                        {{ $customer->name ?? 'Anonymous' }}
                                    </h3>

                                    @if ($review->verified_purchase)
                                        <span
                                            class="text-xs bg-emerald-50 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-full font-bold">
                                            Verified Purchase
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500">
                                        <i data-lucide="calendar" class="inline w-3.5 h-3.5 mb-0.5"></i>
                                        {{ $review->created_at->format('F j, Y') }}
                                    </span>
                                    <span class="text-xs bg-[#f0e7dd] px-2 py-0.5 rounded-full">
                                        <i data-lucide="tag" class="inline w-3 h-3 mr-1"></i>
                                        {{ $review->product->name ?? 'Product' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <i data-lucide="star" class="w-4 h-4 fill-current text-(--hover-color)"></i>
                                @else
                                    <i data-lucide="star" class="w-4 h-4 text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                    </div>
                    @if ($review->comment)
                        <p class="text-[#4a3f35] mt-3 text-[15px]">"{{ $review->comment }}"</p>
                    @else
                        <p class="text-gray-400 mt-3 text-[14px] italic">No comments written.</p>
                    @endif

                    @if ($review->reply)
                        <div class="mt-4 bg-[#E5DCD0]/30 border border-[#ebd7be]/30 rounded-xl p-4 space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-[#C65A3A] flex items-center gap-1">
                                    <i data-lucide="corner-down-right" class="w-4 h-4"></i> Your Response
                                </span>
                                <span
                                    class="text-[10px] text-gray-500">{{ $review->replied_at ? $review->replied_at->format('M j, Y') : '' }}</span>
                            </div>
                            <p class="text-sm text-[#4a3f35] font-medium">{{ $review->reply }}</p>
                        </div>
                    @endif

                    <div class="flex justify-between gap-4 mt-4 pt-3 border-t border-[#efe3d5] overflow-x-auto">
                        <button onclick="toggleReplyBox(this)"
                            data-reply-text="{{ $review->reply ? 'Edit Response' : 'Reply Now' }}"
                            class="reply-btn shrink-0 px-4 py-2 bg-[#C65A3A] text-white rounded-lg text-sm hover:bg-[#B94E31] flex items-center gap-1">
                            <i data-lucide="reply" class="inline w-4 h-4"></i>
                            <span class="inline-flex">{{ $review->reply ? 'Edit Response' : 'Reply Now' }}</span>
                        </button>

                        <button type="button"
                            onclick="confirmDelete('{{ $review->id }}', '{{ addslashes($customer->name ?? 'Anonymous') }}')"
                            class="text-(--text-color)/60 hover:text-[#C65A3A] transition">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <!-- Reply Box Form -->
                    <div class="reply-box hidden mt-5">
                        <form action="{{ route('seller.review.reply', $review->id) }}" method="POST"
                            class="bg-[#FFFCF8] border border-[#efe3d5] rounded-xl p-4 sm:p-5 shadow-sm">
                            @csrf
                            <!-- Header -->
                            <div class="flex items-center gap-2 mb-4">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#C65A3A]/10 flex items-center justify-center shink-0">
                                    <i data-lucide="reply" class="text-[#C65A3A]"></i>
                                </div>
                                <span class="font-medium text-[#2d2a24] text-sm sm:text-base">
                                    {{ $review->reply ? 'Edit Response' : 'Reply to Customer' }}
                                </span>
                            </div>

                            <!-- Textarea -->
                            <textarea name="reply"
                                class="w-full rounded-xl border border-gray-200 bg-(--card-dark) px-3 sm:px-4 py-3 text-sm resize-none focus:outline-none focus:ring-1 focus:ring-[#C65A3A] focus:border-transparent transition"
                                rows="4" placeholder="Write your response to the customer..." required>{{ $review->reply }}</textarea>

                            <!-- Footer -->
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
                                <p class="text-xs text-gray-500 text-center sm:text-left">
                                    Be polite and helpful in your response.
                                </p>

                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 w-full sm:w-auto">
                                    <button type="submit"
                                        class="order-1 sm:order-2 w-full sm:w-auto px-5 py-2 text-sm bg-(--secondary-color) text-white rounded-lg
                               hover:bg-[#B94E31] transition flex items-center justify-center gap-2">
                                        <i data-lucide="send" class="h-4 w-4"></i>
                                        <span> Submit Response</span>
                                    </button>

                                    <button type="button" onclick="toggleReplyBox(this)"
                                        class="order-2 sm:order-1 w-full sm:w-auto px-4 py-2 text-sm border border-gray-300 rounded-lg
                               text-gray-600 hover:bg-gray-100 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-[#FFF7EF] rounded-xl p-10 border border-[#efe3d5] text-center text-gray-500">
                    <div class="flex flex-col items-center gap-3 py-6">
                        <i data-lucide="message-square" class="w-10 h-10 text-gray-400"></i>
                        <p class="font-medium">No reviews found matching your criteria.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @php
            $reviews->withQueryString();
        @endphp
        @if ($reviews->lastPage() > 1)
            <div class="flex items-center justify-center gap-3 mt-12 pb-6">

                {{-- Previous --}}
                @if ($reviews->onFirstPage())
                    <span
                        class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                        <i data-lucide="chevron-left" class="w-3 h-3"></i> </span>
                @else
                    <a href="{{ $reviews->previousPageUrl() }}"
                        class="w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm">
                        <i data-lucide="chevron-left" class="w-3 h-3"></i> </a>
                @endif

                {{-- Page numbers (windowed around the current page) --}}
                <div class="flex items-center gap-1">
                    @php
                        $start = max(1, $reviews->currentPage() - 2);
                        $end = min($reviews->lastPage(), $reviews->currentPage() + 2);
                    @endphp

                    @if ($start > 1)
                        <a href="{{ $reviews->url(1) }}"
                            class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">1</a>
                        @if ($start > 2)
                            <span class="text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none">...</span>
                        @endif
                    @endif

                    @for ($page = $start; $page <= $end; $page++)
                        @if ($page == $reviews->currentPage())
                            <a href="{{ $reviews->url($page) }}"
                                class="w-10 h-10 flex flex-col items-center justify-center text-sm font-bold text-[#1F3D2E] relative">
                                <span>{{ $page }}</span>
                                <span class="absolute bottom-1 w-5 h-0.5 bg-[#1F3D2E] rounded-full"></span>
                            </a>
                        @else
                            <a href="{{ $reviews->url($page) }}"
                                class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">{{ $page }}</a>
                        @endif
                    @endfor

                    @if ($end < $reviews->lastPage())
                        @if ($end < $reviews->lastPage() - 1)
                            <span class="text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none">...</span>
                        @endif
                        <a href="{{ $reviews->url($reviews->lastPage()) }}"
                            class="w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors">{{ $reviews->lastPage() }}</a>
                    @endif
                </div>

                {{-- Next --}}
                @if ($reviews->hasMorePages())
                    <a href="{{ $reviews->nextPageUrl() }}"
                        class="w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> </a>
                @else
                    <span
                        class="w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed">
                        <i data-lucide="chevron-right" class="w-3 h-3"></i> </span>
                @endif
            </div>
        @endif
    </div>
    </div>
    <style>
        .reply-box {
            transition: all 0.3s ease;
        }
    </style>

    <!-- Custom Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true"
                onclick="hideDeleteModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal panel -->
            <div
                class="inline-block align-bottom bg-white dark:bg-(--card-bg) rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all sm:my-8 sm:align-middle relative z-10">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                    </div>
                </div>

                <h3 class="text-2xl font-semibold text-center mb-2" id="modal-title">Delete Review?</h3>
                <p class="text-center text-gray-600 dark:text-gray-400 mb-8">
                    Are you sure you want to delete the review by <strong id="deleteReviewCustomer"></strong>?
                </p>

                <div class="flex gap-3">
                    <button type="button" onclick="hideDeleteModal()"
                        class="flex-1 py-4 text-base font-medium border border-gray-300 dark:border-gray-600 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        No, Keep Review
                    </button>

                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full py-4 text-base font-medium bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-2xl transition flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                            Yes, Delete It
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Reply Box
        function toggleReplyBox(btn) {
            const reviewCard = btn.closest('.review-card');
            const replyBox = reviewCard.querySelector('.reply-box');

            replyBox.classList.toggle('hidden');

            const replyBtn = reviewCard.querySelector('.reply-btn');
            if (!replyBox.classList.contains('hidden')) {
                replyBtn.innerHTML = `<i data-lucide="x" class="inline w-4 h-4"></i> Close &nbsp; Reply`;
                lucide.createIcons();
            } else {
                const text = replyBtn.getAttribute('data-reply-text') || 'Reply Now';
                replyBtn.innerHTML = `<i data-lucide="reply" class="inline w-4 h-4"></i> <span>${text}</span>`;
                lucide.createIcons();
            }
        }

        function confirmDelete(id, customerName) {
            document.getElementById('deleteReviewCustomer').innerText = customerName;
            document.getElementById('deleteForm').action = `/seller-review/${id}`;
            document.getElementById('deleteModal').classList.remove('hidden');
            lucide.createIcons();
        }

        function hideDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
</x-seller_layout>

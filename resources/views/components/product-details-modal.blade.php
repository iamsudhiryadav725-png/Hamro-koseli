<!-- Product Details Modal Overlay -->
<div id="product-details-modal"
    style="overflow-anchor: none;"
    class="fixed inset-0 z-[99999] hidden bg-black/60 backdrop-blur-sm overflow-y-auto p-4 sm:p-6 md:p-10 transition-opacity duration-300 opacity-0 flex justify-center items-start">

    <!-- Modal Content Container -->
    <div class="relative bg-[#F4EAE1] max-w-5xl mx-auto my-auto rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl border border-[#ebd7be]/50 transform scale-95 opacity-0 transition-all duration-300 ease-out"
        id="product-details-container">

        <!-- Close Button -->
        <button id="close-product-details"
            class="absolute top-4 right-4 z-50 bg-white/80 hover:bg-white text-slate-800 rounded-full w-10 h-10 flex items-center justify-center shadow-md transition hover:scale-105 active:scale-95 cursor-pointer focus:outline-none">
            <i class="fas fa-times text-lg"></i>
        </button>

        <div class="p-4 sm:p-6 md:p-10 lg:p-12 space-y-5 sm:space-y-8">

            <!-- Breadcrumbs -->
            <div class="text-[#3A2A1F]/60 text-xs font-semibold">
                Home &nbsp;&rsaquo;&nbsp; Shop &nbsp;&rsaquo;&nbsp; <span class="text-[#C65A3A]"
                    id="modal-breadcrumb-cat">Category</span>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left: Images -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="flex gap-4">
                        <!-- Main image -->
                        <div
                            class="flex-grow aspect-[4/3] rounded-3xl overflow-hidden border border-[#ebd7be]/30 shadow-md bg-white">
                            <img src="" id="modal-main-image" alt="" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Shipping and Returns Badges -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-[#ebd7be]/30">
                        <div class="flex items-start gap-3 bg-[#FFF7EF]/50 p-3 rounded-xl border border-[#ebd7be]/30">
                            <i class="fas fa-truck text-[#C65A3A] text-lg mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-[#1F3D2E] uppercase tracking-wide">Insured Shipping
                                </h4>
                                <p class="text-[10px] text-[#3A2A1F]/60 font-semibold mt-0.5">3-5 days delivery across
                                    Nepal</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 bg-[#FFF7EF]/50 p-3 rounded-xl border border-[#ebd7be]/30">
                            <i class="fas fa-rotate-left text-[#C65A3A] text-lg mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-[#1F3D2E] uppercase tracking-wide">No Return Policy</h4>
                                <p class="text-[10px] text-[#3A2A1F]/60 font-semibold mt-0.5">Returns are not accepted</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Info -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="space-y-3">
                        <span
                            class="inline-flex items-center gap-1.5 bg-[#E5DCD0]/70 text-[#1F3D2E] text-[10px] font-bold tracking-wider uppercase px-3 py-1 rounded-full">
                            Authentic Handmade
                        </span>

                        <div class="flex items-center gap-2 text-xs">
                            <div class="flex text-yellow-500 gap-0.5" id="modal-stars-container">
                                <!-- Stars dynamically loaded -->
                            </div>
                            <span class="text-[#3A2A1F]/60 font-semibold">(<span id="modal-reviews-count">0</span>
                                Reviews)</span>
                        </div>

                        <h1 class="text-xl sm:text-2xl sm:text-3xl font-bold text-[#1F3D2E] leading-tight font-serif modal-product-title"
                            id="modal-product-name">Product Name</h1>

                        <div class="flex flex-wrap items-center gap-2">
                            <span class="text-[#C65A3A] font-extrabold text-2xl modal-product-price"
                                id="modal-product-price">Rs 0</span>
                            <span class="text-slate-400 text-sm line-through hidden"
                                id="modal-product-original-price">Rs 0</span>
                            <span id="modal-discount-tag"
                                class="bg-red-100 text-red-700 text-xs font-bold px-2 py-0.5 rounded-md hidden"></span>
                            <span id="modal-savings-tag" class="text-emerald-700 text-xs font-bold hidden"></span>
                        </div>
                    </div>

                    <p class="text-[#3A2A1F]/80 text-sm leading-relaxed font-medium modal-product-desc"
                        id="modal-product-desc">
                        Product description goes here...
                    </p>

                    <!-- Vendor/Artist Card -->
                    <div id="modal-vendor-card"
                        class="bg-[#FFF7EF] border border-[#ebd7be]/40 rounded-2xl p-4 flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <div id="modal-vendor-avatar"
                                class="w-10 h-10 rounded-full bg-[#1F3D2E] text-white flex items-center justify-center font-bold text-lg border border-[#ebd7be]">
                                A
                            </div>
                            <div>
                                <h3 class="text-xs font-bold text-[#C65A3A] leading-tight" id="modal-vendor-name">
                                    Artist/Store Name</h3>
                                <p class="text-[10px] text-[#3A2A1F]/60 font-semibold mt-0.5">Verified Seller on Hamro Koseli
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-[11px] font-bold text-[#C65A3A] border border-[#C65A3A]/40 px-3 py-1 rounded-full bg-white/60 font-sans">
                            Verified Studio
                        </span>
                    </div>

                    <!-- Quantity Selector -->
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-bold text-[#1F3D2E]">Quantity</span>
                        <div
                            class="flex items-center border border-[#ebd7be] rounded-full bg-white px-3 py-1.5 gap-4 shadow-sm">
                            <button type="button"
                                class="qty-minus-btn text-[#3A2A1F] hover:text-[#C65A3A] font-bold text-sm w-5 h-5 flex items-center justify-center focus:outline-none transition cursor-pointer">−</button>
                            <input type="number"
                                class="qty-val-input text-sm font-bold text-[#1F3D2E] w-10 text-center bg-transparent border-none outline-none"
                                value="1" min="1" max="999">
                            <button type="button"
                                class="qty-plus-btn text-[#3A2A1F] hover:text-[#C65A3A] font-bold text-sm w-5 h-5 flex items-center justify-center focus:outline-none transition cursor-pointer">+</button>
                        </div>
                        <span class="text-xs text-emerald-700 font-bold" id="modal-stock-status">In Stock</span>
                    </div>

                    <!-- Buy Action Buttons -->
                    <div class="flex flex-col xs:flex-row gap-2 sm:gap-3 pt-2">
                        <button id="modal-add-to-cart-btn"
                            class="modal-add-to-cart-btn bg-[#C65A3A] hover:bg-[#b04a2c] text-white font-bold py-3 px-5 rounded-2xl flex-1 text-center shadow-md active:scale-[0.98] transition text-sm cursor-pointer">
                            Add to Cart
                        </button>
                        <button id="modal-buy-now-btn"
                            class="modal-buy-now-btn border-2 border-[#C65A3A] text-[#C65A3A] hover:bg-[#C65A3A]/10 font-bold py-3 px-5 rounded-2xl flex-1 text-center active:scale-[0.98] transition text-sm cursor-pointer">
                            Buy Now
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <div class="pt-8 border-t border-[#ebd7be]/40 space-y-6">
                <div class="flex border-b border-[#ebd7be]/40 gap-3 sm:gap-6 overflow-x-auto">
                    <button
                        class="pb-3 text-sm font-bold text-[#C65A3A] border-b-2 border-[#C65A3A] focus:outline-none transition cursor-default">
                        Product Specifications
                    </button>
                </div>

                <div class="tab-panel text-sm text-[#3A2A1F]/80 leading-relaxed font-medium space-y-3">
                    <p id="modal-product-desc-spec" class="leading-relaxed"></p>
                    <ul class="list-disc pl-5 space-y-1.5 text-xs text-[#3A2A1F]/70">
                        <li><strong>Material:</strong> 100% Authentic Nepalese sourced raw materials</li>
                        <li><strong>Origin:</strong> Hand-crafted by local families under fair trade standards</li>
                        <li><strong>Certification:</strong> Handcrafted Artisan Registry Certified</li>
                    </ul>
                </div>
            </div>
            <!-- Reviews Section -->
            <div class="pt-8 border-t border-[#ebd7be]/40 space-y-6" id="modal-reviews-section">

                <!-- Header -->
                <div class="flex items-center justify-between border-b border-[#ebd7be]/20 pb-4">
                    <h3 class="text-xl font-bold text-[#1F3D2E] font-serif flex items-center gap-2">
                        <i class="fas fa-star text-[#C65A3A]"></i>
                        Customer Reviews
                    </h3>
                    <button type="button" id="toggle-add-review-btn"
                        class="text-sm font-bold text-[#C65A3A] bg-white border border-[#C65A3A]/40 hover:bg-[#C65A3A] hover:text-white px-5 py-2.5 rounded-2xl transition-all duration-200 flex items-center gap-2 shadow-sm">
                        <i class="fas fa-pen-fancy"></i>
                        Write a Review
                    </button>
                </div>


                <!-- Write Review Form -->
                <div id="modal-add-review-section"
                    class="hidden bg-[#FFF7EF] border border-[#C65A3A]/30 rounded-3xl p-6 sm:p-8">
                    <h4 id="add-review-title" class="text-lg font-bold text-[#1F3D2E] mb-5 flex items-center gap-3">
                        <i class="fas fa-pen-fancy text-[#C65A3A]"></i> Share Your Experience
                    </h4>

                    <div id="add-review-form-container">
                        @auth
                            <form id="product-review-form" class="space-y-6">
                                <!-- Star Rating -->
                                <div>
                                    <label class="block text-sm font-bold text-[#1F3D2E] mb-3">Your Rating</label>
                                    <div class="flex gap-2 text-3xl" id="review-stars-selector">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <button type="button" data-rating="{{ $i }}"
                                                class="star-select-btn text-amber-300 hover:text-yellow-400 transition-all hover:scale-125">
                                                <i class="far fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="review-rating-value" value="">
                                </div>

                                <!-- Comment -->
                                <div>
                                    <label for="review-comment" class="block text-sm font-bold text-[#1F3D2E] mb-2">Your
                                        Review</label>
                                    <textarea id="review-comment" name="comment" rows="4"
                                        class="w-full rounded-2xl border border-[#ebd7be]/70 hover:border-[#C65A3A] focus:border-[#C65A3A] focus:ring-1 focus:ring-[#C65A3A] p-5 text-sm resize-none transition duration-200 outline-none"
                                        placeholder="How was the quality? Would you recommend it to others?"></textarea>
                                </div>

                                <button type="submit" id="submit-review-btn"
                                    class="inline-flex items-center gap-2 px-8 py-3.5 bg-(--secondary-color) hover:bg-[#B94E31] text-(--text-light) rounded-2xl font-semibold transition">
                                    Submit Review
                                </button>
                            </form>
                        @else
                            <div class="text-center py-10">
                                <p class="text-[#3A2A1F]/70 mb-4">Login to write a review</p>
                                <button onclick="openLoginModal(null, 'login')"
                                    class="bg-[#1F3D2E] text-white px-8 py-3 rounded-2xl font-semibold">
                                    Login / Register
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Reviews List -->
                <div id="modal-reviews-list"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4 max-h-[420px] overflow-y-auto pr-3 custom-scroll">
                    <!-- Review cards will be injected here -->
                </div>

                <!-- Reviews Pagination -->
                <div id="modal-reviews-pagination" class="flex justify-center gap-2 mt-6 pb-2">
                </div>
            </div>
        </div>
    </div>
</div>

@if (isset($activeProduct))
    <script>
        window.activeProductOnLoad = @json($activeProduct);
    </script>
@else
    <script>
        window.activeProductOnLoad = null;
    </script>
@endif

<script>
    (function() {
        const toggleBtn = document.getElementById('toggle-add-review-btn');
        const reviewSection = document.getElementById('modal-add-review-section');

        if (toggleBtn && reviewSection) {
            toggleBtn.addEventListener('click', function() {
                if (reviewSection.classList.contains('hidden')) {
                    // Before showing the form, check if they are eligible
                    let productId = (window.activeProduct && window.activeProduct.id) ?
                        window.activeProduct.id :
                        (window.activeProductOnLoad ? window.activeProductOnLoad.id : null);

                    if (!productId) {
                        if (window.showToast) window.showToast('Product not found.', 'error');
                        return;
                    }

                    // Disable button temporarily
                    const originalHtml = toggleBtn.innerHTML;
                    toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> loading';
                    toggleBtn.disabled = true;

                    fetch('/product/' + productId + '/can-review')
                        .then(res => res.json())
                        .then(data => {
                            toggleBtn.innerHTML = originalHtml;
                            toggleBtn.disabled = false;

                            if (data.eligible) {
                                reviewSection.classList.remove('hidden');
                                reviewSection.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                // Pre-fill existing review if editing
                                const formTitle = document.getElementById('add-review-title');
                                const submitBtn = document.getElementById('submit-review-btn');
                                const ratingInput = document.getElementById('review-rating-value');
                                const commentInput = document.getElementById('review-comment');
                                const starButtons = document.querySelectorAll('.star-select-btn');

                                if (data.existing && data.review) {
                                    if (formTitle) formTitle.innerHTML =
                                        '<i class="fas fa-edit text-[#C65A3A]"></i> Edit Your Review';
                                    if (submitBtn) submitBtn.textContent = 'Update Review';
                                    if (toggleBtn) toggleBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Review';
                                    if (ratingInput) ratingInput.value = data.review.rating;
                                    if (commentInput) commentInput.value = data.review.comment ||
                                        '';

                                    // Update star visuals
                                    starButtons.forEach((b, index) => {
                                        const icon = b.querySelector('i');
                                        if (index < data.review.rating) {
                                            icon.className = 'fas fa-star text-[#C65A3A]';
                                            b.classList.add('text-yellow-400');
                                        } else {
                                            icon.className =
                                                'far fa-star text-[#3A2A1F]/40';
                                            b.classList.remove('text-yellow-400');
                                        }
                                    });
                                } else {
                                    if (formTitle) formTitle.innerHTML =
                                        '<i class="fas fa-pen-fancy text-[#C65A3A]"></i> Share Your Experience';
                                    if (submitBtn) submitBtn.textContent = 'Submit Review';
                                    if (toggleBtn) toggleBtn.innerHTML = '<i class="fas fa-pen-fancy"></i> Write a Review';
                                    if (ratingInput) ratingInput.value = '';
                                    if (commentInput) commentInput.value = '';

                                    // Reset stars
                                    starButtons.forEach((b) => {
                                        const icon = b.querySelector('i');
                                        icon.className = 'far fa-star text-[#3A2A1F]/40';
                                        b.classList.remove('text-yellow-400');
                                    });
                                }
                            } else {
                                if (window.showToast) {
                                    window.showToast(data.message ||
                                        'You cannot review this product.', 'error');
                                } else {
                                    alert(data.message || 'You cannot review this product.');
                                }
                            }
                        })
                        .catch(err => {
                            toggleBtn.innerHTML = originalHtml;
                            toggleBtn.disabled = false;
                            if (window.showToast) window.showToast(
                                'Something went wrong. Please try again.', 'error');
                        });
                } else {
                    reviewSection.classList.add('hidden');
                }
            });
        }

        // Star selection
        const starButtons = document.querySelectorAll('.star-select-btn');
        const ratingInput = document.getElementById('review-rating-value');

        starButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const rating = btn.dataset.rating;
                ratingInput.value = rating;

                // Update star visuals
                starButtons.forEach((b, index) => {
                    const icon = b.querySelector('i');
                    if (index < rating) {
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        b.classList.add('text-yellow-400');
                    } else {
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        b.classList.remove('text-yellow-400');
                    }
                });
            });
        });
    })();
</script>

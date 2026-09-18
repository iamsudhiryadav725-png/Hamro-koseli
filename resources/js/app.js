//
import './seller-layout';

// ─── Utilities ────────────────────────────────────────────────────────────────
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

function showToast(message, type = 'success') {
    if (typeof Swal !== 'undefined') {
        const typeMap = {
            success: 'success',
            error:   'error',
            status:  'success',
            info:    'info',
            warning: 'warning',
        };
        Swal.fire({
            icon: typeMap[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    } else {
        alert(message);
    }
}

// ─── Drawer ───────────────────────────────────────────────────────────────────
function openDrawer() {
    const drawer  = document.getElementById('mobile-drawer');
    const overlay = document.getElementById('drawer-overlay');
    const hamburger = document.getElementById('hamburger-btn');
    if (drawer)  drawer.classList.add('open');
    if (overlay) overlay.classList.add('open');
    if (hamburger) hamburger.setAttribute('aria-expanded', 'true');
    document.body.style.overflow = 'hidden';
}

function closeDrawer() {
    const drawer    = document.getElementById('mobile-drawer');
    const overlay   = document.getElementById('drawer-overlay');
    const hamburger = document.getElementById('hamburger-btn');
    if (drawer)    drawer.classList.remove('open');
    if (overlay)   overlay.classList.remove('open');
    if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
}

window.showToast = showToast;

// ─── Login Modal ──────────────────────────────────────────────────────────────
let originalUrl = window.location.pathname + window.location.search;
if (originalUrl === '/userlogin' || originalUrl === '/userregister') originalUrl = '/';

function updateUrlState(view) {
    const path = view === 'register' ? '/userregister' : '/userlogin';
    if (window.location.pathname !== path) history.pushState({ modal: view }, '', path);
}

function restoreUrlState() {
    if (window.location.pathname === '/userlogin' || window.location.pathname === '/userregister') {
        history.pushState({ modal: null }, '', originalUrl);
    }
}

function switchModalView(view) {
    const loginView    = document.getElementById('login-view');
    const registerView = document.getElementById('register-view');
    const forgotView   = document.getElementById('forgot-view');
    [loginView, registerView, forgotView].forEach(v => v && v.classList.add('hidden'));
    if (view === 'register' && registerView) registerView.classList.remove('hidden');
    else if (view === 'forgot' && forgotView) forgotView.classList.remove('hidden');
    else if (loginView) loginView.classList.remove('hidden');
}

function openLoginModal(e, view = 'login') {
    if (e) e.preventDefault();
    closeDrawer();
    const loginModal          = document.getElementById('login-modal');
    const loginModalContainer = document.getElementById('login-modal-container');
    if (loginModal && loginModalContainer && !loginModal.classList.contains('hidden')) {
        switchModalView(view);
        if (view === 'register') updateUrlState('register');
        else if (view === 'login') updateUrlState('login');
        return;
    }
    if (loginModal && loginModal.classList.contains('hidden')) {
        const currentPath = window.location.pathname + window.location.search;
        if (currentPath !== '/userlogin' && currentPath !== '/userregister') originalUrl = currentPath;
    }
    if (loginModal && loginModalContainer) {
        switchModalView(view);
        if (view === 'register') updateUrlState('register');
        else if (view === 'login') updateUrlState('login');
        loginModal.classList.remove('hidden');
        loginModal.classList.add('flex');
        setTimeout(() => {
            loginModal.classList.remove('opacity-0');
            loginModal.classList.add('opacity-100');
            loginModalContainer.classList.remove('scale-95', 'opacity-0');
            loginModalContainer.classList.add('scale-100', 'opacity-100');
        }, 10);
        document.body.style.overflow = 'hidden';
    }
}

function closeLoginModal() {
    const loginModal          = document.getElementById('login-modal');
    const loginModalContainer = document.getElementById('login-modal-container');
    if (loginModal && loginModalContainer) {
        restoreUrlState();
        loginModal.classList.remove('opacity-100');
        loginModal.classList.add('opacity-0');
        loginModalContainer.classList.remove('scale-100', 'opacity-100');
        loginModalContainer.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            loginModal.classList.remove('flex');
            loginModal.classList.add('hidden');
            switchModalView('login');
        }, 300);
        document.body.style.overflow = '';
    }
}
window.openLoginModal  = openLoginModal;
window.closeLoginModal = closeLoginModal;

// ─── Product Details Modal ────────────────────────────────────────────────────
function getProductModal()    { return document.getElementById('product-details-modal'); }
function getProductContainer(){ return document.getElementById('product-details-container'); }
function getQtyInput() {
    const modal = getProductModal();
    return modal ? modal.querySelector('.qty-val-input') : null;
}

let originalUrlBeforeProduct = window.location.pathname + window.location.search;
if (originalUrlBeforeProduct.startsWith('/viewdetails/')) originalUrlBeforeProduct = '/shop';

function updateProductUrlState(productId, productSlug) {
    const identifier = productSlug || productId;
    const path = `/viewdetails/${identifier}`;
    if (window.location.pathname !== path) {
        const currentPath = window.location.pathname + window.location.search;
        if (!currentPath.startsWith('/viewdetails/')) originalUrlBeforeProduct = currentPath;
        history.pushState({ productModal: identifier }, '', path);
    }
}

function restoreProductUrlState() {
    if (window.location.pathname.startsWith('/viewdetails/')) {
        history.pushState({ productModal: null }, '', originalUrlBeforeProduct);
    }
}

function populateAndShowProductModal(productData) {
    const productModal     = getProductModal();
    const productContainer = getProductContainer();
    if (!productModal || !productContainer) return;

    // Reset review form
    const reviewForm = document.getElementById('product-review-form');
    if (reviewForm) reviewForm.reset();
    const reviewSuccessMsg = document.getElementById('review-success-message');
    if (reviewSuccessMsg) { reviewSuccessMsg.textContent = ''; reviewSuccessMsg.classList.add('hidden'); }
    const reviewErrorMsg = document.getElementById('review-error-message');
    if (reviewErrorMsg) { reviewErrorMsg.textContent = ''; reviewErrorMsg.classList.add('hidden'); }
    const reviewSection = document.getElementById('modal-add-review-section');
    if (reviewSection) reviewSection.classList.add('hidden');
    const ratingVal = document.getElementById('review-rating-value');
    if (ratingVal) ratingVal.value = '';
    document.querySelectorAll('.star-select-btn').forEach(s => {
        const icon = s.querySelector('i');
        if (icon) icon.className = 'far fa-star text-[#3A2A1F]/40';
        s.classList.remove('text-yellow-400');
    });

    let categoryName = typeof productData.category === 'string' ? productData.category : (productData.category?.cat_name || productData.category?.name || productData.category_name || 'Crafts');
    let vendorName   = typeof productData.vendor === 'string'   ? productData.vendor   : (productData.vendor?.vendor_name || productData.vendor?.business_name || productData.vendor?.name || productData.vendor_name || '');
    let imageUrl     = productData.primary_image_url || (typeof productData.image === 'string' && productData.image.startsWith('http') ? productData.image : (productData.image ? '/' + productData.image.replace(/^\/+/, '') : ''));

    window.activeProduct = {
        id: productData.id, name: productData.name,
        price: parseFloat(productData.price), image: imageUrl,
        category: categoryName, desc: productData.desc || productData.description || '',
        vendor: vendorName, tag: productData.tag || (categoryName === 'Metalware' ? 'Authentic' : 'Handmade')
    };

    const modalProductName          = document.getElementById('modal-product-name');
    const modalMainImage            = document.getElementById('modal-main-image');
    const modalBreadcrumbCat        = document.getElementById('modal-breadcrumb-cat');
    const modalProductDesc          = document.getElementById('modal-product-desc');
    const modalVendorName           = document.getElementById('modal-vendor-name');
    const modalReviewsCount         = document.getElementById('modal-reviews-count');
    const modalProductPrice         = document.getElementById('modal-product-price');
    const modalProductOriginalPrice = document.getElementById('modal-product-original-price');
    const modalStockStatus          = document.getElementById('modal-stock-status');
    const modalStarsContainer       = document.getElementById('modal-stars-container');
    const modalDiscountTag          = document.getElementById('modal-discount-tag');
    const modalSavingsTag           = document.getElementById('modal-savings-tag');

    if (modalProductName)   modalProductName.textContent   = productData.name;
    if (modalMainImage)     { modalMainImage.src = imageUrl; modalMainImage.alt = productData.name; }
    if (modalBreadcrumbCat) modalBreadcrumbCat.textContent = categoryName;

    const descText = productData.desc || productData.description || '';
    if (modalProductDesc) modalProductDesc.innerHTML = descText;
    const modalProductStory = document.getElementById('modal-product-story');
    if (modalProductStory) modalProductStory.innerHTML = descText;

    if (modalVendorName) {
        const vendorCard = document.getElementById('modal-vendor-card');
        const avatarEl   = document.getElementById('modal-vendor-avatar');
        if (vendorName) {
            modalVendorName.textContent = vendorName;
            if (avatarEl)   avatarEl.textContent = vendorName.charAt(0).toUpperCase();
            if (vendorCard) vendorCard.classList.remove('hidden');
        } else {
            if (vendorCard) vendorCard.classList.add('hidden');
        }
    }
    if (modalReviewsCount) modalReviewsCount.textContent = productData.reviews || productData.reviews_count || '0';

    // Pricing
    const price         = Number(productData.price ?? productData.effective_price ?? 0);
    const originalPrice = Number(productData.originalPrice ?? productData.original_price ?? productData.price ?? price);
    const discountPrice = Number(productData.discount_price ?? productData.discountPrice ?? 0);
    const hasDiscount   = productData.discount === 'true'
        || (!isNaN(discountPrice) && discountPrice > 0 && discountPrice < originalPrice)
        || originalPrice > price;

    const displayPrice         = Number.isFinite(price)         ? price         : 0;
    const displayOriginalPrice = Number.isFinite(originalPrice) ? originalPrice : displayPrice;
    const savings              = Math.max(0, displayOriginalPrice - displayPrice);
    const discountPercentage   = displayOriginalPrice > 0 ? Math.round((savings / displayOriginalPrice) * 100) : 0;

    if (modalProductPrice) modalProductPrice.textContent = `Rs. ${displayPrice.toLocaleString()}`;

    if (hasDiscount && modalProductOriginalPrice && savings > 0) {
        modalProductOriginalPrice.textContent = `Rs. ${displayOriginalPrice.toLocaleString()}`;
        modalProductOriginalPrice.classList.remove('hidden');
        if (modalDiscountTag) { modalDiscountTag.textContent = `-${discountPercentage}% OFF`; modalDiscountTag.classList.remove('hidden'); }
        if (modalSavingsTag) modalSavingsTag.classList.add('hidden');
    } else {
        if (modalProductOriginalPrice) modalProductOriginalPrice.classList.add('hidden');
        if (modalDiscountTag) modalDiscountTag.classList.add('hidden');
        if (modalSavingsTag) modalSavingsTag.classList.add('hidden');
    }

    // Export functions to window object
    window.openLoginModal = openLoginModal;
    window.closeLoginModal = closeLoginModal;

    // Use delegated listeners so they survive Livewire wire:navigate DOM swaps
    document.addEventListener('click', function (e) {
        const signinBtn = e.target.closest('#desktop-signin, #mobile-signin');
        if (signinBtn) { openLoginModal(e, 'login'); return; }

        const signupBtn = e.target.closest('#mobile-signup');
        if (signupBtn) { openLoginModal(e, 'register'); return; }

        const closeModalBtn = e.target.closest('#close-login-modal');
        if (closeModalBtn) { closeLoginModal(); return; }
    });

    // Switch to Register View
    const modalShowRegisterBtn = document.getElementById('modal-show-register');
    if (modalShowRegisterBtn) {
        modalShowRegisterBtn.addEventListener('click', function (e) {
            e.preventDefault();
            switchModalView('register');
            updateUrlState('register');
        });
    }

    // Stars
    if (modalStarsContainer) {
        modalStarsContainer.innerHTML = '';
        const rating = parseFloat(productData.rating || 5);
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('i');
            star.style.marginRight = '2px';
            star.className = i <= rating ? 'fas fa-star text-yellow-500' : (i - rating < 1 ? 'fas fa-star-half-alt text-yellow-500' : 'far fa-star text-yellow-500');
            modalStarsContainer.appendChild(star);
        }
    }

    const qtyInput = getQtyInput();
    if (qtyInput) qtyInput.value = 1;

    fetchReviewsForProduct(productData.id);

    const toggleReviewBtn = document.getElementById('toggle-add-review-btn');
    if (toggleReviewBtn) {
        toggleReviewBtn.innerHTML = '<i class="fas fa-pen-fancy"></i> Write a Review';
        if (window.isLoggedIn) {
            fetch('/product/' + productData.id + '/can-review')
                .then(res => res.json())
                .then(data => {
                    if (data.eligible && data.existing) toggleReviewBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Review';
                })
                .catch(() => {});
        }
    }

    // ── Show the modal ──────────────────────────────────────────────────────
    productModal.classList.remove('hidden');
    productModal.classList.add('flex');

    // Use requestAnimationFrame so the browser has painted the flex layout
    // before we try to set scrollTop — this is what fixes the scroll on live
    requestAnimationFrame(() => {
        productModal.scrollTop = 0;
        setTimeout(() => {
            productModal.classList.remove('opacity-0');
            productModal.classList.add('opacity-100');
            productContainer.classList.remove('scale-95', 'opacity-0');
            productContainer.classList.add('scale-100', 'opacity-100');
            productModal.scrollTop = 0;
        }, 10);
    });

    document.body.style.overflow = 'hidden';
}

function closeProductDetailsModal() {
    const productModal     = getProductModal();
    const productContainer = getProductContainer();
    if (!productModal || !productContainer) return;

    restoreProductUrlState();

    productModal.classList.remove('opacity-100');
    productModal.classList.add('opacity-0');
    productContainer.classList.remove('scale-100', 'opacity-100');
    productContainer.classList.add('scale-95', 'opacity-0');

    window.activeProductOnLoad = null;
    window.activeProduct       = null;

    setTimeout(() => {
        productModal.classList.remove('flex', 'block');
        productModal.classList.add('hidden');
        productModal.scrollTop = 0;
    }, 300);
    document.body.style.overflow = '';
}
window.closeProductDetailsModal = closeProductDetailsModal;

// ─── Wishlist ─────────────────────────────────────────────────────────────────
let wishlist = [];

function initWishlist() {
    if (!window.isLoggedIn) {
        localStorage.removeItem('wishlist');
        localStorage.removeItem('wishlist_visited');
        wishlist = [];
    } else {
        wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    }

    if (window.isLoggedIn) {
        fetch('/wishlist/items', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() } })
            .then(res => res.json())
            .then(data => {
                if (data.items) {
                    wishlist = data.items;
                    localStorage.setItem('wishlist', JSON.stringify(wishlist));
                    updateWishlistBadge();
                    syncWishlistIcons();
                    renderWishlistPage();
                }
            })
            .catch(() => {});
    }
}

function updateWishlistBadge() {
    const badge      = document.getElementById('wishlist-badge');
    const headerIcon = document.getElementById('wishlist-header-icon');
    if (badge) {
        const count = wishlist.length;
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('hidden');
            if (headerIcon) { headerIcon.classList.remove('far'); headerIcon.classList.add('fas', 'text-red-400'); }
        } else {
            badge.classList.add('hidden');
            if (headerIcon) { headerIcon.classList.remove('fas', 'text-red-400'); headerIcon.classList.add('far'); }
        }
    }
}

function syncWishlistIcons() {
    document.querySelectorAll('.wishlist-btn').forEach(btn => {
        const productId = btn.getAttribute('data-product-id');
        const icon = btn.querySelector('i');
        if (icon) {
            const inWishlist = wishlist.some(item => String(item.id) === String(productId));
            icon.className = inWishlist ? 'fas fa-heart text-red-500' : 'far fa-heart text-[#C65A3A] hover:text-[#b04a2c]';
        }
    });
}

function toggleWishlistProduct(productData) {
    if (!window.isLoggedIn) {
        showToast('Please sign in to add items to your wishlist.', 'warning');
        if (typeof window.openLoginModal === 'function') window.openLoginModal(null, 'login');
        else window.location.href = window.loginUrl || '/userlogin';
        return false;
    }
    const index = wishlist.findIndex(item => String(item.id) === String(productData.id));
    if (index > -1) {
        wishlist.splice(index, 1);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        showToast(`${productData.name} removed from wishlist.`, 'info');
    } else {
        wishlist.push(productData);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        showToast(`${productData.name} added to wishlist!`, 'success');
    }
    if (window.isLoggedIn) {
        fetch('/wishlist/toggle', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
            body: JSON.stringify({ product_id: productData.id }),
        }).catch(() => {});
    }
    return index === -1;
}

function renderWishlistPage() {
    const wishlistGridContainer = document.getElementById('wishlist-grid-container');
    if (!wishlistGridContainer) return;

    // Seed defaults on first visit
    if (wishlist.length === 0 && !localStorage.getItem('wishlist_visited')) {
        wishlist = [
            { id: "1",   name: "Patan Bronze Bowl",      price: "4500", image: "/images/1st-image.png",  desc: "Hand-hammered ritual vessel by local metalsmiths.", category: "Metalware", tag: "Authentic Patan" },
            { id: "201", name: "Yak Wool Scarf",         price: "3200", image: "/images/4th-image.png",  desc: "100% pure Himalayan wool, naturally dyed.",        category: "Textiles",  tag: "Artisan Made" },
            { id: "202", name: "Traditional Dhaka Topi", price: "1800", image: "/images/Sweaters.png",   desc: "Hand-woven patterns from the Palpa region.",       category: "Textiles" },
            { id: "203", name: "Wild Hemp Backpack",     price: "5600", image: "/images/aboutus.jpg",    desc: "Durable, sustainable, and 100% biodegradable.",    category: "Accessories" }
        ];
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        localStorage.setItem('wishlist_visited', 'true');
    }

    wishlistGridContainer.innerHTML = '';
    if (wishlist.length === 0) {
        const emptyTemplate = document.getElementById('wishlist-empty-template');
        if (emptyTemplate) wishlistGridContainer.appendChild(emptyTemplate.content.cloneNode(true));
        return;
    }

    const grid         = document.createElement('div');
    grid.className     = 'grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6 md:gap-8';
    const cardTemplate = document.getElementById('wishlist-card-template');
    if (!cardTemplate) return;

    wishlist.forEach(item => {
        const clone = cardTemplate.content.cloneNode(true);
        const img = clone.querySelector('.wishlist-img');
        if (img) { img.src = item.image; img.alt = item.name; }
        const tagSpan = clone.querySelector('.wishlist-tag');
        if (tagSpan) { if (item.tag) { tagSpan.textContent = item.tag; tagSpan.classList.remove('hidden'); } else tagSpan.classList.add('hidden'); }
        const title = clone.querySelector('.wishlist-title');
        if (title) title.textContent = item.name;
        const desc = clone.querySelector('.wishlist-desc');
        if (desc) desc.textContent = item.desc || '';
        const price = clone.querySelector('.wishlist-price');
        if (price) price.textContent = `रू ${parseInt(item.price).toLocaleString()}`;
        const deleteBtn = clone.querySelector('.wishlist-delete-btn');
        if (deleteBtn) deleteBtn.addEventListener('click', (e) => { e.preventDefault(); toggleWishlistProduct(item); updateWishlistBadge(); syncWishlistIcons(); renderWishlistPage(); });
        const addCartBtn = clone.querySelector('.wishlist-add-cart-btn');
        if (addCartBtn) addCartBtn.addEventListener('click', () => addToCart({ id: item.id, name: item.name, price: item.price, image: item.image, desc: item.desc, category: item.category, tag: item.tag, specs: item.specs || '' }, 1));
        grid.appendChild(clone);
    });
    wishlistGridContainer.appendChild(grid);
}

// ─── Cart ─────────────────────────────────────────────────────────────────────
let cart        = [];
let dbCartCount = parseInt(window.initialCartCount) || 0;

function initCart() {
    if (window.isLoggedIn) {
        localStorage.removeItem('cart');
        cart = [];
    } else {
        cart = JSON.parse(localStorage.getItem('cart')) || [];
    }
}

function updateCartBadge() {
    const badge      = document.getElementById('cart-badge');
    const headerIcon = document.getElementById('cart-header-icon');
    if (badge) {
        const count = window.isLoggedIn ? dbCartCount : cart.reduce((sum, item) => sum + parseInt(item.qty || 1), 0);
        badge.textContent = count;
        if (count > 0) { badge.classList.remove('hidden'); if (headerIcon) headerIcon.classList.add('text-amber-400'); }
        else           { badge.classList.add('hidden');    if (headerIcon) headerIcon.classList.remove('text-amber-400'); }
    }
}

function addToCartOnServer(productData, qty) {
    const url = window.cartAddUrl || '/cart/add';
    return fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': getCsrfToken() },
        body: JSON.stringify({ product_id: productData.id, variant_id: productData.variantId || null, quantity: qty }),
    })
    .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) { showToast(data.message || 'Could not add this item to your cart.', 'error'); return false; }
        if (typeof data.cart_count === 'number') dbCartCount = data.cart_count; else dbCartCount += qty;
        showToast(data.message || `${productData.name} added to cart!`, 'success');
        updateCartBadge();
        const cartItemsContainer = document.getElementById('cart-items-container');
        if (cartItemsContainer) window.location.reload();
        return true;
    })
    .catch(() => { showToast('Something went wrong adding this item to your cart.', 'error'); return false; });
}

function addToCartAsync(productData, qty = 1) {
    if (!window.isLoggedIn) { window.location.href = window.loginUrl || '/userlogin'; return Promise.resolve(false); }
    qty = parseInt(qty) || 1;
    const isRealProduct = productData.id !== undefined && productData.id !== null && String(productData.id).trim() !== '' && !isNaN(Number(productData.id));
    if (isRealProduct) return addToCartOnServer(productData, qty);

    const index = cart.findIndex(item => String(item.id) === String(productData.id));
    if (index > -1) { cart[index].qty = parseInt(cart[index].qty) + qty; showToast(`Updated quantity of ${productData.name} in cart!`, 'success'); }
    else            { cart.push({ id: productData.id, name: productData.name, price: productData.price, image: productData.image, desc: productData.desc || '', category: productData.category || '', tag: productData.tag || '', specs: productData.specs || '', qty }); showToast(`${productData.name} added to cart!`, 'success'); }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartBadge();
    const cartItemsContainer = document.getElementById('cart-items-container');
    if (cartItemsContainer) renderCartPage();
    return Promise.resolve(true);
}

function addToCart(productData, qty = 1) { addToCartAsync(productData, qty); }

function removeFromCart(productId) {
    const index = cart.findIndex(item => String(item.id) === String(productId));
    if (index > -1) { const name = cart[index].name; cart.splice(index, 1); localStorage.setItem('cart', JSON.stringify(cart)); showToast(`${name} removed from cart.`, 'info'); updateCartBadge(); const c = document.getElementById('cart-items-container'); if (c) renderCartPage(); }
}

function updateCartQuantity(productId, qty) {
    const index = cart.findIndex(item => String(item.id) === String(productId));
    if (index > -1) { cart[index].qty = Math.max(1, parseInt(qty) || 1); localStorage.setItem('cart', JSON.stringify(cart)); updateCartBadge(); const c = document.getElementById('cart-items-container'); if (c) renderCartPage(); }
}

function moveCartItemToWishlist(item) {
    if (!wishlist.find(w => String(w.id) === String(item.id))) { wishlist.push({ id: item.id, name: item.name, price: item.price, image: item.image, desc: item.desc || '', category: item.category || '', tag: item.tag || '' }); localStorage.setItem('wishlist', JSON.stringify(wishlist)); }
    showToast(`${item.name} moved to wishlist!`, 'success');
    const cartIndex = cart.findIndex(c => String(c.id) === String(item.id));
    if (cartIndex > -1) { cart.splice(cartIndex, 1); localStorage.setItem('cart', JSON.stringify(cart)); }
    updateCartBadge(); updateWishlistBadge(); syncWishlistIcons();
    const c = document.getElementById('cart-items-container'); if (c) renderCartPage();
}

function renderCartPage() {
    const cartItemsContainer = document.getElementById('cart-items-container');
    if (!cartItemsContainer) return;
    cartItemsContainer.innerHTML = '';
    if (cart.length === 0) {
        const emptyTemplate = document.getElementById('cart-empty-template');
        if (emptyTemplate) cartItemsContainer.appendChild(emptyTemplate.content.cloneNode(true));
        ['cart-subtotal','cart-total','cart-tax'].forEach(id => { const el = document.getElementById(id); if (el) el.textContent = 'रू 0'; });
        return;
    }
    const cardTemplate = document.getElementById('cart-item-template');
    if (!cardTemplate) return;
    let subtotal = 0;
    cart.forEach(item => {
        const clone = cardTemplate.content.cloneNode(true);
        const img = clone.querySelector('.cart-item-img'); if (img) { img.src = item.image; img.alt = item.name; }
        const tagSpan = clone.querySelector('.cart-item-tag'); const tagText = clone.querySelector('.cart-item-tag-text');
        if (tagSpan) { if (item.tag) { if (tagText) tagText.textContent = item.tag; tagSpan.classList.remove('hidden'); } else tagSpan.classList.add('hidden'); }
        const title = clone.querySelector('.cart-item-title'); if (title) title.textContent = item.name;
        const specs = clone.querySelector('.cart-item-specs'); if (specs) specs.textContent = item.specs || (item.desc || '');
        const price = clone.querySelector('.cart-item-price'); if (price) price.textContent = `रू ${parseInt(item.price).toLocaleString()}`;
        const qtyVal = clone.querySelector('.qty-val'); if (qtyVal) qtyVal.textContent = item.qty;
        const qtyMinus = clone.querySelector('.qty-minus'); if (qtyMinus) qtyMinus.addEventListener('click', (e) => { e.preventDefault(); if (item.qty > 1) updateCartQuantity(item.id, item.qty - 1); });
        const qtyPlus  = clone.querySelector('.qty-plus');  if (qtyPlus)  qtyPlus.addEventListener('click',  (e) => { e.preventDefault(); updateCartQuantity(item.id, item.qty + 1); });
        const wBtn = clone.querySelector('.cart-move-wishlist'); if (wBtn) wBtn.addEventListener('click', (e) => { e.preventDefault(); moveCartItemToWishlist(item); });
        const dBtn = clone.querySelector('.cart-delete-btn');    if (dBtn) dBtn.addEventListener('click', (e) => { e.preventDefault(); removeFromCart(item.id); });
        cartItemsContainer.appendChild(clone);
        subtotal += parseInt(item.price) * parseInt(item.qty);
    });
    const tax   = Math.round(subtotal * 0.0837);
    const total = subtotal + tax;
    const sEl = document.getElementById('cart-subtotal'); if (sEl) sEl.textContent = `रू ${subtotal.toLocaleString()}`;
    const tEl = document.getElementById('cart-tax');      if (tEl) tEl.textContent = `रू ${tax.toLocaleString()}`;
    const ttEl= document.getElementById('cart-total');    if (ttEl) ttEl.textContent = `रू ${total.toLocaleString()}`;
}

window.addToCart           = addToCart;
window.addToCartAsync      = addToCartAsync;
window.updateCartBadge     = updateCartBadge;
window.removeFromCart      = removeFromCart;
window.updateCartQuantity  = updateCartQuantity;
window.moveCartItemToWishlist = moveCartItemToWishlist;

// ─── Reviews AJAX ─────────────────────────────────────────────────────────────
function fetchReviewsForProduct(productId, page = 1) {
    const listContainer  = document.getElementById('modal-reviews-list');
    const pagContainer   = document.getElementById('modal-reviews-pagination');
    const countBadge     = document.getElementById('modal-reviews-count-badge');
    const mainCount      = document.getElementById('modal-reviews-count');
    const starsContainer = document.getElementById('modal-stars-container');
    if (!listContainer) return;

    listContainer.innerHTML = '<p class="text-[#3A2A1F]/60 text-xs italic">Loading customer reviews...</p>';
    if (pagContainer) pagContainer.innerHTML = '';

    fetch('/product/' + productId + '/reviews?page=' + page)
        .then(res => res.json())
        .then(data => {
            const reviews    = data.reviews    || [];
            const pagination = data.pagination || {};
            if (countBadge) countBadge.textContent = pagination.total || reviews.length;
            if (mainCount)  mainCount.textContent  = pagination.total || reviews.length;

            if (starsContainer && reviews.length > 0 && page === 1) {
                const avg = reviews.reduce((sum, r) => sum + r.rating, 0) / reviews.length;
                starsContainer.innerHTML = '';
                for (let i = 1; i <= 5; i++) {
                    const star = document.createElement('i');
                    star.style.marginRight = '2px';
                    star.className = i <= avg ? 'fas fa-star text-yellow-500' : (i - avg < 1 ? 'fas fa-star-half-alt text-yellow-500' : 'far fa-star text-yellow-500');
                    starsContainer.appendChild(star);
                }
            }

            if (reviews.length === 0) { listContainer.innerHTML = '<p class="text-[#3A2A1F]/60 text-xs italic py-4">No reviews yet for this product. Be the first to write one!</p>'; if (pagContainer) pagContainer.innerHTML = ''; return; }

            listContainer.innerHTML = '';
            reviews.forEach(review => {
                let starsHtml = '';
                for (let i = 1; i <= 5; i++) starsHtml += i <= review.rating ? '<i class="fas fa-star text-yellow-500 text-[10px]"></i>' : '<i class="far fa-star text-[#3A2A1F]/20 text-[10px]"></i>';
                const verifiedHtml = review.verified_purchase ? '<span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 uppercase tracking-wider flex items-center gap-1"><i class="fas fa-check-circle"></i> Verified Purchase</span>' : '';
                const replyHtml = review.reply ? `<div class="mt-3 ml-6 bg-[#E5DCD0] border border-[#ebd7be]/30 rounded-xl p-3 space-y-1"><div class="flex items-center justify-between"><span class="text-[10px] font-bold text-[#C65A3A] flex items-center gap-1"><i class="fas fa-reply fa-flip-horizontal"></i> Vendor Response</span><span class="text-[8px] text-[#3A2A1F]/50 font-semibold">${review.replied_at || ''}</span></div><p class="text-xs text-[#3A2A1F]/80 leading-relaxed font-medium">${review.reply}</p></div>` : '';
                const card = document.createElement('div');
                card.className = 'bg-[#FFF7EF] border border-[#ebd7be]/30 rounded-xl p-4 space-y-2';
                card.innerHTML = `<div class="flex items-start justify-between gap-3"><div class="space-y-1"><div class="flex items-center gap-2"><h5 class="text-xs font-bold text-[#1F3D2E]">${review.user_name}</h5>${verifiedHtml}</div><div class="flex items-center gap-2"><div class="flex gap-0.5">${starsHtml}</div><span class="text-[10px] text-[#3A2A1F]/50 font-semibold">${review.date}</span></div></div></div>${review.comment ? `<p class="text-xs text-[#3A2A1F]/80 leading-relaxed font-medium mt-1">"${review.comment}"</p>` : ''}${replyHtml}`;
                listContainer.appendChild(card);
            });

            if (pagContainer) {
                pagContainer.innerHTML = '';
                if (pagination.last_page > 1) {
                    pagContainer.className = 'flex items-center justify-center gap-3 mt-8 pb-4';
                    const prevBtn = document.createElement('button');
                    if (pagination.current_page > 1) { prevBtn.className = 'w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm cursor-pointer'; prevBtn.onclick = () => fetchReviewsForProduct(productId, pagination.current_page - 1); }
                    else prevBtn.className = 'w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed';
                    prevBtn.innerHTML = '<i class="fas fa-chevron-left text-xs"></i>';
                    pagContainer.appendChild(prevBtn);

                    const pageNumbersContainer = document.createElement('div');
                    pageNumbersContainer.className = 'flex items-center gap-1';
                    const start = Math.max(1, pagination.current_page - 2);
                    const end   = Math.min(pagination.last_page, pagination.current_page + 2);
                    if (start > 1) { const b = document.createElement('button'); b.className = 'w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors cursor-pointer'; b.textContent = '1'; b.onclick = () => fetchReviewsForProduct(productId, 1); pageNumbersContainer.appendChild(b); if (start > 2) { const d = document.createElement('span'); d.className = 'text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none'; d.textContent = '...'; pageNumbersContainer.appendChild(d); } }
                    for (let p = start; p <= end; p++) { const pb = document.createElement('button'); if (p === pagination.current_page) { pb.className = 'w-10 h-10 flex flex-col items-center justify-center text-sm font-bold text-[#1F3D2E] relative cursor-pointer'; pb.innerHTML = `<span>${p}</span><span class="absolute bottom-1 w-5 h-0.5 bg-[#1F3D2E] rounded-full"></span>`; } else { pb.className = 'w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors cursor-pointer'; pb.textContent = p; pb.onclick = () => fetchReviewsForProduct(productId, p); } pageNumbersContainer.appendChild(pb); }
                    if (end < pagination.last_page) { if (end < pagination.last_page - 1) { const d = document.createElement('span'); d.className = 'text-sm font-semibold text-[#3A2A1F]/40 px-2 select-none'; d.textContent = '...'; pageNumbersContainer.appendChild(d); } const lb = document.createElement('button'); lb.className = 'w-10 h-10 flex items-center justify-center text-sm font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition-colors cursor-pointer'; lb.textContent = pagination.last_page; lb.onclick = () => fetchReviewsForProduct(productId, pagination.last_page); pageNumbersContainer.appendChild(lb); }
                    pagContainer.appendChild(pageNumbersContainer);

                    const nextBtn = document.createElement('button');
                    if (pagination.current_page < pagination.last_page) { nextBtn.className = 'w-10 h-10 rounded-full border border-[#1F3D2E]/20 flex items-center justify-center text-[#1F3D2E] hover:border-[#1F3D2E] hover:bg-[#1F3D2E]/5 transition duration-300 shadow-sm cursor-pointer'; nextBtn.onclick = () => fetchReviewsForProduct(productId, pagination.current_page + 1); }
                    else nextBtn.className = 'w-10 h-10 rounded-full border border-[#1F3D2E]/10 flex items-center justify-center text-[#1F3D2E]/30 shadow-sm cursor-not-allowed';
                    nextBtn.innerHTML = '<i class="fas fa-chevron-right text-xs"></i>';
                    pagContainer.appendChild(nextBtn);
                }
            }
        })
        .catch(() => { if (listContainer) listContainer.innerHTML = '<p class="text-red-500 text-xs py-4">Failed to load reviews.</p>'; });
}

// ─── Password Toggle Helper ───────────────────────────────────────────────────
function setupPasswordToggle(toggleBtn, passwordInput) {
    if (!toggleBtn || !passwordInput) return;
    const icon = toggleBtn.querySelector('i');
    const showPw = (e) => { if (e) e.preventDefault(); passwordInput.type = 'text';     if (icon) icon.className = 'fas fa-eye text-sm'; };
    const hidePw = (e) => { if (e) e.preventDefault(); passwordInput.type = 'password'; if (icon) icon.className = 'far fa-eye-slash text-sm'; };
    toggleBtn.addEventListener('mousedown', showPw);
    toggleBtn.addEventListener('mouseup',   hidePw);
    toggleBtn.addEventListener('mouseleave',hidePw);
    toggleBtn.addEventListener('touchstart',showPw);
    toggleBtn.addEventListener('touchend',  hidePw);
    toggleBtn.addEventListener('touchcancel',hidePw);
    toggleBtn.addEventListener('click', (e) => e.preventDefault());
}

// ─── Page-level initialisation (runs on first load AND after every wire:navigate) ──
function initPage() {
    // NOTE: Hamburger, close-drawer, overlay, and drawer-link clicks are handled
    // by the global delegated listener on `document` (see below). Do NOT add
    // direct addEventListener calls here — initPage() runs on every
    // livewire:navigated event, so doing so stacks duplicate listeners that
    // cause the drawer to open-and-immediately-close on mobile.

    const mobileSearchBtn   = document.getElementById('mobile-search-btn');
    const mobileSearchBar   = document.getElementById('mobile-search-bar');
    const mobileSearchInput = document.getElementById('mobile-search');

    if (mobileSearchBtn && mobileSearchBar) {
        // Use a named handler so we can avoid stacking duplicates
        if (!mobileSearchBtn._searchHandlerBound) {
            mobileSearchBtn._searchHandlerBound = true;
            mobileSearchBtn.addEventListener('click', () => {
                const hidden = mobileSearchBar.classList.contains('hidden');
                mobileSearchBar.classList.toggle('hidden', !hidden);
                if (hidden && mobileSearchInput) mobileSearchInput.focus();
            });
        }
    }

    // Password toggles
    setupPasswordToggle(document.getElementById('modal-toggle-password'),          document.getElementById('modal-password'));
    setupPasswordToggle(document.getElementById('modal-register-toggle-password'),  document.getElementById('modal-register-password'));
    setupPasswordToggle(document.getElementById('modal-register-toggle-password-confirm'), document.getElementById('modal-register-password_confirmation'));

    // Phone format
    const phoneInput = document.getElementById('modal-register-phone');
    if (phoneInput) phoneInput.addEventListener('input', function () { let v = this.value.replace(/\D/g, ''); if (v.length > 10) v = v.substring(0, 10); this.value = v; });

    // Ensure body scroll is unlocked after navigation
    document.body.style.overflow = '';

    // Sync badges & icons
    updateWishlistBadge();
    updateCartBadge();
    syncWishlistIcons();
    renderWishlistPage();
    renderCartPage();

    // Flash messages are handled exclusively by initFlashMessages() in the layout,
    // which reads from the <template id="__flash_messages"> body tag (refreshed on
    // every Livewire navigation). Removed duplicate handling here to prevent double toasts.

    // Auto-open login modal if on login/register route
    checkPathAndOpenModal();

    // Forgot password form
    initForgotPasswordForm();
}

let _loginModalAutoOpened = false;

function resetLoginModalAutoOpen() {
    _loginModalAutoOpened = false;
}

function showValidationErrorToasts() {
    const tpl = document.getElementById('__validation_errors');
    if (!tpl || typeof window.showToast !== 'function') return;
    try {
        const errors = JSON.parse(tpl.dataset.errors);
        if (errors.length > 0) window.showToast(errors[0], 'error');
    } catch (e) {}
}

function checkPathAndOpenModal() {
    if (_loginModalAutoOpened) return;

    const path = window.location.pathname;
    let view = null;

    if (path === '/userlogin' || path === '/login') view = 'login';
    else if (path === '/userregister') view = 'register';

    if (!view) {
        const stateTpl = document.getElementById('__login_modal_state');
        if (stateTpl?.dataset.openView) view = stateTpl.dataset.openView;
    }

    if (view) {
        _loginModalAutoOpened = true;
        openLoginModal(null, view);
        showValidationErrorToasts();
    }
}

function initForgotPasswordForm() {
    const forgotForm   = document.getElementById('forgot-password-form');
    const forgotResend = document.getElementById('forgot-resend');
    if (forgotResend) forgotResend.addEventListener('click', () => { document.getElementById('forgot-success')?.classList.add('hidden'); document.getElementById('forgot-form-wrap')?.classList.remove('hidden'); });
    if (!forgotForm) return;
    forgotForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const btn     = document.getElementById('forgot-submit-btn');
        const btnText = document.getElementById('forgot-btn-text');
        const email   = document.getElementById('forgot-email').value;
        btn.disabled  = true; btnText.textContent = 'Sending…';
        const iconEl  = btn.querySelector('i'); if (iconEl) iconEl.className = 'fas fa-spinner fa-spin text-xs';
        forgotForm.querySelectorAll('.forgot-error').forEach(el => el.remove());
        try {
            const res  = await fetch(forgotForm.action, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }, body: JSON.stringify({ email }) });
            const json = await res.json();
            if (res.ok && (json.status || json.status === true)) {
                document.getElementById('forgot-form-wrap')?.classList.add('hidden');
                const sentEl = document.getElementById('forgot-sent-email'); if (sentEl) sentEl.textContent = email;
                document.getElementById('forgot-success')?.classList.remove('hidden');
            } else {
                const errMsg = json.errors?.email?.[0] ?? json.message ?? 'Something went wrong. Please try again.';
                const errEl  = document.createElement('p'); errEl.className = 'forgot-error text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1'; errEl.innerHTML = '<i class="fas fa-exclamation-circle text-[10px]"></i> ' + errMsg;
                document.getElementById('forgot-email')?.closest('div[class*="flex"]')?.after(errEl);
                btn.disabled = false; btnText.textContent = 'SEND RESET LINK'; if (iconEl) iconEl.className = 'fas fa-paper-plane text-xs';
            }
        } catch { btn.disabled = false; btnText.textContent = 'SEND RESET LINK'; if (iconEl) iconEl.className = 'fas fa-paper-plane text-xs'; }
    });
}

// ─── Global delegated event listeners (registered once, survive navigation) ───
document.addEventListener('click', function (e) {
    // Drawer triggers
    if (e.target.closest('#hamburger-btn'))    { openDrawer();  return; }
    if (e.target.closest('#close-drawer-btn')) { closeDrawer(); return; }
    if (e.target.closest('#drawer-overlay'))   { closeDrawer(); return; }
    // Close drawer when any nav link inside it is tapped
    const drawerLink = e.target.closest('#mobile-drawer a');
    if (drawerLink) { closeDrawer(); return; }

    // Login modal triggers
    if (e.target.closest('#desktop-signin, #mobile-signin')) { openLoginModal(e, 'login');    return; }
    if (e.target.closest('#mobile-signup'))                  { openLoginModal(e, 'register'); return; }
    if (e.target.closest('#close-login-modal'))              { closeLoginModal();              return; }
    const regBtn = e.target.closest('#modal-show-register'); if (regBtn) { e.preventDefault(); switchModalView('register'); updateUrlState('register'); return; }
    const logBtn = e.target.closest('#modal-show-login');    if (logBtn) { e.preventDefault(); switchModalView('login');    updateUrlState('login');    return; }
    const forgotBtn = e.target.closest('#modal-show-forgot'); if (forgotBtn) { e.preventDefault(); switchModalView('forgot'); return; }
    const backBtn   = e.target.closest('#forgot-back-to-login'); if (backBtn) { e.preventDefault(); switchModalView('login'); updateUrlState('login'); return; }
    const loginModal = document.getElementById('login-modal');
    if (loginModal && e.target === loginModal) { closeLoginModal(); return; }

    // Cart / wishlist header gating
    const cartTarget = e.target.closest('#cart-header-btn, a[href*="/cart"]');
    if (cartTarget && !window.isLoggedIn) { e.preventDefault(); e.stopPropagation(); if (typeof window.openLoginModal === 'function') window.openLoginModal(null, 'login'); else window.location.href = window.loginUrl || '/userlogin'; return; }
    const wishlistTarget = e.target.closest('#wishlist-header-btn, a[href*="/wishlist"]');
    if (wishlistTarget && !window.isLoggedIn) { e.preventDefault(); e.stopPropagation(); if (typeof window.openLoginModal === 'function') window.openLoginModal(null, 'login'); else window.location.href = window.loginUrl || '/userlogin'; return; }

    // Wishlist toggle buttons
    const wBtn = e.target.closest('.wishlist-btn');
    if (wBtn) {
        e.preventDefault(); e.stopPropagation(); e._handledByWishlist = true;
        const added = toggleWishlistProduct({ id: wBtn.getAttribute('data-product-id'), name: wBtn.getAttribute('data-product-name'), slug: wBtn.getAttribute('data-slug'), price: wBtn.getAttribute('data-product-price'), image: wBtn.getAttribute('data-product-image'), desc: wBtn.getAttribute('data-product-desc'), category: wBtn.getAttribute('data-product-category'), tag: wBtn.getAttribute('data-product-tag') });
        updateWishlistBadge(); syncWishlistIcons();
        const wg = document.getElementById('wishlist-grid-container'); if (wg) renderWishlistPage();
        return;
    }

    // Add-to-cart buttons
    const addBtn = e.target.closest('.add-to-cart-btn');
    if (addBtn) {
        e.preventDefault(); e.stopPropagation();
        if (!window.isLoggedIn) { if (typeof window.openLoginModal === 'function') window.openLoginModal(null, 'login'); else window.location.href = window.loginUrl || '/userlogin'; return; }
        addToCart({ id: addBtn.getAttribute('data-product-id'), name: addBtn.getAttribute('data-product-name'), price: addBtn.getAttribute('data-product-price'), image: addBtn.getAttribute('data-product-image'), desc: addBtn.getAttribute('data-product-desc') || '', category: addBtn.getAttribute('data-product-category') || '', tag: addBtn.getAttribute('data-product-tag') || '', specs: addBtn.getAttribute('data-product-specs') || '' }, parseInt(addBtn.getAttribute('data-product-qty') || '1'));
        return;
    }

    // View Details buttons
    const vBtn = e.target.closest('.view-details-btn');
    if (vBtn) {
        if (e.target.closest('.wishlist-btn') || e._handledByWishlist) return;
        const href = vBtn.getAttribute('href');
        const slug = vBtn.getAttribute('data-slug') || vBtn.getAttribute('data-id');
        if (href && href !== '#' && !href.startsWith('javascript:')) {
            return; // Normal anchor link navigation to /viewdetails/...
        } else if (slug) {
            e.preventDefault();
            window.location.href = `/viewdetails/${slug}`;
            return;
        }
    }

    // Product modal close
    if (e.target.closest('#close-product-details') || e.target.matches('#product-details-modal')) { closeProductDetailsModal(); return; }

    // Qty controls inside product modal
    if (e.target.closest('.qty-plus-btn'))  { const qi = getQtyInput(); if (qi) qi.value = parseInt(qi.value) + 1; return; }
    if (e.target.closest('.qty-minus-btn')) { const qi = getQtyInput(); if (qi) { const v = parseInt(qi.value); if (v > 1) qi.value = v - 1; } return; }

    // Add to Cart inside modal
    if (e.target.closest('#modal-add-to-cart-btn')) {
        if (!window.isLoggedIn) { window.location.href = window.loginUrl || '/userlogin'; return; }
        const qi = getQtyInput();
        if (window.activeProduct && typeof window.addToCart === 'function') { window.addToCart(window.activeProduct, qi ? (parseInt(qi.value) || 1) : 1); closeProductDetailsModal(); }
        return;
    }

    // Buy Now inside modal
    if (e.target.closest('#modal-buy-now-btn')) {
        if (!window.isLoggedIn) { window.location.href = window.loginUrl || '/userlogin'; return; }
        const qi = getQtyInput();
        if (window.activeProduct && typeof window.addToCartAsync === 'function') { window.addToCartAsync(window.activeProduct, qi ? (parseInt(qi.value) || 1) : 1).then(() => { window.location.href = '/cart'; }); }
        return;
    }

    // Tab switching inside product modal
    const tabBtn = e.target.closest('.tab-btn');
    if (tabBtn) {
        const modal = getProductModal();
        if (modal) {
            modal.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('text-[#C65A3A]', 'border-b-2', 'border-[#C65A3A]', 'font-bold'); b.classList.add('text-[#3A2A1F]/60', 'font-semibold'); });
            tabBtn.classList.add('text-[#C65A3A]', 'border-b-2', 'border-[#C65A3A]', 'font-bold'); tabBtn.classList.remove('text-[#3A2A1F]/60', 'font-semibold');
            const target = tabBtn.getAttribute('data-tab');
            modal.querySelectorAll('.tab-panel').forEach(panel => { if (panel.getAttribute('data-panel') === target) panel.classList.remove('hidden'); else panel.classList.add('hidden'); });
        }
        return;
    }

    // Star rating selection
    const starBtn = e.target.closest('.star-select-btn');
    if (starBtn) {
        e.preventDefault();
        const rv = document.getElementById('review-rating-value'); if (!rv) return;
        const rating = parseInt(starBtn.getAttribute('data-rating')); rv.value = rating;
        document.querySelectorAll('.star-select-btn').forEach(s => { const r = parseInt(s.getAttribute('data-rating')); const icon = s.querySelector('i'); if (icon) icon.className = r <= rating ? 'fas fa-star text-[#C65A3A]' : 'far fa-star text-[#3A2A1F]/40'; });
        return;
    }

    // Toggle review section
    const toggleReviewBtn = e.target.closest('#toggle-add-review-btn');
    if (toggleReviewBtn) {
        const reviewSection = document.getElementById('modal-add-review-section');
        if (reviewSection) reviewSection.classList.toggle('hidden');
        return;
    }
});

document.addEventListener('change', function (e) {
    if (e.target.matches('.qty-val-input')) { let val = parseInt(e.target.value) || 1; if (val < 1) val = 1; e.target.value = val; }
});

document.addEventListener('submit', function (e) {
    const reviewForm = e.target.closest('#product-review-form');
    if (!reviewForm) return;
    e.preventDefault();
    const ratingVal  = document.getElementById('review-rating-value');
    const commentEl  = document.getElementById('review-comment');
    const errorMsg   = document.getElementById('review-error-message');
    const successMsg = document.getElementById('review-success-message');
    const submitBtn  = document.getElementById('submit-review-btn');
    if (!ratingVal || !submitBtn) return;
    const rating  = ratingVal.value;
    const comment = commentEl ? commentEl.value : '';
    if (!rating) { if (window.showToast) window.showToast('Please select a rating star.', 'error'); else if (errorMsg) { errorMsg.textContent = 'Please select a rating star.'; errorMsg.classList.remove('hidden'); } return; }
    if (errorMsg)   errorMsg.classList.add('hidden');
    if (successMsg) successMsg.classList.add('hidden');
    submitBtn.disabled = true; submitBtn.textContent = 'Submitting…';
    const productId = window.activeProduct?.id || window.activeProductOnLoad?.id;
    if (!productId) { submitBtn.disabled = false; submitBtn.textContent = 'Submit Review'; if (window.showToast) window.showToast('Product ID not found.', 'error'); return; }
    fetch('/product/' + productId + '/reviews', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() }, body: JSON.stringify({ rating, comment }) })
        .then(res => res.json())
        .then(data => {
            submitBtn.disabled = false; submitBtn.textContent = 'Submit Review';
            if (data.success) {
                if (window.showToast) window.showToast(data.message || 'Review submitted successfully!', 'success'); else if (successMsg) { successMsg.textContent = data.message; successMsg.classList.remove('hidden'); }
                reviewForm.reset();
                document.querySelectorAll('.star-select-btn').forEach(s => { const icon = s.querySelector('i'); if (icon) icon.className = 'far fa-star text-[#3A2A1F]/40'; });
                if (ratingVal) ratingVal.value = '';
                fetchReviewsForProduct(productId);
                const trb = document.getElementById('toggle-add-review-btn'); if (trb) trb.innerHTML = '<i class="fas fa-edit"></i> Edit Review';
                const rs  = document.getElementById('modal-add-review-section'); if (rs) rs.classList.add('hidden');
            } else {
                if (window.showToast) window.showToast(data.message || 'Something went wrong.', 'error'); else if (errorMsg) { errorMsg.textContent = data.message || 'Something went wrong.'; errorMsg.classList.remove('hidden'); }
            }
        })
        .catch(() => { submitBtn.disabled = false; submitBtn.textContent = 'Submit Review'; if (window.showToast) window.showToast('Server error. Please try again.', 'error'); });
});

// ─── History / popstate ───────────────────────────────────────────────────────
window.addEventListener('popstate', function (e) {
    const path = window.location.pathname;
    if (path === '/userlogin' || path === '/login') openLoginModal(null, 'login');
    else if (path === '/userregister')              openLoginModal(null, 'register');
    else {
        const loginModal = document.getElementById('login-modal');
        if (loginModal && !loginModal.classList.contains('hidden')) closeLoginModal();
    }
    const currentModal = getProductModal();
    if (currentModal && !currentModal.classList.contains('hidden')) closeProductDetailsModal();
});

// ─── Livewire Navigate hooks ───────────────────────────────────────────────────
document.addEventListener('livewire:navigating', function () {
    resetLoginModalAutoOpen();
    window._flashMessagesShown = false;
    // Close any open modal before leaving the page
    window.activeProductOnLoad = null;
    window.activeProduct       = null;
    const currentModal = getProductModal();
    if (currentModal && !currentModal.classList.contains('hidden')) closeProductDetailsModal();
    // Always unlock body scroll on navigation
    document.body.style.overflow = '';
});

document.addEventListener('livewire:navigated', function () {
    // Re-run page init so all DOM-dependent code works on the new page
    initPage();

    const path = window.location.pathname;
    if (path.startsWith('/viewdetails/') && window.activeProductOnLoad) {
        populateAndShowProductModal(window.activeProductOnLoad);
    } else if (!path.startsWith('/viewdetails/')) {
        window.activeProductOnLoad = null;
        window.activeProduct       = null;
        const currentModal = getProductModal();
        if (currentModal && !currentModal.classList.contains('hidden')) closeProductDetailsModal();
    }
});

// ─── Boot on first load ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    initWishlist();
    initCart();
    initPage();
});

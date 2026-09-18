<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ChatBotController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\EsewaPaymentController;
use App\Http\Controllers\KhaltiPaymentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRegisterController;
use App\Http\Controllers\VendorPasswordResetController;
use App\Http\Controllers\VendorRegisterController;
use App\Http\Middleware\EnsureVendor;
use Illuminate\Support\Facades\Route;

// ─── Seller Auth (guest only, rate limited) ───────────────────────────────────
Route::middleware(['guest:vendor', 'throttle:auth'])->group(function () {
    Route::get('/seller-login', [SellerController::class, 'login'])->name('seller.login');
    Route::post('/seller-login', [SellerController::class, 'loginSubmit'])->name('seller.login.submit');

    Route::get('/seller/forgot-password', [VendorPasswordResetController::class, 'showForgotForm'])->name('seller.password.request');
    Route::post('/seller/forgot-password', [VendorPasswordResetController::class, 'sendResetLink'])->name('seller.password.email');
    Route::get('/seller/reset-password/{token}', [VendorPasswordResetController::class, 'showResetForm'])->name('seller.password.reset');
    Route::post('/seller/reset-password', [VendorPasswordResetController::class, 'resetPassword'])->name('seller.password.update');
});

// ─── Seller logout (outside guest guard so logged-in vendor can hit it) ───────
Route::post('/seller-logout', [SellerController::class, 'logout'])->name('seller.logout');

// ─── Seller profile & password (auth protected — fix #3) ─────────────────────
Route::middleware(['auth:vendor', EnsureVendor::class])->group(function () {
    Route::get('/seller-dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
    Route::get('/seller-dashboard/sales-trend', [SellerController::class, 'salesTrendData'])->name('dashboard.sales-trend');
    Route::get('/product-management', [SellerController::class, 'product_management'])->name('product-management');
    Route::get('/create-product', [SellerController::class, 'productCreate'])->name('product-create');
    Route::post('/create-product', [SellerController::class, 'store'])->name('product.store');
    Route::get('/edit-product/{id}', [SellerController::class, 'productEdit'])->name('product-edit');
    Route::put('/product/{id}', [SellerController::class, 'update'])->name('product.update');
    Route::delete('/product/{id}', [SellerController::class, 'destroy'])->name('product.destroy');
    Route::get('/orders', [SellerController::class, 'order'])->name('order');
    Route::get('/order-details', [SellerController::class, 'orderDetails'])->name('order-details');
    Route::post('/order-details/{order}/payment-status', [SellerController::class, 'updatePaymentStatus'])->name('order-details.payment-status');
    Route::post('/order-details/{order}/order-status', [SellerController::class, 'updateOrderStatus'])->name('order-details.order-status');
    Route::get('/seller-review', [SellerController::class, 'sellerReview'])->name('seller.review');
    Route::post('/seller-review/{id}/reply', [SellerController::class, 'replyToReview'])->name('seller.review.reply');
    Route::delete('/seller-review/{id}', [SellerController::class, 'destroyReview'])->name('seller.review.destroy');
    Route::get('/seller-payments', [SellerController::class, 'sellerPayment'])->name('seller.payment');
    Route::get('/seller-payments/pay-commission', [SellerController::class, 'payCommission'])->name('seller.pay-commission.get');
    Route::post('/seller-payments/pay-commission', [SellerController::class, 'payCommission'])->name('seller.pay-commission');
    Route::get('/seller-payments/khalti/callback', [SellerController::class, 'khaltiCallback'])->name('seller.payment.khalti.callback');
    Route::get('/seller/payment/{id}/details', [SellerController::class, 'paymentDetails'])->name('payment-details');
    Route::get('/seller-support', [SellerController::class, 'sellerSupport'])->name('seller-support');
    Route::get('/create-ticket', [SellerController::class, 'createTicket'])->name('create-ticket');
    Route::post('/create-ticket', [SellerController::class, 'storeTicket'])->name('store-ticket');
    Route::get('/tickets', [SellerController::class, 'sellerTicket'])->name('seller-ticket');
    Route::get('/seller-notification', [SellerController::class, 'sellerNotification'])->name('seller-notification');
    Route::patch('/seller-notification/read-all', [SellerController::class, 'markAllNotificationsRead'])->name('seller.notifications.markAllRead');
    Route::patch('/seller-notification/{id}/read', [SellerController::class, 'markNotificationRead'])->name('seller.notifications.read');
    Route::patch('/seller-notification/{slug}/read', [SellerController::class, 'markNotificationRead'])->name('seller.notifications.read');

    // ── Profile & password moved here so they're auth-guarded (was QA issue #3) ─
    Route::get('/seller-profile', [SellerController::class, 'sellerProfile'])->name('seller.profile');
    Route::post('/seller-profile', [SellerController::class, 'updateProfile'])->name('seller.profile.update');
    Route::post('/seller-password', [SellerController::class, 'updatePassword'])->name('seller.profile.password');
});

// ─── Seller registration (public) ─────────────────────────────────────────────
Route::get('/seller', [SellerController::class, 'seller'])->name('seller');

// ─── Public routes ────────────────────────────────────────────────────────────
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/categories', [PageController::class, 'categories'])->name('categories');
Route::get('/shop', [PageController::class, 'shop'])->name('shop');
Route::get('/new-arrivals', [PageController::class, 'new_arrival'])->name('new-arrivals');
Route::get('/todays-deals', [PageController::class, 'todays_deals'])->name('todays-deals');
Route::get('/top-sellers', [PageController::class, 'top_sellers'])->name('top-sellers');
Route::get('/about-us', [PageController::class, 'about_us'])->name('about-us');
Route::get('/privacypolicy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/contact-us', [PageController::class, 'contactus'])->name('contact-us');
Route::get('/viewdetails/{id}', [PageController::class, 'viewProduct'])->name('viewdetails');
Route::get('/product/{id}/reviews', [PageController::class, 'getProductReviews'])->name('product.reviews');
Route::get('/viewdetails/{slug}', [PageController::class, 'viewProduct'])->name('viewdetails');
Route::get('/terms_&_conditions', [PageController::class, 'terms_conditions'])->name('terms&conditions');
Route::get('/return_&_refund', [PageController::class, 'return_policy'])->name('return&refund');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/FAQ', fn () => redirect()->route('faq')); // ← duplicate fixed: redirect instead of re-registering
Route::get('/seller_policy', [PageController::class, 'seller_policy'])->name('seller-policy');
Route::get('/product/{id}/can-review', [PageController::class, 'canReviewProduct'])->name('product.reviews.check');
Route::get('/shipping-info', [PageController::class, 'shipping_policy'])->name('shipping-policy');

// ─── Requires login ───────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [PageController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle', [UserController::class, 'toggleWishlist'])->name('wishlist.toggle');
    Route::get('/wishlist/items', [UserController::class, 'wishlistItems'])->name('wishlist.items');
    Route::get('/cart', [CartController::class, 'index'])->name('cart');
    Route::post('/cart/add', [CartController::class, 'store'])->name('cart.add');
    Route::post('/product/{id}/reviews', [PageController::class, 'storeProductReview'])->name('product.reviews.store');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.remove');

    // ── Checkout ──────────────────────────────────────────────────────────────
    Route::get('/checkout/{cart}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout/{cart}', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/{cart}/save-user-info', [CheckoutController::class, 'saveUserInfo'])->name('checkout.save-user-info');

    Route::get('/checkout/vendor/{vendor}', [CheckoutController::class, 'showVendor'])->name('checkout.show.vendor');
    Route::post('/checkout/vendor/{vendor}', [CheckoutController::class, 'storeVendor'])->name('checkout.store.vendor');
    Route::post('/checkout/vendor/{vendor}/save-user-info', [CheckoutController::class, 'saveVendorUserInfo'])->name('checkout.save-user-info.vendor');

    Route::get('/order/{order}/confirmation', [CheckoutController::class, 'confirmation'])->name('order.confirmation');

    // ── Payments (rate limited separately) ────────────────────────────────────
    Route::middleware('throttle:payment')->group(function () {
        Route::get('/payment/khalti/{order}/initiate', [KhaltiPaymentController::class, 'initiate'])->name('khalti.initiate');
        Route::get('/payment/khalti/callback', [KhaltiPaymentController::class, 'callback'])->name('khalti.callback');
        Route::get('/payment/esewa/{order}/initiate', [EsewaPaymentController::class, 'initiate'])->name('esewa.initiate');
        Route::get('/payment/esewa/callback', [EsewaPaymentController::class, 'callback'])->name('esewa.callback');
    });

    // ── User dashboard & profile ───────────────────────────────────────────────
    Route::get('/user-dashboard', [UserController::class, 'dashboard'])->name('Userdashboard');
    Route::get('/user-orders', [UserController::class, 'orders'])->name('User-orders');
    Route::get('/user-order-details', [UserController::class, 'orderDetail'])->name('order-detail');
    Route::patch('/user-orders/{order}/cancel', [UserController::class, 'cancelOrder'])->name('order.cancel');
    Route::get('/user-profile', [UserController::class, 'userProfile'])->name('user-profile');
    Route::post('/user-profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/user-password', [UserController::class, 'updatePassword'])->name('user.password.update');
    Route::get('/user-notification', [UserController::class, 'userNotification'])->name('user-notification');
    Route::post('/user-notification/mark-all-read', [UserController::class, 'markAllNotificationsRead'])->name('user.notifications.markAllRead');
    Route::patch('/user-notification/{slug}/read', [UserController::class, 'markNotificationRead'])->name('user.notifications.read');
});

// ─── Google OAuth (outside guest middleware — callback runs after Google sets auth) ──
Route::get('/google/redirect', [AuthController::class, 'redirect'])->name('google.redirect');
Route::get('/google/callback', [AuthController::class, 'callback'])->name('google.callback');

// ─── User Auth — GET pages are guest-only, POST login is open so that a vendor
//     already logged in on the vendor guard can ALSO log in as a user (web guard).
//     Both guards share the same PHP session but use independent session keys,
//     so there is no conflict. AuthController::login() itself checks whether the
//     web guard is already authenticated and redirects home if so.
// ─────────────────────────────────────────────────────────────────────────────

// GET-only guest routes (show login/register pages — redirect away if already on web guard)
Route::middleware(['guest', 'throttle:auth'])->group(function () {
    Route::get('/userlogin', [AuthController::class, 'showLogin'])->name('userlogin');
    Route::get('/userregister', [PageController::class, 'home'])->name('userregister');
    Route::get('/login', fn () => redirect()->route('userlogin'))->name('login');

    Route::get('/vendor/register', [VendorRegisterController::class, 'show'])->name('vendor.register');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->name('password.update');
});

// POST login routes — outside guest middleware so a vendor (authenticated on vendor
// guard only, NOT on web guard) can submit credentials and log in as a user too.
// Rate-limited the same as the guest block above.
Route::middleware('throttle:auth')->group(function () {
    Route::post('/userlogin', [AuthController::class, 'login']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/userregister', [UserRegisterController::class, 'register'])->name('userregister.post');
    Route::post('/register', [UserRegisterController::class, 'register'])->name('register');
    Route::post('/vendor/register', [VendorRegisterController::class, 'register'])->name('vendor.register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::redirect('/login.php', '/userlogin');

Route::get('/sitemap.xml', [PageController::class, 'sitemap'])->name('sitemap');
Route::post('/chatbot/send', [ChatBotController::class, 'send'])->name('chatbot.send');

Route::post('/contact-us', [PageController::class, 'contactusSubmit'])->name('contact-us.submit');


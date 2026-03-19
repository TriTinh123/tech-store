<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\ThreeFactorController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\OrderReturnController;
use App\Http\Controllers\PurchaseRiskController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ComparisonController;
use Illuminate\Support\Facades\Route;

// Coupon AJAX
Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply')->middleware('auth');

// Wishlist (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

// Order Returns (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/orders/{order}/return', [OrderReturnController::class, 'create'])->name('orders.return.create');
    Route::post('/orders/{order}/return', [OrderReturnController::class, 'store'])->name('orders.return.store');
});

// Notifications (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/recent', [NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Comparison (auth required)
Route::middleware(['auth'])->group(function () {
    Route::get('/compare', [ComparisonController::class, 'index'])->name('compare.index');
    Route::get('/compare/count', [ComparisonController::class, 'count'])->name('compare.count');
    Route::post('/compare/toggle/{product}', [ComparisonController::class, 'toggle'])->name('compare.toggle');
    Route::post('/compare/clear', [ComparisonController::class, 'clear'])->name('compare.clear');
});

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gioi-thieu', function () { return view('pages.about'); })->name('about');
Route::get('/dia-chi', function () { return view('pages.address'); })->name('address');
Route::get('/lien-he', function () { return view('pages.contact'); })->name('contact');
Route::post('/lien-he', function () { return back()->with('success', 'Cam on ban da gui!'); })->name('contact.submit');
Route::get('/tin-tuc', function () { return view('pages.news'); })->name('news');

// Chatbot
Route::post('/chatbot/reply', [ChatbotController::class, 'reply'])->name('chatbot.reply')->middleware('throttle:30,1');
Route::post('/chatbot/clear', [ChatbotController::class, 'clear'])->name('chatbot.clear');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{id}/review', [ProductController::class, 'storeReview'])->name('product.review')->middleware('auth');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout & Payment
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Purchase fraud verification (OTP step for high-risk orders)
    Route::get('/checkout/verify-purchase', [PurchaseRiskController::class, 'show'])->name('checkout.verify-purchase');
    Route::post('/checkout/verify-purchase', [PurchaseRiskController::class, 'verify'])->name('checkout.verify-purchase.submit');
    Route::post('/checkout/verify-purchase/resend', [PurchaseRiskController::class, 'resend'])->name('checkout.verify-purchase.resend');
    Route::get('/payment/method/{order}', [CheckoutController::class, 'showPaymentMethod'])->name('checkout.payment.method');
    Route::post('/payment/process/{order}', [CheckoutController::class, 'processPayment'])->name('checkout.payment.process');
    Route::get('/payment/confirm/{order}/{gateway}', [CheckoutController::class, 'confirmPayment'])->name('checkout.payment.confirm');
    Route::post('/payment/confirm-transfer/{order}', [CheckoutController::class, 'confirmTransfer'])->name('checkout.payment.confirm-transfer');
    Route::get('/payment/callback', [CheckoutController::class, 'handleCallback'])->name('checkout.payment.callback');
    Route::get('/order/{id}/success', [CheckoutController::class, 'success'])->name('order.success');
});

// Authentication
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login')->middleware('guest');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware(['guest', 'throttle:10,1']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout')->middleware('auth');
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register')->middleware('guest');
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware('guest');

// Password Reset
Route::get('/forgot-password', [PasswordResetController::class, 'forgot'])->name('password.forgot')->middleware('guest');
Route::post('/forgot-password', [PasswordResetController::class, 'sendReset'])->name('password.send')->middleware('guest');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'updateReset'])->name('password.update');

// Google OAuth
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// OTP (Factor 2) — session-guarded inside controller
Route::get('/auth/otp', [OtpController::class, 'show'])->name('auth.otp');
Route::post('/auth/otp', [OtpController::class, 'verify'])->name('auth.otp.verify')->middleware('throttle:10,1');
Route::post('/auth/otp/resend', [OtpController::class, 'resend'])->name('auth.otp.resend')->middleware('throttle:5,1');

// 3FA (Factor 3) — session-guarded inside controller
Route::get('/auth/3fa', [ThreeFactorController::class, 'show'])->name('auth.3fa');
Route::post('/auth/3fa', [ThreeFactorController::class, 'verify'])->name('auth.3fa.verify')->middleware('throttle:10,1');
Route::post('/auth/3fa/email-send', [ThreeFactorController::class, 'sendConfirmEmail'])->name('auth.3fa.email.send')->middleware('throttle:3,1');
Route::get('/auth/3fa/email-confirm', [ThreeFactorController::class, 'emailConfirm'])->name('auth.3fa.email.confirm');

// Face enrollment (requires full auth)
Route::post('/auth/face-enroll', [ThreeFactorController::class, 'enrollFace'])->name('auth.face.enroll')->middleware(['auth', 'throttle:5,1']);
Route::get('/auth/face-enroll', [ThreeFactorController::class, 'showEnrollFace'])->name('auth.face.enroll.form')->middleware('auth');

// Profile (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', fn() => redirect()->route('profile.show'))->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'editPassword'])->name('profile.change-password');
    Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::put('/profile/security-question', [ProfileController::class, 'updateSecurityQuestion'])->name('profile.update-security-question');
    Route::get('/orders', [ProfileController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{id}', [ProfileController::class, 'orderDetail'])->name('orders.show');
    Route::get('/profile/orders/{id}', [ProfileController::class, 'orderDetail'])->name('profile.order-detail');
});

// Admin Routes
Route::middleware(['admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'dashboard'])->name('admin');

    // Products
    Route::get('/admin/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/admin/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    // Categories
    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/admin/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/admin/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    // Orders
    Route::get('/admin/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::get('/admin/orders/{order}', [AdminController::class, 'orderDetail'])->name('admin.orders.show');
    Route::put('/admin/orders/{order}', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.update');

    // Users
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('/admin/users/{user}/toggle', [AdminController::class, 'toggleUserStatus'])->name('admin.users.toggle');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Security / Login Audit Log
    Route::get('/admin/security', [AdminController::class, 'securityLog'])->name('admin.security');

    // AI Demo: Scenario A/B side-by-side
    Route::get('/admin/demo', [AdminController::class, 'aiDemo'])->name('admin.demo');
    Route::post('/admin/demo/score', [AdminController::class, 'aiDemoScore'])->name('admin.demo.score');

    // Coupons
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::post('/admin/coupons', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
    Route::put('/admin/coupons/{coupon}', [AdminController::class, 'updateCoupon'])->name('admin.coupons.update');
    Route::delete('/admin/coupons/{coupon}', [AdminController::class, 'deleteCoupon'])->name('admin.coupons.delete');

    // Returns / Refunds
    Route::get('/admin/returns', [AdminController::class, 'returns'])->name('admin.returns');
    Route::put('/admin/returns/{orderReturn}', [AdminController::class, 'updateReturn'])->name('admin.returns.update');

    // Reviews
    Route::get('/admin/reviews', [AdminController::class, 'reviews'])->name('admin.reviews');
    Route::delete('/admin/reviews/{review}', [AdminController::class, 'deleteReview'])->name('admin.reviews.delete');
});
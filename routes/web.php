<?php

use App\Http\Controllers\Admin\ComprehensiveDashboardController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\SecurityDashboardController;
use App\Http\Controllers\Admin\SessionTrackingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminSecuritySettingsController;
use App\Http\Controllers\AlertManagementAdminController;
use App\Http\Controllers\AnomalyDetectionAdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\ThreeFactorAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ComparisonController;
use App\Http\Controllers\ComplianceReportController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncidentResponseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\Sessions\SessionController;
use App\Http\Controllers\UserSecurityController;
use App\Http\Controllers\UserSessionManagementController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

// Chatbot Debug Route (remove in production)
Route::get('/chatbot-debug', function () {
    return view('chatbot-test');
})->name('chatbot.debug');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/gioi-thieu', function () {
    return view('pages.about');
})->name('about');
Route::get('/dia-chi', function () {
    return view('pages.address');
})->name('address');
Route::get('/lien-he', function () {
    return view('pages.contact');
})->name('contact');
Route::post('/lien-he', function () {
    return back()->with('success', 'Cảm ơn bạn đã gửi thông điệp!');
})->name('contact.submit');
Route::get('/tin-tuc', function () {
    return view('pages.news');
})->name('news');

// Product Routes
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{id}', [ProductController::class, 'show'])->name('product.show');
Route::post('/product/{id}/review', [ProductController::class, 'storeReview'])->name('product.review');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

// Payment Routes
Route::get('/payment/method/{order}', [CheckoutController::class, 'showPaymentMethod'])->name('payment.method');
Route::post('/payment/process/{order}', [CheckoutController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/confirm/{order}/{gateway}', [CheckoutController::class, 'confirmPayment'])->name('payment.confirm');
Route::post('/payment/confirm-transfer/{order}', [CheckoutController::class, 'confirmTransfer'])->name('payment.confirm-transfer');
Route::get('/payment/callback', [CheckoutController::class, 'handleCallback'])->name('payment.callback');

Route::get('/order/{id}/success', [CheckoutController::class, 'success'])->name('order.success');

// Wishlist Routes (Protected)
Route::middleware(['auth', 'require_3fa'])->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/wishlist/add/{productId}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::post('/wishlist/remove/{productId}', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check'])->name('wishlist.check');
    Route::post('/wishlist/add-to-cart', [WishlistController::class, 'addAllToCart'])->name('wishlist.add-to-cart');
});

// Coupon Routes
Route::post('/coupon/apply', [CouponController::class, 'apply'])->name('coupon.apply');

// Comparison Routes
Route::get('/comparison', [ComparisonController::class, 'index'])->name('comparison.index');
Route::post('/comparison/add/{productId}', [ComparisonController::class, 'add'])->name('comparison.add');
Route::post('/comparison/remove/{productId}', [ComparisonController::class, 'remove'])->name('comparison.remove');
Route::post('/comparison/clear', [ComparisonController::class, 'clear'])->name('comparison.clear');

// Return/Refund Routes (Protected)
Route::middleware(['auth', 'require_3fa'])->group(function () {
    Route::get('/returns', [ReturnController::class, 'index'])->name('returns.index');
    Route::get('/returns/{id}', [ReturnController::class, 'show'])->name('returns.show');
    Route::get('/order/{orderId}/return', [ReturnController::class, 'create'])->name('returns.create');
    Route::post('/order/{orderId}/return', [ReturnController::class, 'store'])->name('returns.store');
});

// Authentication Routes
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'forgot'])->name('password.forgot');
Route::post('/forgot-password', [PasswordResetController::class, 'sendReset'])->name('password.send');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'updateReset'])->name('password.update');

// Google OAuth Routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Three-Factor Authentication Routes
Route::middleware('web')->group(function () {
    // Show 3FA verification
    Route::get('/3fa/verify', [ThreeFactorAuthController::class, 'showVerification'])->name('3fa.verify');

    // OTP Verification
    Route::get('/3fa/verify-otp', [ThreeFactorAuthController::class, 'showOtpForm'])->name('3fa.verify-otp');
    Route::post('/3fa/verify-otp', [ThreeFactorAuthController::class, 'verifyOtp'])->name('3fa.verify-otp.submit');
    Route::post('/3fa/resend-otp', [ThreeFactorAuthController::class, 'resendOtp'])->name('3fa.resend-otp');
    Route::get('/debug/otp/{userId?}', [\App\Http\Controllers\Auth\OtpDebugController::class, 'showOtp'])->name('debug.otp');
    Route::get('/debug/otp-settings/{userId}', [\App\Http\Controllers\Auth\OtpDebugController::class, 'showOtpSettings'])->name('debug.otp-settings');
    Route::get('/debug/otp-reset/{userId}', [\App\Http\Controllers\Auth\OtpDebugController::class, 'resetOtp'])->name('debug.otp-reset');

    // Security Questions Verification
    Route::get('/3fa/security-questions', [ThreeFactorAuthController::class, 'showSecurityQuestionsForm'])->name('3fa.security-questions');
    Route::post('/3fa/verify-security-questions', [ThreeFactorAuthController::class, 'verifySecurityQuestions'])->name('3fa.verify-security-questions');

    // Biometric Verification
    Route::get('/3fa/biometric', [ThreeFactorAuthController::class, 'showBiometricForm'])->name('3fa.biometric');
    Route::post('/3fa/verify-biometric', [ThreeFactorAuthController::class, 'verifyBiometric'])->name('3fa.verify-biometric');

    // Get security questions list
    Route::get('/3fa/questions', [ThreeFactorAuthController::class, 'getSecurityQuestions'])->name('3fa.questions');
});

// Authenticated 3FA Setup Routes
Route::middleware(['auth', 'require_3fa'])->group(function () {
    // Setup security questions
    Route::get('/3fa/setup', [ThreeFactorAuthController::class, 'setupSecurityQuestions'])->name('3fa.setup');
    Route::post('/3fa/setup', [ThreeFactorAuthController::class, 'setupSecurityQuestions'])->name('3fa.setup.submit');

    // 3FA Status
    Route::get('/3fa/status', [ThreeFactorAuthController::class, 'getStatus'])->name('3fa.status');

    // Reset 3FA
    Route::post('/3fa/reset', [ThreeFactorAuthController::class, 'reset'])->name('3fa.reset');
});

// Profile Routes (Protected)
Route::middleware(['auth', 'require_3fa'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/change-password', [ProfileController::class, 'editPassword'])->name('profile.change-password');
    Route::put('/profile/change-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    Route::get('/orders', [ProfileController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{id}', [ProfileController::class, 'orderDetail'])->name('orders.show');
    Route::get('/profile/orders/{id}', [ProfileController::class, 'orderDetail'])->name('profile.order-detail');

    // Security Alerts
    Route::get('/profile/alerts', [ProfileController::class, 'alertsIndex'])->name('profile.alerts.index');
    Route::get('/profile/alerts/{id}', [ProfileController::class, 'alertsShow'])->name('profile.alerts.show');
    Route::post('/profile/alerts/{id}/confirm', [ProfileController::class, 'alertsConfirm'])->name('profile.alerts.confirm');

    // Session Management
    Route::get('/sessions', [UserSessionManagementController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/{sessionId}', [UserSessionManagementController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{sessionId}/terminate', [UserSessionManagementController::class, 'terminate'])->name('sessions.terminate');
    Route::post('/sessions/terminate-others', [UserSessionManagementController::class, 'terminateOthers'])->name('sessions.terminate-others');
    Route::get('/sessions/trusted-devices', [UserSessionManagementController::class, 'trustedDevices'])->name('sessions.trusted-devices');
    Route::post('/sessions/trust-device/{deviceFingerprint}', [UserSessionManagementController::class, 'markTrusted'])->name('sessions.mark-trusted');
    Route::post('/sessions/untrust-device/{deviceFingerprint}', [UserSessionManagementController::class, 'removeTrust'])->name('sessions.remove-trust');
    Route::get('/sessions/activity-timeline', [UserSessionManagementController::class, 'activityTimeline'])->name('sessions.activity-timeline');
    Route::get('/sessions/statistics', [UserSessionManagementController::class, 'statistics'])->name('sessions.statistics');

    // Legacy profile sessions routes for compatibility
    Route::get('/profile/sessions', [UserSessionManagementController::class, 'index'])->name('profile.sessions.index');
    Route::post('/profile/sessions/{sessionId}/terminate', [UserSessionManagementController::class, 'terminate'])->name('profile.sessions.terminate');
    Route::post('/profile/sessions/terminate-others', [UserSessionManagementController::class, 'terminateOthers'])->name('profile.sessions.terminate-others');

    // Notification Management
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/{notification}/unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/preferences', [NotificationController::class, 'preferences'])->name('notifications.preferences');
    Route::post('/notifications/preferences', [NotificationController::class, 'updatePreferences'])->name('notifications.preferences.update');
    Route::post('/notifications/preferences/email', [NotificationController::class, 'updateEmail'])->name('notifications.preferences.update-email');
    Route::post('/notifications/preferences/sms', [NotificationController::class, 'setupSms'])->name('notifications.preferences.setup-sms');
    Route::post('/notifications/preferences/sms/verify', [NotificationController::class, 'verifySmsOtp'])->name('notifications.preferences.verify-sms');

    // AJAX endpoints
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
});

// User Security Routes (Protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/security', [UserSecurityController::class, 'dashboard'])->name('security.dashboard');
    Route::get('/security/logins', [UserSecurityController::class, 'loginHistory'])->name('security.logins');
    Route::get('/security/devices', [UserSecurityController::class, 'devices'])->name('security.devices');
    Route::post('/security/device/{deviceId}/trust', [UserSecurityController::class, 'trustDevice'])->name('security.device.trust');
    Route::post('/security/device/{deviceId}/untrust', [UserSecurityController::class, 'untrustDevice'])->name('security.device.untrust');
    Route::post('/security/device/{deviceId}/remove', [UserSecurityController::class, 'removeDevice'])->name('security.device.remove');
    Route::get('/security/sessions', [UserSecurityController::class, 'sessions'])->name('security.sessions');
    Route::post('/security/sessions/{sessionId}/end', [UserSecurityController::class, 'endSession'])->name('security.sessions.end');
    Route::post('/security/sessions/end-all', [UserSecurityController::class, 'endAllSessions'])->name('security.sessions.end-all');
    Route::get('/security/alerts', [UserSecurityController::class, 'alerts'])->name('security.alerts');
    Route::post('/security/alert/{alertId}/read', [UserSecurityController::class, 'markAlertAsRead'])->name('security.alert.read');
    Route::get('/security/settings/2fa', [UserSecurityController::class, 'twoFaSettings'])->name('security.two-fa');
    Route::get('/security/settings/3fa', [UserSecurityController::class, 'threeAFaSettings'])->name('security.three-fa');

    // 2FA Actions
    Route::post('/security/2fa/enable', function () {
        return back()->with('success', '2FA setup initiated');
    })->name('security.two-fa.enable');
    Route::post('/security/2fa/disable', function () {
        return back()->with('success', '2FA disabled');
    })->name('security.two-fa.disable');

    // 3FA Actions
    Route::post('/security/3fa/enable', function () {
        return back()->with('success', '3FA setup initiated');
    })->name('security.three-fa.enable');
    Route::post('/security/3fa/disable', function () {
        return back()->with('success', '3FA disabled');
    })->name('security.three-fa.disable');
});

// User Dashboard Route (Redirect after 3FA)
Route::middleware(['auth', 'require_3fa'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('profile.show');
    })->name('dashboard');
});

// Admin Routes (Protected with AdminMiddleware and Require3FA)
Route::middleware(['admin', 'require_3fa'])->group(function () {
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

    // Coupons
    Route::get('/admin/coupons', [AdminController::class, 'coupons'])->name('admin.coupons');
    Route::get('/admin/coupons/create', [AdminController::class, 'createCoupon'])->name('admin.coupons.create');
    Route::post('/admin/coupons', [AdminController::class, 'storeCoupon'])->name('admin.coupons.store');
    Route::get('/admin/coupons/{coupon}/edit', [AdminController::class, 'editCoupon'])->name('admin.coupons.edit');
    Route::put('/admin/coupons/{coupon}', [AdminController::class, 'updateCoupon'])->name('admin.coupons.update');
    Route::delete('/admin/coupons/{coupon}', [AdminController::class, 'deleteCoupon'])->name('admin.coupons.delete');

    // Returns
    Route::get('/admin/returns', [AdminController::class, 'returns'])->name('admin.returns');
    Route::get('/admin/returns/{return}', [AdminController::class, 'returnDetail'])->name('admin.returns.show');
    Route::post('/admin/returns/{return}/approve', [AdminController::class, 'approveReturn'])->name('admin.returns.approve');
    Route::post('/admin/returns/{return}/reject', [AdminController::class, 'rejectReturn'])->name('admin.returns.reject');
    Route::post('/admin/returns/{return}/complete', [AdminController::class, 'completeReturn'])->name('admin.returns.complete');

    // Shipping
    Route::get('/admin/shipping', [AdminController::class, 'shippingOrders'])->name('admin.shipping.orders');
    Route::get('/admin/shipping/{order}/edit', [AdminController::class, 'updateShippingForm'])->name('admin.shipping.edit');
    Route::put('/admin/shipping/{order}', [AdminController::class, 'updateShipping'])->name('admin.shipping.update');

    // Logs
    Route::get('/admin/logs/login', [LogController::class, 'loginLogs'])->name('admin.logs.login');
    Route::get('/admin/logs/system', [LogController::class, 'systemLogs'])->name('admin.logs.system');

    // Security Dashboard
    Route::get('/admin/security-dashboard', [SecurityDashboardController::class, 'index'])->name('admin.security-dashboard');

    // Comprehensive Dashboard
    Route::get('/admin/dashboard', [ComprehensiveDashboardController::class, 'index'])->name('admin.dashboard');

    // Security Monitoring
    Route::get('/admin/security-monitoring', [AdminController::class, 'securityMonitoring'])->name('admin.security-monitoring');

    // Security Settings
    Route::get('/admin/security/settings', [AdminSecuritySettingsController::class, 'index'])->name('admin.security-settings.index');
    Route::get('/admin/security/settings/edit', [AdminSecuritySettingsController::class, 'edit'])->name('admin.security-settings.edit');
    Route::put('/admin/security/settings', [AdminSecuritySettingsController::class, 'update'])->name('admin.security-settings.update');

    // Blocked IPs Management
    Route::get('/admin/security/blocked-ips', [AdminSecuritySettingsController::class, 'blockedIps'])->name('admin.security-settings.blocked-ips');
    Route::post('/admin/security/blocked-ips', [AdminSecuritySettingsController::class, 'blockIp'])->name('admin.security-settings.block-ip');
    Route::get('/admin/security/blocked-ips/{ipAddress}', [AdminSecuritySettingsController::class, 'showBlockedIp'])->name('admin.security-settings.blocked-ip-detail');
    Route::put('/admin/security/blocked-ips/{ipAddress}', [AdminSecuritySettingsController::class, 'updateBlockedIp'])->name('admin.security-settings.update-blocked-ip');
    Route::delete('/admin/security/blocked-ips/{ipAddress}', [AdminSecuritySettingsController::class, 'unblockIp'])->name('admin.security-settings.unblock-ip');

    // Auto Responses
    Route::get('/admin/security/auto-responses', [AdminSecuritySettingsController::class, 'autoResponses'])->name('admin.security-settings.auto-responses');
    Route::get('/admin/security/auto-responses/{id}', [AdminSecuritySettingsController::class, 'showAutoResponse'])->name('admin.security-settings.auto-response-detail');
    Route::post('/admin/security/auto-responses/{id}/approve', [AdminSecuritySettingsController::class, 'approveAutoResponse'])->name('admin.security-settings.approve-response');
    Route::post('/admin/security/auto-responses/{id}/reject', [AdminSecuritySettingsController::class, 'rejectAutoResponse'])->name('admin.security-settings.reject-response');

    // Test Response Action
    Route::post('/admin/security/test-response', [AdminSecuritySettingsController::class, 'testResponseAction'])->name('admin.security-settings.test-response');

    // Statistics & Reports
    Route::get('/admin/security/statistics', [AdminSecuritySettingsController::class, 'statistics'])->name('admin.security-settings.statistics');
    Route::post('/admin/security/export-report', [AdminSecuritySettingsController::class, 'exportReport'])->name('admin.security-settings.export-report');

    // Security Settings
    Route::get('/admin/security/settings', [AdminSecuritySettingsController::class, 'index'])->name('admin.security-settings.index');
    Route::get('/admin/security/settings/edit', [AdminSecuritySettingsController::class, 'edit'])->name('admin.security-settings.edit');
    Route::put('/admin/security/settings', [AdminSecuritySettingsController::class, 'update'])->name('admin.security-settings.update');
    Route::put('/admin/security/settings/general', [AdminSecuritySettingsController::class, 'updateGeneralSettings'])->name('admin.security-settings.update-general');
    Route::put('/admin/security/settings/3fa', [AdminSecuritySettingsController::class, 'updateThreeFactorAuthSettings'])->name('admin.security-settings.update-3fa');
    Route::put('/admin/security/settings/thresholds', [AdminSecuritySettingsController::class, 'updateAlertThresholds'])->name('admin.security-settings.update-thresholds');
    Route::put('/admin/security/settings/sessions', [AdminSecuritySettingsController::class, 'updateSessionManagement'])->name('admin.security-settings.update-sessions');
    Route::put('/admin/security/settings/encryption', [AdminSecuritySettingsController::class, 'updateEncryptionSettings'])->name('admin.security-settings.update-encryption');
    Route::post('/admin/security/settings/whitelist', [AdminSecuritySettingsController::class, 'addToWhitelist'])->name('admin.security-settings.whitelist-add');
    Route::delete('/admin/security/settings/whitelist', [AdminSecuritySettingsController::class, 'removeFromWhitelist'])->name('admin.security-settings.whitelist-remove');
    Route::post('/admin/security/settings/blacklist', [AdminSecuritySettingsController::class, 'addToBlacklist'])->name('admin.security-settings.blacklist-add');
    Route::delete('/admin/security/settings/blacklist', [AdminSecuritySettingsController::class, 'removeFromBlacklist'])->name('admin.security-settings.blacklist-remove');
    Route::get('/admin/security/settings/export', [AdminSecuritySettingsController::class, 'exportSettings'])->name('admin.security-settings.export');
    Route::post('/admin/security/settings/restore', [AdminSecuritySettingsController::class, 'restoreSettings'])->name('admin.security-settings.restore');
    Route::get('/admin/security/settings/audit-log', [AdminSecuritySettingsController::class, 'auditLog'])->name('admin.security-settings.audit-log');

    // Blocked IPs Management
    Route::get('/admin/security/blocked-ips', [AdminSecuritySettingsController::class, 'blockedIps'])->name('admin.security-settings.blocked-ips');
    Route::post('/admin/security/blocked-ips', [AdminSecuritySettingsController::class, 'blockIp'])->name('admin.security-settings.block-ip');
    Route::get('/admin/security/blocked-ips/{ipAddress}', [AdminSecuritySettingsController::class, 'showBlockedIp'])->name('admin.security-settings.blocked-ip-detail');
    Route::put('/admin/security/blocked-ips/{ipAddress}', [AdminSecuritySettingsController::class, 'updateBlockedIp'])->name('admin.security-settings.update-blocked-ip');
    Route::delete('/admin/security/blocked-ips/{ipAddress}', [AdminSecuritySettingsController::class, 'unblockIp'])->name('admin.security-settings.unblock-ip');

    // Auto Responses
    Route::get('/admin/security/auto-responses', [AdminSecuritySettingsController::class, 'autoResponses'])->name('admin.security-settings.auto-responses');
    Route::get('/admin/security/auto-responses/{id}', [AdminSecuritySettingsController::class, 'showAutoResponse'])->name('admin.security-settings.auto-response-detail');
    Route::post('/admin/security/auto-responses/{id}/approve', [AdminSecuritySettingsController::class, 'approveAutoResponse'])->name('admin.security-settings.approve-response');
    Route::post('/admin/security/auto-responses/{id}/reject', [AdminSecuritySettingsController::class, 'rejectAutoResponse'])->name('admin.security-settings.reject-response');

    // Test Response Action
    Route::post('/admin/security/test-response', [AdminSecuritySettingsController::class, 'testResponseAction'])->name('admin.security-settings.test-response');

    // Statistics & Reports
    Route::get('/admin/security/statistics', [AdminSecuritySettingsController::class, 'statistics'])->name('admin.security-settings.statistics');
    Route::post('/admin/security/export-report', [AdminSecuritySettingsController::class, 'exportReport'])->name('admin.security-settings.export-report');

    // Session Monitoring Management
    Route::get('/admin/sessions', [SessionController::class, 'adminIndex'])->name('admin.sessions.index');
    Route::get('/admin/sessions/{session}', [SessionController::class, 'adminShow'])->name('admin.sessions.show');
    Route::post('/admin/sessions/{session}/terminate', [SessionController::class, 'adminTerminate'])->name('admin.sessions.terminate');
    Route::post('/admin/sessions/{session}/flag', [SessionController::class, 'flag'])->name('admin.sessions.flag');
    Route::post('/admin/sessions/{session}/unflag', [SessionController::class, 'unflag'])->name('admin.sessions.unflag');

    // Concurrent Login Management
    Route::post('/admin/concurrent-logins/{concurrentLogin}/confirm', [SessionController::class, 'confirmConcurrentLogin'])->name('admin.concurrent-logins.confirm');
    Route::post('/admin/concurrent-logins/{concurrentLogin}/authorize', [SessionController::class, 'authorizeConcurrentLogin'])->name('admin.concurrent-logins.authorize');
    Route::post('/admin/concurrent-logins/{concurrentLogin}/false-positive', [SessionController::class, 'markFalsePositive'])->name('admin.concurrent-logins.false-positive');

    // Session Tracking & Activity Logs
    Route::get('/admin/activity-logs', [SessionTrackingController::class, 'activityLogs'])->name('admin.activity-logs');
    Route::get('/admin/activity-logs/user/{userId}', [SessionTrackingController::class, 'userActivityLogs'])->name('admin.activity-logs.user');
    Route::get('/admin/activity-logs/export', [SessionTrackingController::class, 'exportActivityLogs'])->name('admin.activity-logs.export');
    Route::get('/admin/activity-logs/statistics', [SessionTrackingController::class, 'statistics'])->name('admin.activity-logs.statistics');
    Route::post('/admin/activity-logs/cleanup', [SessionTrackingController::class, 'cleanupActivityLogs'])->name('admin.activity-logs.cleanup');
    Route::get('/admin/sessions/user/{userId}', [SessionTrackingController::class, 'userSessions'])->name('admin.sessions.user');

    // Advanced Admin Reports Dashboard
    Route::get('/admin/reports', [AdminReportController::class, 'dashboard'])->name('admin.reports.dashboard');
    Route::get('/admin/reports/sales', [AdminReportController::class, 'salesReport'])->name('admin.reports.sales');
    Route::get('/admin/reports/users', [AdminReportController::class, 'userAnalytics'])->name('admin.reports.users');
    Route::get('/admin/reports/products', [AdminReportController::class, 'productAnalytics'])->name('admin.reports.products');
    Route::get('/admin/reports/security', [AdminReportController::class, 'securityMetrics'])->name('admin.reports.security');
    Route::get('/admin/reports/system-health', [AdminReportController::class, 'systemHealth'])->name('admin.reports.system-health');
    Route::get('/admin/reports/export', [AdminReportController::class, 'exportReport'])->name('admin.reports.export');

    // Audit Trail / Security Logs Viewer
    Route::get('/admin/audit', [AuditLogController::class, 'dashboard'])->name('admin.audit.dashboard');
    Route::get('/admin/audit/logs', [AuditLogController::class, 'index'])->name('admin.audit.index');
    Route::get('/admin/audit/logs/{id}', [AuditLogController::class, 'show'])->name('admin.audit.show');
    Route::get('/admin/audit/search', [AuditLogController::class, 'search'])->name('admin.audit.search');
    Route::get('/admin/audit/filter', [AuditLogController::class, 'filter'])->name('admin.audit.filter');
    Route::get('/admin/audit/user/{userId}', [AuditLogController::class, 'userActivity'])->name('admin.audit.user-activity');
    Route::get('/admin/audit/period', [AuditLogController::class, 'timePeriod'])->name('admin.audit.time-period');
    Route::get('/admin/audit/export/csv', [AuditLogController::class, 'exportCsv'])->name('admin.audit.export-csv');
    Route::get('/admin/audit/export/json', [AuditLogController::class, 'exportJson'])->name('admin.audit.export-json');
    Route::post('/admin/audit/compliance-report', [AuditLogController::class, 'complianceReport'])->name('admin.audit.compliance-report');

    // Anomaly Detection Admin Dashboard
    Route::get('/admin/anomalies', [AnomalyDetectionAdminController::class, 'dashboard'])->name('admin.anomalies.dashboard');
    Route::get('/admin/anomalies/list', [AnomalyDetectionAdminController::class, 'index'])->name('admin.anomalies.index');
    Route::get('/admin/anomalies/{id}', [AnomalyDetectionAdminController::class, 'show'])->name('admin.anomalies.show');
    Route::get('/admin/anomalies/user/{userId}', [AnomalyDetectionAdminController::class, 'byUser'])->name('admin.anomalies.by-user');
    Route::get('/admin/anomalies-geo', [AnomalyDetectionAdminController::class, 'geoMap'])->name('admin.anomalies.geo-map');
    Route::get('/admin/anomalies-devices', [AnomalyDetectionAdminController::class, 'devices'])->name('admin.anomalies.devices');
    Route::get('/admin/anomalies-ips', [AnomalyDetectionAdminController::class, 'ips'])->name('admin.anomalies.ips');
    Route::post('/admin/anomalies/{id}/resolve', [AnomalyDetectionAdminController::class, 'resolve'])->name('admin.anomalies.resolve');
    Route::post('/admin/anomalies/block-ip/{ipAddress}', [AnomalyDetectionAdminController::class, 'blockIp'])->name('admin.anomalies.block-ip');
    Route::get('/admin/anomalies/export-anomalies', [AnomalyDetectionAdminController::class, 'export'])->name('admin.anomalies.export');
    Route::get('/admin/anomalies-risk-report', [AnomalyDetectionAdminController::class, 'riskReport'])->name('admin.anomalies.risk-report');

    // Alert Management Admin Dashboard
    Route::get('/admin/alerts', [AlertManagementAdminController::class, 'dashboard'])->name('admin.alerts.dashboard');
    Route::get('/admin/alerts/list', [AlertManagementAdminController::class, 'index'])->name('admin.alerts.index');
    Route::get('/admin/alerts/{id}', [AlertManagementAdminController::class, 'show'])->name('admin.alerts.show');
    Route::get('/admin/alerts/response-center', [AlertManagementAdminController::class, 'responseCenter'])->name('admin.alerts.response-center');
    Route::post('/admin/alerts/{id}/respond', [AlertManagementAdminController::class, 'respond'])->name('admin.alerts.respond');
    Route::post('/admin/alerts/{id}/acknowledge', [AlertManagementAdminController::class, 'acknowledge'])->name('admin.alerts.acknowledge');
    Route::post('/admin/alerts/{id}/investigate', [AlertManagementAdminController::class, 'investigate'])->name('admin.alerts.investigate');
    Route::post('/admin/alerts/{id}/resolve', [AlertManagementAdminController::class, 'resolve'])->name('admin.alerts.resolve');
    Route::post('/admin/alerts/{id}/dismiss', [AlertManagementAdminController::class, 'dismiss'])->name('admin.alerts.dismiss');
    Route::post('/admin/alerts/bulk-action', [AlertManagementAdminController::class, 'bulkAction'])->name('admin.alerts.bulk-action');
    Route::get('/admin/alerts/export', [AlertManagementAdminController::class, 'export'])->name('admin.alerts.export');
    Route::get('/admin/alerts/statistics', [AlertManagementAdminController::class, 'statistics'])->name('admin.alerts.statistics');

    // Risk Assessment Dashboard
    Route::get('/admin/risk-assessment', [RiskAssessmentController::class, 'dashboard'])->name('admin.risk-assessment.dashboard');
    Route::get('/admin/risk-assessment/analysis', [RiskAssessmentController::class, 'analysis'])->name('admin.risk-assessment.analysis');
    Route::get('/admin/risk-assessment/user/{userId}', [RiskAssessmentController::class, 'userRiskDetail'])->name('admin.risk-assessment.user-detail');

    // Email Templates Management
    Route::get('/admin/email-templates', [EmailTemplateController::class, 'index'])->name('admin.email-templates.index');
    Route::get('/admin/email-templates/create', [EmailTemplateController::class, 'create'])->name('admin.email-templates.create');
    Route::post('/admin/email-templates', [EmailTemplateController::class, 'store'])->name('admin.email-templates.store');
    Route::get('/admin/email-templates/{id}', [EmailTemplateController::class, 'show'])->name('admin.email-templates.show');
    Route::get('/admin/email-templates/{id}/edit', [EmailTemplateController::class, 'edit'])->name('admin.email-templates.edit');
    Route::put('/admin/email-templates/{id}', [EmailTemplateController::class, 'update'])->name('admin.email-templates.update');
    Route::delete('/admin/email-templates/{id}', [EmailTemplateController::class, 'destroy'])->name('admin.email-templates.destroy');
    Route::get('/admin/email-templates/{id}/preview', [EmailTemplateController::class, 'preview'])->name('admin.email-templates.preview');
    Route::post('/admin/email-templates/{id}/test', [EmailTemplateController::class, 'testSend'])->name('admin.email-templates.test');

    // Compliance Reports (9 routes)
    Route::get('/admin/compliance', [ComplianceReportController::class, 'dashboard'])->name('admin.compliance.dashboard');
    Route::get('/admin/compliance/reports/gdpr', [ComplianceReportController::class, 'gdprReport'])->name('admin.compliance.gdpr');
    Route::post('/admin/compliance/reports/gdpr/generate', [ComplianceReportController::class, 'gdprReport'])->name('admin.compliance.gdpr-generate');
    Route::get('/admin/compliance/reports/pcidss', [ComplianceReportController::class, 'pciDssReport'])->name('admin.compliance.pcidss');
    Route::post('/admin/compliance/reports/pcidss/generate', [ComplianceReportController::class, 'pciDssReport'])->name('admin.compliance.pcidss-generate');
    Route::get('/admin/compliance/reports/hipaa', [ComplianceReportController::class, 'hipaaReport'])->name('admin.compliance.hipaa');
    Route::post('/admin/compliance/reports/hipaa/generate', [ComplianceReportController::class, 'hipaaReport'])->name('admin.compliance.hipaa-generate');
    Route::get('/admin/compliance/reports', [ComplianceReportController::class, 'dashboard'])->name('admin.compliance.reports');
    Route::post('/admin/compliance/export', [ComplianceReportController::class, 'exportReport'])->name('admin.compliance.export-report');

    // Incident Response Management
    Route::get('/admin/incidents', [IncidentResponseController::class, 'dashboard'])->name('admin.incidents.dashboard');
    Route::get('/admin/incidents/list', [IncidentResponseController::class, 'index'])->name('admin.incidents.index');
    Route::get('/admin/incidents/create', [IncidentResponseController::class, 'create'])->name('admin.incidents.create');
    Route::post('/admin/incidents', [IncidentResponseController::class, 'store'])->name('admin.incidents.store');
    Route::get('/admin/incidents/{id}', [IncidentResponseController::class, 'show'])->name('admin.incidents.show');
    Route::put('/admin/incidents/{id}/status', [IncidentResponseController::class, 'updateStatus'])->name('admin.incidents.update-status');
    Route::post('/admin/incidents/{id}/assign', [IncidentResponseController::class, 'assign'])->name('admin.incidents.assign');
    Route::post('/admin/incidents/{id}/response', [IncidentResponseController::class, 'addResponse'])->name('admin.incidents.add-response');
    Route::post('/admin/incidents/{id}/close', [IncidentResponseController::class, 'close'])->name('admin.incidents.close');
    Route::get('/admin/incidents/{id}/report', [IncidentResponseController::class, 'report'])->name('admin.incidents.report');
    Route::get('/admin/incidents/{id}/responses', [IncidentResponseController::class, 'responses'])->name('admin.incidents.responses');
});

// Payment
Route::middleware(['auth', 'require_3fa'])->group(function () {
    Route::get('/payment/{order}/method', [PaymentController::class, 'chooseMethod'])->name('payment.method');
    Route::post('/payment/{order}/process', [PaymentController::class, 'process'])->name('payment.process');
    Route::post('/payment/{order}/confirm-bank', [PaymentController::class, 'confirmBankTransfer'])->name('payment.confirm-bank');
    Route::get('/payment/{order}/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/{order}/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::post('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');
});

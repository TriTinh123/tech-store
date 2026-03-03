<?php

use App\Http\Controllers\Api\TrackingController;
use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders/{order}/tracking', [TrackingController::class, 'track'])->name('api.tracking.track');
    Route::post('/shipping/estimate', [TrackingController::class, 'estimate'])->name('api.shipping.estimate');
});

// Chatbot routes
Route::post('/chatbot/start', [ChatbotController::class, 'startConversation'])->name('api.chatbot.start');
Route::post('/chatbot/send-message', [ChatbotController::class, 'sendMessage'])->name('api.chatbot.send-message');
Route::get('/chatbot/history', [ChatbotController::class, 'getHistory'])->name('api.chatbot.history');
Route::get('/chatbot/recommendations', [ChatbotController::class, 'getRecommendations'])->name('api.chatbot.recommendations');
Route::post('/chatbot/check-suspicious-login', [ChatbotController::class, 'checkSuspiciousLogin'])->middleware('auth:sanctum')->name('api.chatbot.suspicious-login');

// New Chatbot Features - Tier 1
Route::post('/chatbot/compare-products', [ChatbotController::class, 'compareProducts'])->name('api.chatbot.compare');
Route::post('/chatbot/check-order', [ChatbotController::class, 'checkOrderStatus'])->middleware('auth:sanctum')->name('api.chatbot.check-order');
Route::post('/chatbot/get-discounts', [ChatbotController::class, 'getSuggestedDiscounts'])->name('api.chatbot.discounts');
Route::post('/chatbot/search-faq', [ChatbotController::class, 'searchFAQ'])->name('api.chatbot.faq');

// Admin Chatbot Analytics
Route::post('/chatbot/admin/analytics', [ChatbotController::class, 'getAdminAnalytics'])->middleware('auth:sanctum')->name('api.chatbot.admin-analytics');

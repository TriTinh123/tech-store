<?php

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Product::observe(ProductObserver::class);

        // ── Rate limiting ─────────────────────────────────────────────────
        // Login: max 10 attempts per minute per IP (blocks brute-force at HTTP layer)
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // OTP verify: max 5 attempts per minute per user session
        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(5)->by(
                session('auth.pending_user_id', $request->ip())
            );
        });
    }
}

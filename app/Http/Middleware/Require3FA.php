<?php

namespace App\Http\Middleware;

use App\Services\ThreeFactorAuthService;
use Closure;
use Illuminate\Http\Request;

class Require3FA
{
    protected $tfaService;

    public function __construct(ThreeFactorAuthService $tfaService)
    {
        $this->tfaService = $tfaService;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Check if 3FA is verified for this session
        if ($this->tfaService->isVerificationRequired($user)) {
            // Redirect to OTP verification if not yet verified
            return redirect()->route('3fa.verify-otp');
        }

        return $next($request);
    }
}

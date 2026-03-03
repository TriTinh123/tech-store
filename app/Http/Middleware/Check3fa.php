<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Check3fa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Skip if not authenticated
        if (! $user) {
            return $next($request);
        }

        // Skip if already verified 3FA
        if (session()->has("3fa_verified_{$user->id}")) {
            return $next($request);
        }

        // Skip if user doesn't have 3FA enabled
        if (! $user->is3faEnabled()) {
            return $next($request);
        }

        // Redirect to 3FA verification
        return redirect()->route('3fa.verify.show');
    }
}

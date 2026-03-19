<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            // Store a dedicated flag so completeLogin() knows this request
            // was an attempt to reach the admin panel, even in multi-step auth.
            session(['auth.tried_admin' => true]);
            return redirect()->guest(route('login'));
        }

        if (! auth()->user()->isAdmin()) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}

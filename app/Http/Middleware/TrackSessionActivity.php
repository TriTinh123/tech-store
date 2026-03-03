<?php

namespace App\Http\Middleware;

use App\Models\UserSession;
use App\Services\SessionMonitoringService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackSessionActivity
{
    public function __construct(
        private SessionMonitoringService $sessionMonitoringService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $sessionId = session()->getId();
            $user = auth()->user();

            // Find or update session activity
            $userSession = UserSession::where('session_id', $sessionId)->first();

            if ($userSession) {
                // Update last activity timestamp
                $userSession->touchActivity();

                // Log activity
                $this->sessionMonitoringService->logActivity(
                    $user->id,
                    $userSession->id,
                    'page_view',
                    $request->ip(),
                    "User viewed {$request->path()}"
                );

                // Store session ID in request for later use
                $request->attributes->set('user_session_id', $userSession->id);
            }
        }

        return $next($request);
    }
}

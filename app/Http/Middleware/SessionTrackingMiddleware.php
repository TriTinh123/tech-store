<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Models\UserSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionTrackingMiddleware
{
    /**
     * List of routes to exclude from tracking
     */
    protected $exclude = [
        'debugbar',
        'health',
        'ping',
    ];

    /**
     * List of routes to exclude from detailed logging
     */
    protected $excludeFromDetailedLogging = [
        'assets',
        'images',
        'css',
        'js',
        'fonts',
        'build',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip static assets and excluded routes
        if ($this->shouldExclude($request)) {
            return $response;
        }

        // Track the request after response is generated
        $this->trackActivity($request, $response);

        return $response;
    }

    /**
     * Check if request should be excluded from tracking
     */
    protected function shouldExclude(Request $request): bool
    {
        $path = $request->getPathInfo();

        foreach ($this->exclude as $exclude) {
            if (strpos($path, $exclude) !== false) {
                return true;
            }
        }

        // Skip API routes with specific patterns
        if ($request->is('api/*')) {
            return true;
        }

        return false;
    }

    /**
     * Check if request should be excluded from detailed logging
     */
    protected function shouldExcludeFromDetailedLogging(Request $request): bool
    {
        $path = $request->getPathInfo();

        foreach ($this->excludeFromDetailedLogging as $exclude) {
            if (strpos($path, $exclude) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Track the user activity
     */
    protected function trackActivity(Request $request, Response $response): void
    {
        try {
            // Get authenticated user
            $user = auth()->user();

            // Track individual request
            $this->logActivity($request, $response, $user);

            // Update user session last activity
            $this->updateSessionActivity($user, $request);

        } catch (\Exception $e) {
            // Silently fail to prevent affecting application
            \Log::error('Session tracking error: '.$e->getMessage());
        }
    }

    /**
     * Log activity to activity_logs table
     */
    protected function logActivity(Request $request, Response $response, $user): void
    {
        // Skip detailed logging for static files
        if ($this->shouldExcludeFromDetailedLogging($request)) {
            return;
        }

        // Determine action based on route and method
        $action = $this->determineAction($request);

        ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'description' => $this->generateDescription($request),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->getMethod(),
            'url' => $request->getPathInfo(),
            'status_code' => $response->getStatusCode(),
        ]);
    }

    /**
     * Update user session last activity timestamp
     */
    protected function updateSessionActivity($user, Request $request): void
    {
        if (! $user) {
            return;
        }

        try {
            $sessionId = session()->getId();

            // Find active session for this user
            $userSession = UserSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->whereNull('logged_out_at')
                ->first();

            if ($userSession) {
                // Update last activity
                $userSession->update([
                    'last_activity_at' => now(),
                    'last_activity_ip' => $request->ip(),
                    'last_activity_url' => $request->getPathInfo(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to update session activity: '.$e->getMessage());
        }
    }

    /**
     * Determine the action type based on request
     */
    protected function determineAction(Request $request): string
    {
        $method = $request->getMethod();
        $path = $request->getPathInfo();

        // Extract action from route name if available
        $routeName = $request->route()?->getName();

        if ($routeName) {
            return strtoupper($method).' '.$routeName;
        }

        // Fallback to method + path
        return strtoupper($method).' '.$path;
    }

    /**
     * Generate a human-readable description
     */
    protected function generateDescription(Request $request): string
    {
        $method = $request->getMethod();
        $path = $request->getPathInfo();
        $routeName = $request->route()?->getName() ?? 'unknown';

        $descriptions = [
            'GET' => "Viewed {$routeName}",
            'POST' => "Created/Updated via {$routeName}",
            'PUT' => "Updated via {$routeName}",
            'PATCH' => "Modified via {$routeName}",
            'DELETE' => "Deleted via {$routeName}",
        ];

        $description = $descriptions[$method] ?? "{$method} request to {$path}";

        // Add form data summary for POST/PUT requests
        if (in_array($method, ['POST', 'PUT', 'PATCH']) && $request->isXmlHttpRequest()) {
            $description .= ' (AJAX)';
        }

        return $description;
    }
}

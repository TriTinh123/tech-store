<?php

namespace App\Services;

use App\Models\ConcurrentLogin;
use App\Models\SessionActivity;
use App\Models\User;
use App\Models\UserSession;
// use Jenssegers\Agent\Agent;  // Optional - only if package is installed
use Exception;

class SessionMonitoringService
{
    // protected Agent $agent;  // Optional - only if Jenssegers\Agent is installed
    protected IpBlockingService $ipBlockingService;

    public function __construct(IpBlockingService $ipBlockingService)
    {
        // $this->agent = new Agent();  // Optional
        $this->ipBlockingService = $ipBlockingService;
    }

    /**
     * Create a new user session
     */
    public function createSession(User $user, string $sessionId, string $ipAddress): ?UserSession
    {
        try {
            $session = UserSession::create([
                'user_id' => $user->id,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => request()->userAgent(),
                'device_name' => $this->getDeviceName(),
                'device_type' => $this->getDeviceType(),
                'browser' => $this->parseBrowserFromUserAgent(request()->userAgent()),
                'os' => $this->parseOsFromUserAgent(request()->userAgent()),
                'location' => $this->getLocationFromIp($ipAddress),
                'latitude' => $this->getLatitudeFromIp($ipAddress),
                'longitude' => $this->getLongitudeFromIp($ipAddress),
                'last_activity_at' => now(),
                'is_active' => true,
            ]);

            $this->logActivity($user->id, $session->id, 'login', $ipAddress, 'User logged in');

            return $session;
        } catch (Exception $e) {
            \Log::error("Failed to create session for user {$user->id}: ".$e->getMessage());

            return null;
        }
    }

    /**
     * Update last activity for session
     */
    public function touchSession(string $sessionId): void
    {
        try {
            UserSession::where('session_id', $sessionId)
                ->where('is_active', true)
                ->update(['last_activity_at' => now()]);
        } catch (Exception $e) {
            \Log::warning('Failed to touch session: '.$e->getMessage());
        }
    }

    /**
     * End user session
     */
    public function endSession(string $sessionId): void
    {
        try {
            $session = UserSession::where('session_id', $sessionId)->first();
            if ($session) {
                $session->logout();
                $this->logActivity($session->user_id, $session->id, 'logout', $session->ip_address, 'User logged out');
            }
        } catch (Exception $e) {
            \Log::error('Failed to end session: '.$e->getMessage());
        }
    }

    /**
     * Get all active sessions for user
     */
    public function getActiveSessions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return UserSession::forUser($user->id)
            ->active()
            ->orderBy('last_activity_at', 'desc')
            ->get();
    }

    /**
     * Get all sessions for user (including inactive)
     */
    public function getAllSessions(User $user, int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return UserSession::forUser($user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Detect concurrent logins
     */
    public function detectConcurrentLogins(User $user, string $newSessionId, string $newIpAddress): void
    {
        try {
            // Get other active sessions
            $activeSessions = UserSession::forUser($user->id)
                ->active()
                ->where('session_id', '!=', $newSessionId)
                ->get();

            if ($activeSessions->isEmpty()) {
                return; // No other active sessions
            }

            foreach ($activeSessions as $activeSession) {
                // Calculate time difference
                $timeDifference = now()->diffInSeconds($activeSession->last_activity_at);

                // Check if suspicious (different locations or rapid login)
                $isSuspicious = $activeSession->ip_address !== $newIpAddress && $timeDifference < 300; // 5 minutes

                if ($isSuspicious) {
                    // Create concurrent login record
                    ConcurrentLogin::create([
                        'user_id' => $user->id,
                        'primary_session_id' => $activeSession->session_id,
                        'secondary_session_id' => $newSessionId,
                        'primary_ip_address' => $activeSession->ip_address,
                        'secondary_ip_address' => $newIpAddress,
                        'primary_location' => $activeSession->location,
                        'secondary_location' => $this->getLocationFromIp($newIpAddress),
                        'time_difference_seconds' => $timeDifference,
                        'status' => 'detected',
                    ]);

                    // Flag both sessions if flag method exists
                    if (method_exists($activeSession, 'flag')) {
                        $activeSession->flag('Concurrent login detected');
                    }
                    $newSession = UserSession::where('session_id', $newSessionId)->first();
                    if ($newSession && method_exists($newSession, 'flag')) {
                        $newSession->flag('Concurrent login detected');
                    }

                    \Log::warning("Concurrent login detected for user {$user->id}: {$activeSession->ip_address} AND {$newIpAddress}");
                }
            }
        } catch (Exception $e) {
            \Log::error('Error detecting concurrent logins: '.$e->getMessage());
        }
    }

    /**
     * Terminate session by ID
     */
    public function terminateSession(int $sessionId, ?User $requestingUser = null): bool
    {
        try {
            $session = UserSession::find($sessionId);

            if (! $session) {
                return false;
            }

            // Check permissions
            if ($requestingUser && ! $requestingUser->is_admin && $requestingUser->id !== $session->user_id) {
                return false;
            }

            $session->logout();

            // Log activity
            $this->logActivity(
                $session->user_id,
                $session->id,
                'logout',
                $session->ip_address,
                $requestingUser ? 'Session terminated by '.($requestingUser->is_admin ? 'admin' : 'user') : 'Session terminated'
            );

            return true;
        } catch (Exception $e) {
            \Log::error('Failed to terminate session: '.$e->getMessage());

            return false;
        }
    }

    /**
     * Terminate all other sessions for user
     */
    public function terminateOtherSessions(User $user, string $currentSessionId): int
    {
        try {
            $sessions = UserSession::forUser($user->id)
                ->active()
                ->where('session_id', '!=', $currentSessionId)
                ->get();

            $count = 0;
            foreach ($sessions as $session) {
                if ($this->terminateSession($session->id, $user)) {
                    $count++;
                }
            }

            return $count;
        } catch (Exception $e) {
            \Log::error('Error terminating other sessions: '.$e->getMessage());

            return 0;
        }
    }

    /**
     * Log session activity
     */
    public function logActivity(
        int $userId,
        ?int $sessionId,
        string $activityType,
        string $ipAddress,
        ?string $description = null,
        array $metadata = []
    ): void {
        try {
            SessionActivity::create([
                'user_id' => $userId,
                'user_session_id' => $sessionId,
                'activity_type' => $activityType,
                'ip_address' => $ipAddress,
                'user_agent' => request()->userAgent(),
                'method' => request()->method(),
                'path' => request()->path(),
                'status_code' => 200,
                'description' => $description,
                'metadata' => $metadata,
                'is_suspicious' => false,
            ]);
        } catch (Exception $e) {
            \Log::error('Failed to log session activity: '.$e->getMessage());
        }
    }

    /**
     * Get device type
     */
    protected function getDeviceType(): string
    {
        $userAgent = strtolower(request()->userAgent() ?? '');
        if (strpos($userAgent, 'mobile') !== false || strpos($userAgent, 'android') !== false) {
            return 'mobile';
        } elseif (strpos($userAgent, 'tablet') !== false || strpos($userAgent, 'ipad') !== false) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Get device name
     */
    protected function getDeviceName(): string
    {
        $userAgent = request()->userAgent() ?? 'Unknown Device';

        return substr($userAgent, 0, 100);
    }

    /**
     * Parse browser from user agent
     */
    protected function parseBrowserFromUserAgent(string $userAgent): string
    {
        if (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        }
        if (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        }
        if (strpos($userAgent, 'Opera') !== false) {
            return 'Opera';
        }

        return 'Unknown';
    }

    /**
     * Parse OS from user agent
     */
    protected function parseOsFromUserAgent(string $userAgent): string
    {
        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows';
        }
        if (strpos($userAgent, 'Mac') !== false) {
            return 'macOS';
        }
        if (strpos($userAgent, 'Linux') !== false) {
            return 'Linux';
        }
        if (strpos($userAgent, 'Android') !== false) {
            return 'Android';
        }
        if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'iOS';
        }

        return 'Unknown';
    }

    /**
     * Get location from IP (using ip-api.com)
     */
    protected function getLocationFromIp(string $ipAddress): ?string
    {
        try {
            $response = \Http::get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful()) {
                $data = $response->json();

                return $data['city'].', '.$data['country'] ?? null;
            }
        } catch (Exception $e) {
            \Log::warning("Failed to get location from IP {$ipAddress}: ".$e->getMessage());
        }

        return null;
    }

    /**
     * Get latitude from IP
     */
    protected function getLatitudeFromIp(string $ipAddress): ?string
    {
        try {
            $response = \Http::get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful()) {
                $data = $response->json();

                return $data['lat'] ?? null;
            }
        } catch (Exception $e) {
            // Silent fail
        }

        return null;
    }

    /**
     * Get longitude from IP
     */
    protected function getLongitudeFromIp(string $ipAddress): ?string
    {
        try {
            $response = \Http::get("http://ip-api.com/json/{$ipAddress}");

            if ($response->successful()) {
                $data = $response->json();

                return $data['lon'] ?? null;
            }
        } catch (Exception $e) {
            // Silent fail
        }

        return null;
    }

    /**
     * Get session statistics
     */
    public function getSessionStatistics(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_sessions' => UserSession::where('created_at', '>=', $startDate)->count(),
            'active_sessions' => UserSession::active()->count(),
            'unique_users' => UserSession::where('created_at', '>=', $startDate)
                ->distinct('user_id')
                ->count('user_id'),
            'unique_ips' => UserSession::where('created_at', '>=', $startDate)
                ->distinct('ip_address')
                ->count('ip_address'),
            'concurrent_logins_detected' => ConcurrentLogin::where('created_at', '>=', $startDate)->count(),
            'flagged_sessions' => UserSession::flagged()->count(),
            'by_device_type' => UserSession::where('created_at', '>=', $startDate)
                ->select('device_type')
                ->groupBy('device_type')
                ->selectRaw('count(*) as count')
                ->pluck('count', 'device_type')
                ->toArray(),
            'by_location' => UserSession::where('created_at', '>=', $startDate)
                ->select('location')
                ->groupBy('location')
                ->selectRaw('count(*) as count')
                ->pluck('count', 'location')
                ->toArray(),
        ];
    }

    /**
     * Cleanup old sessions
     */
    public function cleanupOldSessions(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return UserSession::where('created_at', '<', $cutoffDate)
            ->where('is_active', false)
            ->delete();
    }

    /**
     * Cleanup old activities
     */
    public function cleanupOldActivities(int $daysToKeep = 60): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        return SessionActivity::where('created_at', '<', $cutoffDate)->delete();
    }
}

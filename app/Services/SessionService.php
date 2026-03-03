<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;

class SessionService
{
    /**
     * Create a new session when user logs in
     */
    public function createSession(User $user, string $ipAddress, string $userAgent, array $location = []): UserSession
    {
        // Mark other sessions as non-current
        UserSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->update(['is_current' => false]);

        // Generate device fingerprint
        $deviceFingerprint = $this->generateDeviceFingerprint($userAgent, $ipAddress);

        // Create new session
        $session = UserSession::create([
            'user_id' => $user->id,
            'session_id' => session()->getId(),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_fingerprint' => $deviceFingerprint,
            'device_type' => $this->getDeviceType($userAgent),
            'browser' => $this->getBrowserInfo($userAgent),
            'city' => $location['city'] ?? 'Unknown',
            'country' => $location['country'] ?? 'Unknown',
            'logged_in_at' => now(),
            'last_activity_at' => now(),
            'status' => 'active',
            'is_current' => true,
        ]);

        return $session;
    }

    /**
     * Record user logout
     */
    public function logoutSession(User $user, string $reason = 'manual'): ?UserSession
    {
        $session = UserSession::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('is_current', true)
            ->first();

        if ($session) {
            $session->logout();   // UserSession::logout() takes no parameters
        }

        return $session;
    }

    /**
     * Terminate a specific session
     */
    public function terminateSession(int $sessionId, User $user): bool
    {
        $session = UserSession::find($sessionId);

        if (! $session || $session->user_id !== $user->id) {
            return false;
        }

        $session->terminate('user');

        return true;
    }

    /**
     * Terminate all other sessions for a user
     */
    public function terminateOtherSessions(User $user): int
    {
        // Get current session ID
        $currentSessionId = session()->getId();

        $session = UserSession::where('user_id', $user->id)
            ->where('session_id', $currentSessionId)
            ->first();

        if (! $session) {
            return 0;
        }

        // Terminate all sessions except current
        return UserSession::where('user_id', $user->id)
            ->where('id', '!=', $session->id)
            ->where('status', 'active')
            ->update([
                'status' => 'terminated',
                'logged_out_at' => now(),
                'logout_reason' => 'user_terminated_others',
            ]);
    }

    /**
     * Get current active session for user
     */
    public function getCurrentSession(User $user): ?UserSession
    {
        return UserSession::where('user_id', $user->id)
            ->where('session_id', session()->getId())
            ->where('status', 'active')
            ->first();
    }

    /**
     * Get all active sessions for user
     */
    public function getActiveSessions(User $user)
    {
        return UserSession::where('user_id', $user->id)
            ->active()
            ->orderBy('is_current', 'desc')
            ->orderBy('logged_in_at', 'desc')
            ->get();
    }

    /**
     * Get all sessions for user (including inactive)
     */
    public function getAllSessions(User $user)
    {
        return UserSession::where('user_id', $user->id)
            ->orderBy('logged_in_at', 'desc')
            ->paginate(15);
    }

    /**
     * Detect simultaneous logins from different IPs
     */
    public function detectSimultaneousLogins(User $user): array
    {
        $activeSessions = UserSession::where('user_id', $user->id)
            ->active()
            ->get();

        $ips = $activeSessions->pluck('ip_address')->unique();

        return [
            'has_multiple_ips' => $ips->count() > 1,
            'ips' => $ips->values()->toArray(),
            'count' => $activeSessions->count(),
            'sessions' => $activeSessions,
        ];
    }

    /**
     * Record activity update for session
     */
    public function recordActivity(User $user): void
    {
        $session = $this->getCurrentSession($user);
        if ($session) {
            $session->recordActivity();
        }
    }

    /**
     * Cleanup old inactive sessions (keep 90 days of history)
     */
    public function cleanupOldSessions(?int $daysToKeep = 90): int
    {
        return UserSession::where('created_at', '<', now()->subDays($daysToKeep)->startOfDay())
            ->delete();
    }

    /**
     * Generate device fingerprint
     */
    private function generateDeviceFingerprint(string $userAgent, string $ipAddress): string
    {
        return hash('sha256', $userAgent.'|'.$ipAddress);
    }

    /**
     * Get device type from user agent
     */
    private function getDeviceType(string $userAgent): string
    {
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/ipad|android|tablet/i', $userAgent)) {
            return 'Tablet';
        }

        return 'Desktop';
    }

    /**
     * Get browser info from user agent
     */
    private function getBrowserInfo(string $userAgent): string
    {
        if (preg_match('/chrome/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/safari/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/edge|edg\//i', $userAgent)) {
            return 'Edge';
        }

        return 'Unknown';
    }

    /**
     * Get session statistics for user
     */
    public function getSessionStats(User $user): array
    {
        $sessions = UserSession::forUser($user->id);

        return [
            'active_count' => $sessions->active()->count(),
            'total_sessions_today' => $sessions->where('logged_in_at', '>=', now()->startOfDay())->count(),
            'total_sessions_week' => $sessions->where('logged_in_at', '>=', now()->subDays(7)->startOfDay())->count(),
            'unique_ips' => $sessions->active()->pluck('ip_address')->unique()->count(),
            'unique_devices' => $sessions->active()->pluck('device_fingerprint')->unique()->count(),
        ];
    }
}

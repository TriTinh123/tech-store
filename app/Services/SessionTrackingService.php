<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\UserSession;
use Illuminate\Http\Request;

class SessionTrackingService
{
    /**
     * Log an activity
     */
    public function logActivity(User $user, string $action, string $description, Request $request): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->getMethod(),
            'url' => $request->getPathInfo(),
            'status_code' => 200, // Will be updated if available
        ]);
    }

    /**
     * Update session activity
     */
    public function updateSessionActivity(User $user, Request $request): ?UserSession
    {
        try {
            $sessionId = session()->getId();

            $userSession = UserSession::where('user_id', $user->id)
                ->where('session_id', $sessionId)
                ->whereNull('logged_out_at')
                ->first();

            if ($userSession) {
                $userSession->update([
                    'last_activity_at' => now(),
                    'last_activity_ip' => $request->ip(),
                    'last_activity_url' => $request->getPathInfo(),
                    'is_active' => true,
                ]);

                return $userSession;
            }

            return null;
        } catch (\Exception $e) {
            \Log::error('Failed to update session activity: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Get user's active sessions
     */
    public function getUserActiveSessions(User $user)
    {
        return UserSession::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->where('is_active', true)
            ->orderByDesc('last_activity_at')
            ->get();
    }

    /**
     * Get user's activity logs
     */
    public function getUserActivityLogs(User $user, int $limit = 50)
    {
        return ActivityLog::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity logs by action
     */
    public function getActivityLogsByAction(string $action, int $limit = 50)
    {
        return ActivityLog::where('action', 'LIKE', "%{$action}%")
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent activity across all users
     */
    public function getRecentActivity(int $limit = 50)
    {
        return ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Track a custom event
     */
    public function trackEvent(User $user, string $eventName, array $data = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'EVENT:'.strtoupper($eventName),
            'description' => json_encode($data),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => 'EVENT',
            'url' => request()->getPathInfo(),
        ]);
    }

    /**
     * Get user activity statistics
     */
    public function getUserActivityStats(User $user, string $period = 'today')
    {
        $query = ActivityLog::where('user_id', $user->id);

        $dateFrom = match ($period) {
            'today' => today(),
            'week' => today()->subDays(7),
            'month' => today()->subDays(30),
            'year' => today()->subDays(365),
            default => today(),
        };

        $query->where('created_at', '>=', $dateFrom);

        return [
            'total_activities' => $query->count(),
            'activities_by_action' => $query->groupBy('action')
                ->selectRaw('action, COUNT(*) as count')
                ->pluck('count', 'action'),
            'activities_by_method' => $query->groupBy('method')
                ->selectRaw('method, COUNT(*) as count')
                ->pluck('count', 'method'),
            'unique_ips' => $query->distinct('ip_address')->count('ip_address'),
            'unique_devices' => $query->distinct('user_agent')->count('user_agent'),
        ];
    }

    /**
     * Get most active users
     */
    public function getMostActiveUsers(int $limit = 10, string $period = 'today')
    {
        $dateFrom = match ($period) {
            'today' => today(),
            'week' => today()->subDays(7),
            'month' => today()->subDays(30),
            'year' => today()->subDays(365),
            default => today(),
        };

        return ActivityLog::where('created_at', '>=', $dateFrom)
            ->groupBy('user_id')
            ->selectRaw('user_id, COUNT(*) as activity_count')
            ->with('user')
            ->orderByDesc('activity_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user login history
     */
    public function getUserLoginHistory(User $user, int $limit = 20)
    {
        return ActivityLog::where('user_id', $user->id)
            ->whereIn('action', ['GET login', 'POST /login'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Detect suspicious activities
     */
    public function detectSuspiciousActivity(User $user, string $threshold = 'medium'): array
    {
        $activities = ActivityLog::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subHours(1))
            ->get();

        $suspicious = [];

        // High volume of requests in short time
        if ($activities->count() > 100) {
            $suspicious[] = [
                'type' => 'high_request_volume',
                'severity' => 'high',
                'message' => 'Abnormally high request volume detected',
                'count' => $activities->count(),
            ];
        }

        // Request from multiple IPs
        $ips = $activities->pluck('ip_address')->unique()->count();
        if ($ips > 3) {
            $suspicious[] = [
                'type' => 'multiple_ips',
                'severity' => 'medium',
                'message' => 'Requests from multiple IP addresses',
                'ips' => $ips,
            ];
        }

        // Failed login attempts
        $failedLogins = ActivityLog::where('user_id', $user->id)
            ->where('action', 'LIKE', '%login%')
            ->where('status_code', '!=', 200)
            ->where('created_at', '>=', now()->subHours(1))
            ->count();

        if ($failedLogins > 5) {
            $suspicious[] = [
                'type' => 'failed_logins',
                'severity' => 'high',
                'message' => 'Multiple failed login attempts',
                'count' => $failedLogins,
            ];
        }

        return $suspicious;
    }

    /**
     * Cleanup old activity logs
     */
    public function cleanupOldLogs(int $daysOld = 30): int
    {
        return ActivityLog::where('created_at', '<', now()->subDays($daysOld))->delete();
    }

    /**
     * Mark session as inactive
     */
    public function markSessionInactive(User $user): void
    {
        $sessionId = session()->getId();

        UserSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->update([
                'is_active' => false,
                'logged_out_at' => now(),
            ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoResponse;
use App\Models\BlockedIp;
use App\Models\ConcurrentLogin;
use App\Models\Notification;
use App\Models\SuspiciousLogin;
use App\Models\User;
use App\Models\UserSession;

class SecurityDashboardController extends Controller
{
    /**
     * Display comprehensive security dashboard
     */
    public function index()
    {
        // Key Statistics
        $stats = [
            'active_users' => User::where('is_blocked', false)->count(),
            'total_users' => User::count(),
            'active_sessions' => UserSession::whereNull('logged_out_at')->count(),
            'concurrent_logins' => ConcurrentLogin::where('status', 'detected')->count(),
            'blocked_ips' => BlockedIp::where('is_active', true)->count(),
            'suspicious_activities' => SuspiciousLogin::where('is_resolved', false)->count(),
            'unread_notifications' => Notification::unread()->count(),
            'total_sessions' => UserSession::count(),
            'auto_responses' => AutoResponse::where('status', 'active')->count(),
        ];

        // Recent concurrent logins (last 5)
        $concurrentLogins = ConcurrentLogin::where('status', 'detected')
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent suspicious activities (last 5)
        $suspiciousActivities = SuspiciousLogin::where('is_resolved', false)
            ->with('user')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent security notifications (last 10)
        $recentNotifications = Notification::query()
            ->with('user')
            ->where('severity', '!=', 'info')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Active sessions by device type
        $sessionsByDevice = UserSession::whereNull('logged_out_at')
            ->groupBy('device_type')
            ->selectRaw('device_type, COUNT(*) as count')
            ->get()
            ->pluck('count', 'device_type');

        // Top locations with active sessions
        $topLocations = UserSession::whereNull('logged_out_at')
            ->whereNotNull('location')
            ->groupBy('location')
            ->selectRaw('location, COUNT(*) as count')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Login attempts last 24 hours
        $loginAttempts24h = \App\Models\LoginAttempt::where('attempted_at', '>=', now()->subHours(24))
            ->count();

        $failedLogins24h = \App\Models\LoginAttempt::where('attempted_at', '>=', now()->subHours(24))
            ->where('success', false)
            ->count();

        // Notification delivery stats
        $notificationStats = [
            'email_sent_today' => \App\Models\NotificationLog::where('channel', 'email')
                ->whereDate('sent_at', today())
                ->count(),
            'sms_sent_today' => \App\Models\NotificationLog::where('channel', 'sms')
                ->whereDate('sent_at', today())
                ->count(),
            'failed_notifications' => \App\Models\NotificationLog::where('status', 'failed')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        return view('admin.security-dashboard-advanced', [
            'stats' => $stats,
            'concurrentLogins' => $concurrentLogins,
            'suspiciousActivities' => $suspiciousActivities,
            'recentNotifications' => $recentNotifications,
            'sessionsByDevice' => $sessionsByDevice,
            'topLocations' => $topLocations,
            'loginAttempts24h' => $loginAttempts24h,
            'failedLogins24h' => $failedLogins24h,
            'notificationStats' => $notificationStats,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\SessionTrackingService;

class SessionTrackingController extends Controller
{
    protected $sessionTrackingService;

    public function __construct(SessionTrackingService $sessionTrackingService)
    {
        $this->sessionTrackingService = $sessionTrackingService;
    }

    /**
     * Display activity logs dashboard
     */
    public function activityLogs()
    {
        $recentActivities = $this->sessionTrackingService->getRecentActivity(50);
        $mostActiveUsers = $this->sessionTrackingService->getMostActiveUsers(10, 'today');

        return view('admin.activity-logs', [
            'activities' => $recentActivities,
            'mostActiveUsers' => $mostActiveUsers,
        ]);
    }

    /**
     * Get user's activity logs
     */
    public function userActivityLogs($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $activityLogs = $this->sessionTrackingService->getUserActivityLogs($user, 100);
        $stats = $this->sessionTrackingService->getUserActivityStats($user, 'week');
        $suspiciousActivities = $this->sessionTrackingService->detectSuspiciousActivity($user);

        return view('admin.user-activity-logs', [
            'user' => $user,
            'activities' => $activityLogs,
            'stats' => $stats,
            'suspicious' => $suspiciousActivities,
        ]);
    }

    /**
     * Display user sessions
     */
    public function userSessions($userId)
    {
        $user = \App\Models\User::findOrFail($userId);
        $sessions = $this->sessionTrackingService->getUserActiveSessions($user);

        return view('admin.user-sessions', [
            'user' => $user,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Download activity logs CSV
     */
    public function exportActivityLogs()
    {
        $activities = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(1000)
            ->get();

        $csv = "User,Action,Description,IP Address,Method,URL,Status Code,Timestamp\n";

        foreach ($activities as $activity) {
            $userName = $activity->user ? $activity->user->name : 'System';
            $csv .= "\"$userName\"";
            $csv .= ",\"{$activity->action}\"";
            $csv .= ",\"{$activity->description}\"";
            $csv .= ",\"{$activity->ip_address}\"";
            $csv .= ",\"{$activity->method}\"";
            $csv .= ",\"{$activity->url}\"";
            $csv .= ",\"{$activity->status_code}\"";
            $csv .= ",\"{$activity->created_at}\"";
            $csv .= "\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="activity-logs.csv"');
    }

    /**
     * Get activity statistics
     */
    public function statistics()
    {
        $totalActivities = ActivityLog::count();
        $activitiesInLast24h = ActivityLog::where('created_at', '>=', now()->subHours(24))->count();
        $activitiesThisWeek = ActivityLog::where('created_at', '>=', now()->subDays(7))->count();
        $activitiesThisMonth = ActivityLog::where('created_at', '>=', now()->subDays(30))->count();

        $activityByMethod = ActivityLog::selectRaw('method, COUNT(*) as count')
            ->groupBy('method')
            ->pluck('count', 'method');

        $activityByAction = ActivityLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'action');

        return view('admin.activity-statistics', [
            'totalActivities' => $totalActivities,
            'activitiesInLast24h' => $activitiesInLast24h,
            'activitiesThisWeek' => $activitiesThisWeek,
            'activitiesThisMonth' => $activitiesThisMonth,
            'activityByMethod' => $activityByMethod,
            'activityByAction' => $activityByAction,
        ]);
    }

    /**
     * Cleanup old activity logs
     */
    public function cleanupActivityLogs()
    {
        $deleted = $this->sessionTrackingService->cleanupOldLogs(30);

        return redirect()->back()->with('success', "Deleted {$deleted} old activity logs.");
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\UserSession;
use App\Services\SessionTrackingService;

class UserSessionManagementController extends Controller
{
    protected $sessionTrackingService;

    public function __construct(SessionTrackingService $sessionTrackingService)
    {
        $this->sessionTrackingService = $sessionTrackingService;
        $this->middleware(['auth', 'require_3fa']);
    }

    /**
     * Display list of user's active sessions
     */
    public function index()
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();

        // Get all active sessions for user
        $activeSessions = UserSession::where('user_id', $user->id)
            ->whereNull('logged_out_at')
            ->where('is_active', true)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $session->is_current = $session->session_id === $currentSessionId;

                return $session;
            });

        // Get inactive sessions (last 7 days)
        $inactiveSessions = UserSession::where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereNotNull('logged_out_at')
                    ->orWhere('is_active', false);
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('sessions.index', [
            'activeSessions' => $activeSessions,
            'inactiveSessions' => $inactiveSessions,
            'currentSessionId' => $currentSessionId,
        ]);
    }

    /**
     * Display session details
     */
    public function show($sessionId)
    {
        $user = auth()->user();
        $session = UserSession::where('user_id', $user->id)
            ->findOrFail($sessionId);

        // Get activity log for this session
        $activities = ActivityLog::where('user_id', $user->id)
            ->where('created_at', '>=', $session->created_at)
            ->where('created_at', '<=', $session->logged_out_at ?? now())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        // Calculate session duration
        $endTime = $session->logged_out_at ?? now();
        $duration = $session->created_at->diffForHumans($endTime, true);

        return view('sessions.show', [
            'session' => $session,
            'activities' => $activities,
            'duration' => $duration,
            'isCurrent' => $session->session_id === session()->getId(),
        ]);
    }

    /**
     * Terminate a session
     */
    public function terminate($sessionId)
    {
        $user = auth()->user();
        $session = UserSession::where('user_id', $user->id)->findOrFail($sessionId);

        // Don't allow terminating current session via this route (should use logout)
        if ($session->session_id === session()->getId()) {
            return redirect()->back()->with('error', 'Không thể kết thúc phiên hiện tại. Vui lòng sử dụng đăng xuất.');
        }

        // Terminate the session
        $session->update([
            'is_active' => false,
            'logged_out_at' => now(),
        ]);

        // Log this action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'SESSION_TERMINATED',
            'description' => "Người dùng kết thúc phiên: {$session->device_type} ({$session->browser})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => 'POST',
            'url' => '/sessions/terminate',
        ]);

        return redirect()->route('sessions.index')->with('success', 'Phiên đã được kết thúc.');
    }

    /**
     * Terminate all other sessions
     */
    public function terminateOthers()
    {
        $user = auth()->user();
        $currentSessionId = session()->getId();

        // Terminate all other active sessions
        $count = UserSession::where('user_id', $user->id)
            ->where('session_id', '!=', $currentSessionId)
            ->whereNull('logged_out_at')
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'logged_out_at' => now(),
            ]);

        // Log this action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'ALL_SESSIONS_TERMINATED',
            'description' => "Người dùng kết thúc tất cả các phiên khác ($count phiên)",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => 'POST',
            'url' => '/sessions/terminate-others',
        ]);

        return redirect()->route('sessions.index')->with('success', "Đã kết thúc $count phiên đăng nhập khác.");
    }

    /**
     * Display trusted devices
     */
    public function trustedDevices()
    {
        $user = auth()->user();

        // Get unique trusted devices (successful logins)
        $devices = UserSession::where('user_id', $user->id)
            ->where('success', true)
            ->select('device_fingerprint', 'ip_address', 'device_type', 'browser', 'os', 'location', 'created_at')
            ->distinct()
            ->orderByDesc('created_at')
            ->get()
            ->unique('device_fingerprint');

        // Get device stats
        $deviceStats = [];
        foreach ($devices as $device) {
            $sessions = UserSession::where('user_id', $user->id)
                ->where('device_fingerprint', $device->device_fingerprint)
                ->count();

            $deviceStats[] = [
                'device' => $device,
                'session_count' => $sessions,
            ];
        }

        return view('sessions.trusted-devices', [
            'devices' => $deviceStats,
        ]);
    }

    /**
     * Mark device as trusted
     */
    public function markTrusted($deviceFingerprint)
    {
        $user = auth()->user();

        // Find the device
        $session = UserSession::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        if (! $session) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Update all sessions with this device fingerprint as trusted
        UserSession::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->update(['is_trusted' => true]);

        // Log action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'DEVICE_TRUSTED',
            'description' => "Thiết bị được đánh dấu là đáng tin cậy: {$session->device_type}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => 'POST',
            'url' => '/sessions/trust-device',
        ]);

        return redirect()->back()->with('success', 'Thiết bị đã được đánh dấu là đáng tin cậy.');
    }

    /**
     * Remove device trust
     */
    public function removeTrust($deviceFingerprint)
    {
        $user = auth()->user();

        // Find the device
        $session = UserSession::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->first();

        if (! $session) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        // Remove trust from all sessions with this device fingerprint
        UserSession::where('user_id', $user->id)
            ->where('device_fingerprint', $deviceFingerprint)
            ->update(['is_trusted' => false]);

        // Log action
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'DEVICE_UNTRUSTED',
            'description' => "Loại bỏ tin cậy khỏi thiết bị: {$session->device_type}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'method' => 'POST',
            'url' => '/sessions/untrust-device',
        ]);

        return redirect()->back()->with('success', 'Thiết bị không còn được tin cậy.');
    }

    /**
     * Display activity timeline
     */
    public function activityTimeline()
    {
        $user = auth()->user();

        // Get user's activity logs
        $activities = $this->sessionTrackingService->getUserActivityLogs($user, 100);

        // Get stats
        $stats = $this->sessionTrackingService->getUserActivityStats($user, 'week');

        return view('sessions.activity-timeline', [
            'activities' => $activities,
            'stats' => $stats,
        ]);
    }

    /**
     * Get session statistics (AJAX)
     */
    public function statistics()
    {
        $user = auth()->user();

        $stats = [
            'active_sessions' => UserSession::where('user_id', $user->id)
                ->whereNull('logged_out_at')
                ->where('is_active', true)
                ->count(),
            'total_sessions_today' => UserSession::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'unique_devices' => UserSession::where('user_id', $user->id)
                ->select('device_fingerprint')
                ->distinct()
                ->count(),
            'unique_locations' => UserSession::where('user_id', $user->id)
                ->select('location')
                ->whereNotNull('location')
                ->distinct()
                ->count(),
            'failed_logins_24h' => ActivityLog::where('user_id', $user->id)
                ->where('action', 'LIKE', '%login%')
                ->where('status_code', '!=', 200)
                ->where('created_at', '>=', now()->subHours(24))
                ->count(),
        ];

        return response()->json($stats);
    }
}

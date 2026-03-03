<?php

namespace App\Http\Controllers\Sessions;

use App\Http\Controllers\Controller;
use App\Models\ConcurrentLogin;
use App\Models\User;
use App\Models\UserSession;
use App\Services\SessionMonitoringService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function __construct(
        private SessionMonitoringService $sessionMonitoringService
    ) {}

    /**
     * Display user's own sessions
     */
    public function index()
    {
        $sessions = auth()->user()->sessions()->orderByDesc('last_activity_at')->get();
        $currentSessionId = session()->getId();

        return view('profile.sessions.index', [
            'sessions' => $sessions,
            'currentSessionId' => $currentSessionId,
        ]);
    }

    /**
     * Terminate a specific session
     */
    public function terminate(Request $request, UserSession $session)
    {
        // Check authorization - user can only terminate their own sessions
        if ($session->user_id !== auth()->id()) {
            abort(403, 'Không có quyền truy cập');
        }

        // Don't allow terminating current session
        if ($session->id === session()->getId()) {
            return back()->with('error', 'Không thể đăng xuất khỏi phiên hiện tại từ đây');
        }

        $this->sessionMonitoringService->terminateSession($session->id);

        return back()->with('success', 'Đã đăng xuất khỏi thiết bị');
    }

    /**
     * Terminate all other sessions
     */
    public function terminateOthers(Request $request)
    {
        $currentSessionId = session()->getId();
        $terminatedCount = $this->sessionMonitoringService->terminateOtherSessions(auth()->user(), $currentSessionId);

        return back()->with('success', "Đã đăng xuất khỏi $terminatedCount thiết bị khác");
    }

    /**
     * Admin: Display all sessions
     */
    public function adminIndex(Request $request)
    {
        $query = UserSession::with('user', 'activities')
            ->orderByDesc('last_activity_at');

        // Filter by device type
        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNull('logged_out_at');
            } elseif ($request->status === 'flagged') {
                $query->where('is_flagged', true);
            }
        }

        $sessions = $query->paginate(15);
        $concurrentLogins = ConcurrentLogin::where('status', 'detected')->get();

        // Statistics
        $stats = [
            'total_active_sessions' => UserSession::whereNull('logged_out_at')->count(),
            'concurrent_logins_count' => ConcurrentLogin::where('status', 'detected')->count(),
            'unique_devices' => UserSession::distinct('device_type')->count('device_type'),
            'unique_locations' => UserSession::distinct('location')->whereNotNull('location')->count('location'),
        ];

        return view('admin.sessions.index', [
            'sessions' => $sessions,
            'concurrentLogins' => $concurrentLogins,
            'stats' => $stats,
            'activeTab' => 'sessions',
        ]);
    }

    /**
     * Admin: Concurrent logins view
     */
    public function adminConcurrentLogins()
    {
        $concurrentLogins = ConcurrentLogin::all();
        $sessions = UserSession::whereNull('logged_out_at')->paginate(15);

        $stats = [
            'total_active_sessions' => UserSession::whereNull('logged_out_at')->count(),
            'concurrent_logins_count' => ConcurrentLogin::where('status', 'detected')->count(),
            'unique_devices' => UserSession::distinct('device_type')->count('device_type'),
            'unique_locations' => UserSession::distinct('location')->whereNotNull('location')->count('location'),
        ];

        return view('admin.sessions.index', [
            'sessions' => $sessions,
            'concurrentLogins' => $concurrentLogins,
            'stats' => $stats,
            'activeTab' => 'concurrent',
        ]);
    }

    /**
     * Admin: View session details
     */
    public function adminShow(UserSession $session)
    {
        return view('admin.sessions.show', [
            'session' => $session->load('user', 'activities'),
        ]);
    }

    /**
     * Admin: Terminate a session
     */
    public function adminTerminate(Request $request, UserSession $session)
    {
        $this->sessionMonitoringService->terminateSession($session->id);

        return back()->with('success', 'Đã đăng xuất phiên');
    }

    /**
     * Admin: Flag session as suspicious
     */
    public function flag(Request $request, UserSession $session)
    {
        $reason = $request->input('reason', 'Admin flagged this session as suspicious');
        $session->flag($reason);

        return back()->with('success', 'Đã đánh dấu phiên');
    }

    /**
     * Admin: Unflag session
     */
    public function unflag(Request $request, UserSession $session)
    {
        $session->update(['is_flagged' => false, 'flag_reason' => null]);

        return back()->with('success', 'Đã bỏ đánh dấu phiên');
    }

    /**
     * Admin: Confirm concurrent login as threat
     */
    public function confirmConcurrentLogin(ConcurrentLogin $concurrentLogin)
    {
        $concurrentLogin->confirm(auth()->user()->id);

        // Terminate the new session
        $newSession = UserSession::where('session_id', $concurrentLogin->second_session_id)->first();
        if ($newSession) {
            $this->sessionMonitoringService->terminateSession($newSession->id);
        }

        return back()->with('success', 'Đã xác nhận đe dọa và đăng xuất phiên mới');
    }

    /**
     * Admin: Authorize concurrent login
     */
    public function authorizeConcurrentLogin(ConcurrentLogin $concurrentLogin)
    {
        $concurrentLogin->authorize(auth()->user()->id);

        return back()->with('success', 'Đã cho phép đăng nhập đồng thời');
    }

    /**
     * Admin: Mark concurrent login as false positive
     */
    public function markFalsePositive(ConcurrentLogin $concurrentLogin)
    {
        $concurrentLogin->markFalsePositive(auth()->user()->id);

        return back()->with('success', 'Đã đánh dấu là cảnh báo giả');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\LoginAnomaly;
use App\Models\SecurityAlert;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserSession;
use Illuminate\Http\Request;

class UserSecurityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show user security dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();

        $data = [
            'user' => $user,
            'recent_alerts' => $this->getRecentAlerts($user->id),
            'recent_anomalies' => $this->getRecentAnomalies($user->id),
            'active_sessions' => $this->getActiveSessions($user->id),
            'trusted_devices' => $this->getTrustedDevices($user->id),
            'login_history' => $this->getLoginHistory($user->id, 5),
            'security_score' => $this->calculateSecurityScore($user->id),
            'recommendations' => $this->getSecurityRecommendations($user->id),
            'two_fa_enabled' => $user->two_fa_enabled ?? false,
            'three_fa_enabled' => $user->three_fa_enabled ?? false,
        ];

        return view('security.dashboard', $data);
    }

    /**
     * Show login history
     */
    public function loginHistory(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->get('per_page', 50);

        $loginHistory = UserSession::where('user_id', $user->id)
            ->where('login_at', '!=', null)
            ->orderBy('login_at', 'desc')
            ->paginate($perPage);

        return view('security.login-history', [
            'loginHistory' => $loginHistory,
            'user' => $user,
        ]);
    }

    /**
     * Show devices
     */
    public function devices()
    {
        $user = auth()->user();
        $devices = UserDevice::where('user_id', $user->id)
            ->orderBy('last_used_at', 'desc')
            ->get();

        return view('security.devices', [
            'devices' => $devices,
            'user' => $user,
        ]);
    }

    /**
     * Trust a device
     */
    public function trustDevice(Request $request, $deviceId)
    {
        $user = auth()->user();
        $device = UserDevice::where('id', $deviceId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $device->update(['is_trusted' => true]);

        return redirect()->back()->with('success', 'Device marked as trusted');
    }

    /**
     * Untrust a device
     */
    public function untrustDevice(Request $request, $deviceId)
    {
        $user = auth()->user();
        $device = UserDevice::where('id', $deviceId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $device->update(['is_trusted' => false]);

        return redirect()->back()->with('success', 'Device marked as untrusted');
    }

    /**
     * Remove device
     */
    public function removeDevice(Request $request, $deviceId)
    {
        $user = auth()->user();
        $device = UserDevice::where('id', $deviceId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $device->delete();

        return redirect()->back()->with('success', 'Device removed');
    }

    /**
     * Show active sessions
     */
    public function sessions()
    {
        $user = auth()->user();
        $sessions = UserSession::where('user_id', $user->id)
            ->where('logged_out_at', null)
            ->orderBy('login_at', 'desc')
            ->get();

        return view('security.sessions', [
            'sessions' => $sessions,
            'user' => $user,
        ]);
    }

    /**
     * End specific session
     */
    public function endSession(Request $request, $sessionId)
    {
        $user = auth()->user();
        $session = UserSession::where('id', $sessionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $session->update(['logged_out_at' => now()]);

        return redirect()->back()->with('success', 'Session ended');
    }

    /**
     * End all sessions
     */
    public function endAllSessions(Request $request)
    {
        $user = auth()->user();

        UserSession::where('user_id', $user->id)
            ->where('logged_out_at', null)
            ->update(['logged_out_at' => now()]);

        return redirect()->back()->with('success', 'All sessions ended');
    }

    /**
     * Show security alerts
     */
    public function alerts()
    {
        $user = auth()->user();
        $alerts = SecurityAlert::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('security.alerts', [
            'alerts' => $alerts,
            'user' => $user,
        ]);
    }

    /**
     * Mark alert as read
     */
    public function markAlertAsRead(Request $request, $alertId)
    {
        $user = auth()->user();
        $alert = SecurityAlert::where('id', $alertId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $alert->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    /**
     * Show 2FA settings
     */
    public function twoFaSettings()
    {
        $user = auth()->user();

        return view('security.two-fa-settings', [
            'user' => $user,
            'two_fa_enabled' => $user->two_fa_enabled ?? false,
        ]);
    }

    /**
     * Show 3FA settings
     */
    public function threeAFaSettings()
    {
        $user = auth()->user();

        return view('security.three-fa-settings', [
            'user' => $user,
            'three_fa_enabled' => $user->three_fa_enabled ?? false,
        ]);
    }

    /**
     * Get recent alerts
     */
    private function getRecentAlerts($userId)
    {
        return SecurityAlert::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get recent anomalies
     */
    private function getRecentAnomalies($userId)
    {
        return LoginAnomaly::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get active sessions
     */
    private function getActiveSessions($userId)
    {
        return UserSession::where('user_id', $userId)
            ->where('logged_out_at', null)
            ->orderBy('login_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get trusted devices
     */
    private function getTrustedDevices($userId)
    {
        return UserDevice::where('user_id', $userId)
            ->where('is_trusted', true)
            ->orderBy('last_used_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get login history
     */
    private function getLoginHistory($userId, $limit = 10)
    {
        return UserSession::where('user_id', $userId)
            ->where('login_at', '!=', null)
            ->orderBy('login_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Calculate security score for user
     */
    private function calculateSecurityScore($userId)
    {
        $user = User::find($userId);
        $score = 50; // Base score

        // 2FA enabled: +15 points
        if ($user->two_fa_enabled ?? false) {
            $score += 15;
        }

        // 3FA enabled: +20 points
        if ($user->three_fa_enabled ?? false) {
            $score += 20;
        }

        // No alerts in last 30 days: +10 points
        $recentAlerts = SecurityAlert::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        if ($recentAlerts === 0) {
            $score += 10;
        }

        // No anomalies in last 30 days: +5 points
        $recentAnomalies = LoginAnomaly::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(30))
            ->count();
        if ($recentAnomalies === 0) {
            $score += 5;
        }

        return min(100, $score);
    }

    /**
     * Get security recommendations
     */
    private function getSecurityRecommendations($userId)
    {
        $user = User::find($userId);
        $recommendations = [];

        // Check 2FA
        if (! ($user->two_fa_enabled ?? false)) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Enable Two-Factor Authentication',
                'description' => 'Add an extra layer of security to your account',
                'action' => 'Enable 2FA',
                'route' => route('security.two-fa'),
            ];
        }

        // Check 3FA
        if (! ($user->three_fa_enabled ?? false)) {
            $recommendations[] = [
                'priority' => 'medium',
                'title' => 'Enable Three-Factor Authentication',
                'description' => 'Maximum security with three authentication methods',
                'action' => 'Enable 3FA',
                'route' => route('security.three-fa'),
            ];
        }

        // Check password age
        $lastPasswordChange = $user->password_changed_at ?? $user->created_at;
        if ($lastPasswordChange->diffInDays(now()) > 90) {
            $recommendations[] = [
                'priority' => 'high',
                'title' => 'Update Your Password',
                'description' => 'Your password hasn\'t changed in over 90 days',
                'action' => 'Change Password',
                'route' => route('profile.edit'),
            ];
        }

        // Check for trusted devices
        $trustedDevices = UserDevice::where('user_id', $userId)
            ->where('is_trusted', true)
            ->count();
        if ($trustedDevices === 0) {
            $recommendations[] = [
                'priority' => 'low',
                'title' => 'Mark Your Devices',
                'description' => 'Trust your regular devices for smoother access',
                'action' => 'Manage Devices',
                'route' => route('security.devices'),
            ];
        }

        return $recommendations;
    }
}

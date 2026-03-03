<?php

namespace App\Http\Controllers;

use App\Models\AutoResponse;
use App\Models\BlockedIp;
use App\Models\SecurityConfig;
use App\Models\User;
use App\Services\AutoResponseService;
use App\Services\IpBlockingService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AdminSecuritySettingsController extends BaseController
{
    protected $ipBlockingService;

    protected $autoResponseService;

    public function __construct(
        IpBlockingService $ipBlockingService,
        AutoResponseService $autoResponseService
    ) {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->ipBlockingService = $ipBlockingService;
        $this->autoResponseService = $autoResponseService;
    }

    /**
     * Display security settings overview
     */
    public function index()
    {
        $currentConfig = SecurityConfig::getCurrent();
        $blockedIpsCount = BlockedIp::active()->count();
        $autoResponsesCount = AutoResponse::pending()->count();

        $stats = [
            'total_configs' => SecurityConfig::count(),
            'active_configs' => SecurityConfig::where('enable_security_config', true)->count(),
            'blocked_ips' => $blockedIpsCount,
            'pending_responses' => $autoResponsesCount,
            'blocking_stats' => $this->ipBlockingService->getBlockingStatistics(),
            'response_stats' => $this->autoResponseService->getResponseStatistics(),
        ];

        return view('admin.security-settings.overview', [
            'currentConfig' => $currentConfig,
            'stats' => $stats,
        ]);
    }

    /**
     * Show edit form for security settings
     */
    public function edit()
    {
        $config = SecurityConfig::getCurrent() ?? new SecurityConfig;

        $countries = [
            'VN' => 'Việt Nam (Vietnam)',
            'US' => 'Mỹ (United States)',
            'JP' => 'Nhật Bản (Japan)',
            'SG' => 'Singapore',
            'TH' => 'Thái Lan (Thailand)',
            'CN' => 'Trung Quốc (China)',
            'KR' => 'Hàn Quốc (South Korea)',
            'GB' => 'Anh (United Kingdom)',
            'DE' => 'Đức (Germany)',
            'FR' => 'Pháp (France)',
        ];

        return view('admin.security-settings.edit', [
            'config' => $config,
            'countries' => $countries,
        ]);
    }

    /**
     * Update security settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'auth_level' => 'required|in:basic,standard,strict,ultra',
            'require_otp' => 'boolean',
            'otp_method' => 'required|in:email,sms,both',
            'otp_expiry_minutes' => 'required|integer|min:5|max:60',
            'require_security_questions' => 'boolean',
            'security_questions_min_answers' => 'required|integer|min:1|max:5',
            'require_device_verification' => 'boolean',
            'max_concurrent_devices' => 'required|integer|min:1|max:10',
            'max_login_attempts' => 'required|integer|min:3|max:20',
            'login_attempt_lockout_minutes' => 'required|integer|min:5|max:1440',
            'anomaly_detection_enabled' => 'boolean',
            'anomaly_detection_threshold' => 'required|integer|min:0|max:100',
            'auto_lockout_critical' => 'boolean',
            'enable_ip_blocking' => 'boolean',
            'block_ips_after_failed_attempts' => 'required|integer|min:3|max:50',
            'ip_block_duration_minutes' => 'required|integer|min:5|max:1440',
            'enable_geo_restrictions' => 'boolean',
            'allowed_countries' => 'array',
            'allowed_countries.*' => 'string|size:2',
            'require_new_location_confirmation' => 'boolean',
            'allow_concurrent_sessions' => 'boolean',
            'max_concurrent_sessions' => 'required|integer|min:1|max:10',
            'session_timeout_minutes' => 'required|integer|min:5|max:1440',
            'notify_on_new_ip' => 'boolean',
            'notify_on_new_device' => 'boolean',
            'notify_on_failed_attempts' => 'boolean',
            'notify_on_account_lockout' => 'boolean',
            'enforce_password_expiry_days' => 'nullable|integer|min:30',
            'require_password_history_count' => 'nullable|integer|min:1|max:24',
            'require_strong_password' => 'boolean',
            'enable_biometric_authentication' => 'boolean',
            'enable_hardware_key_support' => 'boolean',
            'idle_timeout_minutes' => 'required|integer|min:5|max:1440',
            'suspicious_activity_threshold' => 'required|integer|min:0|max:100',
            'log_all_activities' => 'boolean',
            'data_retention_days' => 'required|integer|min:7|max:3650',
            'enable_security_config' => 'boolean',
        ]);

        // Get current config or create new
        $config = SecurityConfig::getCurrent();
        if (! $config) {
            $config = new SecurityConfig;
        }

        // Deactivate previous config if enabling a new one
        if ($validated['enable_security_config']) {
            SecurityConfig::where('id', '!=', $config->id ?? 0)
                ->update(['enable_security_config' => false]);
        }

        // Update config
        $config->fill($validated);
        $config->created_by_admin_id = auth()->id();
        $config->updated_by_admin_id = auth()->id();
        $config->save();

        return redirect()
            ->route('admin.security-settings.index')
            ->with('success', 'Cấu hình bảo mật đã được cập nhật thành công!');
    }

    /**
     * Display list of blocked IPs
     */
    public function blockedIps()
    {
        $blockedIps = BlockedIp::with('blockedByAdmin')
            ->latest('blocked_at')
            ->paginate(20);

        $stats = $this->ipBlockingService->getBlockingStatistics();

        return view('admin.security-settings.blocked-ips', [
            'blockedIps' => $blockedIps,
            'stats' => $stats,
        ]);
    }

    /**
     * Block an IP address
     */
    public function blockIp(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:500',
            'is_permanent' => 'boolean',
            'block_duration_minutes' => 'required|integer|min:5|max:10080',
        ]);

        // Check if already blocked
        if (BlockedIp::where('ip_address', $validated['ip_address'])->exists()) {
            return back()->with('error', 'IP này đã bị chặn rồi!');
        }

        $this->ipBlockingService->blockIp(
            $validated['ip_address'],
            $validated['reason'] ?? null,
            auth()->id(),
            $validated['is_permanent'],
            $validated['block_duration_minutes']
        );

        return back()->with('success', 'IP đã bị chặn thành công!');
    }

    /**
     * Unblock an IP address
     */
    public function unblockIp($ipAddress)
    {
        $success = $this->ipBlockingService->unblockIp($ipAddress, auth()->id());

        if ($success) {
            return back()->with('success', 'IP đã được bỏ chặn!');
        }

        return back()->with('error', 'Không tìm thấy IP này!');
    }

    /**
     * Update blocked IP information
     */
    public function updateBlockedIp(Request $request, $ipAddress)
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
            'risk_level' => 'required|in:low,medium,high,critical',
            'extend_block_minutes' => 'nullable|integer|min:5|max:1440',
            'notes' => 'nullable|string',
        ]);

        $success = $this->ipBlockingService->updateBlockInfo($ipAddress, $validated);

        if ($success) {
            return back()->with('success', 'Thông tin IP đã được cập nhật!');
        }

        return back()->with('error', 'Không tìm thấy IP này!');
    }

    /**
     * Get blocked IP details
     */
    public function showBlockedIp($ipAddress)
    {
        $blockedIp = BlockedIp::where('ip_address', $ipAddress)
            ->with('blockedByAdmin')
            ->firstOrFail();

        $relatedAttempts = \App\Models\LoginAttempt::where('ip_address', $ipAddress)
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.security-settings.blocked-ip-detail', [
            'blockedIp' => $blockedIp,
            'relatedAttempts' => $relatedAttempts,
        ]);
    }

    /**
     * Display auto-responses
     */
    public function autoResponses()
    {
        $responses = AutoResponse::with(['user', 'suspiciousLogin'])
            ->latest('triggered_at')
            ->paginate(20);

        $stats = $this->autoResponseService->getResponseStatistics();

        return view('admin.security-settings.auto-responses', [
            'responses' => $responses,
            'stats' => $stats,
        ]);
    }

    /**
     * Show auto-response details
     */
    public function showAutoResponse($id)
    {
        $response = AutoResponse::with([
            'user',
            'suspiciousLogin',
            'triggeredByAdmin',
            'reviewedByAdmin',
        ])->findOrFail($id);

        return view('admin.security-settings.auto-response-detail', [
            'response' => $response,
        ]);
    }

    /**
     * Review and approve auto-response
     */
    public function approveAutoResponse(Request $request, $id)
    {
        $response = AutoResponse::findOrFail($id);

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $response->reviewBy(auth()->id(), $validated['notes'] ?? null);
        $response->markExecuted('Approved and executed by admin');

        return back()->with('success', 'Phản ứng đã được duyệt và thực thi!');
    }

    /**
     * Reject auto-response
     */
    public function rejectAutoResponse(Request $request, $id)
    {
        $response = AutoResponse::findOrFail($id);

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $response->markCancelled($validated['reason']);
        $response->reviewBy(auth()->id(), "Rejected: {$validated['reason']}");

        return back()->with('success', 'Phản ứng đã bị từ chối!');
    }

    /**
     * Test response action
     */
    public function testResponseAction(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:send_alert,request_confirmation,lock_account',
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($validated['user_id']);

        $success = $this->autoResponseService->testResponseAction(
            $validated['action'],
            $user->id
        );

        if ($success) {
            return back()->with('success', "Phản ứng '{$validated['action']}' đã được test gửi đến {$user->email}");
        }

        return back()->with('error', 'Lỗi khi test phản ứng!');
    }

    /**
     * Get security statistics dashboard
     */
    public function statistics()
    {
        $config = SecurityConfig::getCurrent();

        // Last 30 days statistics
        $loginAttempts = \App\Models\LoginAttempt::whereBetween(
            'created_at',
            [now()->subDays(30), now()]
        )->count();

        $successfulLogins = \App\Models\LoginAttempt::where('status', 'success')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->count();

        $failedLogins = \App\Models\LoginAttempt::where('status', 'failed')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->count();

        $suspiciousLogins = \App\Models\SuspiciousLogin::whereBetween(
            'created_at',
            [now()->subDays(30), now()]
        )->count();

        $lockedAccounts = User::where('is_blocked', true)->count();

        $activeBlocks = BlockedIp::active()->count();

        // Daily trend (last 7 days)
        $dailyTrend = \App\Models\LoginAttempt::selectRaw('DATE(created_at) as date, count(*) as total, sum(case when status = "success" then 1 else 0 end) as success')
            ->whereBetween('created_at', [now()->subDays(7), now()])
            ->groupBy('date')
            ->get();

        // Risk distribution
        $riskDistribution = BlockedIp::active()
            ->groupBy('risk_level')
            ->selectRaw('risk_level, count(*) as count')
            ->pluck('count', 'risk_level');

        // Top suspicious users
        $topSuspiciousUsers = \App\Models\SuspiciousLogin::with('user')
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->groupBy('user_id')
            ->selectRaw('user_id, count(*) as count')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return view('admin.security-settings.statistics', [
            'config' => $config,
            'loginAttempts' => $loginAttempts,
            'successfulLogins' => $successfulLogins,
            'failedLogins' => $failedLogins,
            'suspiciousLogins' => $suspiciousLogins,
            'lockedAccounts' => $lockedAccounts,
            'activeBlocks' => $activeBlocks,
            'dailyTrend' => $dailyTrend,
            'riskDistribution' => $riskDistribution,
            'topSuspiciousUsers' => $topSuspiciousUsers,
        ]);
    }

    /**
     * Export security report
     */
    public function exportReport(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|in:blocked_ips,auto_responses,all',
            'format' => 'required|in:csv,json',
        ]);

        $data = [];

        if (in_array($validated['report_type'], ['blocked_ips', 'all'])) {
            $data['blocked_ips'] = BlockedIp::active()
                ->with('blockedByAdmin')
                ->get()
                ->map(function ($ip) {
                    return [
                        'ip_address' => $ip->ip_address,
                        'country' => $ip->country_code,
                        'block_type' => $ip->block_type,
                        'risk_level' => $ip->risk_level,
                        'blocked_at' => $ip->blocked_at,
                        'blocked_by' => $ip->blockedByAdmin?->name ?? 'System',
                    ];
                })->toArray();
        }

        if (in_array($validated['report_type'], ['auto_responses', 'all'])) {
            $data['auto_responses'] = AutoResponse::with('user')
                ->latest('triggered_at')
                ->limit(500)
                ->get()
                ->map(function ($response) {
                    return [
                        'user' => $response->user->email,
                        'trigger_type' => $response->trigger_type,
                        'response_action' => $response->response_action,
                        'severity' => $response->severity,
                        'status' => $response->status,
                        'triggered_at' => $response->triggered_at,
                    ];
                })->toArray();
        }

        if ($validated['format'] === 'json') {
            return response()->json($data)
                ->header('Content-Disposition', 'attachment; filename="security-report.json"');
        }

        // CSV format
        $csv = "Report Type,Data\n";
        $csv .= implode("\n", array_map(function ($key, $value) {
            return $key.','.json_encode($value);
        }, array_keys($data), array_values($data)));

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="security-report.csv"');
    }

    /**
     * Get whitelisted IPs for display
     */
    public function whitelist()
    {
        // For future implementation: IPs that bypass certain security checks
        return view('admin.security-settings.whitelist', [
            'whitelistedIps' => [],
        ]);
    }

    /**
     * Add IP to whitelist
     */
    public function addToWhitelist(Request $request)
    {
        $validated = $request->validate([
            'ip_address' => 'required|ip',
            'reason' => 'nullable|string|max:500',
        ]);

        // For future implementation
        return back()->with('success', 'IP đã được thêm vào danh sách trắng!');
    }
}

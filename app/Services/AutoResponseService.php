<?php

namespace App\Services;

use App\Mail\SecurityAlertMail;
use App\Models\AutoResponse;
use App\Models\SecurityAlert;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AutoResponseService
{
    protected $ipBlockingService;

    protected $alertService;

    public function __construct(
        IpBlockingService $ipBlockingService,
        AlertService $alertService
    ) {
        $this->ipBlockingService = $ipBlockingService;
        $this->alertService = $alertService;
    }

    /**
     * Handle auto response based on anomaly severity
     */
    public function handleAnomalyResponse($user, $suspiciousLogin, $severity = 'medium')
    {
        // Get security config
        $config = \App\Models\SecurityConfig::getCurrent();
        if (! $config || ! $config->enable_security_config) {
            return null;
        }

        // Determine response action based on severity
        $responseAction = $this->determineResponseAction($severity, $config);

        // Create auto response record
        $autoResponse = AutoResponse::create([
            'user_id' => $user->id,
            'suspicious_login_id' => $suspiciousLogin->id,
            'trigger_type' => 'anomaly_detection',
            'severity' => $severity,
            'trigger_description' => "Phát hiện bất thường: {$suspiciousLogin->anomaly_type}",
            'response_action' => $responseAction,
            'action_description' => $this->getActionDescription($responseAction),
            'status' => 'pending',
            'security_config_snapshot' => $config->toArray(),
            'user_context' => [
                'ip_address' => $suspiciousLogin->ip_address,
                'device_fingerprint' => $suspiciousLogin->device_fingerprint,
                'user_agent' => $suspiciousLogin->user_agent,
                'location' => $suspiciousLogin->location,
            ],
            'anomaly_details' => [
                'anomaly_type' => $suspiciousLogin->anomaly_type,
                'risk_score' => $suspiciousLogin->risk_score,
                'detected_at' => $suspiciousLogin->created_at,
            ],
            'requires_user_confirmation' => in_array($responseAction, ['lock_account', 'logout_all_sessions']),
            'expires_at' => now()->addDay(),
        ]);

        // Execute the response action
        $this->executeResponseAction($autoResponse, $user, $suspiciousLogin, $config);

        return $autoResponse;
    }

    /**
     * Determine response action based on severity
     */
    private function determineResponseAction($severity, $config)
    {
        return match ($severity) {
            'low' => 'send_alert',
            'medium' => $config->require_device_verification ? 'request_confirmation' : 'send_alert',
            'high' => 'request_confirmation',
            'critical' => $config->auto_lockout_critical ? 'lock_account' : 'request_confirmation',
            default => 'send_alert',
        };
    }

    /**
     * Get action description
     */
    private function getActionDescription($action)
    {
        return match ($action) {
            'send_alert' => 'Gửi cảnh báo đến người dùng qua email',
            'request_confirmation' => 'Yêu cầu người dùng xác nhận đăng nhập',
            'lock_account' => 'Khóa tài khoản để bảo vệ',
            'block_ip' => 'Chặn địa chỉ IP này',
            'logout_all_sessions' => 'Đăng xuất tất cả các phiên khác',
            'force_2fa_reauth' => 'Buộc xác thực lại 3FA',
            'temporary_lockout' => 'Khóa tạm thời tài khoản',
            default => 'Thực thi phản ứng tự động',
        };
    }

    /**
     * Execute the response action
     */
    private function executeResponseAction($autoResponse, $user, $suspiciousLogin, $config)
    {
        try {
            $action = $autoResponse->response_action;

            match ($action) {
                'send_alert' => $this->executeSendAlert($autoResponse, $user, $suspiciousLogin),
                'request_confirmation' => $this->executeRequestConfirmation($autoResponse, $user, $suspiciousLogin),
                'lock_account' => $this->executeLockAccount($autoResponse, $user, $suspiciousLogin),
                'block_ip' => $this->executeBlockIp($autoResponse, $user, $suspiciousLogin),
                'logout_all_sessions' => $this->executeLogoutAllSessions($autoResponse, $user),
                'force_2fa_reauth' => $this->executeForceTwoFaReauth($autoResponse, $user),
                'temporary_lockout' => $this->executeTemporaryLockout($autoResponse, $user, $suspiciousLogin),
                default => null,
            };

            $autoResponse->markExecuted('Action executed successfully');
        } catch (\Exception $e) {
            $autoResponse->markFailed($e->getMessage());
        }
    }

    /**
     * Send security alert to user
     */
    private function executeSendAlert($autoResponse, $user, $suspiciousLogin)
    {
        // Create security alert first
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'suspicious_login',
            'message' => "Phát hiện đăng nhập bất thường từ {$suspiciousLogin->location}",
            'severity' => $autoResponse->severity,
            'data' => [
                'ip_address' => $suspiciousLogin->ip_address,
                'location' => $suspiciousLogin->location,
                'device' => $suspiciousLogin->device_type,
                'time' => $suspiciousLogin->created_at,
                'anomaly_type' => $suspiciousLogin->anomaly_type,
            ],
        ]);

        // Send email alert with correct parameters
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Request user confirmation
     */
    private function executeRequestConfirmation($autoResponse, $user, $suspiciousLogin)
    {
        // Create confirmation alert
        SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'suspicious_login',
            'description' => "Yêu cầu xác nhận: Phát hiện đăng nhập bất thường từ {$suspiciousLogin->location}. Vui lòng xác nhận trong 10 phút.",
            'ip_address' => $suspiciousLogin->ip_address,
            'severity' => $autoResponse->severity,
            'is_confirmed' => false,
        ]);

        // Send confirmation email
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'confirm_suspicious_login',
            'title' => 'Xác nhận đăng nhập bất thường',
            'message' => "Xác nhận đăng nhập từ {$suspiciousLogin->location}",
            'severity' => 'high',
            'data' => [
                'ip_address' => $suspiciousLogin->ip_address,
                'location' => $suspiciousLogin->location,
                'confirmation_link' => route('profile.confirm-login', ['token' => $autoResponse->id]),
            ],
        ]);
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Lock user account
     */
    private function executeLockAccount($autoResponse, $user, $suspiciousLogin)
    {
        $lockoutMinutes = 30;

        // Lock account
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'is_blocked' => true,
                'updated_at' => now(),
            ]);

        // Store lockout info
        $autoResponse->lockout_until = now()->addMinutes($lockoutMinutes);
        $autoResponse->lockout_reason = "Phát hiện bất thường: {$suspiciousLogin->anomaly_type}";
        $autoResponse->lockout_auto_unlock = true;

        // Notify user
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'account_locked',
            'title' => 'Tài khoản bị khóa',
            'message' => $autoResponse->lockout_reason,
            'severity' => 'critical',
            'data' => [
                'reason' => $autoResponse->lockout_reason,
                'unlock_time' => $lockoutMinutes,
                'contact_support' => route('support.contact'),
            ],
        ]);
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Block the IP address
     */
    private function executeBlockIp($autoResponse, $user, $suspiciousLogin)
    {
        $ipAddress = $suspiciousLogin->ip_address;

        // Auto-block the suspicious IP
        $this->ipBlockingService->autoBlockIpOnAttack(
            $ipAddress,
            $suspiciousLogin->anomaly_type,
            null // No admin ID for auto-response
        );

        $autoResponse->blocked_ip_address = $ipAddress;
        $autoResponse->is_permanent_block = false;

        // Notify user
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'ip_blocked',
            'title' => 'IP bị chặn',
            'message' => "IP {$ipAddress} từ {$suspiciousLogin->location} đã bị chặn tự động",
            'severity' => 'critical',
        ]);
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Logout all user sessions
     */
    private function executeLogoutAllSessions($autoResponse, $user)
    {
        // Invalidate all sessions for this user
        \App\Models\UserSession::where('user_id', $user->id)
            ->update([
                'status' => 'terminated',
                'logged_out_at' => now(),
                'logout_reason' => 'Terminated by auto-response system',
            ]);

        // Clear session data
        DB::table('sessions')
            ->where('user_id', $user->id)
            ->delete();

        // Notify user
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'sessions_terminated',
            'title' => 'Đã kết thúc tất cả phiên',
            'message' => 'Tất cả các phiên khác đã bị đăng xuất để bảo vệ tài khoản',
            'severity' => 'medium',
        ]);
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Force 2FA reauthentication
     */
    private function executeForceTwoFaReauth($autoResponse, $user)
    {
        // Update user to require 2FA reauth
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'require_2fa_reauth' => true,
                'updated_at' => now(),
            ]);

        // Notify user
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'force_2fa_reauth',
            'title' => 'Yêu cầu xác thực 3FA lại',
            'message' => 'Vì mục đích bảo mật, bạn cần xác thực lại 3FA',
            'severity' => 'high',
        ]);
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Temporary lockout
     */
    private function executeTemporaryLockout($autoResponse, $user, $suspiciousLogin)
    {
        $lockoutMinutes = 15;

        // Create temporary lockout
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'is_blocked' => true,
                'updated_at' => now(),
            ]);

        $autoResponse->lockout_until = now()->addMinutes($lockoutMinutes);
        $autoResponse->lockout_reason = "Khóa tạm thời: {$suspiciousLogin->anomaly_type}";
        $autoResponse->lockout_auto_unlock = true;

        // Notify user
        $alert = SecurityAlert::create([
            'user_id' => $user->id,
            'type' => 'temporary_lockout',
            'title' => 'Tài khoản bị khóa tạm thời',
            'message' => "Phát hiện bất thường: {$suspiciousLogin->anomaly_type}",
            'severity' => 'high',
        ]);
        Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));

        $autoResponse->notifyUser('email');
    }

    /**
     * Test response action (for admin configuration)
     */
    public function testResponseAction($action, $userId)
    {
        $user = User::find($userId);
        if (! $user) {
            return false;
        }

        try {
            match ($action) {
                'send_alert' => (function () use ($user) {
                    $alert = SecurityAlert::create([
                        'user_id' => $user->id,
                        'type' => 'test_alert',
                        'title' => 'Cảnh báo kiểm tra',
                        'message' => 'Đây là thư kiểm tra cảnh báo bảo mật',
                        'severity' => 'info',
                    ]);
                    Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));
                })(),
                'request_confirmation' => (function () use ($user) {
                    $alert = SecurityAlert::create([
                        'user_id' => $user->id,
                        'type' => 'test_confirmation',
                        'title' => 'Xác nhận kiểm tra',
                        'message' => 'Đây là thư kiểm tra xác nhận',
                        'severity' => 'info',
                    ]);
                    Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));
                })(),
                'lock_account' => (function () use ($user) {
                    $alert = SecurityAlert::create([
                        'user_id' => $user->id,
                        'type' => 'test_lockout',
                        'title' => 'Khóa tài khoản kiểm tra',
                        'message' => 'Đây là thư kiểm tra khóa tài khoản',
                        'severity' => 'critical',
                    ]);
                    Mail::to($user->email)->send(new SecurityAlertMail($alert, $user));
                })(),
                default => false,
            };

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get response statistics
     */
    public function getResponseStatistics()
    {
        return [
            'total_responses' => AutoResponse::count(),
            'pending' => AutoResponse::pending()->count(),
            'executed' => AutoResponse::executed()->count(),
            'failed' => AutoResponse::failed()->count(),
            'by_action' => AutoResponse::groupBy('response_action')
                ->selectRaw('response_action, count(*) as count')
                ->pluck('count', 'response_action'),
            'by_severity' => AutoResponse::groupBy('severity')
                ->selectRaw('severity, count(*) as count')
                ->pluck('count', 'severity'),
            'by_trigger_type' => AutoResponse::groupBy('trigger_type')
                ->selectRaw('trigger_type, count(*) as count')
                ->pluck('count', 'trigger_type'),
            'success_rate' => $this->calculateSuccessRate(),
        ];
    }

    /**
     * Calculate response success rate
     */
    private function calculateSuccessRate()
    {
        $total = AutoResponse::count();
        if ($total === 0) {
            return 0;
        }

        $executed = AutoResponse::where('status', 'executed')->count();

        return round(($executed / $total) * 100, 2);
    }

    /**
     * Get recent responses
     */
    public function getRecentResponses($limit = 20)
    {
        return AutoResponse::with(['user', 'suspiciousLogin'])
            ->latest('triggered_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get responses for user
     */
    public function getUserResponses($userId, $limit = 50)
    {
        return AutoResponse::where('user_id', $userId)
            ->with('suspiciousLogin')
            ->latest('triggered_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Cancel pending response
     */
    public function cancelResponse($responseId, $reason = null)
    {
        $response = AutoResponse::find($responseId);
        if (! $response || $response->status !== 'pending') {
            return false;
        }

        $response->markCancelled($reason);

        return true;
    }

    /**
     * Auto-unlock account (run via scheduler)
     */
    public function autoUnlockAccounts()
    {
        $responses = AutoResponse::where('lockout_until', '<=', now())
            ->where('lockout_auto_unlock', true)
            ->where('status', 'executed')
            ->get();

        foreach ($responses as $response) {
            // Unlock account
            DB::table('users')
                ->where('id', $response->user_id)
                ->update(['is_blocked' => false]);

            // Update response status safely
            if (method_exists($response, 'addToHistory')) {
                $response->addToHistory('auto_unlocked', ['unlocked_at' => now()]);
            }
            if (method_exists($response, 'save')) {
                $response->save();
            } else {
                // Fallback to direct update
                AutoResponse::where('id', $response->id)->update(['status' => 'auto_unlocked']);
            }
        }

        return count($responses);
    }

    /**
     * Cleanup expired responses
     */
    public function cleanupExpiredResponses()
    {
        $expired = AutoResponse::where('expires_at', '<=', now())
            ->where('status', 'pending')
            ->update(['status' => 'expired']);

        return $expired;
    }
}

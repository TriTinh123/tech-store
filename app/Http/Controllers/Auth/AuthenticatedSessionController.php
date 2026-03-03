<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AnomalyDetectionService;
use App\Services\IpBlockingService;
use App\Services\NotificationService;
use App\Services\SessionMonitoringService;
use App\Services\SessionService;
use App\Services\ThreeFactorAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    protected $tfaService;

    protected $anomalyService;

    protected $sessionService;

    protected $ipBlockingService;

    protected $sessionMonitoringService;

    protected $notificationService;

    public function __construct(
        ThreeFactorAuthService $tfaService,
        AnomalyDetectionService $anomalyService,
        SessionService $sessionService,
        IpBlockingService $ipBlockingService,
        SessionMonitoringService $sessionMonitoringService,
        NotificationService $notificationService
    ) {
        $this->tfaService = $tfaService;
        $this->anomalyService = $anomalyService;
        $this->sessionService = $sessionService;
        $this->ipBlockingService = $ipBlockingService;
        $this->sessionMonitoringService = $sessionMonitoringService;
        $this->notificationService = $notificationService;
    }

    /**
     * Show the login form
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Check if IP is blocked before processing login
        $clientIp = $request->ip();
        if ($this->ipBlockingService->isIpBlocked($clientIp)) {
            throw ValidationException::withMessages([
                'email' => 'Địa chỉ IP của bạn đã bị chặn do lý do bảo mật. Vui lòng liên hệ với quản trị viên.',
            ]);
        }

        // Check if user recently completed 3FA verification
        $verified_3fa_user_id = $request->session()->get('verified_3fa_user_id');
        $verified_3fa_user_email = $request->session()->get('verified_3fa_user_email');
        
        if ($verified_3fa_user_id && $verified_3fa_user_email === $credentials['email']) {
            // User already verified via 3FA, complete login without password check
            $user = \App\Models\User::find($verified_3fa_user_id);
            
            if ($user) {
                // Clear 3FA verification session keys
                $request->session()->forget('verified_3fa_user_id');
                $request->session()->forget('verified_3fa_user_email');
                
                // Regenerate session
                $request->session()->regenerate();
                
                // Manually authenticate the user (already verified via 3FA)
                Auth::login($user, $request->filled('remember'));
                
                // Continue with normal post-login session tracking
                $ipAddress = $request->ip();
                $userAgent = $request->userAgent();
                
                // Create session tracking record
                $location = ['city' => 'Unknown', 'country' => 'Unknown'];
                $this->sessionService->createSession($user, $ipAddress, $userAgent, $location);
                
                // Create new session in SessionMonitoringService
                $sessionId = session()->getId();
                $this->sessionMonitoringService->createSession($user, $sessionId, $ipAddress);
                
                // Detect concurrent logins
                $this->sessionMonitoringService->detectConcurrentLogins($user, $sessionId, $ipAddress);
                
                // Record successful login attempt
                \App\Models\LoginAttempt::create([
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent,
                    'device_fingerprint' => $this->anomalyService->generateDeviceFingerprint($userAgent, $ipAddress),
                    'success' => true,
                    'reason' => null,
                    'attempted_at' => now(),
                ]);
                
                // Redirect to admin dashboard (middleware will handle role check)
                // Admin: Shows dashboard
                // User: Middleware intercepts and shows 403 page
                return redirect()->route('admin.dashboard');
            }
        }

        if (! Auth::attempt($credentials, $request->filled('remember'))) {
            // Record failed login attempt
            $user = \App\Models\User::where('email', $credentials['email'])->first();
            if ($user) {
                \App\Models\LoginAttempt::create([
                    'user_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'device_fingerprint' => $this->anomalyService->generateDeviceFingerprint(
                        $request->userAgent(),
                        $request->ip()
                    ),
                    'success' => false,
                    'reason' => 'invalid_credentials',
                    'attempted_at' => now(),
                ]);

                // Check IP blocking threshold and auto-block if needed
                try {
                    $this->ipBlockingService->autoBlockIpAfterFailedAttempts($clientIp, $user->id);
                } catch (\Exception $e) {
                    // Log but don't fail the login response
                    \Log::warning('IP auto-blocking failed: '.$e->getMessage());
                }

                // Check if account should be locked
                if ($this->anomalyService->isAccountLocked($user)) {
                    throw ValidationException::withMessages([
                        'email' => 'Tài khoản đã bị khóa do quá nhiều lần đăng nhập thất bại. Vui lòng thử lại sau 15 phút.',
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không chính xác.',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        // Get client information
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();

        // Check for anomalies
        $anomalyResult = $this->anomalyService->checkLoginAnomaly($user, $ipAddress, $userAgent);

        // Record login attempt
        $loginAttempt = \App\Models\LoginAttempt::create([
            'user_id' => $user->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_fingerprint' => $anomalyResult['device_fingerprint'],
            'success' => true,
            'reason' => null,
            'attempted_at' => now(),
        ]);

        // If anomaly detected, record it
        if ($anomalyResult['is_anomaly']) {
            $this->anomalyService->recordSuspiciousLogin(
                $user,
                $ipAddress,
                $userAgent,
                implode(',', $anomalyResult['anomalies']),
                $anomalyResult['risk_level']
            );

            // For high risk, might want to require additional verification
            // For now, still proceed but log it
        }

        // Create session tracking record
        $location = [
            'city' => 'Unknown',
            'country' => 'Unknown',
        ];
        $this->sessionService->createSession($user, $ipAddress, $userAgent, $location);

        // Create new session in SessionMonitoringService
        $sessionId = session()->getId();
        $this->sessionMonitoringService->createSession($user, $sessionId, $ipAddress);

        // Detect concurrent logins
        $this->sessionMonitoringService->detectConcurrentLogins($user, $sessionId, $ipAddress);

        // Check for concurrent logins and send notification if detected
        $concurrentLogins = \App\Models\ConcurrentLogin::where('user_id', $user->id)
            ->where('status', 'detected')
            ->where('created_at', '>=', now()->subMinutes(5))
            ->get();

        if ($concurrentLogins->count() > 0) {
            foreach ($concurrentLogins as $login) {
                $this->notificationService->sendConcurrentLoginAlert($user, [
                    $login->first_session_location ?? 'Unknown Location',
                    $login->second_session_location ?? 'Unknown Location',
                ]);
            }
        }

        // Generate and send OTP for 3FA
        $otpResult = $this->tfaService->generateAndSendOTP($user);

        if (! is_array($otpResult) || ! $otpResult['success']) {
            Auth::logout();
            $request->session()->invalidate();
            throw ValidationException::withMessages([
                'email' => 'Lỗi gửi OTP. Vui lòng thử lại.',
            ]);
        }

        // Set pending user ID in session for 3FA verification
        $request->session()->put('pending_3fa_user_id', $user->id);

        // Redirect to 3FA verification
        return redirect()->route('3fa.verify-otp')->with('success', 'OTP đã được gửi đến email của bạn. Vui lòng xác minh để tiếp tục.');
    }

    /**
     * Handle logout request
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $sessionId = session()->getId();

            // Record logout in session table
            $this->sessionService->logoutSession($user, 'manual');

            // End session in SessionMonitoringService
            $userSession = \App\Models\UserSession::where('session_id', $sessionId)->first();
            if ($userSession) {
                $this->sessionMonitoringService->terminateSession($userSession->id);
            }

            // Record logout in login attempts
            \App\Models\LoginAttempt::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_fingerprint' => $this->anomalyService->generateDeviceFingerprint(
                    $request->userAgent(),
                    $request->ip()
                ),
                'success' => true,
                'reason' => 'logout',
                'attempted_at' => now(),
            ]);

            // Cancel any pending 3FA and invalidate session on logout
            $this->tfaService->cancelThreeFactorAuth($user);
            $this->tfaService->invalidateSession($user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\AiRiskService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function __construct(
        private OtpService    $otpService,
        private AiRiskService $aiRisk,
    ) {}

    /** GET /auth/otp — show OTP entry form */
    public function show()
    {
        if (! session('auth.pending_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify');
    }

    /** POST /auth/otp — verify OTP, run AI risk, decide next step */
    public function verify(Request $request)
    {
        $userId = session('auth.pending_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($userId);
        // ── Verify OTP ─────────────────────────────────────────────────────
        if (! $this->otpService->verify($user, $request->otp_code)) {
            return back()->withErrors([
                'otp_code' => 'Invalid or expired OTP code.',
            ]);
        }
        // ── AI Risk Assessment ─────────────────────────────────────────────
        $riskData = $this->aiRisk->assess($request, $user);
        $geoData  = $this->aiRisk->geoIp($request->ip());
        // Persist login attempt record
        LoginAttempt::create([
            'user_id'             => $user->id,
            'email'               => $user->email,
            'ip_address'          => $request->ip(),
            'user_agent'          => $request->userAgent(),
            'password_ok'         => true,
            'otp_ok'              => true,
            'risk_level'          => $riskData['risk_level'],
            'risk_numeric'        => $riskData['risk_numeric'],
            'risk_score'          => $riskData['risk_score'],
            'is_anomaly'          => $riskData['is_anomaly'],
            'explanation'         => $riskData['explanation'],
            'required_3fa'        => $riskData['requires_3fa'],
            'geo_country'         => $geoData['country'] ?? null,
            'geo_country_code'    => $geoData['country_code'] ?? null,
            'geo_city'            => $geoData['city'] ?? null,
            'geo_is_vn'           => $geoData['is_vn'] ?? null,
            'geo_is_foreign_risk' => $geoData['is_known_vpn_country'] ?? null,
        ]);

        // Store risk result in pending session
        session(['auth.risk_data' => $riskData]);

        // ── Decision ──────────────────────────────────────────────────────
        if (($riskData['action'] ?? null) === 'deny') {
            session()->forget([
                'auth.pending_user_id', 'auth.remember', 'auth.risk_data',
                'auth.keystroke_speed_ms', 'auth.keystroke_irregularity',
                'auth.click_count_per_min', 'auth.mouse_move_count', 'auth.mouse_avg_speed',
                'auth.screen_w', 'auth.screen_h', 'auth.timezone',
            ]);

            return redirect()->route('login')->withErrors([
                'email' => $riskData['recommendation'] ?? 'Login blocked by security policy.',
            ]);
        }

        if ($riskData['requires_3fa']) {
            // High / Critical risk → Factor 3 required
            return redirect()->route('auth.3fa');
        }

        // Low / Medium risk → complete login now
        return $this->completeLogin($request, $user, false);
    }

    /** POST /auth/otp/resend — resend a fresh OTP */
    public function resend()
    {
        $userId = session('auth.pending_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);
        app(OtpService::class)->send($user);

        return back()->with('info', 'A new OTP code has been sent to ' . $user->email);
    }

    /** Finalise the login session (shared with 3FA controller) */
    public static function completeLogin(Request $request, User $user, bool $passed3fa): \Symfony\Component\HttpFoundation\Response
    {
        Auth::login($user, session('auth.remember', false));

        // Update user record
        $user->update(['last_login_at' => now(), 'failed_login_count' => 0]);

        // Mark the latest attempt as successful
        LoginAttempt::where('user_id', $user->id)
            ->latest()
            ->limit(1)
            ->update(['success' => true, 'passed_3fa' => $passed3fa]);

        // Remember this device & IP for future logins
        app(AiRiskService::class)->rememberDevice($request, $user);

        // Capture risk data before clearing session
        $riskData  = session('auth.risk_data', []);
        $riskLevel = $riskData['risk_level'] ?? 'low';

        // Clear pending session keys
        session()->forget([
            'auth.pending_user_id', 'auth.remember', 'auth.risk_data',
            'auth.keystroke_speed_ms', 'auth.keystroke_irregularity',
            'auth.click_count_per_min', 'auth.mouse_move_count', 'auth.mouse_avg_speed',
            'auth.screen_w', 'auth.screen_h', 'auth.timezone',
        ]);

        if ($user->isAdmin()) {
            session()->forget('auth.tried_admin');
            return redirect()->route('admin');
        }

        // Flash security warning for elevated risk logins
        if (in_array($riskLevel, ['medium', 'high', 'critical'])) {
            session()->flash('security_warning', ['level' => $riskLevel]);
        }

        return redirect()->intended(route('home'));
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Adaptive Authentication (similar to Google/GitHub):
     *   - Wrong password     → write audit log → AI brute-force check
     *   - Correct + normal   → direct login (no OTP)
     *   - Correct + suspicious → OTP (F2) → AI → Factor 3 if HIGH
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // ── Wrong password ───────────────────────────────────────────────────
        if (! $user || ! Auth::validate($credentials)) {
            LoginAttempt::create([
                'user_id'    => $user?->id,
                'email'      => $credentials['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'password_ok'=> false,
            ]);

            app(AuditLogService::class)->record($request, $user, false);

            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        if ($user->is_blocked) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been locked. Please contact the administrator.',
            ]);
        }

        // ── Correct password: AI behaviour check ─────────────────────────────

        // Demo attack: ≥10 fails → lock account immediately (real effect)
        if ($request->input('demo_mode') === '1' && (int) $request->input('demo_failed_attempts', 0) >= 10) {
            $user->update(['is_blocked' => true]);
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    "[🚨 ATTACK DETECTED]\n\nAI detected a brute-force attack (demo). Account has been locked.\nRisk score: 100",
                    fn($m) => $m->to($user->email)->subject('🚨 Account Locked — AI Detected Attack')
                );
            } catch (\Throwable) {}
            throw ValidationException::withMessages([
                'email' => '🚨 [DEMO] AI detected brute-force attack — account has been locked.',
            ]);
        }

        $suspicious = app(AuditLogService::class)->isSuspicious($request, $user);

        if (! $suspicious) {
            // ✅ Normal → direct login (trusted device, like Google)
            LoginAttempt::create([
                'user_id'     => $user->id,
                'email'       => $user->email,
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
                'password_ok' => true,
                'otp_ok'      => true,
                'success'     => true,
                'risk_level'  => 'low',
                'risk_numeric'=> 0,
            ]);

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(
                $user->role === 'admin' ? route('admin') : route('home')
            );
        }

        // ⚠️ Suspicious → send OTP (F2), then AI decides if F3 is needed
        $triedAdmin = session('auth.tried_admin', false);
        session([
            'auth.pending_user_id'        => $user->id,
            'auth.remember'               => $request->filled('remember'),
            'auth.keystroke_speed_ms'     => (float) $request->input('keystroke_speed_ms', 150),
            'auth.keystroke_irregularity' => (float) $request->input('keystroke_irregularity', 30),
            'auth.click_count_per_min'    => (int)   $request->input('click_count_per_min', 30),
            'auth.mouse_move_count'       => (int)   $request->input('mouse_move_count', 0),
            'auth.mouse_avg_speed'        => (float) $request->input('mouse_avg_speed', 0),
            'auth.screen_w'               => $request->input('screen_w', 1920),
            'auth.screen_h'               => $request->input('screen_h', 1080),
            'auth.timezone'               => $request->input('timezone', 'Asia/Ho_Chi_Minh'),
            'auth.tried_admin'            => $triedAdmin,
            // Demo mode — save to session so OtpController can read back
            'auth.demo_mode'              => $request->input('demo_mode', '0'),
            'auth.demo_failed_attempts'   => (int) $request->input('demo_failed_attempts', 0),
            'auth.demo_new_ip'            => $request->input('demo_new_ip', '0'),
            'auth.demo_new_device'        => $request->input('demo_new_device', '0'),
            'auth.demo_geo_changed'       => $request->input('demo_geo_changed', '0'),
            'auth.demo_ip_count'          => (int) $request->input('demo_ip_count', 0),
            'auth.demo_fake_ip'           => $request->input('demo_fake_ip', ''),
        ]);

        app(OtpService::class)->send($user);

        return redirect()->route('auth.otp')->with(
            'warning', "⚠️ Suspicious activity detected. Please verify with OTP sent to {$user->email}"
        );
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}


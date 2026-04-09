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
     * Adaptive Authentication (như Google/GitHub):
     *   - Sai mật khẩu       → ghi audit log → AI check brute-force
     *   - Đúng + bình thường → login thẳng (không OTP)
     *   - Đúng + nghi ngờ    → OTP (F2) → AI → Factor 3 nếu HIGH
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // ── Sai mật khẩu ──────────────────────────────────────────────────
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

        // ── Đúng mật khẩu: AI kiểm tra hành vi ───────────────────────────

        // Demo attack: ≥10 fail → khóa tài khoản ngay lập tức (tác động thật)
        if ($request->input('demo_mode') === '1' && (int) $request->input('demo_failed_attempts', 0) >= 10) {
            $user->update(['is_blocked' => true]);
            try {
                \Illuminate\Support\Facades\Mail::raw(
                    "[🚨 TẤN CÔNG PHÁT HIỆN]\n\nAI phát hiện tấn công brute-force (demo). Tài khoản đã bị khóa.\nRisk score: 100",
                    fn($m) => $m->to($user->email)->subject('🚨 Tài khoản bị khoá — AI phát hiện tấn công')
                );
            } catch (\Throwable) {}
            throw ValidationException::withMessages([
                'email' => '🚨 [DEMO] AI phát hiện tấn công brute-force — tài khoản đã bị khóa.',
            ]);
        }

        $suspicious = app(AuditLogService::class)->isSuspicious($request, $user);

        if (! $suspicious) {
            // ✅ Bình thường → login thẳng (như Google trên thiết bị quen)
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

        // ⚠️ Nghi ngờ → gửi OTP (F2), sau đó AI quyết định có cần F3 không
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
            // Demo mode — lưu để OtpController đọc lại
            'auth.demo_mode'              => $request->input('demo_mode', '0'),
            'auth.demo_failed_attempts'   => (int) $request->input('demo_failed_attempts', 0),
            'auth.demo_new_ip'            => $request->input('demo_new_ip', '0'),
            'auth.demo_new_device'        => $request->input('demo_new_device', '0'),
            'auth.demo_geo_changed'       => $request->input('demo_geo_changed', '0'),
            'auth.demo_ip_count'          => (int) $request->input('demo_ip_count', 0),
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


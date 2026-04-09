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
     * Login flow (Adaptive):
     *   - Sai mật khẩu → ghi audit log → AI check nếu cần
     *   - Đúng mật khẩu + KHÔNG nghi ngờ → đăng nhập thẳng
     *   - Đúng mật khẩu + CÓ nghi ngờ   → gửi OTP (Factor 2)
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

            // Ghi audit log + AI check brute-force
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

        // ── Đúng mật khẩu: kiểm tra có nghi ngờ không ────────────────────
        $auditService = app(AuditLogService::class);
        $suspicious   = $auditService->isSuspicious($request, $user);

        if (! $suspicious) {
            // ✅ Bình thường → đăng nhập thẳng, không cần OTP
            LoginAttempt::create([
                'user_id'    => $user->id,
                'email'      => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'password_ok'=> true,
                'otp_ok'     => true,
                'success'    => true,
                'risk_level' => 'low',
                'risk_numeric' => 0,
            ]);

            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->intended(
                $user->is_admin ? route('admin.dashboard') : route('home')
            );
        }

        // ⚠️ Có dấu hiệu nghi ngờ → gửi OTP (Factor 2)
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
        ]);

        app(OtpService::class)->send($user);

        return redirect()->route('auth.otp')->with(
            'warning', "⚠️ Suspicious activity detected. An OTP code has been sent to {$user->email}"
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


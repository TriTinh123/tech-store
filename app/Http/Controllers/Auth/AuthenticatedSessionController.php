<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use App\Models\User;
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
     * Step 1 of 3FA: Verify password, then send OTP (Factor 2).
     */
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user first so we can record the attempt
        $user = User::where('email', $credentials['email'])->first();

        // ── Sanity checks ──────────────────────────────────────────────────
        if (! $user || ! Auth::validate($credentials)) {
            // Record failed password attempt
            LoginAttempt::create([
                'user_id'    => $user?->id,
                'email'      => $credentials['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'password_ok'=> false,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Invalid email or password.',
            ]);
        }

        if ($user->is_blocked) {
            throw ValidationException::withMessages([
                'email' => 'Your account has been locked. Please contact the administrator.',
            ]);
        }

        // ── Store pending session data for OTP + AI stages ─────────────────
        // Preserve auth.tried_admin flag set by AdminMiddleware (if any)
        $triedAdmin = session('auth.tried_admin', false);
        session([
            'auth.pending_user_id'         => $user->id,
            'auth.remember'                => $request->filled('remember'),
            // Keystroke fingerprint collected by JS in the login form
            'auth.keystroke_speed_ms'      => (float) $request->input('keystroke_speed_ms', 150),
            'auth.keystroke_irregularity'  => (float) $request->input('keystroke_irregularity', 30),
            'auth.click_count_per_min'     => (int)   $request->input('click_count_per_min', 30),
            'auth.mouse_move_count'        => (int)   $request->input('mouse_move_count', 0),
            'auth.mouse_avg_speed'         => (float) $request->input('mouse_avg_speed', 0),
            'auth.screen_w'                => $request->input('screen_w', 1920),
            'auth.screen_h'                => $request->input('screen_h', 1080),
            'auth.timezone'                => $request->input('timezone', 'Asia/Ho_Chi_Minh'),
            'auth.tried_admin'             => $triedAdmin,
        ]);

        // ── Send OTP email (Factor 2) ─────────────────────────────────────
        app(OtpService::class)->send($user);

        return redirect()->route('auth.otp')->with(
            'info', "An OTP code has been sent to {$user->email}"
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


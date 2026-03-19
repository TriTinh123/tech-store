<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Forgot password page
    public function forgot()
    {
        return view('auth.forgot-password');
    }

    // Send reset email
    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users']);

        $user = User::where('email', $request->email)->first();
        $token = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $user->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        // Send reset email
        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
        Mail::send(new PasswordResetMail($user, $resetLink));

        return redirect()->back()->with('success', 'Please check your email to reset your password!');
    }

    // Reset password page
    public function reset($token, $email)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.forgot')->with('error', 'Email not found');
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $resetRecord || ! Hash::check($token, $resetRecord->token)) {
            return redirect()->route('password.forgot')->with('error', 'Invalid token');
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return redirect()->route('password.forgot')->with('error', 'Password reset link has expired (60 minutes). Please request a new one.');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    // Confirm password reset
    public function updateReset(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email|exists:users',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $validated['email'])
            ->first();

        if (! $resetRecord || ! Hash::check($validated['token'], $resetRecord->token)) {
            return redirect()->route('password.forgot')->with('error', 'Invalid or expired token');
        }

        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();
            return redirect()->route('password.forgot')->with('error', 'Password reset link has expired (60 minutes). Please request a new one.');
        }

        User::where('email', $validated['email'])->update([
            'password' => Hash::make($validated['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('success', 'Password has been reset. Please log in!');
    }
}

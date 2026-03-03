<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    // Trang quên mật khẩu
    public function forgot()
    {
        return view('auth.forgot-password');
    }

    // Gửi email reset
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

        // Gửi email reset
        $resetLink = route('password.reset', ['token' => $token, 'email' => $user->email]);
        Mail::send(new PasswordResetMail($user, $resetLink));

        return redirect()->back()->with('success', 'Hãy kiểm tra email của bạn để đặt lại mật khẩu!');
    }

    // Trang reset mật khẩu
    public function reset($token, $email)
    {
        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.forgot')->with('error', 'Email không tồn tại');
        }

        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (! $resetRecord || ! Hash::check($token, $resetRecord->token)) {
            return redirect()->route('password.forgot')->with('error', 'Token không hợp lệ');
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    // Xác nhận reset mật khẩu
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
            return redirect()->route('password.forgot')->with('error', 'Token không hợp lệ hoặc đã hết hạn');
        }

        User::where('email', $validated['email'])->update([
            'password' => Hash::make($validated['password']),
        ]);

        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được reset. Hãy đăng nhập!');
    }
}

<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Log;

class OtpService
{
    const EXPIRES_MINUTES = 10;
    const MAX_ATTEMPTS    = 5;

    /**
     * Generate a new OTP, persist it, and email it to the user.
     */
    public function send(User $user): void
    {
        // Invalidate any previous unused OTPs for this user
        OtpCode::where('user_id', $user->id)->delete();

        $plain = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
Log::info("Generated OTP for user {$user->email}: {$plain} (hashed in DB)");
        OtpCode::create([
            'user_id'    => $user->id,
            'code'       => Hash::make($plain),
            'expires_at' => now()->addMinutes(self::EXPIRES_MINUTES),
        ]);

        Mail::to($user->email)->send(new OtpMail($plain, $user->name));
    }

    /**
     * Verify the plain-text OTP submitted by the user.
     * Returns true and marks the code as used on success.
     */
    public function verify(User $user, string $plain): bool
    {
        $otp = OtpCode::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();

        if (! $otp || ! Hash::check($plain, $otp->code)) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }
}

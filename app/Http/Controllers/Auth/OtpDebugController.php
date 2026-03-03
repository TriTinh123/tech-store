<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;

class OtpDebugController
{
    /**
     * Show OTP code from cache - with user ID parameter
     */
    public function showOtp($userId = null)
    {
        if (! app()->isLocal()) {
            abort(403, 'This endpoint is only available in local development');
        }

        // If no user ID provided, try to get from auth
        if (! $userId) {
            $userId = auth()->id();
        }
        
        if (! $userId) {
            return $this->showDebugForm();
        }

        $otp = \Cache::get("otp_debug_{$userId}");
        
        if (! $otp) {
            return response('
                <h1>OTP Not Found</h1>
                <p>No active OTP found for your account.</p>
                <p>Please request a new OTP first and come back here to see the code.</p>
                <p><a href="javascript:history.back()">← Go Back</a></p>
                <style>
                    body { font-family: Arial; margin: 3rem; line-height: 1.6; }
                    a { color: #0066cc; }
                </style>
            ', 404);
        }

        $otp = \Cache::get("otp_debug_{$userId}");
        
        if (! $otp) {
            return response("
                <h1>OTP Not Found</h1>
                <p>No active OTP found for user ID: <strong>{$userId}</strong></p>
                <p>Please request a new OTP first and come back here to see the code.</p>
                <p><a href='/debug/otp'>← Try Another User</a></p>
                <style>
                    body { font-family: Arial; margin: 3rem; line-height: 1.6; }
                    a { color: #0066cc; }
                </style>
            ", 404);
        }

        $user = \App\Models\User::find($userId);
        $userEmail = $user ? $user->email : "User {$userId}";

        return response("
            <h1 style='color: #28a745;'>✓ OTP Code (Development Only)</h1>
            <div style='background: #f0f0f0; padding: 2rem; border-radius: 8px; font-family: monospace; font-size: 2.5rem; letter-spacing: 3px; text-align: center; margin: 2rem 0;'>
                <span style='color: #d32f2f;'>{$otp}</span>
            </div>
            <p><strong>User ID:</strong> {$userId}</p>
            <p><strong>Email:</strong> {$userEmail}</p>
            <p style='color: #666; font-size: 0.9rem;'><em>Sao chép mã OTP này và nhập vào form đăng nhập. OTP sẽ hết hạn trong 10 phút.</em></p>
            <div style='margin-top: 2rem; display: flex; gap: 1rem;'>
                <a href='/3fa/verify-otp' style='flex: 1; padding: 0.8rem 1.5rem; background: #0066cc; color: white; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;'>← Quay lại nhập OTP</a>
                <a href='/debug/otp' style='flex: 1; padding: 0.8rem 1.5rem; background: #666; color: white; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;'>Xem User Khác</a>
            </div>
            <p style='text-align: center; color: #999; font-size: 0.85rem; margin-top: 1rem;'>This page is only visible in development mode.</p>
            <style>
                body { font-family: Arial, sans-serif; margin: 2rem; line-height: 1.6; max-width: 400px; margin-left: auto; margin-right: auto; }
                a { text-decoration: none; }
                a:hover { opacity: 0.9; }
            </style>
        ");
    }

    /**
     * Show debug form to input user ID
     */
    private function showDebugForm()
    {
        return response("
            <h1>🔐 OTP Debug Tool (Development Only)</h1>
            <p>Enter a user ID to retrieve their OTP code:</p>
            <form method='GET' style='margin: 2rem 0;'>
                <input type='number' name='userId' placeholder='Enter User ID' min='1' required style='padding: 0.5rem; font-size: 1rem; width: 200px;'>
                <button type='submit' style='padding: 0.5rem 1.5rem; font-size: 1rem; cursor: pointer; background: #0066cc; color: white; border: none; border-radius: 4px;'>Get OTP</button>
            </form>
            <hr style='margin: 2rem 0;'>
            <h2>Quick Links:</h2>
            <ul>
                <li><a href='/debug/otp/1'>User ID 1</a></li>
                <li><a href='/debug/otp/2'>User ID 2</a></li>
                <li><a href='/debug/otp/3'>User ID 3</a></li>
            </ul>
            <hr style='margin: 2rem 0;'>
            <h2>Check OTP Settings:</h2>
            <form method='GET' action='/debug/otp-settings' style='margin: 1rem 0;'>
                <input type='number' name='userId' placeholder='Enter User ID' min='1' required style='padding: 0.5rem; font-size: 1rem; width: 200px;'>
                <button type='submit' style='padding: 0.5rem 1.5rem; font-size: 1rem; cursor: pointer; background: #28a745; color: white; border: none; border-radius: 4px;'>Check Settings</button>
            </form>
            <p style='color: #666; margin-top: 2rem; font-size: 0.9rem;'><em>This tool is only available in local development mode.</em></p>
            <style>
                body { font-family: Arial, sans-serif; margin: 2rem; line-height: 1.6; max-width: 600px; }
                a { color: #0066cc; text-decoration: none; }
                a:hover { text-decoration: underline; }
                input { border: 1px solid #ccc; border-radius: 4px; }
                button { color: white; border: none; border-radius: 4px; cursor: pointer; padding: 0.7rem 1.5rem; }
                button:hover { opacity: 0.9; }
                hr { border: none; border-top: 1px solid #ccc; }
            </style>
        ");
    }

    /**
     * Show OTP settings info
     */
    public function showOtpSettings($userId)
    {
        if (! app()->isLocal()) {
            abort(403, 'This endpoint is only available in local development');
        }

        $otpSetting = \App\Models\OtpSetting::where('user_id', $userId)->first();
        $user = \App\Models\User::find($userId);

        if (! $otpSetting) {
            return response("
                <h1>❌ No OTP Setting Found</h1>
                <p>User ID {$userId} has no OTP settings yet.</p>
                <p><a href='/debug/otp'>← Back to Debug Tool</a></p>
                <style>
                    body { font-family: Arial; margin: 2rem; }
                    a { color: #0066cc; text-decoration: none; }
                </style>
            ", 404);
        }

        $expired = $otpSetting->isOtpExpired();
        $expiryTime = $otpSetting->otp_expires_at ? \Carbon\Carbon::parse($otpSetting->otp_expires_at)->format('Y-m-d H:i:s') : 'N/A';
        $userEmail = $user ? $user->email : "User {$userId}";
        $otpCodeHash = $otpSetting->otp_code ? substr($otpSetting->otp_code, 0, 20) . '...' : 'No OTP';

        return response("
            <h1>🔍 OTP Settings for User {$userId}</h1>
            <div style='background: #f5f5f5; padding: 1.5rem; border-radius: 8px; font-family: monospace; margin: 1.5rem 0;'>
                <p><strong>User ID:</strong> {$userId}</p>
                <p><strong>Email:</strong> {$userEmail}</p>
                <p><strong>OTP Enabled:</strong> " . ($otpSetting->otp_enabled ? '✅ Yes' : '❌ No') . "</p>
                <p><strong>OTP Code (hash):</strong> {$otpCodeHash}</p>
                <p><strong>OTP Attempts:</strong> {$otpSetting->otp_attempts}</p>
                <p><strong>OTP Expires At:</strong> {$expiryTime}</p>
                <p><strong>Expired:</strong> " . ($expired ? '❌ Yes (expired)' : '✅ No (still valid)') . "</p>
                <p><strong>Last OTP Sent:</strong> " . ($otpSetting->last_otp_sent_at ? \Carbon\Carbon::parse($otpSetting->last_otp_sent_at)->format('Y-m-d H:i:s') : 'Never') . "</p>
                <p><strong>OTP Delivery Method:</strong> {$otpSetting->otp_delivery_method}</p>
            </div>
            <div style='margin: 1.5rem 0;'>
                <p style='color: #0066cc; font-weight: bold;'><a href='/debug/otp/{$userId}'>👁️ View Plain OTP</a></p>
                <p style='color: #666; font-size: 0.9rem;'><em>The plain OTP is cached temporarily for development. Use this to test login.</em></p>
            </div>
            <div style='margin: 1.5rem 0;'>
                <p style='color: #d32f2f; font-weight: bold;'><a href='/debug/otp-reset/{$userId}' onclick=\"return confirm('Generate new OTP for user {$userId}?')\">🔄 Generate New OTP</a></p>
                <p style='color: #666; font-size: 0.9rem;'><em>This will discard the current OTP and generate a new one (bypasses rate limiting in dev mode).</em></p>
            </div>
            <p><a href='/debug/otp'>← Back to Debug Tool</a></p>
            <style>
                body { font-family: Arial, sans-serif; margin: 2rem; line-height: 1.6; max-width: 600px; }
                a { color: #0066cc; text-decoration: none; }
                a:hover { text-decoration: underline; }
            </style>
        ");
    }

    /**
     * Reset OTP - generate new one (dev mode only)
     */
    public function resetOtp($userId)
    {
        if (! app()->isLocal()) {
            abort(403, 'This endpoint is only available in local development');
        }

        $user = \App\Models\User::find($userId);

        if (! $user) {
            return response('<h1>User not found</h1><p><a href="/debug/otp">← Back</a></p>', 404);
        }

        try {
            // Get existing setting or create
            $otpSetting = \App\Models\OtpSetting::firstOrCreate(
                ['user_id' => $user->id],
                ['otp_delivery_method' => 'email', 'otp_enabled' => true]
            );

            // Generate new OTP
            $otp = str_pad(random_int(0, pow(10, 6) - 1), 6, '0', STR_PAD_LEFT);
            $expiresAt = now()->addSeconds(1800); // 30 minutes for easier testing

            // Update with new OTP
            $otpSetting->update([
                'otp_code' => hash('sha256', $otp),
                'otp_expires_at' => $expiresAt,
                'otp_attempts' => 0,
                'last_otp_sent_at' => now(),
            ]);

            // Cache plain OTP
            \Cache::put("otp_debug_{$user->id}", $otp, now()->addMinutes(30));
            \Log::info("OTP Reset for user {$user->id}: {$otp}");

            return response("
                <h1 style='color: #28a745;'>✅ New OTP Generated!</h1>
                <div style='background: #f0f0f0; padding: 2rem; border-radius: 8px; font-family: monospace; font-size: 2.5rem; letter-spacing: 3px; text-align: center; margin: 2rem 0;'>
                    <span style='color: #d32f2f;'>{$otp}</span>
                </div>
                <p><strong>User:</strong> {$user->email} (ID: {$userId})</p>
                <p style='color: #666; font-size: 0.9rem;'><em>A new OTP has been generated and will expire in 30 minutes.</em></p>
                <div style='margin-top: 2rem; display: flex; gap: 1rem;'>
                    <a href='/3fa/verify-otp' style='flex: 1; padding: 0.8rem 1.5rem; background: #0066cc; color: white; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;'>← Go to OTP Verification</a>
                    <a href='/debug/otp-settings/{$userId}' style='flex: 1; padding: 0.8rem 1.5rem; background: #666; color: white; text-decoration: none; border-radius: 4px; text-align: center; font-weight: bold;'>View Settings</a>
                </div>
                <style>
                    body { font-family: Arial, sans-serif; margin: 2rem; line-height: 1.6; max-width: 500px; margin-left: auto; margin-right: auto; }
                </style>
            ");
        } catch (\Exception $e) {
            \Log::error("Error resetting OTP for user {$userId}: " . $e->getMessage());

            return response(
                "<h1>❌ Error</h1><p>Failed to reset OTP: " . $e->getMessage() . "</p><p><a href='/debug/otp'>← Back</a></p>",
                500
            );
        }
    }
}

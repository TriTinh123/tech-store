<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $table = 'login_attempts';

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'success',
        'reason',
        'attempted_at',
    ];

    protected $casts = [
        'success' => 'boolean',
        'attempted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function recordFailedAttempt($userId, $ipAddress, $userAgent, $reason = null)
    {
        return self::create([
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_fingerprint' => self::generateFingerprint($userAgent, $ipAddress),
            'success' => false,
            'reason' => $reason,
            'attempted_at' => now(),
        ]);
    }

    public static function recordSuccessAttempt($userId, $ipAddress, $userAgent)
    {
        return self::create([
            'user_id' => $userId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'device_fingerprint' => self::generateFingerprint($userAgent, $ipAddress),
            'success' => true,
            'attempted_at' => now(),
        ]);
    }

    public static function generateFingerprint($userAgent, $ipAddress)
    {
        return hash('sha256', $userAgent.'|'.$ipAddress);
    }
}

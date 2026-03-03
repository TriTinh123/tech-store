<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ThreeFactorAuthentication extends Model
{
    protected $table = 'three_factor_authentications';

    protected $fillable = [
        'user_id',
        'otp_code',
        'otp_expires_at',
        'otp_attempts',
        'security_question_id',
        'is_verified',
        'verified_at',
        'verification_method',
    ];

    protected $casts = [
        'otp_expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function securityQuestion()
    {
        return $this->belongsTo(SecurityQuestion::class);
    }

    public function isOtpExpired()
    {
        return $this->otp_expires_at && now()->isAfter($this->otp_expires_at);
    }

    public function hasExceededOtpAttempts()
    {
        return $this->otp_attempts >= 5;
    }
}

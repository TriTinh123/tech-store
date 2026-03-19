<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'user_agent',
        'password_ok', 'otp_ok',
        'risk_level', 'risk_numeric', 'risk_score', 'is_anomaly', 'explanation',
        'required_3fa', 'passed_3fa', 'success',
        'geo_country', 'geo_country_code', 'geo_city', 'geo_is_vn', 'geo_is_foreign_risk',
    ];

    protected $casts = [
        'explanation'  => 'array',
        'password_ok'  => 'boolean',
        'otp_ok'       => 'boolean',
        'is_anomaly'   => 'boolean',
        'required_3fa'       => 'boolean',
        'passed_3fa'         => 'boolean',
        'success'            => 'boolean',
        'geo_is_vn'          => 'boolean',
        'geo_is_foreign_risk'=> 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Risk level badge colour for views */
    public function riskBadgeClass(): string
    {
        return match ($this->risk_level) {
            'critical' => 'danger',
            'high'     => 'warning',
            'medium'   => 'info',
            default    => 'success',
        };
    }
}


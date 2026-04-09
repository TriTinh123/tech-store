<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'email', 'ip_address', 'device_fingerprint',
        'event', 'password_ok', 'failed_attempts', 'ip_count', 'device_count',
        'geo_country', 'geo_changed', 'ai_result', 'ai_risk_score',
        'email_sent', 'account_locked', 'raw_features',
    ];

    protected $casts = [
        'password_ok'    => 'boolean',
        'geo_changed'    => 'boolean',
        'email_sent'     => 'boolean',
        'account_locked' => 'boolean',
        'raw_features'   => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resultBadgeClass(): string
    {
        return match ($this->ai_result) {
            'attack'     => 'danger',
            'suspicious' => 'warning',
            default      => 'success',
        };
    }
}

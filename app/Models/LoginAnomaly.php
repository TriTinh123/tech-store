<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAnomaly extends Model
{
    protected $table = 'login_anomalies';

    protected $fillable = [
        'user_id',
        'anomaly_type',
        'description',
        'ip_address',
        'device_fingerprint',
        'user_agent',
        'country',
        'city',
        'latitude',
        'longitude',
        'risk_level',
        'status',
        'is_whitelisted',
        'whitelisted_at',
        'whitelisted_by',
        'admin_notes',
        'handled_by',
        'handled_at',
    ];

    protected $casts = [
        'whitelisted_at' => 'datetime',
        'handled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with this anomaly
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who handled this anomaly
     */
    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Get the admin who whitelisted this anomaly
     */
    public function whitelistedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'whitelisted_by');
    }

    /**
     * Scope: unresolved anomalies
     */
    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['new', 'under_investigation']);
    }

    /**
     * Scope: critical anomalies
     */
    public function scopeCritical($query)
    {
        return $query->where('risk_level', 'critical');
    }

    /**
     * Scope: by risk level
     */
    public function scopeByRiskLevel($query, $level)
    {
        return $query->where('risk_level', $level);
    }

    /**
     * Scope: by anomaly type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('anomaly_type', $type);
    }

    /**
     * Scope: whitelisted
     */
    public function scopeWhitelisted($query)
    {
        return $query->where('is_whitelisted', true);
    }

    /**
     * Scope: not whitelisted
     */
    public function scopeNotWhitelisted($query)
    {
        return $query->where('is_whitelisted', false);
    }

    /**
     * Scope: recent
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

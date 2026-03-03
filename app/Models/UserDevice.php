<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    protected $table = 'user_devices';

    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'browser',
        'os',
        'ip_address',
        'user_agent',
        'device_fingerprint',
        'is_trusted',
        'last_used_at',
    ];

    protected $casts = [
        'is_trusted' => 'boolean',
        'last_used_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns this device
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for trusted devices
     */
    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }

    /**
     * Scope for untrusted devices
     */
    public function scopeUntrusted($query)
    {
        return $query->where('is_trusted', false);
    }

    /**
     * Scope by device type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('device_type', $type);
    }

    /**
     * Get recently used devices
     */
    public function scopeRecentlyUsed($query, $days = 30)
    {
        return $query->where('last_used_at', '>=', now()->subDays($days));
    }
}

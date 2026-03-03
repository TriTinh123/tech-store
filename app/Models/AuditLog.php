<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'entity_type',
        'entity_id',
        'changes',
        'ip_address',
        'user_agent',
        'description',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user associated with this audit log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get logs for a specific action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get logs from a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get logs from a specific IP
     */
    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope: Get recent logs
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days))
            ->orderBy('created_at', 'desc');
    }

    /**
     * Scope: Get failed login attempts
     */
    public function scopeFailedLogins($query)
    {
        return $query->where('action', 'login_failed');
    }

    /**
     * Scope: Get successful logins
     */
    public function scopeSuccessfulLogins($query)
    {
        return $query->where('action', 'login_success');
    }

    /**
     * Get the full user agent info
     */
    public function getUserAgentAttribute()
    {
        return $this->attributes['user_agent'] ?? 'Unknown';
    }

    /**
     * Check if this is a security-related action
     */
    public function isSecurityRelated(): bool
    {
        $securityActions = [
            'login_failed',
            'unauthorized_access',
            'failed_2fa',
            'password_changed',
            'permissions_changed',
        ];

        return in_array($this->action, $securityActions);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertResponse extends Model
{
    protected $table = 'alert_responses';

    protected $fillable = [
        'alert_id',
        'action',
        'notes',
        'response_notes',
        'responded_by',
        'timestamp',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the alert this response belongs to
     */
    public function alert(): BelongsTo
    {
        return $this->belongsTo(SecurityAlert::class, 'alert_id', 'id');
    }

    /**
     * Get the user who responded to this alert
     */
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by', 'id');
    }

    /**
     * Get the action type
     */
    public function getActionBadgeAttribute(): string
    {
        switch ($this->action) {
            case 'acknowledge':
                return 'primary';
            case 'investigate':
                return 'info';
            case 'escalate':
                return 'warning';
            case 'resolve':
                return 'success';
            case 'dismiss':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Scope: Get responses from a specific user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('responded_by', $userId);
    }

    /**
     * Scope: Get responses with specific action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope: Get recent responses
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('timestamp', 'desc');
    }

    /**
     * Scope: Get responses within a date range
     */
    public function scopeInDateRange($query, $start, $end)
    {
        return $query->whereBetween('timestamp', [$start, $end]);
    }
}

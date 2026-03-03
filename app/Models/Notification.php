<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Notification
 *
 * @property-read int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $message
 * @property array|null $details
 * @property string $severity
 * @property bool $read
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property string|null $action_url
 * @property string|null $action_label
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @mixin \Eloquent
 */
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'details',
        'severity',
        'read',
        'read_at',
        'action_url',
        'action_label',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'details' => 'array',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): self
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);

        return $this;
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): self
    {
        $this->update([
            'read' => false,
            'read_at' => null,
        ]);

        return $this;
    }

    /**
     * Get severity badge color
     */
    public function getSeverityColor(): string
    {
        return match ($this->severity) {
            'critical' => 'red',
            'warning' => 'yellow',
            'info' => 'blue',
            default => 'gray',
        };
    }

    /**
     * Get severity icon
     */
    public function getSeverityIcon(): string
    {
        return match ($this->severity) {
            'critical' => '🚨',
            'warning' => '⚠️',
            'info' => 'ℹ️',
            default => '•',
        };
    }

    /**
     * Scope: Unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Filter by severity
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope: Recent notifications
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

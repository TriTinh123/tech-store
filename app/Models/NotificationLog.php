<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'user_id',
        'channel',
        'event_type',
        'recipient',
        'status',
        'content',
        'error_message',
        'retry_count',
        'sent_at',
        'read_at',
        'provider',
        'provider_id',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark as sent
     */
    public function markAsSent(?string $providerId = null, ?array $metadata = null): self
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'provider_id' => $providerId,
            'metadata' => $metadata,
        ]);

        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage): self
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'retry_count' => $this->retry_count + 1,
        ]);

        return $this;
    }

    /**
     * Mark as read
     */
    public function markAsRead(): self
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);

        return $this;
    }

    /**
     * Can retry
     */
    public function canRetry(): bool
    {
        return $this->retry_count < 3 && $this->status === 'failed';
    }

    /**
     * Scope: By channel
     */
    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Scope: By status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Failed deliveries
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Pending delivery
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}

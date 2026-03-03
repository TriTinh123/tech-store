<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionActivity extends Model
{
    protected $table = 'session_activities';

    protected $fillable = [
        'user_id',
        'user_session_id',
        'activity_type',
        'ip_address',
        'user_agent',
        'method',
        'path',
        'status_code',
        'description',
        'metadata',
        'is_suspicious',
    ];

    protected $casts = [
        'metadata' => 'json',
        'is_suspicious' => 'boolean',
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the session this activity belongs to
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(UserSession::class, 'user_session_id');
    }

    /**
     * Get human-readable activity type
     */
    public function getActivityTypeLabel(): string
    {
        $types = [
            'login' => 'Đăng nhập',
            'logout' => 'Đăng xuất',
            'page_view' => 'Xem trang',
            'api_call' => 'Gọi API',
            'suspicious_activity' => 'Hoạt động đáng ngờ',
        ];

        return $types[$this->activity_type] ?? $this->activity_type;
    }

    /**
     * Scope for specific activity type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope for suspicious activities
     */
    public function scopeSuspicious($query)
    {
        return $query->where('is_suspicious', true);
    }

    /**
     * Scope for specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent activities
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}

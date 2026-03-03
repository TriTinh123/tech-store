<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentResponse extends Model
{
    protected $table = 'incident_responses';

    protected $fillable = [
        'incident_case_id',
        'responder_id',
        'action',
        'notes',
    ];

    /**
     * Get the incident
     */
    public function incident(): BelongsTo
    {
        return $this->belongsTo(IncidentCase::class, 'incident_case_id');
    }

    /**
     * Get the responder
     */
    public function responder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responder_id');
    }

    /**
     * Scope for recent responses
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope by responder
     */
    public function scopeByResponder($query, $userId)
    {
        return $query->where('responder_id', $userId);
    }
}

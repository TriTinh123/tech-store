<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentCase extends Model
{
    protected $table = 'incident_cases';

    protected $fillable = [
        'title',
        'description',
        'severity',
        'incident_type',
        'status',
        'affected_users',
        'assigned_to',
        'created_by',
        'related_alert_id',
        'resolution',
        'lessons_learned',
        'closed_at',
    ];

    protected $casts = [
        'affected_users' => 'array',
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the creator of the incident
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the assigned user
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the related alert
     */
    public function alertResponse(): BelongsTo
    {
        return $this->belongsTo(AlertResponse::class, 'related_alert_id');
    }

    /**
     * Get all responses for this incident
     */
    public function responses(): HasMany
    {
        return $this->hasMany(IncidentResponse::class);
    }

    /**
     * Scope for active incidents
     */
    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'closed');
    }

    /**
     * Scope for critical incidents
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope by severity
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get resolution time in hours
     */
    public function getResolutionTimeAttribute()
    {
        if ($this->closed_at) {
            return $this->created_at->diffInHours($this->closed_at);
        }

        return null;
    }
}

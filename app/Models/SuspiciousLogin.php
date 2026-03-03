<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuspiciousLogin extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
        'country',
        'city',
        'device_type',
        'browser',
        'risk_level',
        'reason',
        'confirmed_by_user',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

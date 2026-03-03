<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_logs';

    protected $fillable = [
        'type',
        'description',
        'user_id',
        'data',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }
}

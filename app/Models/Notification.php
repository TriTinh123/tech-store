<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id','type','title','message','details',
        'severity','read','read_at','action_url','action_label',
    ];

    protected $casts = [
        'read'     => 'boolean',
        'read_at'  => 'datetime',
        'details'  => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }
}

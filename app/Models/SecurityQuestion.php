<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecurityQuestion extends Model
{
    protected $table = 'security_questions';

    protected $fillable = [
        'question',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function userAnswers()
    {
        return $this->hasMany(UserSecurityAnswer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSecurityAnswer extends Model
{
    protected $table = 'user_security_answers';

    protected $fillable = [
        'user_id',
        'security_question_id',
        'answer_hash',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function securityQuestion()
    {
        return $this->belongsTo(SecurityQuestion::class);
    }

    public function verifyAnswer($answer)
    {
        return hash('sha256', strtolower($answer)) === $this->answer_hash;
    }
}

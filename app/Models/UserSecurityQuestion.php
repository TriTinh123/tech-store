<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $security_question_id
 * @property string $answer_hash
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 * @property-read SecurityQuestion $question
 */
class UserSecurityQuestion extends Model
{
    protected $table = 'user_security_answers';

    protected $fillable = [
        'user_id',
        'security_question_id',
        'answer_hash',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Get the user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the security question
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(SecurityQuestion::class, 'security_question_id');
    }

    /**
     * Scope: Get questions for a user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get active questions
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('answer_hash');
    }

    /**
     * Set answer (hashed)
     */
    public function setAnswer(string $answer): void
    {
        $this->answer_hash = hash('sha256', strtolower(trim($answer)));
    }

    /**
     * Check answer
     */
    public function checkAnswer(string $answer): bool
    {
        return $this->answer_hash === hash('sha256', strtolower(trim($answer)));
    }

    /**
     * Verify answer (alias for checkAnswer)
     */
    public function verifyAnswer(string $answer): bool
    {
        return $this->checkAnswer($answer);
    }

    /**
     * Increment verification attempts
     */
    public function incrementVerificationAttempts(): void
    {
        // Track failed verification attempts if we add that column
    }

    /**
     * Check if exceeded attempts
     */
    public function hasExceededAttempts(int $maxAttempts = 5): bool
    {
        // Implement if we add verification_attempts column
        return false;
    }

    /**
     * Deactivate answer
     */
    public function deactivate(): void
    {
        $this->update(['answer_hash' => null]);
    }

    /**
     * Record successful verification
     */
    public function recordSuccessfulVerification(): void
    {
        // Could track successful verifications if needed
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender',
        'message',
        'metadata',
        'suggested_products',
        'sentiment_score',
    ];

    protected $casts = [
        'metadata' => 'json',
        'suggested_products' => 'json',
        'sentiment_score' => 'float',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class);
    }
}

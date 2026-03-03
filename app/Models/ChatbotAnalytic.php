<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotAnalytic extends Model
{
    protected $table = 'chatbot_analytics';

    protected $fillable = ['conversation_id', 'metric_type', 'metric_name', 'count', 'metadata', 'date'];

    protected $casts = ['metadata' => 'json'];

    public function conversation()
    {
        return $this->belongsTo(ChatbotConversation::class);
    }
}

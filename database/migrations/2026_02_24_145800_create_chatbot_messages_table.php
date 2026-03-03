<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->enum('sender', ['user', 'bot'])->default('user');
            $table->text('message');
            $table->json('metadata')->nullable(); // Store intent, entities, etc
            $table->json('suggested_products')->nullable(); // Store product recommendations
            $table->decimal('sentiment_score', 3, 2)->nullable(); // -1 to 1
            $table->timestamps();
            $table->index('conversation_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
    }
};

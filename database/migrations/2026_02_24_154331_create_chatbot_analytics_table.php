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
        Schema::create('chatbot_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->nullable()->constrained('chatbot_conversations')->cascadeOnDelete();
            $table->string('metric_type'); // 'intent', 'product_viewed', 'product_recommended', 'order_checked', etc.
            $table->string('metric_name'); // actual metric (e.g., 'chuột', 'thanh toán', 'kiểm tra đơn')
            $table->integer('count')->default(1);
            $table->json('metadata')->nullable(); // Extra data
            $table->date('date')->default(now()); // For daily aggregation
            $table->timestamps();
            $table->index(['metric_type', 'date']);
            $table->index('metric_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_analytics');
    }
};

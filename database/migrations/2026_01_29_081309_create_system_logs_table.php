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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // login_attempt, product_change, order_update, user_blocked, etc.
            $table->text('description');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->json('data')->nullable(); // Additional data like old/new values
            $table->timestamps();
            $table->index('type');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};

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
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->boolean('success')->default(false);
            $table->string('reason')->nullable(); // 'invalid_credentials', 'account_locked', etc
            $table->timestamp('attempted_at');
            $table->timestamps();
            $table->index(['user_id', 'attempted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};

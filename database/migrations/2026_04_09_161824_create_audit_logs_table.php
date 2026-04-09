<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->string('event')->default('login_attempt');
            $table->boolean('password_ok')->default(false);
            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->unsignedTinyInteger('ip_count')->default(1);
            $table->unsignedTinyInteger('device_count')->default(1);
            $table->string('geo_country', 10)->nullable();
            $table->boolean('geo_changed')->default(false);
            $table->string('ai_result')->nullable();
            $table->unsignedTinyInteger('ai_risk_score')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->boolean('account_locked')->default(false);
            $table->json('raw_features')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index(['user_id', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

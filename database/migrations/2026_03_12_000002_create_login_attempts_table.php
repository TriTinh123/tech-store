<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('login_attempts');
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();

            // Auth stages
            $table->boolean('password_ok')->default(false);
            $table->boolean('otp_ok')->default(false);

            // AI Risk Assessment
            $table->string('risk_level')->nullable();     // low|medium|high|critical
            $table->unsignedTinyInteger('risk_numeric')->nullable(); // 0-100
            $table->float('risk_score')->nullable();      // raw IF score
            $table->boolean('is_anomaly')->default(false);
            $table->json('explanation')->nullable();      // array of reasons

            // 3FA
            $table->boolean('required_3fa')->default(false);
            $table->boolean('passed_3fa')->default(false);

            $table->boolean('success')->default(false);  // final login result
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};

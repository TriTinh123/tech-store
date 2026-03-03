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
        Schema::create('three_factor_auth_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('auth_method'); // otp, security_question, biometric
            $table->string('status'); // pending, success, failed
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->text('failure_reason')->nullable();
            $table->integer('attempt_number')->default(1);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('three_factor_auth_logs');
    }
};

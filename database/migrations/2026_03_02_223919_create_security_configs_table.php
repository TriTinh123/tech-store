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
        Schema::create('security_configs', function (Blueprint $table) {
            $table->id();
            // Authentication Levels
            $table->enum('auth_level', ['basic', 'standard', 'strict', 'ultra'])->default('standard');
            // OTP Settings
            $table->boolean('require_otp')->default(true);
            $table->enum('otp_method', ['email', 'sms', 'both'])->default('email');
            $table->integer('otp_expiry_minutes')->default(10);
            // Security Questions
            $table->boolean('require_security_questions')->default(true);
            $table->integer('min_questions_required')->default(2);
            // Device Verification
            $table->boolean('require_device_verification')->default(true);
            $table->integer('max_devices_per_user')->default(5);
            // Login Attempt Restrictions
            $table->integer('max_failed_attempts')->default(5);
            $table->integer('lockout_duration_minutes')->default(15);
            // Suspicious Activity
            $table->integer('anomaly_detection_threshold')->default(50); // 0-100 risk score
            $table->boolean('auto_lockout_critical')->default(true); // Auto-lock on critical risk
            // IP Blocking
            $table->boolean('enable_ip_blocking')->default(true);
            $table->integer('block_ips_after_attempts')->default(10);
            // Geographic Restrictions
            $table->boolean('enable_geo_restriction')->default(false);
            $table->json('allowed_countries')->nullable(); // List of allowed countries
            $table->boolean('require_confirmation_new_location')->default(true);
            // Session Management
            $table->boolean('allow_concurrent_sessions')->default(false);
            $table->integer('session_timeout_minutes')->default(60);
            // Email Notifications
            $table->boolean('notify_on_new_ip')->default(true);
            $table->boolean('notify_on_new_device')->default(true);
            $table->boolean('notify_on_failed_attempts')->default(true);
            $table->boolean('notify_on_account_lockout')->default(true);
            // Admin Settings
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_configs');
    }
};

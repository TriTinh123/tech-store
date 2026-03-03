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
        Schema::create('auto_responses', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('suspicious_login_id')->nullable()->constrained('suspicious_logins')->onDelete('set null');
            $table->foreignId('triggered_by_admin_id')->nullable()->constrained('users')->onDelete('set null');

            // Trigger Information
            $table->enum('trigger_type', ['anomaly_detection', 'manual_trigger', 'scheduled', 'threshold_breach'])->default('anomaly_detection');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('trigger_description')->nullable();

            // Response Action
            $table->enum('response_action', ['send_alert', 'request_confirmation', 'lock_account', 'block_ip', 'logout_all_sessions', 'force_2fa_reauth', 'temporary_lockout'])->default('send_alert');
            $table->text('action_description')->nullable();

            // Status Tracking
            $table->enum('status', ['pending', 'in_progress', 'executed', 'failed', 'cancelled', 'expired'])->default('pending');
            $table->text('execution_result')->nullable();
            $table->text('error_message')->nullable();

            // Execution Details
            $table->timestamp('triggered_at')->useCurrent();
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('expires_at')->nullable();

            // Configuration Snapshot (at time of response)
            $table->json('security_config_snapshot')->nullable();
            $table->json('user_context')->nullable(); // IP, device, location at time of response
            $table->json('anomaly_details')->nullable(); // Store what was detected

            // Confirmation & Follow-up
            $table->boolean('requires_user_confirmation')->default(false);
            $table->boolean('user_confirmed')->default(false);
            $table->timestamp('user_confirmation_at')->nullable();
            $table->text('user_confirmation_details')->nullable();

            // Lockout Information (if action was lock/lockout)
            $table->timestamp('lockout_until')->nullable();
            $table->string('lockout_reason')->nullable();
            $table->boolean('lockout_auto_unlock')->default(true);

            // IP Block Information (if action was block_ip)
            $table->string('blocked_ip_address')->nullable();
            $table->boolean('is_permanent_block')->default(false);

            // Notification Tracking
            $table->boolean('user_notified')->default(false);
            $table->timestamp('notification_sent_at')->nullable();
            $table->string('notification_method')->nullable(); // email, sms, push, etc

            // Audit & Logging
            $table->text('admin_notes')->nullable();
            $table->json('action_history')->nullable(); // Log all changes to this response
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by_admin_id')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('response_action');
            $table->index('trigger_type');
            $table->index('severity');
            $table->index('triggered_at');
            $table->index('executed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_responses');
    }
};

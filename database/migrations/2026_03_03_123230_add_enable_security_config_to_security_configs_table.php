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
        Schema::table('security_configs', function (Blueprint $table) {
            // Add missing columns from the model
            if (!Schema::hasColumn('security_configs', 'enable_security_config')) {
                $table->boolean('enable_security_config')->default(true);
            }
            if (!Schema::hasColumn('security_configs', 'security_questions_min_answers')) {
                $table->integer('security_questions_min_answers')->default(2);
            }
            if (!Schema::hasColumn('security_configs', 'max_concurrent_devices')) {
                $table->integer('max_concurrent_devices')->default(5);
            }
            if (!Schema::hasColumn('security_configs', 'max_login_attempts')) {
                $table->integer('max_login_attempts')->default(5);
            }
            if (!Schema::hasColumn('security_configs', 'login_attempt_lockout_minutes')) {
                $table->integer('login_attempt_lockout_minutes')->default(15);
            }
            if (!Schema::hasColumn('security_configs', 'anomaly_detection_enabled')) {
                $table->boolean('anomaly_detection_enabled')->default(true);
            }
            if (!Schema::hasColumn('security_configs', 'auto_lockout_on_critical')) {
                $table->boolean('auto_lockout_on_critical')->default(true);
            }
            if (!Schema::hasColumn('security_configs', 'block_ips_after_failed_attempts')) {
                $table->integer('block_ips_after_failed_attempts')->default(10);
            }
            if (!Schema::hasColumn('security_configs', 'ip_block_duration_minutes')) {
                $table->integer('ip_block_duration_minutes')->default(60);
            }
            if (!Schema::hasColumn('security_configs', 'max_concurrent_sessions')) {
                $table->integer('max_concurrent_sessions')->default(3);
            }
            if (!Schema::hasColumn('security_configs', 'enforce_password_expiry_days')) {
                $table->integer('enforce_password_expiry_days')->nullable();
            }
            if (!Schema::hasColumn('security_configs', 'require_password_history_count')) {
                $table->integer('require_password_history_count')->nullable();
            }
            if (!Schema::hasColumn('security_configs', 'require_strong_password')) {
                $table->boolean('require_strong_password')->default(true);
            }
            if (!Schema::hasColumn('security_configs', 'enable_biometric_authentication')) {
                $table->boolean('enable_biometric_authentication')->default(false);
            }
            if (!Schema::hasColumn('security_configs', 'enable_hardware_key_support')) {
                $table->boolean('enable_hardware_key_support')->default(false);
            }
            if (!Schema::hasColumn('security_configs', 'idle_timeout_minutes')) {
                $table->integer('idle_timeout_minutes')->default(30);
            }
            if (!Schema::hasColumn('security_configs', 'suspicious_activity_threshold')) {
                $table->integer('suspicious_activity_threshold')->default(50);
            }
            if (!Schema::hasColumn('security_configs', 'log_all_activities')) {
                $table->boolean('log_all_activities')->default(true);
            }
            if (!Schema::hasColumn('security_configs', 'data_retention_days')) {
                $table->integer('data_retention_days')->default(90);
            }
            if (!Schema::hasColumn('security_configs', 'created_by_admin_id')) {
                $table->unsignedBigInteger('created_by_admin_id')->nullable();
            }
            if (!Schema::hasColumn('security_configs', 'updated_by_admin_id')) {
                $table->unsignedBigInteger('updated_by_admin_id')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('security_configs', function (Blueprint $table) {
            $columns = [
                'enable_security_config',
                'security_questions_min_answers',
                'max_concurrent_devices',
                'max_login_attempts',
                'login_attempt_lockout_minutes',
                'anomaly_detection_enabled',
                'auto_lockout_on_critical',
                'block_ips_after_failed_attempts',
                'ip_block_duration_minutes',
                'max_concurrent_sessions',
                'enforce_password_expiry_days',
                'require_password_history_count',
                'require_strong_password',
                'enable_biometric_authentication',
                'enable_hardware_key_support',
                'idle_timeout_minutes',
                'suspicious_activity_threshold',
                'log_all_activities',
                'data_retention_days',
                'created_by_admin_id',
                'updated_by_admin_id',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('security_configs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

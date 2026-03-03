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
        Schema::create('blocked_ips', function (Blueprint $table) {
            $table->id();

            // IP Address
            $table->string('ip_address')->unique();
            $table->string('country_code')->nullable();
            $table->string('location')->nullable();

            // Block Details
            $table->enum('block_type', ['manual', 'auto_attack', 'auto_failed_attempts'])->default('manual');
            $table->text('reason')->nullable();
            $table->boolean('is_permanent')->default(false);

            // Block Timing
            $table->timestamp('blocked_at')->useCurrent();
            $table->timestamp('unblock_at')->nullable();

            // Admin Information
            $table->foreignId('blocked_by_admin_id')->nullable()->constrained('users')->onDelete('set null');

            // Statistics
            $table->integer('failed_attempts')->default(0);
            $table->integer('suspicious_activities_count')->default(0);
            $table->integer('total_login_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();

            // Risk Details
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->json('suspicious_patterns')->nullable(); // Store detected suspicious patterns

            // Whitelist/Exception Settings
            $table->boolean('requires_email_verification')->default(false);
            $table->boolean('requires_otp_unlock')->default(false);
            $table->json('unlock_conditions')->nullable();

            // Audit Trail
            $table->text('notes')->nullable();
            $table->json('history')->nullable(); // JSON log of block/unblock history

            $table->timestamps();

            // Indexes
            $table->index('ip_address');
            $table->index('block_type');
            $table->index('is_permanent');
            $table->index('blocked_at');
            $table->index('risk_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blocked_ips');
    }
};

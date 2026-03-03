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
        Schema::create('security_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('suspicious_login_id')
                ->nullable()
                ->constrained('suspicious_logins')
                ->onDelete('cascade');
            $table->enum('alert_type', [
                'new_ip',
                'new_device',
                'unusual_time',
                'rapid_location',
                'failed_attempt',
                'account_locked',
            ]);
            $table->text('message');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->json('notification_channels')->default('["email"]'); // array: email, website, sms
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable(); // For website notifications
            $table->boolean('confirmed_by_user')->default(false); // User confirms if suspicious
            $table->timestamps();

            // Indexes for faster queries
            $table->index(['user_id', 'created_at']);
            $table->index(['severity', 'read_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_alerts');
    }
};

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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('channel'); // email, sms, in_app
            $table->string('event_type'); // concurrent_login, suspicious_activity, etc.
            $table->string('recipient'); // email or phone number
            $table->string('status')->default('pending'); // pending, sent, failed, read
            $table->text('content')->nullable();
            $table->text('error_message')->nullable();
            $table->integer('retry_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('provider')->nullable(); // aws_sns, twilio, sendgrid, etc.
            $table->string('provider_id')->nullable(); // Message ID from provider
            $table->json('metadata')->nullable(); // Additional provider info
            $table->timestamps();

            $table->index('user_id');
            $table->index('channel');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};

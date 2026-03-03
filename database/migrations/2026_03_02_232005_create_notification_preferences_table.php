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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Notification channels
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('in_app_enabled')->default(true);

            // Event preferences
            $table->boolean('notify_concurrent_login')->default(true);
            $table->boolean('notify_suspicious_activity')->default(true);
            $table->boolean('notify_3fa_changes')->default(true);
            $table->boolean('notify_ip_blocked')->default(true);
            $table->boolean('notify_password_change')->default(true);
            $table->boolean('notify_new_device')->default(true);
            $table->boolean('notify_location_change')->default(true);

            // SMS preferences
            $table->string('phone_number')->nullable();
            $table->boolean('phone_verified')->default(false);
            $table->timestamp('phone_verified_at')->nullable();

            // Email preferences
            $table->string('notification_email')->nullable();
            $table->boolean('email_verified')->default(false);

            // Digest/Frequency settings
            $table->string('email_frequency')->default('immediate'); // immediate, daily, weekly
            $table->string('sms_frequency')->default('immediate');

            // Quiet hours
            $table->boolean('quiet_hours_enabled')->default(false);
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();

            $table->timestamps();
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};

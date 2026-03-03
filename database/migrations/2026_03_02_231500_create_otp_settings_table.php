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
        Schema::create('otp_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('otp_code')->nullable();
            $table->timestamp('otp_expires_at')->nullable();
            $table->integer('otp_attempts')->default(0);
            $table->boolean('is_email_verified')->default(false);
            $table->boolean('is_sms_verified')->default(false);
            $table->string('phone_number')->nullable();
            $table->string('otp_delivery_method')->default('email'); // email, sms, both
            $table->boolean('otp_enabled')->default(true);
            $table->timestamp('last_otp_sent_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_settings');
    }
};

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
        Schema::create('biometric_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('biometric_type'); // fingerprint, face_id, iris_scan
            $table->longText('biometric_data_encrypted');
            $table->string('device_id')->nullable();
            $table->string('device_name')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('verification_success_count')->default(0);
            $table->integer('verification_fail_count')->default(0);
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamp('last_failed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biometric_data');
    }
};

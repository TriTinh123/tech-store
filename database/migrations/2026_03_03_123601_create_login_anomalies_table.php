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
        Schema::create('login_anomalies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('anomaly_type'); // e.g., 'unusual_location', 'impossible_travel', 'new_device', etc.
            $table->string('description')->nullable();
            $table->ipAddress('ip_address');
            $table->string('device_fingerprint')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', ['new', 'investigating', 'confirmed', 'false_positive', 'resolved'])->default('new');
            $table->boolean('is_whitelisted')->default(false);
            $table->timestamp('whitelisted_at')->nullable();
            $table->unsignedBigInteger('whitelisted_by')->nullable();
            $table->text('admin_notes')->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('handled_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('whitelisted_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index('user_id');
            $table->index('created_at');
            $table->index('risk_level');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_anomalies');
    }
};

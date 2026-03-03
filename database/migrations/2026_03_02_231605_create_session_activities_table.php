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
        Schema::create('session_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_session_id')->nullable()->constrained('user_sessions')->onDelete('set null');
            $table->string('activity_type'); // login, logout, page_view, api_call, suspicious_activity
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('method')->nullable(); // GET, POST, etc.
            $table->string('path')->nullable();
            $table->integer('status_code')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('activity_type');
            $table->index('is_suspicious');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_activities');
    }
};

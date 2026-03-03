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
        Schema::create('concurrent_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('primary_session_id');
            $table->string('secondary_session_id');
            $table->string('primary_ip_address');
            $table->string('secondary_ip_address');
            $table->string('primary_location')->nullable();
            $table->string('secondary_location')->nullable();
            $table->integer('time_difference_seconds')->nullable();
            $table->string('status')->default('detected'); // detected, confirmed, authorized, false_positive
            $table->text('admin_notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concurrent_logins');
    }
};

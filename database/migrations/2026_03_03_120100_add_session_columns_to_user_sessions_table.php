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
        if (Schema::hasTable('user_sessions')) {
            Schema::table('user_sessions', function (Blueprint $table) {
                if (! Schema::hasColumn('user_sessions', 'session_id')) {
                    $table->string('session_id')->unique()->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'ip_address')) {
                    $table->string('ip_address')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'user_agent')) {
                    $table->string('user_agent')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'device_name')) {
                    $table->string('device_name')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'device_type')) {
                    $table->string('device_type')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'device_fingerprint')) {
                    $table->string('device_fingerprint')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'browser')) {
                    $table->string('browser')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'os')) {
                    $table->string('os')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'location')) {
                    $table->string('location')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'city')) {
                    $table->string('city')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'country')) {
                    $table->string('country')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'latitude')) {
                    $table->string('latitude')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'longitude')) {
                    $table->string('longitude')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'logged_in_at')) {
                    $table->timestamp('logged_in_at')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'last_activity_at')) {
                    $table->timestamp('last_activity_at')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'logged_out_at')) {
                    $table->timestamp('logged_out_at')->nullable();
                }
                if (! Schema::hasColumn('user_sessions', 'status')) {
                    $table->string('status')->default('active');
                }
                if (! Schema::hasColumn('user_sessions', 'is_active')) {
                    $table->boolean('is_active')->default(true);
                }
                if (! Schema::hasColumn('user_sessions', 'is_flagged')) {
                    $table->boolean('is_flagged')->default(false);
                }
                if (! Schema::hasColumn('user_sessions', 'flag_reason')) {
                    $table->text('flag_reason')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_sessions')) {
            Schema::table('user_sessions', function (Blueprint $table) {
                $columns = ['session_id', 'ip_address', 'user_agent', 'device_name', 'device_type', 'device_fingerprint', 'browser', 'os', 'location', 'city', 'country', 'latitude', 'longitude', 'logged_in_at', 'last_activity_at', 'logged_out_at', 'status', 'is_active', 'is_flagged', 'flag_reason'];
                
                foreach ($columns as $column) {
                    if (Schema::hasColumn('user_sessions', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

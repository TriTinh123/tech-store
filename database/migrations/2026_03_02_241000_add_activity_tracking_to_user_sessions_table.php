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
                // Add columns for detailed activity tracking if they don't exist
                if (! Schema::hasColumn('user_sessions', 'last_activity_ip')) {
                    $table->ipAddress('last_activity_ip')->nullable()->after('last_activity_at');
                }
                if (! Schema::hasColumn('user_sessions', 'last_activity_url')) {
                    $table->string('last_activity_url')->nullable()->after('last_activity_ip');
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
                if (Schema::hasColumn('user_sessions', 'last_activity_ip')) {
                    $table->dropColumn('last_activity_ip');
                }
                if (Schema::hasColumn('user_sessions', 'last_activity_url')) {
                    $table->dropColumn('last_activity_url');
                }
            });
        }
    }
};

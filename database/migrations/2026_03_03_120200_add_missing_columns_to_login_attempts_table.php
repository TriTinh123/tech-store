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
        if (Schema::hasTable('login_attempts')) {
            Schema::table('login_attempts', function (Blueprint $table) {
                if (! Schema::hasColumn('login_attempts', 'success')) {
                    $table->boolean('success')->default(false);
                }
                if (! Schema::hasColumn('login_attempts', 'reason')) {
                    $table->string('reason')->nullable();
                }
                if (! Schema::hasColumn('login_attempts', 'attempted_at')) {
                    $table->timestamp('attempted_at')->nullable();
                }
                if (! Schema::hasColumn('login_attempts', 'device_fingerprint')) {
                    $table->string('device_fingerprint')->nullable();
                }
                if (! Schema::hasColumn('login_attempts', 'user_agent')) {
                    $table->string('user_agent')->nullable();
                }
                if (! Schema::hasColumn('login_attempts', 'ip_address')) {
                    $table->string('ip_address')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('login_attempts')) {
            Schema::table('login_attempts', function (Blueprint $table) {
                $columns = ['success', 'reason', 'attempted_at', 'device_fingerprint', 'user_agent', 'ip_address'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('login_attempts', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};

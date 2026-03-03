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
                if (! Schema::hasColumn('user_sessions', 'is_current')) {
                    $table->boolean('is_current')->default(false);
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
                if (Schema::hasColumn('user_sessions', 'is_current')) {
                    $table->dropColumn('is_current');
                }
            });
        }
    }
};

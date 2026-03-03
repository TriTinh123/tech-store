<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing login times by adding 7 hours to convert from UTC to Asia/Ho_Chi_Minh
        DB::statement('UPDATE login_logs SET login_at = DATE_ADD(login_at, INTERVAL 7 HOUR)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert by subtracting 7 hours
        DB::statement('UPDATE login_logs SET login_at = DATE_SUB(login_at, INTERVAL 7 HOUR)');
    }
};

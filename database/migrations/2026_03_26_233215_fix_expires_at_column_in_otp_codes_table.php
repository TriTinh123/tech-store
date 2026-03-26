<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix: MySQL TIMESTAMP column with explicit_defaults_for_timestamp=OFF
        // auto-updates the first TIMESTAMP column (expires_at) on every UPDATE,
        // causing OTP to appear expired immediately after verification.
        // Switching to DATETIME prevents this implicit ON UPDATE CURRENT_TIMESTAMP behavior.
        DB::statement('ALTER TABLE otp_codes MODIFY COLUMN expires_at DATETIME NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE otp_codes MODIFY COLUMN expires_at TIMESTAMP NOT NULL');
    }
};

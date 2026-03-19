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
        Schema::table('coupons', function (Blueprint $table) {
            $table->timestamp('valid_from')->nullable()->default(null)->change();
            if (Schema::hasColumn('coupons', 'valid_to')) {
                $table->timestamp('valid_to')->nullable()->default(null)->change();
            }
        });
    }

    public function down(): void
    {
        // no-op
    }
};

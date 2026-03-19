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
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->string('geo_country', 100)->nullable()->after('is_anomaly');
            $table->string('geo_country_code', 5)->nullable()->after('geo_country');
            $table->string('geo_city', 100)->nullable()->after('geo_country_code');
            $table->boolean('geo_is_vn')->nullable()->after('geo_city');
            $table->boolean('geo_is_foreign_risk')->nullable()->after('geo_is_vn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropColumn(['geo_country', 'geo_country_code', 'geo_city', 'geo_is_vn', 'geo_is_foreign_risk']);
        });
    }
};

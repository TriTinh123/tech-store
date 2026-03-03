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
                // Drop unnecessary columns that are causing strict mode issues
                $columnsToRemove = ['email', 'location', 'latitude', 'longitude', 'status', 'failure_reason', 'is_suspicious', 'anomaly_type'];
                
                foreach ($columnsToRemove as $column) {
                    if (Schema::hasColumn('login_attempts', $column)) {
                        $table->dropColumn($column);
                    }
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
                $table->string('email')->nullable();
                $table->string('location')->nullable();
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 10, 8)->nullable();
                $table->enum('status', ['success', 'failed', 'blocked'])->default('failed');
                $table->string('failure_reason')->nullable();
                $table->boolean('is_suspicious')->default(false);
                $table->string('anomaly_type')->nullable();
            });
        }
    }
};

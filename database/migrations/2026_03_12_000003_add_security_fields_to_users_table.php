<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 3FA security question
            $table->string('security_question')->nullable()->after('address');
            $table->string('security_answer')->nullable()->after('security_question'); // hashed

            // Device & IP memory (JSON arrays)
            $table->json('known_ips')->nullable()->after('security_answer');
            $table->json('known_devices')->nullable()->after('known_ips');

            // Login tracking
            $table->timestamp('last_login_at')->nullable()->after('known_devices');
            $table->unsignedSmallInteger('failed_login_count')->default(0)->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'security_question', 'security_answer',
                'known_ips', 'known_devices',
                'last_login_at', 'failed_login_count',
            ]);
        });
    }
};

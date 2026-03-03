<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('status');
            $table->enum('shipping_status', ['pending', 'processing', 'shipped', 'out_for_delivery', 'delivered', 'returned'])->default('pending')->after('tracking_number');
            $table->string('shipping_provider')->nullable()->after('shipping_status');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_amount');
            $table->string('coupon_code')->nullable()->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tracking_number', 'shipping_status', 'shipping_provider', 'discount_amount', 'coupon_code']);
        });
    }
};

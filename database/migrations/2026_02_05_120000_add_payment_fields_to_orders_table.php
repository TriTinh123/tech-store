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
        Schema::table('orders', function (Blueprint $table) {
            // Add payment gateway fields if they don't exist
            if (! Schema::hasColumn('orders', 'payment_status')) {
                $table->enum('payment_status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending')->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'payment_gateway')) {
                $table->string('payment_gateway')->nullable()->after('payment_status');
            }

            if (! Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('payment_gateway');
            }

            if (! Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_reference');
            }

            if (! Schema::hasColumn('orders', 'shipping_status')) {
                $table->enum('shipping_status', ['pending', 'processing', 'shipped', 'delivered', 'returned'])->default('pending')->after('paid_at');
            }

            if (! Schema::hasColumn('orders', 'shipping_provider')) {
                $table->string('shipping_provider')->nullable()->after('shipping_status');
            }

            if (! Schema::hasColumn('orders', 'tracking_number')) {
                $table->string('tracking_number')->nullable()->after('shipping_provider');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_gateway',
                'payment_reference',
                'paid_at',
                'shipping_status',
                'shipping_provider',
                'tracking_number',
            ]);
        });
    }
};

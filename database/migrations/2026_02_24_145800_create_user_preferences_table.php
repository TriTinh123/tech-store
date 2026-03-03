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
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->json('favorite_categories')->nullable(); // Store favorite product categories
            $table->json('price_range')->nullable(); // min, max price range
            $table->json('viewed_products')->nullable(); // Recently viewed product IDs
            $table->json('purchased_products')->nullable(); // Previous purchases
            $table->integer('total_purchases')->default(0);
            $table->decimal('average_spending', 10, 2)->default(0);
            $table->string('shopping_frequency')->default('occasional'); // often, sometimes, rarely
            $table->timestamp('last_purchase_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};

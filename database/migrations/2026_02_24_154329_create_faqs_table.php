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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question')->unique();
            $table->longText('answer');
            $table->string('category')->nullable(); // product, payment, order, shipping, etc.
            $table->integer('views')->default(0); // Track how many times it's viewed
            $table->integer('helpful')->default(0); // Track if answer was helpful
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faqs');
    }
};

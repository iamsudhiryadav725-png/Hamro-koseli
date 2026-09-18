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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained('products')
                ->onDelete('cascade');

            $table->tinyInteger('rating')->unsigned()->comment('Star rating 1 to 5');
            $table->text('comment')->nullable();
            $table->tinyInteger('verified_purchase')->default(0)->comment('1 = buyer of this product');

            $table->timestamps();

            // One review per user per product
            $table->unique(['user_id', 'product_id']);

            // Indexes for common query patterns
            $table->index('product_id');
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};

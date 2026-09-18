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
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('label', 50)->nullable()->comment('e.g. Home, Office, Other');
            $table->text('address');
            $table->string('city', 80);
            $table->string('province', 80);
            $table->string('postal_code', 20)->nullable();
            $table->string('country', 60)->default('Nepal');
            $table->string('phone', 20)->nullable();
            $table->tinyInteger('is_default')->default(0)->comment('1 = default delivery address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};

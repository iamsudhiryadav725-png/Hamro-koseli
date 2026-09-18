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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable'); // creates imageable_id (BIGINT UNSIGNED) + imageable_type (VARCHAR) + index
            $table->string('type', 50)->nullable()->comment('e.g. banner, thumbnail, gallery');
            $table->string('path', 255)->notNull();
            $table->tinyInteger('is_primary')->default(0)->comment('1 = primary display image');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};

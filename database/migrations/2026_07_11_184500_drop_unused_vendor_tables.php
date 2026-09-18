<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('vendor_categories');
        Schema::dropIfExists('vendor_reviews');
        Schema::dropIfExists('vendor_earnings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No rollback action as these tables are unused.
    }
};

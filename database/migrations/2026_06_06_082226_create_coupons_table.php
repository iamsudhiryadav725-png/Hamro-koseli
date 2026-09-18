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
        Schema::create('coupons', function (Blueprint $table) {
            // Primary key
            $table->id();                                           // BIGINT UNSIGNED, PK, AUTO_INCREMENT

            // Foreign key -nullable: NULL = platform-wide coupon
            $table->foreignId('vendor_id')
                ->nullable()
                ->constrained('vendors')
                ->nullOnDelete();                                // FK → vendors.id, NULLABLE

            // Coupon identity
            $table->string('code', 50)->unique();                  // UNIQUE, NOT NULL

            // Discount configuration
            $table->enum('discount_type', ['percentage', 'fixed_amount']); // NOT NULL
            $table->decimal('discount_value', 10, 2);              // NOT NULL
            $table->decimal('min_order', 10, 2)->default(0.00);    // Minimum order value
            $table->unsignedInteger('max_uses')->nullable();        // NULL = unlimited
            $table->unsignedInteger('used_count')->default(0);      // Times used counter

            // Validity
            $table->timestamp('expires_at')->nullable();            // NULL = no expiry
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

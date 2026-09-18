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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')
                ->constrained('vendors')
                ->onDelete('cascade');

            // Sum of vendor's net_amount earnings before platform fee
            $table->decimal('gross_amount', 12, 2)->default(0)->comment('Total vendor earnings before 3% platform fee');

            // 3% platform fee deducted
            $table->decimal('platform_fee', 12, 2)->default(0)->comment('3% platform fee deducted from gross_amount');

            // Final amount the vendor receives (gross_amount - platform_fee)
            $table->decimal('amount', 12, 2)->comment('Net amount disbursed to vendor after fee');

            $table->string('method', 50)->comment('Bank transfer | eSewa | Khalti');
            $table->string('transaction_id', 150)->nullable()->comment('Bank/gateway transaction ref');

            $table->enum('status', ['pending', 'processing', 'completed', 'Cancelled'])->default('pending');

            $table->text('notes')->nullable()->comment('Admin notes for this payout');

            $table->timestamp('paid_at')->nullable()->comment('Disbursement timestamp');

            $table->timestamps();

            // Indexes
            $table->index('vendor_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};

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
        Schema::create('notifications', function (Blueprint $table) {
            // Primary key
            $table->id();                                           // BIGINT UNSIGNED, PK, AUTO_INCREMENT

            // Foreign key
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();                             // FK → users.id, NOT NULL

            // Notification content
            $table->string('type', 80);                            // order_placed | payment_received | etc.
            $table->string('title', 200);                          // Short notification title
            $table->text('message');                               // Full notification body

            // Read state
            $table->tinyInteger('is_read')->default(0);            // 0 = unread, 1 = read
            $table->timestamp('read_at')->nullable();              // Time user read the notification

            // Only created_at per schema -no updated_at
            $table->timestamp('created_at')->useCurrent();

            // Indexes for common queries
            $table->index(['user_id', 'is_read']);                 // fetch unread per user fast
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

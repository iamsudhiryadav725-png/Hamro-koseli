<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    protected $fillable = [
        'vendor_id',
        'gross_amount',
        'platform_fee',
        'amount',
        'method',
        'transaction_id',
        'status',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'gross_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';

    const STATUS_PROCESSING = 'processing';

    const STATUS_COMPLETED = 'completed';

    const STATUS_FAILED = 'failed';

    /** 3% platform fee deducted from vendor order earnings */
    const PLATFORM_FEE_RATE = 0.03;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Core Logic — reads directly from order_items
    |--------------------------------------------------------------------------
    */

    /**
     * Get total earnings for a vendor from delivered order items.
     * Uses order_items.subtotal which is quantity × price.
     */
    public static function getTotalEarnings(int $vendorId): float
    {
        return (float) OrderItem::where('vendor_id', $vendorId)
            ->where('status', 'delivered')
            ->sum('subtotal');
    }

    /**
     * Create a payout for a vendor:
     *  1. Sums subtotal from all delivered order_items for the vendor
     *  2. Deducts 3% platform fee
     *  3. Saves the payout record
     */
    public static function createForVendor(int $vendorId, string $method, ?string $notes = null): ?static
    {
        $gross = static::getTotalEarnings($vendorId);

        if ($gross <= 0) {
            return null;
        }

        $fee = round($gross * self::PLATFORM_FEE_RATE, 2);
        $net = round($gross - $fee, 2);

        return static::create([
            'vendor_id' => $vendorId,
            'gross_amount' => $gross,
            'platform_fee' => $fee,
            'amount' => $net,
            'method' => $method,
            'status' => self::STATUS_PENDING,
            'notes' => $notes,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function markAsCompleted(?string $transactionId = null): bool
    {
        return $this->update([
            'status' => self::STATUS_COMPLETED,
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'paid_at' => now(),
        ]);
    }

    public function markAsProcessing(): bool
    {
        return $this->update(['status' => self::STATUS_PROCESSING]);
    }

    public function markAsFailed(): bool
    {
        return $this->update(['status' => self::STATUS_FAILED]);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::saved(function ($payout) {
            if ($payout->wasRecentlyCreated || $payout->wasChanged('status')) {
                NotificationService::vendorPayoutStatusChanged($payout);
            }
        });
    }
}

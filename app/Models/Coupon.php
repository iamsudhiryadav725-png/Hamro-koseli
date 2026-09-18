<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'vendor_id',
        'code',
        'discount_type',
        'discount_value',
        'min_order',
        'max_uses',
        'used_count',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'vendor_id' => 'integer',
        'discount_value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_uses' => 'integer',
        'used_count' => 'integer',
        'expires_at' => 'datetime',
    ];

    // ─── Enum constants ───────────────────────────────────────────────────────

    const DISCOUNT_TYPE_PERCENTAGE = 'percentage';

    const DISCOUNT_TYPE_FIXED_AMOUNT = 'fixed_amount';

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_EXPIRED = 'expired';

    // ─── Relationships ────────────────────────────────────────────────────────

    /**
     * The vendor this coupon belongs to (NULL = platform-wide).
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Orders that have used this coupon.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Check if the coupon is still usable.
     */
    public function isValid(): bool
    {
        if ($this->status !== self::STATUS_ACTIVE) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if (! is_null($this->max_uses) && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given order total.
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($orderTotal < $this->min_order) {
            return 0.0;
        }

        if ($this->discount_type === self::DISCOUNT_TYPE_PERCENTAGE) {
            return round($orderTotal * ($this->discount_value / 100), 2);
        }

        return min($this->discount_value, $orderTotal);
    }
}

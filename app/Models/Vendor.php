<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vendor_name',
        'owner_name',
        'email',
        'phone',
        'vendor_address',
        'city',
        'province',
        'pan_number',
        'rating',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'decimal:2',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Many-to-One: Each vendor belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * One-to-Many: A vendor can list many products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * One-to-Many: A vendor fulfills many order items.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * One-to-Many: A vendor receives many payouts.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    /**
     * One-to-Many: A vendor can have many coupons.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    /**
     * Polymorphic: A vendor can have many images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    /**
     * Get total sales/earnings from completed orders.
     */
    public function getTotalEarnings(): float
    {
        return (float) $this->orderItems()
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->whereHas('order.payment', function ($query) {
                $query->where('status', 'completed');
            })
            ->sum('subtotal');
    }

    /**
     * Get total commission owed (3% of total earnings).
     */
    public function getCommissionOwed(): float
    {
        return round($this->getTotalEarnings() * 0.03, 2);
    }

    /**
     * Get total commission paid to admin.
     */
    public function getCommissionPaid(): float
    {
        return (float) $this->payouts()
            ->where('status', 'completed')
            ->where('platform_fee', 0)
            ->sum('amount');
    }

    /**
     * Get outstanding commission balance.
     */
    public function getCommissionBalance(): float
    {
        return max(0.0, round($this->getCommissionOwed() - $this->getCommissionPaid(), 2));
    }
}

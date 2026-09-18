<?php

namespace App\Models;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'orders';

    /**
     * Mass assignable fields.
     */
    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'coupon_id',
        'total_amount',
        'discount',
        'payment_method',
        'transaction_id',
        'status',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Customer who placed the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Shipping address
     */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    /**
     * Coupon applied (nullable)
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Payment (one-to-one)
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Net amount after discount
     */
    public function getNetAmountAttribute(): float
    {
        return $this->total_amount - $this->discount;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Check if order can be cancelled
     */
    public function isCancellable(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Cancel order
     */
    public function cancel(): bool
    {
        if (! $this->isCancellable()) {
            return false;
        }

        return $this->update([
            'status' => 'cancelled',
        ]);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Filter by status
     */
    public function scopeOfStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Delivered orders
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'delivered');
    }

    /**
     * Orders for a user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::updated(function ($order) {
            if ($order->wasChanged('status')) {
                $order->orderItems()->update(['status' => $order->status]);
                NotificationService::userOrderStatusChanged($order);
            }
        });
    }
}

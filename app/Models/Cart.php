<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $table = 'cart';

    protected $fillable = [
        'user_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The user who owns this cart item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The product in this cart item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * The specific variant selected (nullable).
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Filter cart items for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get the effective unit price (variant price if set, otherwise product price).
     */
    public function unitPrice(): float
    {
        if ($this->variant && $this->variant->price !== null) {
            return (float) $this->variant->price;
        }

        return (float) ($this->product->discount_price ?? $this->product->price);
    }

    /**
     * Get the line subtotal (unit price × quantity).
     */
    public function subtotal(): float
    {
        return $this->unitPrice() * $this->quantity;
    }

    /**
     * Increment quantity by the given amount.
     */
    public function incrementQuantity(int $by = 1): bool
    {
        return $this->update(['quantity' => $this->quantity + $by]);
    }

    /**
     * Decrement quantity by the given amount (minimum 1).
     */
    public function decrementQuantity(int $by = 1): bool
    {
        $newQty = max(1, $this->quantity - $by);

        return $this->update(['quantity' => $newQty]);
    }
}

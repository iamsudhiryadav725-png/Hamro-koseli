<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'product_variants';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'product_id',
        'sku',
        'size',
        'color',
        'price',
        'discount_price',
        'stock',
        'status',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'stock' => 'integer',
    ];

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * A variant belongs to one product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * A variant can appear in many order items.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'variant_id');
    }

    /**
     * A variant can be in many cart items.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class, 'variant_id');
    }

    // -------------------------------------------------------------------------
    // Accessors / Helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve the effective price: use variant override if set, otherwise
     * fall back to the parent product's price.
     */
    public function getEffectivePriceAttribute(): string
    {
        return $this->price ?? $this->product->price;
    }

    /**
     * Check whether the variant is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope to only active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to only variants that have available stock.
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
}

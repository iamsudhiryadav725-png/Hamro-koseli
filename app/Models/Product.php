<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'category_id', 'name', 'slug',
        'product_type', 'description', 'specifications',
        'price', 'discount_price', 'stock', 'sku',
        'image', 'status', 'todays_deal_date',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'stock' => 'integer',
            'specifications' => 'array',
            'todays_deal_date' => 'date',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // -------------------------------------------------------------------------
    // Discount Methods (Single Implementation)
    // -------------------------------------------------------------------------

    /**
     * Get the resolved discount price for this product
     * Returns null if no valid discount exists
     */
    public function resolvedDiscountPrice()
    {
        // Check if product has a valid discount
        if ($this->discount_price !== null && $this->discount_price > 0 && $this->discount_price < $this->price) {
            return $this->discount_price;
        }

        // Check if any variant has a discount
        if ($this->variants->isNotEmpty()) {
            $discountedVariant = $this->variants
                ->filter(function ($variant) {
                    return $variant->discount_price !== null
                        && $variant->discount_price > 0
                        && $variant->discount_price < $variant->price;
                })
                ->sortBy('discount_price')
                ->first();

            if ($discountedVariant) {
                return $discountedVariant->discount_price;
            }
        }

        // No valid discount found
        return null;
    }

    /**
     * Get the effective price (discount if available, otherwise regular price)
     */
    public function effectivePrice()
    {
        $discount = $this->resolvedDiscountPrice();

        return $discount ?? $this->price;
    }

    public function getEffectivePrice()
    {
        return $this->effectivePrice();
    }

    /**
     * Check if product has an active discount
     */
    public function hasDiscount(): bool
    {
        $discount = $this->resolvedDiscountPrice();

        return $discount !== null && $discount < $this->price;
    }

    /**
     * Get the original price (for display purposes)
     */
    public function originalPrice(): float
    {
        return (float) $this->price;
    }

    /**
     * Get the discount percentage (if applicable)
     */
    public function getDiscountPercentage(): ?float
    {
        $discount = $this->resolvedDiscountPrice();
        if ($discount !== null && $this->price > 0) {
            return round((($this->price - $discount) / $this->price) * 100, 0);
        }

        return null;
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function primaryImageUrl(): string
    {
        $image = $this->images->firstWhere('is_primary', true)
            ?? $this->images->first();

        return $image
            ? asset('storage/'.$image->path)
            : asset('images/placeholder.png');
    }

    public function getImageAttribute(?string $value): ?string
    {
        if (filled($value)) {
            return $value;
        }

        return $this->images->firstWhere('is_primary', true)?->path
            ?? $this->images->first()?->path;
    }

    public function getPrimaryImagePathAttribute(): ?string
    {
        return $this->images->firstWhere('is_primary', true)?->path
            ?? $this->images->first()?->path;
    }
}

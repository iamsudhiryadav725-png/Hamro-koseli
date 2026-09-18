<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'verified_purchase',
        'reply',
        'replied_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'replied_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * The user who wrote this review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The product being reviewed.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Filter reviews for a specific product.
     */
    public function scopeForProduct($query, int $productId)
    {
        return $query->where('product_id', $productId);
    }

    /**
     * Filter reviews by a specific star rating.
     */
    public function scopeWithRating($query, int $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Filter to only verified purchase reviews.
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', 1);
    }

    /**
     * Order reviews from most recent to oldest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Calculate the average rating for a given product.
     */
    public static function averageRatingFor(int $productId): float
    {
        return round(
            static::where('product_id', $productId)->avg('rating') ?? 0,
            2
        );
    }

    /**
     * Get the rating breakdown (count per star) for a given product.
     * Returns an array like [1 => 2, 2 => 0, 3 => 5, 4 => 10, 5 => 20]
     */
    public static function ratingBreakdownFor(int $productId): array
    {
        $counts = static::where('product_id', $productId)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        // Ensure all stars 1–5 are present even if count is 0
        return array_replace(array_fill(1, 5, 0), $counts);
    }

    /**
     * Check if a user has already reviewed a product.
     */
    public static function hasReviewed(int $userId, int $productId): bool
    {
        return static::where('user_id', $userId)
            ->where('product_id', $productId)
            ->exists();
    }
}

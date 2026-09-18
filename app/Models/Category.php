<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    protected static function booted()
    {
        static::creating(function ($category) {
            // If a parent ID is provided but does not correspond to an existing category,
            // treat the category as a root by null‑ing the parent reference.
            if (! is_null($category->parent_cat_id)) {
                $exists = static::where('id', $category->parent_cat_id)->exists();
                if (! $exists) {
                    $category->parent_cat_id = null;
                }
            }
        });
    }

    use HasFactory;

    protected $fillable = [
        'cat_name',
        'image',
        'slug',
        'parent_cat_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'parent_cat_id' => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Attribute Mutators
    // -------------------------------------------------------------------------

    /**
     * Validate parent_cat_id before setting. If the parent doesn't exist, set to null.
     */
    public function setParentCatIdAttribute($value): void
    {
        if (! is_null($value)) {
            $exists = static::where('id', $value)->exists();
            $this->attributes['parent_cat_id'] = $exists ? $value : null;
        } else {
            $this->attributes['parent_cat_id'] = null;
        }
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Self-referencing: belongs to a parent category (NULL = root category).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_cat_id');
    }

    /**
     * Self-referencing: a category can have many child categories.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_cat_id');
    }

    /**
     * One-to-Many: A category can have many products.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Polymorphic: A category can have many images.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // -------------------------------------------------------------------------
    // Helper Methods
    // -------------------------------------------------------------------------

    public function isRoot(): bool
    {
        return is_null($this->parent_cat_id);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

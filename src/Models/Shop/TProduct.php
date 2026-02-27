<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TProduct extends Model
{
    protected $table = 't_products';

    protected $fillable = [
        'catalog_id',
        'name',
        'slug',
        'title',
        'description',
        'keywords',
        'price',
        'old_price',
        'sku',
        'tags',
        'is_new',
        'is_hot',
        'is_recommended',
        'content',
        'gallery',
    ];

    protected $casts = [
        'catalog_id' => 'integer',
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'is_new' => 'boolean',
        'is_hot' => 'boolean',
        'is_recommended' => 'boolean',
        'tags' => 'array',
        'gallery' => 'array',
    ];

    /**
     * Get the category that owns the product
     */
    public function catalog(): BelongsTo
    {
        return $this->belongsTo(TCatalog::class, 'catalog_id');
    }

    /**
     * Get product variants
     */
    public function variants(): HasMany
    {
        return $this->hasMany(TProductVariant::class, 'product_id');
    }

    /**
     * Check if product has variants
     */
    public function hasVariants(): bool
    {
        return $this->variants()->exists();
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->old_price || $this->old_price <= $this->price) {
            return null;
        }

        return round((($this->old_price - $this->price) / $this->old_price) * 100);
    }

    /**
     * Generate unique slug
     */
    public static function generateSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $count = 1;
        $originalSlug = $slug;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}

<?php

namespace App\Models;

use App\Livewire\Products\Catalog as CatalogComponent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category_id',
        'brand_id',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'discount',
        'rating',
        'reviews',
        'images',
        'meta_title',
        'meta_description',
        'release_date',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'images' => 'array',
        'release_date' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        $clearCatalogCache = static function (): void {
            Cache::forget(CatalogComponent::CACHE_KEY);
        };

        static::created($clearCatalogCache);
        static::updated($clearCatalogCache);
        static::deleted($clearCatalogCache);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

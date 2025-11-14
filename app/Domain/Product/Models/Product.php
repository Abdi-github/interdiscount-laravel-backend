<?php

namespace App\Domain\Product\Models;

use App\Domain\Product\Enums\AvailabilityState;
use App\Domain\Product\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Product extends Model
{
    use HasFactory, Searchable;

    protected static function newFactory(): \Database\Factories\ProductFactory
    {
        return \Database\Factories\ProductFactory::new();
    }

    protected $fillable = [
        'name',
        'name_short',
        'slug',
        'code',
        'displayed_code',
        'brand_id',
        'category_id',
        'price',
        'original_price',
        'currency',
        'images',
        'rating',
        'review_count',
        'specification',
        'availability_state',
        'delivery_days',
        'in_store_possible',
        'release_date',
        'services',
        'promo_labels',
        'is_speed_product',
        'is_orderable',
        'is_sustainable',
        'is_active',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'images' => 'array',
            'rating' => 'decimal:2',
            'review_count' => 'integer',
            'availability_state' => AvailabilityState::class,
            'delivery_days' => 'integer',
            'in_store_possible' => 'boolean',
            'release_date' => 'date',
            'services' => 'array',
            'promo_labels' => 'array',
            'is_speed_product' => 'boolean',
            'is_orderable' => 'boolean',
            'is_sustainable' => 'boolean',
            'is_active' => 'boolean',
            'status' => ProductStatus::class,
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Brand\Models\Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Category\Models\Category::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Domain\Review\Models\Review::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Domain\Order\Models\OrderItem::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(\App\Domain\Inventory\Models\StoreInventory::class);
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_short' => $this->name_short,
            'specification' => $this->specification,
            'price' => (float) $this->price,
            'brand' => $this->brand?->name,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'availability_state' => $this->availability_state?->value,
            'status' => $this->status?->value,
            'rating' => (float) $this->rating,
            'is_active' => $this->is_active,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', ProductStatus::PUBLISHED);
    }
}

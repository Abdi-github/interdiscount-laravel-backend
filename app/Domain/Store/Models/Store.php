<?php

namespace App\Domain\Store\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\StoreFactory
    {
        return \Database\Factories\StoreFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'store_id',
        'street',
        'street_number',
        'postal_code',
        'city_id',
        'canton_id',
        'remarks',
        'phone',
        'email',
        'latitude',
        'longitude',
        'format',
        'is_xxl',
        'opening_hours',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_xxl' => 'boolean',
            'opening_hours' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Location\Models\City::class);
    }

    public function canton(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Location\Models\Canton::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(\App\Domain\Admin\Models\Admin::class);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(\App\Domain\Inventory\Models\StoreInventory::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(\App\Domain\Promotion\Models\StorePromotion::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

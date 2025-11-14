<?php

namespace App\Domain\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreInventory extends Model
{
    protected $table = 'store_inventories';

    protected $fillable = [
        'store_id',
        'product_id',
        'quantity',
        'reserved',
        'min_stock',
        'max_stock',
        'last_restock_at',
        'last_sold_at',
        'location_in_store',
        'is_display_unit',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'reserved' => 'integer',
            'min_stock' => 'integer',
            'max_stock' => 'integer',
            'last_restock_at' => 'datetime',
            'last_sold_at' => 'datetime',
            'is_display_unit' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Store\Models\Store::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Product\Models\Product::class);
    }

    public function availableQuantity(): int
    {
        return max(0, $this->quantity - $this->reserved);
    }

    public function isLowStock(): bool
    {
        return $this->min_stock > 0 && $this->quantity <= $this->min_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->availableQuantity() <= 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity', '<=', 'min_stock')
            ->where('min_stock', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->whereRaw('quantity - reserved <= 0');
    }
}

<?php

namespace App\Domain\Promotion\Models;

use App\Domain\Shared\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorePromotion extends Model
{
    protected $fillable = [
        'store_id',
        'product_id',
        'category_id',
        'title',
        'description',
        'discount_type',
        'discount_value',
        'buy_quantity',
        'get_quantity',
        'valid_from',
        'valid_until',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:2',
            'buy_quantity' => 'integer',
            'get_quantity' => 'integer',
            'valid_from' => 'datetime',
            'valid_until' => 'datetime',
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Category\Models\Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Admin\Models\Admin::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

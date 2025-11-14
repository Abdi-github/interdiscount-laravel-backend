<?php

namespace App\Domain\Brand\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\BrandFactory
    {
        return \Database\Factories\BrandFactory::new();
    }

    protected $fillable = [
        'name',
        'slug',
        'product_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'product_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(\App\Domain\Product\Models\Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

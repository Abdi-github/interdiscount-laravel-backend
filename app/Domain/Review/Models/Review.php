<?php

namespace App\Domain\Review\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\ReviewFactory
    {
        return \Database\Factories\ReviewFactory::new();
    }

    protected $fillable = [
        'product_id',
        'user_id',
        'rating',
        'title',
        'comment',
        'language',
        'is_verified_purchase',
        'is_approved',
        'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_verified_purchase' => 'boolean',
            'is_approved' => 'boolean',
            'helpful_count' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Product\Models\Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\User::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}

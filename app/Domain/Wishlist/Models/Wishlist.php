<?php

namespace App\Domain\Wishlist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Product\Models\Product::class);
    }
}

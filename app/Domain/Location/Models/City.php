<?php

namespace App\Domain\Location\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name'];

    protected $fillable = [
        'name',
        'slug',
        'canton_id',
        'postal_codes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'postal_codes' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function canton(): BelongsTo
    {
        return $this->belongsTo(Canton::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

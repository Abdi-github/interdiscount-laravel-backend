<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'first_name',
        'last_name',
        'street',
        'street_number',
        'postal_code',
        'city',
        'canton_code',
        'country',
        'phone',
        'is_default',
        'is_billing',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_billing' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

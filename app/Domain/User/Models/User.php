<?php

namespace App\Domain\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function newFactory(): \Database\Factories\UserFactory
    {
        return \Database\Factories\UserFactory::new();
    }

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'preferred_language',
        'avatar_url',
        'is_active',
        'is_verified',
        'verified_at',
        'last_login_at',
        'verification_token',
        'verification_token_expires',
        'reset_password_token',
        'reset_password_expires',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
        'reset_password_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'verification_token_expires' => 'datetime',
            'reset_password_expires' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Domain\Order\Models\Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Domain\Review\Models\Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(\App\Domain\Wishlist\Models\Wishlist::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(\App\Domain\Notification\Models\Notification::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Domain\Payment\Models\Payment::class);
    }

    public function defaultAddress()
    {
        return $this->addresses()->where('is_default', true)->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}

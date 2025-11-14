<?php

namespace App\Domain\Admin\Models;

use App\Domain\Admin\Enums\AdminType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasFactory, TwoFactorAuthenticatable, HasApiTokens;

    protected static function newFactory(): \Database\Factories\AdminFactory
    {
        return \Database\Factories\AdminFactory::new();
    }

    protected $fillable = [
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'admin_type',
        'store_id',
        'avatar_url',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'admin_type' => AdminType::class,
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Store\Models\Store::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domain\RBAC\Models\Role::class,
            'admin_roles',
            'admin_id',
            'role_id'
        )->withPivot(['assigned_by', 'assigned_at', 'is_active'])
         ->withTimestamps();
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermissionTo(string $permission): bool
    {
        // Check wildcard
        if ($this->roles()->whereHas('permissions', fn ($q) => $q->where('name', '*:*'))->exists()) {
            return true;
        }

        // Direct match
        if ($this->roles()->whereHas('permissions', fn ($q) => $q->where('name', $permission))->exists()) {
            return true;
        }

        // Resource wildcard: 'products:*'
        $parts = explode(':', $permission);
        if (count($parts) === 2) {
            $resourceWildcard = $parts[0] . ':*';
            if ($this->roles()->whereHas('permissions', fn ($q) => $q->where('name', $resourceWildcard))->exists()) {
                return true;
            }
        }

        return false;
    }

    public function getAllPermissions()
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->unique('id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->admin_type === AdminType::SUPER_ADMIN;
    }

    public function isPlatformAdmin(): bool
    {
        return $this->admin_type === AdminType::PLATFORM_ADMIN;
    }

    public function isStoreManager(): bool
    {
        return $this->admin_type === AdminType::STORE_MANAGER;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

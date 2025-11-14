<?php

namespace App\Domain\RBAC\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Role extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['display_name', 'description'];

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permissions',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domain\Admin\Models\Admin::class,
            'admin_roles',
            'role_id',
            'admin_id'
        )->withPivot(['assigned_by', 'assigned_at', 'is_active'])
         ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

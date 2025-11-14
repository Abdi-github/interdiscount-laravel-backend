<?php

namespace App\Domain\RBAC\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminRole extends Model
{
    protected $fillable = [
        'admin_id',
        'role_id',
        'assigned_by',
        'assigned_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Admin\Models\Admin::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Admin\Models\Admin::class, 'assigned_by');
    }
}

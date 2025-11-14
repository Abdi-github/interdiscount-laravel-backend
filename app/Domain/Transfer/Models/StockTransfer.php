<?php

namespace App\Domain\Transfer\Models;

use App\Domain\Transfer\Enums\TransferStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    protected $fillable = [
        'transfer_number',
        'from_store_id',
        'to_store_id',
        'initiated_by',
        'status',
        'items',
        'notes',
        'approved_by',
        'shipped_at',
        'received_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => TransferStatus::class,
            'items' => 'array',
            'shipped_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Store\Models\Store::class, 'from_store_id');
    }

    public function toStore(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Store\Models\Store::class, 'to_store_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Admin\Models\Admin::class, 'initiated_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Admin\Models\Admin::class, 'approved_by');
    }
}

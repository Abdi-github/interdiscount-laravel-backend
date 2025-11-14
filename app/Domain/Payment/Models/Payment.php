<?php

namespace App\Domain\Payment\Models;

use App\Domain\Payment\Enums\PaymentMethod;
use App\Domain\Payment\Enums\PaymentTransactionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'stripe_payment_intent_id',
        'stripe_client_secret',
        'failure_reason',
        'paid_at',
        'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'status' => PaymentTransactionStatus::class,
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Order\Models\Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\User::class);
    }
}

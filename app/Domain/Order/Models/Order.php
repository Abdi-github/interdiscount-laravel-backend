<?php

namespace App\Domain\Order\Models;

use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected static function newFactory(): \Database\Factories\OrderFactory
    {
        return \Database\Factories\OrderFactory::new();
    }

    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_address_id',
        'billing_address_id',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'shipping_fee',
        'discount',
        'total',
        'currency',
        'coupon_code',
        'notes',
        'store_pickup_id',
        'is_store_pickup',
        'estimated_delivery',
        'delivered_at',
        'cancelled_at',
        'cancellation_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'payment_status' => PaymentStatus::class,
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'is_store_pickup' => 'boolean',
            'estimated_delivery' => 'date',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\User::class);
    }

    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\Address::class, 'shipping_address_id');
    }

    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\User\Models\Address::class, 'billing_address_id');
    }

    public function storePickup(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Store\Models\Store::class, 'store_pickup_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(\App\Domain\Payment\Models\Payment::class);
    }

    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return $this->status->canTransitionTo($newStatus);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}

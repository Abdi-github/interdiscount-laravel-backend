<?php

namespace Database\Factories;

use App\Domain\Order\Enums\OrderStatus;
use App\Domain\Order\Enums\PaymentStatus;
use App\Domain\Order\Models\Order;
use App\Domain\Payment\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Order\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 29.90, 4999.90);
        $shippingFee = $subtotal >= 49.90 ? 0 : 9.90;
        $discount = 0;

        return [
            'order_number' => 'ORD-' . fake()->unique()->numerify('########'),
            'status' => OrderStatus::PENDING,
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'payment_status' => PaymentStatus::PENDING,
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount' => $discount,
            'total' => $subtotal + $shippingFee - $discount,
            'currency' => 'CHF',
            'is_store_pickup' => false,
        ];
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::CONFIRMED,
            'payment_status' => PaymentStatus::PAID,
        ]);
    }

    public function shipped(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::SHIPPED,
            'payment_status' => PaymentStatus::PAID,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => 'Customer requested cancellation',
        ]);
    }
}

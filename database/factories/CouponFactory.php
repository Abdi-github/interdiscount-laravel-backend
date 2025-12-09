<?php

namespace Database\Factories;

use App\Domain\Coupon\Models\Coupon;
use App\Domain\Shared\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Coupon\Models\Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'description' => [
                'de' => fake()->sentence(),
                'en' => fake()->sentence(),
                'fr' => fake()->sentence(),
                'it' => fake()->sentence(),
            ],
            'discount_type' => fake()->randomElement(DiscountType::cases()),
            'discount_value' => fake()->randomFloat(2, 5, 50),
            'minimum_order' => fake()->optional(0.5)->randomFloat(2, 20, 200),
            'max_uses' => fake()->numberBetween(10, 1000),
            'used_count' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addMonths(3),
            'is_active' => true,
        ];
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'valid_from' => now()->subMonths(3),
            'valid_until' => now()->subDay(),
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Domain\Product\Models\Product;
use App\Domain\Product\Enums\AvailabilityState;
use App\Domain\Product\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Product\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => $name,
            'name_short' => Str::limit($name, 30, ''),
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'code' => fake()->unique()->numerify('PRD-######'),
            'displayed_code' => fake()->numerify('######'),
            'price' => fake()->randomFloat(2, 9.90, 2999.90),
            'original_price' => fake()->optional(0.3)->randomFloat(2, 19.90, 3499.90),
            'currency' => 'CHF',
            'images' => [],
            'rating' => fake()->randomFloat(2, 1.0, 5.0),
            'review_count' => fake()->numberBetween(0, 200),
            'specification' => fake()->paragraph(),
            'availability_state' => fake()->randomElement(AvailabilityState::cases()),
            'delivery_days' => fake()->numberBetween(1, 14),
            'in_store_possible' => fake()->boolean(70),
            'services' => [],
            'promo_labels' => [],
            'is_speed_product' => fake()->boolean(20),
            'is_orderable' => true,
            'is_sustainable' => fake()->boolean(15),
            'is_active' => true,
            'status' => ProductStatus::PUBLISHED,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
            'status' => ProductStatus::INACTIVE,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => ProductStatus::DRAFT,
        ]);
    }
}

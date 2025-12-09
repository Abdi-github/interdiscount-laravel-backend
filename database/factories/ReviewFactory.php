<?php

namespace Database\Factories;

use App\Domain\Review\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Review\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'rating' => fake()->numberBetween(1, 5),
            'title' => fake()->sentence(4),
            'comment' => fake()->paragraph(),
            'language' => fake()->randomElement(['de', 'en', 'fr', 'it']),
            'is_verified_purchase' => fake()->boolean(60),
            'is_approved' => true,
            'helpful_count' => fake()->numberBetween(0, 50),
        ];
    }

    public function unapproved(): static
    {
        return $this->state(fn () => [
            'is_approved' => false,
        ]);
    }
}

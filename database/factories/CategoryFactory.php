<?php

namespace Database\Factories;

use App\Domain\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Category\Models\Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);

        return [
            'name' => [
                'de' => $name,
                'en' => $name,
                'fr' => $name,
                'it' => $name,
            ],
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'category_id' => 'cat-' . fake()->unique()->numerify('######'),
            'level' => 1,
            'sort_order' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }

    public function child(int $parentId, int $level = 2): static
    {
        return $this->state(fn () => [
            'parent_id' => $parentId,
            'level' => $level,
        ]);
    }
}

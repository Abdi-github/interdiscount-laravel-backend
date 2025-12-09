<?php

namespace Database\Factories;

use App\Domain\Store\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Domain\Store\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition(): array
    {
        $name = 'Interdiscount ' . fake()->city();

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('##'),
            'store_id' => 'STR-' . fake()->unique()->numerify('####'),
            'street' => fake()->streetName(),
            'street_number' => fake()->buildingNumber(),
            'postal_code' => fake()->numerify('####'),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'latitude' => fake()->latitude(45.8, 47.8),
            'longitude' => fake()->longitude(5.9, 10.5),
            'format' => fake()->randomElement(['standard', 'compact', 'xxl']),
            'is_xxl' => false,
            'opening_hours' => [
                ['day' => ['de' => 'Montag', 'en' => 'Monday', 'fr' => 'Lundi', 'it' => 'Lunedì'], 'open' => '09:00', 'close' => '18:30', 'is_closed' => false],
                ['day' => ['de' => 'Dienstag', 'en' => 'Tuesday', 'fr' => 'Mardi', 'it' => 'Martedì'], 'open' => '09:00', 'close' => '18:30', 'is_closed' => false],
                ['day' => ['de' => 'Mittwoch', 'en' => 'Wednesday', 'fr' => 'Mercredi', 'it' => 'Mercoledì'], 'open' => '09:00', 'close' => '18:30', 'is_closed' => false],
                ['day' => ['de' => 'Donnerstag', 'en' => 'Thursday', 'fr' => 'Jeudi', 'it' => 'Giovedì'], 'open' => '09:00', 'close' => '21:00', 'is_closed' => false],
                ['day' => ['de' => 'Freitag', 'en' => 'Friday', 'fr' => 'Vendredi', 'it' => 'Venerdì'], 'open' => '09:00', 'close' => '21:00', 'is_closed' => false],
                ['day' => ['de' => 'Samstag', 'en' => 'Saturday', 'fr' => 'Samedi', 'it' => 'Sabato'], 'open' => '09:00', 'close' => '17:00', 'is_closed' => false],
                ['day' => ['de' => 'Sonntag', 'en' => 'Sunday', 'fr' => 'Dimanche', 'it' => 'Domenica'], 'open' => null, 'close' => null, 'is_closed' => true],
            ],
            'is_active' => true,
        ];
    }
}

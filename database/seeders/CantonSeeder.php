<?php

namespace Database\Seeders;

use App\Domain\Location\Models\Canton;
use Illuminate\Database\Seeder;

class CantonSeeder extends Seeder
{
    public function run(): void
    {
        $cantons = json_decode(file_get_contents(base_path('data/cantons.json')), true);

        foreach ($cantons as $canton) {
            Canton::create([
                'name' => $canton['name'],
                'code' => $canton['code'],
                'slug' => $canton['slug'],
                'is_active' => $canton['is_active'] ?? true,
            ]);
        }
    }
}

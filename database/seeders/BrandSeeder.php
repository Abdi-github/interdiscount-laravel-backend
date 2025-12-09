<?php

namespace Database\Seeders;

use App\Domain\Brand\Models\Brand;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = json_decode(file_get_contents(base_path('data/brands.json')), true);

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => $brand['slug'],
                'product_count' => $brand['product_count'] ?? 0,
                'is_active' => $brand['is_active'] ?? true,
            ]);
        }
    }
}

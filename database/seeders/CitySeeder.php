<?php

namespace Database\Seeders;

use App\Domain\Location\Models\Canton;
use App\Domain\Location\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = json_decode(file_get_contents(base_path('data/cities.json')), true);

        // Build canton ID map: mongo_id -> mysql_id
        $cantonMap = Canton::all()->pluck('id', 'code')->toArray();
        $cantonMongoMap = [];
        $cantonsRaw = json_decode(file_get_contents(base_path('data/cantons.json')), true);
        foreach ($cantonsRaw as $canton) {
            $cantonMongoMap[$canton['_id']] = $cantonMap[$canton['code']] ?? null;
        }

        foreach ($cities as $city) {
            City::create([
                'name' => $city['name'],
                'slug' => $city['slug'],
                'canton_id' => $cantonMongoMap[$city['canton_id']] ?? null,
                'postal_codes' => $city['postal_codes'],
                'is_active' => $city['is_active'] ?? true,
            ]);
        }
    }
}

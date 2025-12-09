<?php

namespace Database\Seeders;

use App\Domain\Location\Models\Canton;
use App\Domain\Location\Models\City;
use App\Domain\Store\Models\Store;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $stores = json_decode(file_get_contents(base_path('data/stores.json')), true);
        $cantonsRaw = json_decode(file_get_contents(base_path('data/cantons.json')), true);
        $citiesRaw = json_decode(file_get_contents(base_path('data/cities.json')), true);

        // Build canton mongo_id -> mysql_id map
        $cantonsByCode = Canton::all()->pluck('id', 'code')->toArray();
        $cantonMap = [];
        foreach ($cantonsRaw as $canton) {
            $cantonMap[$canton['_id']] = $cantonsByCode[$canton['code']] ?? null;
        }

        // Build city mongo_id -> mysql_id map
        $citiesBySlug = City::all()->pluck('id', 'slug')->toArray();
        $cityMap = [];
        foreach ($citiesRaw as $city) {
            $cityMap[$city['_id']] = $citiesBySlug[$city['slug']] ?? null;
        }

        foreach ($stores as $store) {
            Store::create([
                'name' => $store['name'],
                'slug' => $store['slug'],
                'store_id' => $store['store_id'],
                'street' => $store['street'] ?? null,
                'street_number' => $store['street_number'] ?? null,
                'postal_code' => $store['postal_code'] ?? null,
                'city_id' => $cityMap[$store['city_id']] ?? null,
                'canton_id' => $cantonMap[$store['canton_id']] ?? null,
                'remarks' => $store['remarks'] ?? null,
                'phone' => $store['phone'] ?? null,
                'email' => $store['email'] ?? null,
                'latitude' => $store['latitude'] ?? null,
                'longitude' => $store['longitude'] ?? null,
                'format' => $store['format'] ?? null,
                'is_xxl' => $store['is_xxl'] ?? false,
                'opening_hours' => $store['opening_hours'] ?? null,
                'is_active' => $store['is_active'] ?? true,
            ]);
        }
    }
}

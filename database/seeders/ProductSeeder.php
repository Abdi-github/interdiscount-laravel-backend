<?php

namespace Database\Seeders;

use App\Domain\Brand\Models\Brand;
use App\Domain\Category\Models\Category;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = json_decode(file_get_contents(base_path('data/products.json')), true);
        $brandsRaw = json_decode(file_get_contents(base_path('data/brands.json')), true);
        $categoriesRaw = json_decode(file_get_contents(base_path('data/categories.json')), true);

        // Build brand mongo_id -> mysql_id map
        $brandsBySlug = Brand::all()->pluck('id', 'slug')->toArray();
        $brandMap = [];
        foreach ($brandsRaw as $brand) {
            $brandMap[$brand['_id']] = $brandsBySlug[$brand['slug']] ?? null;
        }

        // Build category mongo_id -> mysql_id map
        $catsByCatId = Category::all()->pluck('id', 'category_id')->toArray();
        $categoryMap = [];
        foreach ($categoriesRaw as $cat) {
            $categoryMap[$cat['_id']] = $catsByCatId[$cat['category_id']] ?? null;
        }

        foreach ($products as $product) {
            Product::create([
                'name' => $product['name'],
                'name_short' => $product['name_short'] ?? null,
                'slug' => $product['slug'],
                'code' => $product['code'],
                'displayed_code' => $product['displayed_code'] ?? null,
                'brand_id' => $brandMap[$product['brand_id']] ?? null,
                'category_id' => $categoryMap[$product['category_id']] ?? null,
                'price' => $product['price'] ?? 0,
                'original_price' => $product['original_price'] ?? null,
                'currency' => $product['currency'] ?? 'CHF',
                'images' => $product['images'] ?? null,
                'rating' => $product['rating'] ?? null,
                'review_count' => $product['review_count'] ?? 0,
                'specification' => $product['specification'] ?? null,
                'availability_state' => $product['availability_state'] ?? 'INSTOCK',
                'delivery_days' => $product['delivery_days'] ?? null,
                'in_store_possible' => $product['in_store_possible'] ?? false,
                'release_date' => $product['release_date'] ?? null,
                'services' => $product['services'] ?? null,
                'promo_labels' => $product['promo_labels'] ?? null,
                'is_speed_product' => $product['is_speed_product'] ?? false,
                'is_orderable' => $product['is_orderable'] ?? true,
                'is_sustainable' => $product['is_sustainable'] ?? false,
                'is_active' => $product['is_active'] ?? true,
                'status' => $product['status'] ?? 'PUBLISHED',
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Domain\Product\Models\Product;
use App\Domain\Review\Models\Review;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $reviews = json_decode(file_get_contents(base_path('data/reviews.json')), true);
        $usersRaw = json_decode(file_get_contents(base_path('data/users.json')), true);
        $productsRaw = json_decode(file_get_contents(base_path('data/products.json')), true);

        // Build user mongo_id -> mysql_id (customers only)
        $customersByEmail = User::all()->pluck('id', 'email')->toArray();
        $userMap = [];
        foreach ($usersRaw as $user) {
            if ($user['user_type'] === 'customer') {
                $userMap[$user['_id']] = $customersByEmail[$user['email']] ?? null;
            }
        }

        // Build product mongo_id -> mysql_id map
        $productsByCode = Product::all()->pluck('id', 'code')->toArray();
        $productMap = [];
        foreach ($productsRaw as $product) {
            $productMap[$product['_id']] = $productsByCode[$product['code']] ?? null;
        }

        foreach ($reviews as $review) {
            $userId = $userMap[$review['user_id']] ?? null;
            $productId = $productMap[$review['product_id']] ?? null;

            if (!$userId || !$productId) {
                continue;
            }

            Review::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'rating' => $review['rating'],
                'title' => $review['title'] ?? null,
                'comment' => $review['comment'] ?? null,
                'language' => $review['language'] ?? 'de',
                'is_verified_purchase' => $review['is_verified_purchase'] ?? false,
                'is_approved' => $review['is_approved'] ?? false,
                'helpful_count' => $review['helpful_count'] ?? 0,
                'created_at' => isset($review['created_at']) ? Carbon::parse($review['created_at']) : now(),
                'updated_at' => isset($review['updated_at']) ? Carbon::parse($review['updated_at']) : now(),
            ]);
        }
    }
}

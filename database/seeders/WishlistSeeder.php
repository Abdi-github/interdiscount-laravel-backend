<?php

namespace Database\Seeders;

use App\Domain\Product\Models\Product;
use App\Domain\User\Models\User;
use App\Domain\Wishlist\Models\Wishlist;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class WishlistSeeder extends Seeder
{
    public function run(): void
    {
        $wishlists = json_decode(file_get_contents(base_path('data/wishlists.json')), true);
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

        foreach ($wishlists as $wishlist) {
            $userId = $userMap[$wishlist['user_id']] ?? null;
            $productId = $productMap[$wishlist['product_id']] ?? null;

            if (!$userId || !$productId) {
                continue;
            }

            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => isset($wishlist['created_at']) ? Carbon::parse($wishlist['created_at']) : now(),
            ]);
        }
    }
}

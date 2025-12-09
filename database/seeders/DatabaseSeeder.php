<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            // 1. Locations (no dependencies)
            CantonSeeder::class,
            CitySeeder::class,

            // 2. RBAC (no dependencies)
            RoleSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,

            // 3. Stores (depends on cantons, cities)
            StoreSeeder::class,

            // 4. Admins (depends on stores for store_id)
            AdminSeeder::class,
            AdminRoleSeeder::class,

            // 5. Customers
            UserSeeder::class,

            // 6. Catalog (no user dependencies)
            BrandSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,

            // 7. Customer data (depends on users, products)
            AddressSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            ReviewSeeder::class,
            WishlistSeeder::class,
            CouponSeeder::class,
        ]);
    }
}

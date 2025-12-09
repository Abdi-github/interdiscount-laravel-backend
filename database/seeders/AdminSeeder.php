<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $users = json_decode(file_get_contents(base_path('data/users.json')), true);
        $storesRaw = json_decode(file_get_contents(base_path('data/stores.json')), true);

        // Build store mongo_id -> mysql_id map
        $storeSlugToId = \App\Domain\Store\Models\Store::all()->pluck('id', 'slug')->toArray();
        $storeMap = [];
        foreach ($storesRaw as $store) {
            $storeMap[$store['_id']] = $storeSlugToId[$store['slug']] ?? null;
        }

        // Filter non-customer users -> admins
        $admins = array_filter($users, fn ($u) => $u['user_type'] !== 'customer');

        foreach ($admins as $admin) {
            // Convert Node.js bcrypt $2b$ prefix to PHP-compatible $2y$
            $password = str_replace('$2b$', '$2y$', $admin['password_hash']);

            DB::table('admins')->insert([
                'email' => $admin['email'],
                'password' => $password,
                'first_name' => $admin['first_name'],
                'last_name' => $admin['last_name'],
                'phone' => $admin['phone'] ?? null,
                'admin_type' => $admin['user_type'],
                'store_id' => isset($admin['store_id']) && $admin['store_id'] ? ($storeMap[$admin['store_id']] ?? null) : null,
                'avatar_url' => $admin['avatar_url'] ?? null,
                'is_active' => $admin['is_active'] ?? true,
                'last_login_at' => isset($admin['last_login_at']) ? Carbon::parse($admin['last_login_at']) : null,
                'created_at' => isset($admin['created_at']) ? Carbon::parse($admin['created_at']) : now(),
                'updated_at' => isset($admin['updated_at']) ? Carbon::parse($admin['updated_at']) : now(),
            ]);
        }
    }
}

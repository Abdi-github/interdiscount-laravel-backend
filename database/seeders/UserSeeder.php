<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = json_decode(file_get_contents(base_path('data/users.json')), true);

        // Filter customers only -> users table
        $customers = array_filter($users, fn ($u) => $u['user_type'] === 'customer');

        foreach ($customers as $customer) {
            // Convert Node.js bcrypt $2b$ prefix to PHP-compatible $2y$
            $password = str_replace('$2b$', '$2y$', $customer['password_hash']);

            DB::table('users')->insert([
                'email' => $customer['email'],
                'password' => $password,
                'first_name' => $customer['first_name'],
                'last_name' => $customer['last_name'],
                'phone' => $customer['phone'] ?? null,
                'preferred_language' => $customer['preferred_language'] ?? 'de',
                'avatar_url' => $customer['avatar_url'] ?? null,
                'is_active' => $customer['is_active'] ?? true,
                'is_verified' => $customer['is_verified'] ?? false,
                'verified_at' => isset($customer['verified_at']) ? Carbon::parse($customer['verified_at']) : null,
                'last_login_at' => isset($customer['last_login_at']) ? Carbon::parse($customer['last_login_at']) : null,
                'created_at' => isset($customer['created_at']) ? Carbon::parse($customer['created_at']) : now(),
                'updated_at' => isset($customer['updated_at']) ? Carbon::parse($customer['updated_at']) : now(),
            ]);
        }
    }
}

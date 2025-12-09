<?php

namespace Database\Seeders;

use App\Domain\User\Models\User;
use App\Domain\User\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $addresses = json_decode(file_get_contents(base_path('data/addresses.json')), true);
        $usersRaw = json_decode(file_get_contents(base_path('data/users.json')), true);

        // Build user mongo_id -> mysql_id map (customers only)
        $customersByEmail = User::all()->pluck('id', 'email')->toArray();
        $userMap = [];
        foreach ($usersRaw as $user) {
            if ($user['user_type'] === 'customer') {
                $userMap[$user['_id']] = $customersByEmail[$user['email']] ?? null;
            }
        }

        foreach ($addresses as $address) {
            $userId = $userMap[$address['user_id']] ?? null;

            // Only seed addresses for customers (users table)
            if (!$userId) {
                continue;
            }

            Address::create([
                'user_id' => $userId,
                'label' => $address['label'] ?? 'Home',
                'first_name' => $address['first_name'],
                'last_name' => $address['last_name'],
                'street' => $address['street'],
                'street_number' => $address['street_number'] ?? null,
                'postal_code' => $address['postal_code'],
                'city' => $address['city'],
                'canton_code' => $address['canton_code'] ?? null,
                'country' => $address['country'] ?? 'CH',
                'phone' => $address['phone'] ?? null,
                'is_default' => $address['is_default'] ?? false,
                'is_billing' => $address['is_billing'] ?? false,
                'created_at' => isset($address['created_at']) ? Carbon::parse($address['created_at']) : now(),
                'updated_at' => isset($address['updated_at']) ? Carbon::parse($address['updated_at']) : now(),
            ]);
        }
    }
}

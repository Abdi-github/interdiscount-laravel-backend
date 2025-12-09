<?php

namespace Database\Seeders;

use App\Domain\Order\Models\Order;
use App\Domain\Store\Models\Store;
use App\Domain\User\Models\Address;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = json_decode(file_get_contents(base_path('data/orders.json')), true);
        $usersRaw = json_decode(file_get_contents(base_path('data/users.json')), true);
        $addressesRaw = json_decode(file_get_contents(base_path('data/addresses.json')), true);
        $storesRaw = json_decode(file_get_contents(base_path('data/stores.json')), true);

        // Build user mongo_id -> mysql_id (customers only)
        $customersByEmail = User::all()->pluck('id', 'email')->toArray();
        $userMap = [];
        foreach ($usersRaw as $user) {
            if ($user['user_type'] === 'customer') {
                $userMap[$user['_id']] = $customersByEmail[$user['email']] ?? null;
            }
        }

        // Build address mongo_id -> mysql_id map
        // Addresses are ordered same as in JSON, use sequential matching
        $allAddresses = Address::orderBy('id')->get();
        $addressMap = [];
        $addressIdx = 0;
        foreach ($addressesRaw as $addr) {
            // Only customer addresses were seeded
            if (isset($userMap[$addr['user_id']])) {
                if (isset($allAddresses[$addressIdx])) {
                    $addressMap[$addr['_id']] = $allAddresses[$addressIdx]->id;
                    $addressIdx++;
                }
            }
        }

        // Build store mongo_id -> mysql_id map
        $storesBySlug = Store::all()->pluck('id', 'slug')->toArray();
        $storeMap = [];
        foreach ($storesRaw as $store) {
            $storeMap[$store['_id']] = $storesBySlug[$store['slug']] ?? null;
        }

        foreach ($orders as $order) {
            $userId = $userMap[$order['user_id']] ?? null;
            if (!$userId) {
                continue;
            }

            Order::create([
                'order_number' => $order['order_number'],
                'user_id' => $userId,
                'shipping_address_id' => $addressMap[$order['shipping_address_id']] ?? null,
                'billing_address_id' => $addressMap[$order['billing_address_id']] ?? null,
                'status' => $order['status'],
                'payment_method' => $order['payment_method'],
                'payment_status' => $order['payment_status'],
                'subtotal' => $order['subtotal'] ?? 0,
                'shipping_fee' => $order['shipping_fee'] ?? 0,
                'discount' => $order['discount'] ?? 0,
                'total' => $order['total'] ?? 0,
                'currency' => $order['currency'] ?? 'CHF',
                'notes' => $order['notes'] ?? null,
                'store_pickup_id' => $order['store_pickup_id'] ? ($storeMap[$order['store_pickup_id']] ?? null) : null,
                'is_store_pickup' => $order['is_store_pickup'] ?? false,
                'estimated_delivery' => isset($order['estimated_delivery']) ? Carbon::parse($order['estimated_delivery']) : null,
                'delivered_at' => isset($order['delivered_at']) ? Carbon::parse($order['delivered_at']) : null,
                'cancelled_at' => isset($order['cancelled_at']) ? Carbon::parse($order['cancelled_at']) : null,
                'cancellation_reason' => $order['cancellation_reason'] ?? null,
                'created_at' => isset($order['created_at']) ? Carbon::parse($order['created_at']) : now(),
                'updated_at' => isset($order['updated_at']) ? Carbon::parse($order['updated_at']) : now(),
            ]);
        }
    }
}

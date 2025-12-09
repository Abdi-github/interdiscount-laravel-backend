<?php

namespace Database\Seeders;

use App\Domain\Order\Models\Order;
use App\Domain\Order\Models\OrderItem;
use App\Domain\Product\Models\Product;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = json_decode(file_get_contents(base_path('data/order_items.json')), true);
        $ordersRaw = json_decode(file_get_contents(base_path('data/orders.json')), true);
        $productsRaw = json_decode(file_get_contents(base_path('data/products.json')), true);

        // Build order mongo_id -> mysql_id map
        $ordersByNumber = Order::all()->pluck('id', 'order_number')->toArray();
        $orderMap = [];
        foreach ($ordersRaw as $order) {
            $orderMap[$order['_id']] = $ordersByNumber[$order['order_number']] ?? null;
        }

        // Build product mongo_id -> mysql_id map
        $productsByCode = Product::all()->pluck('id', 'code')->toArray();
        $productMap = [];
        foreach ($productsRaw as $product) {
            $productMap[$product['_id']] = $productsByCode[$product['code']] ?? null;
        }

        foreach ($items as $item) {
            $orderId = $orderMap[$item['order_id']] ?? null;
            if (!$orderId) {
                continue;
            }

            OrderItem::create([
                'order_id' => $orderId,
                'product_id' => $productMap[$item['product_id']] ?? null,
                'product_name' => $item['product_name'],
                'product_code' => $item['product_code'],
                'quantity' => $item['quantity'] ?? 1,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'currency' => $item['currency'] ?? 'CHF',
            ]);
        }
    }
}

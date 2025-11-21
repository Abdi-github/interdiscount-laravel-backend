<?php

namespace App\Http\Controllers\Api\Store;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreAnalyticsController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $admin = request()->user();
        $storeId = $admin->store_id;

        $days = (int) $request->input('days', 30);
        $startDate = now()->subDays($days)->startOfDay();

        // Daily sales for the period
        $dailySales = DB::table('orders')
            ->where('store_pickup_id', $storeId)
            ->where('is_store_pickup', true)
            ->where('created_at', '>=', $startDate)
            ->whereNotIn('status', ['CANCELLED'])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top products by order items
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.store_pickup_id', $storeId)
            ->where('orders.is_store_pickup', true)
            ->where('orders.created_at', '>=', $startDate)
            ->selectRaw('order_items.product_name, order_items.product_id, SUM(order_items.quantity) as total_quantity, SUM(order_items.total_price) as total_revenue')
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // Inventory turnover (products with most sales vs current stock)
        $inventoryTurnover = DB::table('store_inventories')
            ->where('store_inventories.store_id', $storeId)
            ->where('store_inventories.is_active', true)
            ->join('products', 'products.id', '=', 'store_inventories.product_id')
            ->select(
                'products.id',
                'products.name',
                'store_inventories.quantity as current_stock',
                'store_inventories.min_stock',
            )
            ->orderBy('store_inventories.quantity')
            ->limit(20)
            ->get();

        return $this->success([
            'daily_sales' => $dailySales,
            'top_products' => $topProducts,
            'inventory_overview' => $inventoryTurnover,
            'period_days' => $days,
        ], 'Store analytics retrieved successfully');
    }
}

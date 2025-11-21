<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        /* Log::debug('AnalyticsController index - compiling analytics'); */
        $days = $request->integer('days', 30);
        $startDate = now()->subDays($days);

        $stats = [
            'revenue' => [
                // Revenue aggregation
                'total' => (float) DB::table('orders')
                    ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                    ->sum('total'),
                'period' => (float) DB::table('orders')
                    ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                    ->where('created_at', '>=', $startDate)
                    ->sum('total'),
            ],
            'orders' => [
                // Order statistics
                'total' => DB::table('orders')->count(),
                'period' => DB::table('orders')->where('created_at', '>=', $startDate)->count(),
                // TODO: Add predictive analytics for order patterns
                'average_value' => (float) DB::table('orders')
                    ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                    ->avg('total') ?? 0,
            ],
            'daily_revenue' => DB::table('orders')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as orders'),
                    DB::raw('SUM(total) as revenue'),
                )
                ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get(),
            'top_products' => DB::table('order_items')
                ->select(
                    'product_id',
                    'product_name',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('SUM(total_price) as total_revenue'),
                )
                ->groupBy('product_id', 'product_name')
                ->orderByDesc('total_quantity')
                ->limit(10)
                ->get(),
            'orders_by_status' => DB::table('orders')
                ->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            'payments_by_method' => DB::table('orders')
                ->select('payment_method', DB::raw('COUNT(*) as count'))
                ->whereNotNull('payment_method')
                ->groupBy('payment_method')
                ->get()
                ->pluck('count', 'payment_method'),
            'new_users' => DB::table('users')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as count'),
                )
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get(),
            'top_categories' => DB::table('order_items')
                ->join('products', 'order_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->select(
                    'categories.id',
                    'categories.name',
                    DB::raw('SUM(order_items.quantity) as total_quantity'),
                    DB::raw('SUM(order_items.total_price) as total_revenue'),
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_revenue')
                ->limit(10)
                ->get(),
        ];

        return $this->success($stats, 'Analytics retrieved successfully');
    }
}

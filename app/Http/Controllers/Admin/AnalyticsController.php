<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $days = $request->integer('days', 30);
        $startDate = now()->subDays($days);

        $confirmedStatuses = ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'];

        $stats = [
            'revenue' => [
                'total' => (float) DB::table('orders')
                    ->whereIn('status', $confirmedStatuses)
                    ->sum('total'),
                'period' => (float) DB::table('orders')
                    ->whereIn('status', $confirmedStatuses)
                    ->where('created_at', '>=', $startDate)
                    ->sum('total'),
            ],
            'orders' => [
                'total' => DB::table('orders')->count(),
                'period' => DB::table('orders')->where('created_at', '>=', $startDate)->count(),
                'average_value' => round((float) (DB::table('orders')
                    ->whereIn('status', $confirmedStatuses)
                    ->avg('total') ?? 0), 2),
            ],
            'daily_revenue' => DB::table('orders')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('COUNT(*) as orders'),
                    DB::raw('ROUND(SUM(total), 2) as revenue'),
                )
                ->whereIn('status', $confirmedStatuses)
                ->where('created_at', '>=', $startDate)
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get(),
            'top_products' => DB::table('order_items')
                ->select(
                    'product_id',
                    'product_name',
                    DB::raw('SUM(quantity) as total_quantity'),
                    DB::raw('ROUND(SUM(total_price), 2) as total_revenue'),
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
                    DB::raw('ROUND(SUM(order_items.total_price), 2) as total_revenue'),
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderByDesc('total_revenue')
                ->limit(10)
                ->get(),
        ];

        return Inertia::render('Analytics/Index', [
            'stats' => $stats,
            'days' => $days,
        ]);
    }
}

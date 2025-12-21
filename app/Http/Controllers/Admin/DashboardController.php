<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'totalOrders' => DB::table('orders')->count(),
            'totalProducts' => DB::table('products')->count(),
            'totalUsers' => DB::table('users')->count(),
            'totalStores' => DB::table('stores')->count(),
            'totalRevenue' => (float) DB::table('orders')
                ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                ->sum('total'),
            'pendingOrders' => DB::table('orders')->where('status', 'PENDING')->count(),
            'pendingReviews' => DB::table('reviews')->where('is_approved', false)->count(),
            'lowStockItems' => DB::table('store_inventories')
                ->where('is_active', true)
                ->whereColumn('quantity', '<=', 'min_stock')
                ->count(),
        ];

        $recentOrders = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->select(
                'orders.id',
                'orders.order_number',
                'orders.status',
                'orders.total',
                'orders.currency',
                'orders.created_at',
                DB::raw("CONCAT(users.first_name, ' ', users.last_name) as customer_name")
            )
            ->orderBy('orders.created_at', 'desc')
            ->limit(10)
            ->get();

        $ordersByStatus = DB::table('orders')
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        $monthlySales = DB::table('orders')
            ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'ordersByStatus' => $ordersByStatus,
            'monthlySales' => $monthlySales,
        ]);
    }
}

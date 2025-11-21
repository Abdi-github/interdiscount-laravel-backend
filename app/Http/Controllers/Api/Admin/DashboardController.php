<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $stats = [
            'users' => [
                // Users data aggregation in progress
                'total' => DB::table('users')->count(),
                'active' => DB::table('users')->where('is_active', true)->count(),
                'verified' => DB::table('users')->where('is_verified', true)->count(),
                'new_today' => DB::table('users')->whereDate('created_at', today())->count(),
                'new_this_week' => DB::table('users')->where('created_at', '>=', now()->startOfWeek())->count(),
                'new_this_month' => DB::table('users')->where('created_at', '>=', now()->startOfMonth())->count(),
            ],
            'orders' => [
                // Order statistics aggregation
                'total' => DB::table('orders')->count(),
                'pending' => DB::table('orders')->where('status', 'PENDING')->count(),
                'confirmed' => DB::table('orders')->where('status', 'CONFIRMED')->count(),
                'processing' => DB::table('orders')->where('status', 'PROCESSING')->count(),
                'shipped' => DB::table('orders')->where('status', 'SHIPPED')->count(),
                'delivered' => DB::table('orders')->where('status', 'DELIVERED')->count(),
                'cancelled' => DB::table('orders')->where('status', 'CANCELLED')->count(),
                // TODO: Implement order revenue caching for performance
                'revenue' => (float) DB::table('orders')
                    ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                    ->sum('total'),
                'revenue_this_month' => (float) DB::table('orders')
                    ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                    ->where('created_at', '>=', now()->startOfMonth())
                    ->sum('total'),
            ],
            'products' => [
                'total' => DB::table('products')->count(),
                'active' => DB::table('products')->where('is_active', true)->count(),
                'out_of_stock' => DB::table('products')->where('availability_state', 'not_available')->count(),
            ],
            'stores' => [
                'total' => DB::table('stores')->count(),
                'active' => DB::table('stores')->where('is_active', true)->count(),
            ],
            'reviews' => [
                'total' => DB::table('reviews')->count(),
                'pending' => DB::table('reviews')->where('is_approved', false)->count(),
                'approved' => DB::table('reviews')->where('is_approved', true)->count(),
            ],
            'transfers' => [
                'total' => DB::table('stock_transfers')->count(),
                'pending' => DB::table('stock_transfers')->where('status', 'REQUESTED')->count(),
                'in_transit' => DB::table('stock_transfers')->where('status', 'SHIPPED')->count(),
            ],
        ];

        return $this->success($stats, 'Dashboard stats retrieved successfully');
    }
}

<?php

namespace App\Http\Controllers\Api\Store;

use App\Domain\Inventory\Repositories\StoreInventoryRepositoryInterface;
use App\Domain\Order\Repositories\OrderRepositoryInterface;
use App\Domain\Transfer\Repositories\StockTransferRepositoryInterface;
use App\Domain\Transfer\Enums\TransferStatus;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoreDashboardController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private StoreInventoryRepositoryInterface $inventoryRepository,
        private StockTransferRepositoryInterface $transferRepository,
    ) {}

    public function index(): JsonResponse
    {
        $admin = request()->user();
        $storeId = $admin->store_id;

        // Order stats for this store (pickup orders)
        $orderStats = DB::table('orders')
            ->where('store_pickup_id', $storeId)
            ->where('is_store_pickup', true)
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'PENDING' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'CONFIRMED' THEN 1 ELSE 0 END) as confirmed_orders,
                SUM(CASE WHEN status = 'READY_FOR_PICKUP' THEN 1 ELSE 0 END) as ready_for_pickup,
                SUM(CASE WHEN status = 'PICKED_UP' THEN 1 ELSE 0 END) as picked_up,
                SUM(total) as total_revenue
            ")
            ->first();

        // Inventory stats
        $inventoryStats = DB::table('store_inventories')
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->selectRaw("
                COUNT(*) as total_products,
                SUM(CASE WHEN quantity <= min_stock AND quantity > 0 THEN 1 ELSE 0 END) as low_stock_count,
                SUM(CASE WHEN quantity = 0 THEN 1 ELSE 0 END) as out_of_stock_count,
                SUM(quantity) as total_units
            ")
            ->first();

        // Transfer stats
        $transferStats = DB::table('stock_transfers')
            ->where(function ($q) use ($storeId) {
                $q->where('from_store_id', $storeId)
                  ->orWhere('to_store_id', $storeId);
            })
            ->selectRaw("
                SUM(CASE WHEN status = 'REQUESTED' THEN 1 ELSE 0 END) as pending_transfers,
                SUM(CASE WHEN status = 'SHIPPED' THEN 1 ELSE 0 END) as in_transit,
                SUM(CASE WHEN status = 'RECEIVED' THEN 1 ELSE 0 END) as completed_transfers
            ")
            ->first();

        return $this->success([
            'orders' => [
                'total' => (int) ($orderStats->total_orders ?? 0),
                'pending' => (int) ($orderStats->pending_orders ?? 0),
                'confirmed' => (int) ($orderStats->confirmed_orders ?? 0),
                'ready_for_pickup' => (int) ($orderStats->ready_for_pickup ?? 0),
                'picked_up' => (int) ($orderStats->picked_up ?? 0),
                'revenue' => (float) ($orderStats->total_revenue ?? 0),
            ],
            'inventory' => [
                'total_products' => (int) ($inventoryStats->total_products ?? 0),
                'low_stock' => (int) ($inventoryStats->low_stock_count ?? 0),
                'out_of_stock' => (int) ($inventoryStats->out_of_stock_count ?? 0),
                'total_units' => (int) ($inventoryStats->total_units ?? 0),
            ],
            'transfers' => [
                'pending' => (int) ($transferStats->pending_transfers ?? 0),
                'in_transit' => (int) ($transferStats->in_transit ?? 0),
                'completed' => (int) ($transferStats->completed_transfers ?? 0),
            ],
        ], 'Store dashboard retrieved successfully');
    }
}

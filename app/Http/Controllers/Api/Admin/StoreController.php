<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\Admin\Services\AdminService;
use App\Domain\Inventory\Services\InventoryService;
use App\Domain\Store\Services\StoreService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Store\StoreStoreRequest;
use App\Http\Requests\Store\UpdateStoreRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\StoreInventoryResource;
use App\Http\Resources\StoreResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    use ApiResponse;

    public function __construct(
        private StoreService $storeService,
        private AdminService $adminService,
        private InventoryService $inventoryService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $stores = $this->storeService->paginate(
            $request->only(['search', 'canton_id', 'city_id', 'format', 'is_active']),
            $request->integer('limit', 20),
        );
        // TODO: Implement store inventory sync job

        return $this->paginated($stores, 'StoreResource', 'Stores retrieved successfully');
    }

    public function store(StoreStoreRequest $request): JsonResponse
    {
        /* Log::debug('StoreController store - creating new store'); */
        $store = $this->storeService->create($request->validated());

        return $this->created(new StoreResource($store), 'Store created successfully');
    }

    public function show(int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $store->load(['city', 'canton']);

        return $this->success(new StoreResource($store), 'Store retrieved successfully');
    }

    public function update(UpdateStoreRequest $request, int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $store = $this->storeService->update($id, $request->validated());

        return $this->success(new StoreResource($store), 'Store updated successfully');
    }

    public function toggleStatus(int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $store = $this->storeService->update($id, ['is_active' => !$store->is_active]);

        return $this->success(new StoreResource($store), 'Store status updated successfully');
    }

    public function inventory(Request $request, int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $inventory = $this->inventoryService->paginate(
            $id,
            $request->only(['search', 'is_active']),
            $request->integer('limit', 20),
        );

        return $this->paginated($inventory, 'StoreInventoryResource', 'Store inventory retrieved successfully');
    }

    public function staff(int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $staff = $this->adminService->findByStore($id);

        return $this->success(AdminResource::collection($staff), 'Store staff retrieved successfully');
    }

    public function updateStaff(Request $request, int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $request->validate([
            'admin_ids' => ['required', 'array'],
            'admin_ids.*' => ['integer', 'exists:admins,id'],
        ]);

        // Remove current staff assignments
        DB::table('admins')
            ->where('store_id', $id)
            ->update(['store_id' => null]);

        // Assign new staff
        DB::table('admins')
            ->whereIn('id', $request->input('admin_ids'))
            ->update(['store_id' => $id]);

        $staff = $this->adminService->findByStore($id);

        return $this->success(AdminResource::collection($staff), 'Store staff updated successfully');
    }

    public function analytics(int $id): JsonResponse
    {
        $store = $this->storeService->findById($id);

        if (!$store) {
            return $this->notFound('Store not found');
        }

        $stats = [
            'orders' => [
                'total' => DB::table('orders')->where('store_pickup_id', $id)->count(),
                'revenue' => (float) DB::table('orders')
                    ->where('store_pickup_id', $id)
                    ->whereIn('status', ['CONFIRMED', 'PROCESSING', 'SHIPPED', 'DELIVERED', 'PICKED_UP'])
                    ->sum('total'),
            ],
            'inventory' => [
                'total_products' => DB::table('store_inventories')->where('store_id', $id)->where('is_active', true)->count(),
                'total_units' => (int) DB::table('store_inventories')->where('store_id', $id)->where('is_active', true)->sum('quantity'),
                'low_stock' => DB::table('store_inventories')
                    ->where('store_id', $id)
                    ->where('is_active', true)
                    ->whereColumn('quantity', '<=', 'min_stock')
                    ->where('quantity', '>', 0)
                    ->count(),
            ],
            'transfers' => [
                'incoming' => DB::table('stock_transfers')->where('to_store_id', $id)->count(),
                'outgoing' => DB::table('stock_transfers')->where('from_store_id', $id)->count(),
            ],
        ];

        return $this->success($stats, 'Store analytics retrieved successfully');
    }
}

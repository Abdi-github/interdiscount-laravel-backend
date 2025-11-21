<?php

namespace App\Http\Controllers\Api\Store;

use App\Domain\Inventory\Services\InventoryService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\BulkUpdateInventoryRequest;
use App\Http\Requests\Inventory\ScanUpdateRequest;
use App\Http\Requests\Inventory\UpdateInventoryRequest;
use App\Http\Resources\StoreInventoryResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use ApiResponse;

    public function __construct(
        private InventoryService $inventoryService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $admin = request()->user();
        $perPage = (int) $request->input('limit', 20);
        $filters = $request->only(['search']);

        $inventory = $this->inventoryService->paginate($admin->store_id, $filters, $perPage);

        return $this->paginated($inventory, 'StoreInventoryResource', 'Inventory retrieved successfully');
    }

    public function show(Request $request, int $productId): JsonResponse
    {
        $admin = request()->user();
        $inventory = $this->inventoryService->findByStoreAndProduct($admin->store_id, $productId);

        if (!$inventory) {
            return $this->notFound('Inventory record not found');
        }

        return $this->success(
            new StoreInventoryResource($inventory),
            'Inventory record retrieved successfully',
        );
    }

    public function update(UpdateInventoryRequest $request, int $productId): JsonResponse
    {
        $admin = request()->user();

        try {
            $inventory = $this->inventoryService->updateInventory(
                $admin->store_id,
                $productId,
                $request->validated(),
            );

            return $this->success(
                new StoreInventoryResource($inventory),
                'Inventory updated successfully',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->notFound($e->getMessage());
        }
    }

    public function bulkUpdate(BulkUpdateInventoryRequest $request): JsonResponse
    {
        $admin = request()->user();
        $updated = $this->inventoryService->bulkUpdate($admin->store_id, $request->validated('items'));

        return $this->success(
            StoreInventoryResource::collection($updated),
            "Updated {$updated->count()} inventory records",
        );
    }

    public function scan(ScanUpdateRequest $request): JsonResponse
    {
        $admin = request()->user();

        try {
            $inventory = $this->inventoryService->scanUpdate(
                $admin->store_id,
                $request->validated('product_code'),
                $request->validated(),
            );

            return $this->success(
                new StoreInventoryResource($inventory),
                'Inventory updated via scan',
            );
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    public function lowStock(Request $request): JsonResponse
    {
        $admin = request()->user();
        $items = $this->inventoryService->getLowStock($admin->store_id);

        return $this->success(
            StoreInventoryResource::collection($items),
            'Low stock items retrieved successfully',
        );
    }

    public function outOfStock(Request $request): JsonResponse
    {
        $admin = request()->user();
        $items = $this->inventoryService->getOutOfStock($admin->store_id);

        return $this->success(
            StoreInventoryResource::collection($items),
            'Out of stock items retrieved successfully',
        );
    }

    public function export(Request $request): \Illuminate\Http\Response
    {
        $admin = request()->user();
        $csv = $this->inventoryService->exportCsv($admin->store_id);

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory-export.csv"',
        ]);
    }
}

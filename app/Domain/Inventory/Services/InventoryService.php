<?php

namespace App\Domain\Inventory\Services;

use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Inventory\Repositories\StoreInventoryRepositoryInterface;
use App\Domain\Product\Repositories\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InventoryService
{
    public function __construct(
        private StoreInventoryRepositoryInterface $inventoryRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function paginate(int $storeId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->inventoryRepository->paginateByStore($storeId, $filters, $perPage);
    }

    public function findByStoreAndProduct(int $storeId, int $productId): ?StoreInventory
    {
        return $this->inventoryRepository->findByStoreAndProduct($storeId, $productId);
    }

    public function getLowStock(int $storeId): Collection
    {
        return $this->inventoryRepository->getLowStock($storeId);
    }

    public function getOutOfStock(int $storeId): Collection
    {
        return $this->inventoryRepository->getOutOfStock($storeId);
    }

    public function updateInventory(int $storeId, int $productId, array $data): StoreInventory
    {
        $inventory = $this->inventoryRepository->findByStoreAndProduct($storeId, $productId);

        if (!$inventory) {
            throw new \InvalidArgumentException('Inventory record not found for this store and product');
        }

        if (isset($data['quantity']) && $data['quantity'] > $inventory->quantity) {
            $data['last_restock_at'] = now();
        }

        return $this->inventoryRepository->update($inventory->id, $data);
    }

    public function bulkUpdate(int $storeId, array $items): Collection
    {
        $updatedItems = [];

        foreach ($items as $item) {
            $item['store_id'] = $storeId;
            $updatedItems[] = $item;
        }

        return $this->inventoryRepository->bulkUpdate($updatedItems);
    }

    public function scanUpdate(int $storeId, string $productCode, array $data): StoreInventory
    {
        $product = $this->productRepository->findByCode($productCode);

        if (!$product) {
            throw new \InvalidArgumentException("Product with code {$productCode} not found");
        }

        $inventory = $this->inventoryRepository->findByStoreAndProduct($storeId, $product->id);

        if (!$inventory) {
            throw new \InvalidArgumentException('No inventory record for this product in this store');
        }

        $updateData = [];
        if (isset($data['quantity'])) {
            $updateData['quantity'] = $data['quantity'];
            if ($data['quantity'] > $inventory->quantity) {
                $updateData['last_restock_at'] = now();
            }
        }
        if (isset($data['location_in_store'])) {
            $updateData['location_in_store'] = $data['location_in_store'];
        }

        return $this->inventoryRepository->update($inventory->id, $updateData);
    }

    public function exportCsv(int $storeId): string
    {
        $inventories = $this->inventoryRepository->paginateByStore($storeId, [], 10000);

        $csv = "product_id,product_name,product_code,quantity,reserved,available,min_stock,max_stock,location_in_store,is_low_stock\n";

        foreach ($inventories as $inv) {
            $csv .= implode(',', [
                $inv->product_id,
                '"' . str_replace('"', '""', $inv->product?->name ?? '') . '"',
                $inv->product?->code ?? '',
                $inv->quantity,
                $inv->reserved,
                $inv->availableQuantity(),
                $inv->min_stock,
                $inv->max_stock,
                '"' . str_replace('"', '""', $inv->location_in_store ?? '') . '"',
                $inv->isLowStock() ? 'yes' : 'no',
            ]) . "\n";
        }

        return $csv;
    }
}

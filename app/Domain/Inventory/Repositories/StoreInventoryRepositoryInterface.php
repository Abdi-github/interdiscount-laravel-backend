<?php

namespace App\Domain\Inventory\Repositories;

use App\Domain\Inventory\Models\StoreInventory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface StoreInventoryRepositoryInterface
{
    public function findById(int $id): ?StoreInventory;
    public function findByStoreAndProduct(int $storeId, int $productId): ?StoreInventory;
    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function getLowStock(int $storeId): Collection;
    public function getOutOfStock(int $storeId): Collection;
    public function create(array $data): StoreInventory;
    public function update(int $id, array $data): StoreInventory;
    public function bulkUpdate(array $items): Collection;
}

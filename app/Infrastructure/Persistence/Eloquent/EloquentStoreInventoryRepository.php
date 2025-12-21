<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Inventory\Models\StoreInventory;
use App\Domain\Inventory\Repositories\StoreInventoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentStoreInventoryRepository implements StoreInventoryRepositoryInterface
{
    public function __construct(private StoreInventory $model) {}

    public function findById(int $id): ?StoreInventory
    {
        return $this->model->with(['store', 'product'])->find($id);
    }

    public function findByStoreAndProduct(int $storeId, int $productId): ?StoreInventory
    {
        return $this->model
            ->where('store_id', $storeId)
            ->where('product_id', $productId)
            ->first();
    }

    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['product.brand'])
            ->where('store_id', $storeId)
            ->active();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function getLowStock(int $storeId): Collection
    {
        return $this->model->with(['product'])
            ->where('store_id', $storeId)
            ->active()
            ->lowStock()
            ->get();
    }

    public function getOutOfStock(int $storeId): Collection
    {
        return $this->model->with(['product'])
            ->where('store_id', $storeId)
            ->active()
            ->outOfStock()
            ->get();
    }

    public function create(array $data): StoreInventory
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): StoreInventory
    {
        $inventory = $this->model->findOrFail($id);
        $inventory->update($data);
        return $inventory->fresh(['store', 'product']);
    }

    public function bulkUpdate(array $items): Collection
    {
        $updated = collect();
        foreach ($items as $item) {
            $inventory = $this->model
                ->where('store_id', $item['store_id'])
                ->where('product_id', $item['product_id'])
                ->first();

            if ($inventory) {
                $inventory->update($item);
                $updated->push($inventory->fresh());
            }
        }
        return $updated;
    }
}

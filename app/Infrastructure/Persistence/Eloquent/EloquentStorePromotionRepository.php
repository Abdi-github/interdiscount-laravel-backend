<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Promotion\Models\StorePromotion;
use App\Domain\Promotion\Repositories\StorePromotionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentStorePromotionRepository implements StorePromotionRepositoryInterface
{
    public function __construct(private StorePromotion $model) {}

    public function findById(int $id): ?StorePromotion
    {
        return $this->model->with(['store', 'product', 'category', 'creator'])->find($id);
    }

    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['product', 'category', 'creator'])
            ->where('store_id', $storeId);

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): StorePromotion
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): StorePromotion
    {
        $promotion = $this->model->findOrFail($id);
        $promotion->update($data);
        return $promotion->fresh(['store', 'product', 'category', 'creator']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}

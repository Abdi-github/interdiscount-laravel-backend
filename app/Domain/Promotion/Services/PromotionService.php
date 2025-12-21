<?php

namespace App\Domain\Promotion\Services;

use App\Domain\Promotion\Models\StorePromotion;
use App\Domain\Promotion\Repositories\StorePromotionRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PromotionService
{
    public function __construct(
        private StorePromotionRepositoryInterface $promotionRepository,
    ) {}

    public function findById(int $id): ?StorePromotion
    {
        return $this->promotionRepository->findById($id);
    }

    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->promotionRepository->paginateByStore($storeId, $filters, $perPage);
    }

    public function create(array $data): StorePromotion
    {
        return $this->promotionRepository->create($data);
    }

    public function update(int $id, array $data): StorePromotion
    {
        return $this->promotionRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->promotionRepository->delete($id);
    }
}

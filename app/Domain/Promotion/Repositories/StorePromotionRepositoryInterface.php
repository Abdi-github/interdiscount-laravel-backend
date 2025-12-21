<?php

namespace App\Domain\Promotion\Repositories;

use App\Domain\Promotion\Models\StorePromotion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StorePromotionRepositoryInterface
{
    public function findById(int $id): ?StorePromotion;
    public function paginateByStore(int $storeId, array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): StorePromotion;
    public function update(int $id, array $data): StorePromotion;
    public function delete(int $id): bool;
}

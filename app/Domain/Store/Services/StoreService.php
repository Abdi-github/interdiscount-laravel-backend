<?php

namespace App\Domain\Store\Services;

use App\Domain\Store\Models\Store;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StoreService
{
    public function __construct(
        private StoreRepositoryInterface $storeRepository,
    ) {}

    public function findById(int $id): ?Store
    {
        return $this->storeRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Store
    {
        return $this->storeRepository->findBySlug($slug);
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return $this->storeRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Store
    {
        return $this->storeRepository->create($data);
    }

    public function update(int $id, array $data): Store
    {
        return $this->storeRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->storeRepository->delete($id);
    }
}

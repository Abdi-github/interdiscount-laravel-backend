<?php

namespace App\Domain\Brand\Services;

use App\Domain\Brand\Models\Brand;
use App\Domain\Brand\Repositories\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BrandService
{
    public function __construct(
        private BrandRepositoryInterface $brandRepository,
    ) {}

    public function findById(int $id): ?Brand
    {
        return $this->brandRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Brand
    {
        return $this->brandRepository->findBySlug($slug);
    }

    public function all(): Collection
    {
        return $this->brandRepository->all();
    }

    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        return $this->brandRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Brand
    {
        return $this->brandRepository->create($data);
    }

    public function update(int $id, array $data): Brand
    {
        return $this->brandRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->brandRepository->delete($id);
    }
}

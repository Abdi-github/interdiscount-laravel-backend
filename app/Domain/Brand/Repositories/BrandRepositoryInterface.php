<?php

namespace App\Domain\Brand\Repositories;

use App\Domain\Brand\Models\Brand;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BrandRepositoryInterface
{
    public function findById(int $id): ?Brand;
    public function findBySlug(string $slug): ?Brand;
    public function all(): Collection;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Brand;
    public function update(int $id, array $data): Brand;
    public function delete(int $id): bool;
}

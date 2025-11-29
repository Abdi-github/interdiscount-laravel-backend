<?php

namespace App\Domain\Store\Repositories;

use App\Domain\Store\Models\Store;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface StoreRepositoryInterface
{
    public function findById(int $id): ?Store;
    public function findBySlug(string $slug): ?Store;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Store;
    public function update(int $id, array $data): Store;
    public function delete(int $id): bool;
}

<?php

namespace App\Domain\Product\Repositories;

use App\Domain\Product\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findBySlug(string $slug): ?Product;
    public function findByCode(string $code): ?Product;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Product;
    public function update(int $id, array $data): Product;
    public function delete(int $id): bool;
    public function updateStatus(int $id, string $status): Product;
    public function getRelated(int $productId, int $limit = 8): Collection;
}

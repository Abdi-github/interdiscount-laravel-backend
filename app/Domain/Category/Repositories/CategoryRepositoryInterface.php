<?php

namespace App\Domain\Category\Repositories;

use App\Domain\Category\Models\Category;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;
    public function findBySlug(string $slug): ?Category;
    public function all(array $filters = []): Collection;
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator;
    public function create(array $data): Category;
    public function update(int $id, array $data): Category;
    public function delete(int $id): bool;
    public function getChildren(int $parentId): Collection;
    public function getDescendantIds(int $categoryId): array;
    public function getBreadcrumb(int $categoryId): Collection;
    public function getWithProductCounts(): Collection;
}

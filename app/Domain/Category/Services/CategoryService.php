<?php

namespace App\Domain\Category\Services;

use App\Domain\Category\Models\Category;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function findById(int $id): ?Category
    {
        return $this->categoryRepository->findById($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->categoryRepository->findBySlug($slug);
    }

    public function all(array $filters = []): Collection
    {
        return $this->categoryRepository->all($filters);
    }

    public function paginate(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        return $this->categoryRepository->paginate($filters, $perPage);
    }

    public function create(array $data): Category
    {
        return $this->categoryRepository->create($data);
    }

    public function update(int $id, array $data): Category
    {
        return $this->categoryRepository->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->categoryRepository->delete($id);
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->categoryRepository->getChildren($parentId);
    }

    public function getDescendantIds(int $categoryId): array
    {
        return $this->categoryRepository->getDescendantIds($categoryId);
    }

    public function getBreadcrumb(int $categoryId): Collection
    {
        return $this->categoryRepository->getBreadcrumb($categoryId);
    }

    public function getWithProductCounts(): Collection
    {
        return $this->categoryRepository->getWithProductCounts();
    }
}

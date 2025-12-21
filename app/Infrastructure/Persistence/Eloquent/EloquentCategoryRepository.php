<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Category\Models\Category;
use App\Domain\Category\Repositories\CategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private Category $model) {}

    public function findById(int $id): ?Category
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Category
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function all(array $filters = []): Collection
    {
        $query = $this->model->query();

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        return $query->orderBy('sort_order')->get();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.de')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.en')) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.fr')) LIKE ?", ["%{$search}%"]);
            });
        }

        if (!empty($filters['level'])) {
            $query->where('level', $filters['level']);
        }

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        return $query->orderBy('sort_order')->paginate($perPage);
    }

    public function create(array $data): Category
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->model->findOrFail($id);
        $category->update($data);
        return $category->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function getChildren(int $parentId): Collection
    {
        return $this->model->where('parent_id', $parentId)->orderBy('sort_order')->get();
    }

    public function getDescendantIds(int $categoryId): array
    {
        $ids = [];
        $childIds = $this->model->where('parent_id', $categoryId)->pluck('id')->toArray();

        foreach ($childIds as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getDescendantIds($childId));
        }

        return $ids;
    }

    public function getBreadcrumb(int $categoryId): Collection
    {
        $category = $this->model->findOrFail($categoryId);
        $ancestors = $category->ancestors();
        $ancestors[] = $category;

        return collect($ancestors);
    }

    public function getWithProductCounts(): Collection
    {
        return $this->model->withCount('products')->orderBy('sort_order')->get();
    }
}

<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Brand\Models\Brand;
use App\Domain\Brand\Repositories\BrandRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentBrandRepository implements BrandRepositoryInterface
{
    public function __construct(private Brand $model) {}

    public function findById(int $id): ?Brand
    {
        return $this->model->find($id);
    }

    public function findBySlug(string $slug): ?Brand
    {
        return $this->model->where('slug', $slug)->first();
    }

    public function all(): Collection
    {
        return $this->model->active()->orderBy('name')->get();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Brand
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Brand
    {
        $brand = $this->model->findOrFail($id);
        $brand->update($data);
        return $brand->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}

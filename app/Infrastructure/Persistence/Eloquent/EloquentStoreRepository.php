<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Store\Models\Store;
use App\Domain\Store\Repositories\StoreRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentStoreRepository implements StoreRepositoryInterface
{
    public function __construct(private Store $model) {}

    public function findById(int $id): ?Store
    {
        return $this->model->with(['city', 'canton'])->find($id);
    }

    public function findBySlug(string $slug): ?Store
    {
        return $this->model->with(['city', 'canton'])->where('slug', $slug)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->with(['city', 'canton']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('postal_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['canton_id'])) {
            $query->where('canton_id', $filters['canton_id']);
        }

        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        if (!empty($filters['format'])) {
            $query->where('format', $filters['format']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function create(array $data): Store
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Store
    {
        $store = $this->model->findOrFail($id);
        $store->update($data);
        return $store->fresh(['city', 'canton']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }
}

<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Admin\Models\Admin;
use App\Domain\Admin\Repositories\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentAdminRepository implements AdminRepositoryInterface
{
    public function __construct(private Admin $model) {}

    public function findById(int $id): ?Admin
    {
        return $this->model->find($id);
    }

    public function findByEmail(string $email): ?Admin
    {
        return $this->model->where('email', $email)->first();
    }

    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['admin_type'])) {
            $query->where('admin_type', $filters['admin_type']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function create(array $data): Admin
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Admin
    {
        $admin = $this->model->findOrFail($id);
        $admin->update($data);
        return $admin->fresh();
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function findByStore(int $storeId): Collection
    {
        return $this->model->where('store_id', $storeId)->get();
    }
}

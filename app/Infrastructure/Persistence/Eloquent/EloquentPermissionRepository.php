<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\RBAC\Models\Permission;
use App\Domain\RBAC\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentPermissionRepository implements PermissionRepositoryInterface
{
    public function __construct(private Permission $model) {}

    public function findById(int $id): ?Permission
    {
        return $this->model->find($id);
    }

    public function findByName(string $name): ?Permission
    {
        return $this->model->where('name', $name)->first();
    }

    public function all(): Collection
    {
        return $this->model->orderBy('resource')->orderBy('action')->get();
    }

    public function getByResource(string $resource): Collection
    {
        return $this->model->where('resource', $resource)->orderBy('action')->get();
    }
}

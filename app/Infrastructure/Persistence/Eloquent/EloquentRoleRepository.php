<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\RBAC\Models\Role;
use App\Domain\RBAC\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentRoleRepository implements RoleRepositoryInterface
{
    public function __construct(private Role $model) {}

    public function findById(int $id): ?Role
    {
        return $this->model->with(['permissions'])->find($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->model->with(['permissions'])->where('name', $name)->first();
    }

    public function all(): Collection
    {
        return $this->model->with(['permissions'])->orderBy('name')->get();
    }

    public function create(array $data): Role
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): Role
    {
        $role = $this->model->findOrFail($id);
        $role->update($data);
        return $role->fresh(['permissions']);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $role = $this->model->findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
    }
}

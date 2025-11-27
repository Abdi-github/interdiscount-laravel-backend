<?php

namespace App\Domain\RBAC\Repositories;

use App\Domain\RBAC\Models\Role;
use Illuminate\Support\Collection;

interface RoleRepositoryInterface
{
    public function findById(int $id): ?Role;
    public function findByName(string $name): ?Role;
    public function all(): Collection;
    public function create(array $data): Role;
    public function update(int $id, array $data): Role;
    public function delete(int $id): bool;
    public function syncPermissions(int $roleId, array $permissionIds): void;
}

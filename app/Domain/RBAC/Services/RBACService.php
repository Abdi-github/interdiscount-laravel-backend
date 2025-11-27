<?php

namespace App\Domain\RBAC\Services;

use App\Domain\RBAC\Models\Role;
use App\Domain\RBAC\Models\Permission;
use App\Domain\RBAC\Repositories\RoleRepositoryInterface;
use App\Domain\RBAC\Repositories\PermissionRepositoryInterface;
use Illuminate\Support\Collection;

class RBACService
{
    public function __construct(
        private RoleRepositoryInterface $roleRepository,
        private PermissionRepositoryInterface $permissionRepository,
    ) {}

    // Roles

    public function getAllRoles(): Collection
    {
        return $this->roleRepository->all();
    }

    public function findRoleById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function createRole(array $data): Role
    {
        return $this->roleRepository->create($data);
    }

    public function updateRole(int $id, array $data): Role
    {
        return $this->roleRepository->update($id, $data);
    }

    public function deleteRole(int $id): bool
    {
        $role = $this->roleRepository->findById($id);

        if ($role && $role->is_system) {
            throw new \RuntimeException('Cannot delete system role');
        }

        return $this->roleRepository->delete($id);
    }

    public function getRolePermissions(int $roleId): Collection
    {
        $role = $this->roleRepository->findById($roleId);

        return $role ? $role->permissions : collect();
    }

    public function syncRolePermissions(int $roleId, array $permissionIds): void
    {
        $this->roleRepository->syncPermissions($roleId, $permissionIds);
    }

    // Permissions

    public function getAllPermissions(): Collection
    {
        return $this->permissionRepository->all();
    }

    public function getPermissionsByResource(string $resource): Collection
    {
        return $this->permissionRepository->getByResource($resource);
    }

    // Admin role management

    public function getAdminRoles(int $adminId): Collection
    {
        $admin = \App\Domain\Admin\Models\Admin::find($adminId);

        return $admin ? $admin->roles()->with('permissions')->get() : collect();
    }

    public function syncAdminRoles(int $adminId, array $roleIds, ?int $assignedBy = null): void
    {
        $admin = \App\Domain\Admin\Models\Admin::findOrFail($adminId);

        $syncData = [];
        foreach ($roleIds as $roleId) {
            $syncData[$roleId] = [
                'assigned_by' => $assignedBy,
                'assigned_at' => now(),
                'is_active' => true,
            ];
        }

        $admin->roles()->sync($syncData);
    }
}

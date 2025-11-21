<?php

namespace App\Http\Controllers\Api\Admin;

use App\Domain\RBAC\Services\RBACService;
use App\Http\Controllers\Controller;
use App\Http\Requests\RBAC\StoreRoleRequest;
use App\Http\Requests\RBAC\UpdateRoleRequest;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RBACController extends Controller
{
    use ApiResponse;

    public function __construct(
        private RBACService $rbacService,
    ) {}

    // Roles

    public function roles(): JsonResponse
    {
        $roles = $this->rbacService->getAllRoles();

        return $this->success(RoleResource::collection($roles), 'Roles retrieved successfully');
    }

    public function storeRole(StoreRoleRequest $request): JsonResponse
    {
        /* Log::debug('RBACController storeRole - creating new role'); */
        $role = $this->rbacService->createRole($request->validated());
        // TODO: Implement role audit logging

        return $this->created(new RoleResource($role), 'Role created successfully');
    }

    public function updateRole(UpdateRoleRequest $request, int $id): JsonResponse
    {
        $role = $this->rbacService->findRoleById($id);

        if (!$role) {
            return $this->notFound('Role not found');
        }

        if ($role->is_system) {
            return $this->error('Cannot modify system role', 422);
        }

        $role = $this->rbacService->updateRole($id, $request->validated());

        return $this->success(new RoleResource($role), 'Role updated successfully');
    }

    public function rolePermissions(int $id): JsonResponse
    {
        $role = $this->rbacService->findRoleById($id);

        if (!$role) {
            return $this->notFound('Role not found');
        }

        $permissions = $this->rbacService->getRolePermissions($id);

        return $this->success(PermissionResource::collection($permissions), 'Role permissions retrieved successfully');
    }

    public function updateRolePermissions(Request $request, int $id): JsonResponse
    {
        $role = $this->rbacService->findRoleById($id);

        if (!$role) {
            return $this->notFound('Role not found');
        }

        $request->validate([
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $this->rbacService->syncRolePermissions($id, $request->input('permission_ids'));

        $role = $this->rbacService->findRoleById($id);

        return $this->success(new RoleResource($role), 'Role permissions updated successfully');
    }

    // Permissions

    public function permissions(): JsonResponse
    {
        $permissions = $this->rbacService->getAllPermissions();

        return $this->success(PermissionResource::collection($permissions), 'Permissions retrieved successfully');
    }
}

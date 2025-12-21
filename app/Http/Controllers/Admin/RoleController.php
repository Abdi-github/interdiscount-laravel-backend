<?php

namespace App\Http\Controllers\Admin;

use App\Domain\RBAC\Services\RBACService;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(private RBACService $rbacService) {}

    public function index(): Response
    {
        $roles = $this->rbacService->getAllRoles();

        return Inertia::render('Roles/Index', [
            'roles' => RoleResource::collection($roles->load('permissions')),
        ]);
    }

    public function show(int $id): Response
    {
        $role = $this->rbacService->findRoleById($id);

        if (!$role) {
            abort(404);
        }

        $role->load('permissions');
        $allPermissions = $this->rbacService->getAllPermissions();

        return Inertia::render('Roles/Show', [
            'role' => new RoleResource($role),
            'allPermissions' => PermissionResource::collection($allPermissions),
        ]);
    }

    public function updatePermissions(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $this->rbacService->syncRolePermissions($id, $validated['permission_ids']);

        return redirect()->route('admin.roles.show', $id)
            ->with('success', 'Role permissions updated successfully');
    }
}

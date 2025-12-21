<?php

namespace App\Http\Controllers\Admin;

use App\Domain\RBAC\Services\RBACService;
use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function __construct(private RBACService $rbacService) {}

    public function index(): Response
    {
        $permissions = $this->rbacService->getAllPermissions();

        return Inertia::render('Permissions/Index', [
            'permissions' => PermissionResource::collection($permissions),
        ]);
    }
}

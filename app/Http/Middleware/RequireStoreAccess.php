<?php

namespace App\Http\Middleware;

use App\Domain\Admin\Enums\AdminType;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireStoreAccess
{
    use ApiResponse;

    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->user();

        if (!$admin) {
            return $this->unauthorized('Admin authentication required.');
        }

        // Ensure the authenticated user is an Admin model
        if (!$admin instanceof \App\Domain\Admin\Models\Admin) {
            return $this->forbidden('Store access requires admin authentication.');
        }

        // Super admin and platform admin have access to all stores
        if (in_array($admin->admin_type, [AdminType::SUPER_ADMIN, AdminType::PLATFORM_ADMIN])) {
            return $next($request);
        }

        // Store-level staff must be assigned to a store
        if (!$admin->store_id) {
            return $this->forbidden('You are not assigned to any store.');
        }

        // Check if the requested store matches the admin's store
        $requestedStoreId = $request->route('storeId') ?? $request->route('store');
        if ($requestedStoreId && (int) $requestedStoreId !== $admin->store_id) {
            return $this->forbidden('You do not have access to this store.');
        }

        return $next($request);
    }
}

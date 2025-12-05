<?php

namespace App\Http\Middleware;

use App\Domain\Admin\Enums\AdminType;
use App\Domain\Admin\Models\Admin;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdmin
{
    use ApiResponse;

    private const ALLOWED_TYPES = [
        AdminType::SUPER_ADMIN,
        AdminType::PLATFORM_ADMIN,
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $admin = $request->user();

        if (!$admin || !($admin instanceof Admin)) {
            return $this->unauthorized('Admin authentication required.');
        }

        if (!in_array($admin->admin_type, self::ALLOWED_TYPES)) {
            return $this->forbidden('Insufficient admin privileges.');
        }

        return $next($request);
    }
}

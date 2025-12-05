<?php

namespace App\Http\Middleware;

use App\Domain\Admin\Models\Admin;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        /** @var Admin|null $admin */
        $admin = $request->user('web');

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $admin ? [
                    'id' => $admin->id,
                    'name' => $admin->first_name . ' ' . $admin->last_name,
                    'email' => $admin->email,
                    'admin_type' => $admin->admin_type->value,
                    'permissions' => $admin->getAllPermissions()->pluck('name')->values(),
                    'roles' => $admin->roles->pluck('name')->values(),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'locale' => app()->getLocale(),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingsController extends Controller
{
    public function index(Request $request): Response
    {
        $admin = $request->user('web');

        return Inertia::render('Settings/Index', [
            'admin' => [
                'id' => $admin->id,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'email' => $admin->email,
                'phone' => $admin->phone,
                'admin_type' => $admin->admin_type,
                'avatar_url' => $admin->avatar_url,
                'last_login_at' => $admin->last_login_at,
                'created_at' => $admin->created_at,
            ],
            'appInfo' => [
                'name' => config('app.name'),
                'version' => app()->version(),
                'php_version' => PHP_VERSION,
                'locale' => app()->getLocale(),
                'supported_locales' => config('interdiscount.supported_locales', ['de', 'en', 'fr', 'it']),
                'environment' => app()->environment(),
            ],
        ]);
    }
}

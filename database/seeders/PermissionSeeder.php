<?php

namespace Database\Seeders;

use App\Domain\RBAC\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = json_decode(file_get_contents(base_path('data/permissions.json')), true);

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'display_name' => $permission['display_name'],
                'description' => $permission['description'],
                'resource' => $permission['resource'],
                'action' => $permission['action'],
                'is_active' => $permission['is_active'] ?? true,
            ]);
        }
    }
}

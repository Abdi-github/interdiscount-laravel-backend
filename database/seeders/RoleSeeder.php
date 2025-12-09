<?php

namespace Database\Seeders;

use App\Domain\RBAC\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = json_decode(file_get_contents(base_path('data/roles.json')), true);

        foreach ($roles as $role) {
            Role::create([
                'name' => $role['name'],
                'display_name' => $role['display_name'],
                'description' => $role['description'],
                'is_system' => $role['is_system'] ?? false,
                'is_active' => $role['is_active'] ?? true,
            ]);
        }
    }
}

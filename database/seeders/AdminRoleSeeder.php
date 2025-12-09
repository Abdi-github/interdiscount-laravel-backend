<?php

namespace Database\Seeders;

use App\Domain\Admin\Models\Admin;
use App\Domain\RBAC\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        $userRoles = json_decode(file_get_contents(base_path('data/user_roles.json')), true);
        $usersRaw = json_decode(file_get_contents(base_path('data/users.json')), true);
        $rolesRaw = json_decode(file_get_contents(base_path('data/roles.json')), true);

        // Build user mongo_id -> mysql admin id map (non-customers only)
        $adminMongoMap = [];
        $adminsByEmail = Admin::all()->pluck('id', 'email')->toArray();
        foreach ($usersRaw as $user) {
            if ($user['user_type'] !== 'customer') {
                $adminMongoMap[$user['_id']] = $adminsByEmail[$user['email']] ?? null;
            }
        }

        // Build role mongo_id -> mysql_id map
        $rolesByName = Role::all()->pluck('id', 'name')->toArray();
        $roleMongoMap = [];
        foreach ($rolesRaw as $role) {
            $roleMongoMap[$role['_id']] = $rolesByName[$role['name']] ?? null;
        }

        foreach ($userRoles as $ur) {
            $adminId = $adminMongoMap[$ur['user_id']] ?? null;
            $roleId = $roleMongoMap[$ur['role_id']] ?? null;

            // Only seed role assignments for admins (non-customers)
            if ($adminId && $roleId) {
                DB::table('admin_roles')->insert([
                    'admin_id' => $adminId,
                    'role_id' => $roleId,
                    'assigned_by' => $ur['assigned_by'] ? ($adminMongoMap[$ur['assigned_by']] ?? null) : null,
                    'assigned_at' => isset($ur['assigned_at']) ? Carbon::parse($ur['assigned_at']) : now(),
                    'is_active' => $ur['is_active'] ?? true,
                    'created_at' => isset($ur['created_at']) ? Carbon::parse($ur['created_at']) : now(),
                    'updated_at' => isset($ur['updated_at']) ? Carbon::parse($ur['updated_at']) : now(),
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use App\Domain\RBAC\Models\Role;
use App\Domain\RBAC\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $rolePermissions = json_decode(file_get_contents(base_path('data/role_permissions.json')), true);
        $rolesRaw = json_decode(file_get_contents(base_path('data/roles.json')), true);
        $permissionsRaw = json_decode(file_get_contents(base_path('data/permissions.json')), true);

        // Build mongo_id -> mysql_id maps
        $roleMap = $this->buildIdMap($rolesRaw, Role::class, 'name');
        $permissionMap = $this->buildIdMap($permissionsRaw, Permission::class, 'name');

        foreach ($rolePermissions as $rp) {
            $roleId = $roleMap[$rp['role_id']] ?? null;
            $permissionId = $permissionMap[$rp['permission_id']] ?? null;

            if ($roleId && $permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function buildIdMap(array $rawData, string $modelClass, string $uniqueField): array
    {
        $dbRecords = $modelClass::all()->pluck('id', $uniqueField)->toArray();
        $map = [];
        foreach ($rawData as $record) {
            $map[$record['_id']] = $dbRecords[$record[$uniqueField]] ?? null;
        }
        return $map;
    }
}

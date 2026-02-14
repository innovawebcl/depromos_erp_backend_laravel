
<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Roles\Ports\RolePermissionRepository;
use Illuminate\Support\Facades\DB;

class EloquentRolePermissionRepository implements RolePermissionRepository
{
    public function isModuleEnabled(int $roleId, string $moduleKey): bool
    {
        return DB::table('role_module_permissions as rmp')
            ->join('modules as m', 'm.id', '=', 'rmp.module_id')
            ->where('rmp.role_id', $roleId)
            ->where('m.key', $moduleKey)
            ->where('m.active', true)
            ->where('rmp.enabled', true)
            ->exists();
    }
}

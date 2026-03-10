<?php

namespace App\Domain\Roles\Ports;

interface RolePermissionRepository
{
    public function isModuleEnabled(int $roleId, string $moduleKey): bool;
}

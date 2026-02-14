
<?php

namespace App\Interfaces\Http\Middleware;

use App\Domain\Roles\Ports\RolePermissionRepository;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanUseModule
{
    public function __construct(private readonly RolePermissionRepository $perms) {}

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $roleId = $request->attributes->get('role_id');
        if (!$roleId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if (!$this->perms->isModuleEnabled((int)$roleId, $moduleKey)) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}

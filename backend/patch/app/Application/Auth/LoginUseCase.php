<?php

namespace App\Application\Auth;

use App\Domain\Auth\Ports\JwtIssuer;
use App\Domain\Common\Result;
use App\Domain\Users\Ports\UserRepository;
use App\Models\RoleModulePermission;
use Illuminate\Support\Facades\Hash;

class LoginUseCase
{
    public function __construct(
        private readonly UserRepository $users,
        private readonly JwtIssuer $jwt
    ) {}

    public function execute(string $username, string $password): Result
    {
        if (empty(config('backoffice.jwt_secret'))) {
            return Result::fail('JWT_SECRET no configurado en el servidor', 500);
        }

        $user = $this->users->findByUsername($username);
        if (!$user) {
            return Result::fail('Usuario inválido', 401);
        }
        if (!Hash::check($password, $user->passwordHash)) {
            return Result::fail('Contraseña inválida', 401);
        }

        $ttl = (int) config('backoffice.jwt_ttl_seconds', 60 * 60 * 8);

        // Módulos habilitados por rol (para que el front prenda/apague menú y guards)
        $modules = [];
        if ($user->roleId) {
            $perms = RoleModulePermission::query()
                ->with('module')
                ->where('role_id', $user->roleId)
                ->get();
            foreach ($perms as $p) {
                if ($p->module) {
                    $modules[$p->module->key] = (bool)$p->enabled;
                }
            }
        }

        $claims = [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            // El front actual distingue role/admin_role, mantenemos ambos:
            'role' => 'admin',
            'admin_role' => $user->roleId ? (string)$user->roleId : '',
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'first_login' => (bool)$user->firstLogin,
            'modules' => $modules,
        ];

        $token = $this->jwt->issue($claims, $ttl);

        return Result::ok(['token' => $token]);
    }
}

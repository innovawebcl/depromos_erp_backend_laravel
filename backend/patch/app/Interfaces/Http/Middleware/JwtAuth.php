<?php

namespace App\Interfaces\Http\Middleware;

use App\Domain\Auth\Ports\JwtIssuer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuth
{
    public function __construct(private readonly JwtIssuer $jwt) {}

    public function handle(Request $request, Closure $next): Response
    {
        $auth = $request->header('Authorization', '');
        if (!str_starts_with($auth, 'Bearer ')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = trim(substr($auth, 7));
        try {
            $claims = $this->jwt->decode($token);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Exponemos el usuario "activo" al request sin acoplar a Eloquent
        $request->attributes->set('jwt_claims', $claims);
        $request->attributes->set('user_id', (int)($claims['id'] ?? 0));
        $request->attributes->set('role_id', isset($claims['admin_role']) ? (int)$claims['admin_role'] : null);

        return $next($request);
    }
}

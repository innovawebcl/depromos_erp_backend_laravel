<?php

namespace App\Infrastructure\Auth;

use App\Domain\Auth\Ports\JwtIssuer;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class FirebaseJwtIssuer implements JwtIssuer
{
    public function __construct(
        private readonly string $secret,
        private readonly string $algo = 'HS256'
    ) {}

    public function issue(array $claims, int $ttlSeconds): string
    {
        $now = time();
        $payload = array_merge($claims, [
            'iat' => $now,
            'exp' => $now + $ttlSeconds,
        ]);

        return JWT::encode($payload, $this->secret, $this->algo);
    }

    public function decode(string $jwt): array
    {
        $decoded = JWT::decode($jwt, new Key($this->secret, $this->algo));
        return json_decode(json_encode($decoded), true);
    }
}

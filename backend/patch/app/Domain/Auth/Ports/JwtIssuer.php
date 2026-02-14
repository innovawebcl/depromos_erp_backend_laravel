
<?php

namespace App\Domain\Auth\Ports;

interface JwtIssuer
{
    /** @return string JWT */
    public function issue(array $claims, int $ttlSeconds): string;

    /** @return array decoded claims */
    public function decode(string $jwt): array;
}


<?php

namespace App\Domain\Users;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $username,
        public readonly string $email,
        public readonly string $passwordHash,
        public readonly ?int $roleId,
        public readonly bool $firstLogin = false,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
    ) {}
}

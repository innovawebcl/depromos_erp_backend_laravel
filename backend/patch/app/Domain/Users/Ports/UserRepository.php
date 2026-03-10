<?php

namespace App\Domain\Users\Ports;

use App\Domain\Users\User;

interface UserRepository
{
    public function findByUsername(string $username): ?User;
    public function findById(int $id): ?User;
}

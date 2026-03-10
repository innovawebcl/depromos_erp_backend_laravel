<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Users\Ports\UserRepository;
use App\Domain\Users\User as DomainUser;
use App\Models\User;

class EloquentUserRepository implements UserRepository
{
    public function findByUsername(string $username): ?DomainUser
    {
        $u = User::query()
            ->where('active', true)
            ->where(function ($q) use ($username) {
                $q->where('username', $username)->orWhere('email', $username);
            })
            ->first();
        return $u ? $this->map($u) : null;
    }

    public function findById(int $id): ?DomainUser
    {
        $u = User::query()->find($id);
        return $u ? $this->map($u) : null;
    }

    private function map(User $u): DomainUser
    {
        return new DomainUser(
            id: $u->id,
            name: $u->full_name,
            username: $u->username ?? $u->email,
            email: $u->email,
            passwordHash: $u->password,
            roleId: $u->role_id,
            firstLogin: (bool) ($u->first_login ?? false),
            firstName: $u->first_name ?? null,
            lastName: $u->last_name ?? null,
        );
    }
}

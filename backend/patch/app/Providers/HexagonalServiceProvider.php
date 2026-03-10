<?php

namespace App\Providers;

use App\Domain\Auth\Ports\JwtIssuer;
use App\Domain\Orders\Ports\PushNotifier;
use App\Domain\Roles\Ports\RolePermissionRepository;
use App\Domain\Users\Ports\UserRepository;
use App\Infrastructure\Auth\FirebaseJwtIssuer;
use App\Infrastructure\Notifications\LogPushNotifier;
use App\Infrastructure\Persistence\EloquentRolePermissionRepository;
use App\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class HexagonalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(RolePermissionRepository::class, EloquentRolePermissionRepository::class);

        $this->app->singleton(JwtIssuer::class, function () {
            return new FirebaseJwtIssuer(config('backoffice.jwt_secret'));
        });

        $this->app->bind(PushNotifier::class, LogPushNotifier::class);
    }
}

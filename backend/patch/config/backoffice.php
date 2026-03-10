<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret
    |--------------------------------------------------------------------------
    | OBLIGATORIO: Definir JWT_SECRET en .env. Sin esta variable, la aplicación
    | no arrancará para evitar tokens firmados con un secret por defecto.
    */
    'jwt_secret' => env('JWT_SECRET') ?: throw new \RuntimeException(
        'JWT_SECRET no está definido en .env. Genere uno con: php -r "echo bin2hex(random_bytes(32));"'
    ),

    'jwt_ttl_seconds' => (int) env('JWT_TTL_SECONDS', 60 * 60 * 8),
];

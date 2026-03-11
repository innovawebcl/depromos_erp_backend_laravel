<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret
    |--------------------------------------------------------------------------
    | OBLIGATORIO: Definir JWT_SECRET en .env. Sin esta variable, la aplicación
    | no arrancará para evitar tokens firmados con un secret por defecto.
    */
    'jwt_secret' => env('JWT_SECRET', ''),

    'jwt_ttl_seconds' => (int) env('JWT_TTL_SECONDS', 60 * 60 * 8),
];


<?php

return [
    'jwt_secret' => env('JWT_SECRET', 'change-me'),
    'jwt_ttl_seconds' => (int) env('JWT_TTL_SECONDS', 60 * 60 * 8),
];

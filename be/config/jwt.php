<?php

return [
    'secret' => env('JWT_SECRET', env('APP_KEY')),
    'algo' => env('JWT_ALGO', 'HS256'),
    'ttl' => env('JWT_TTL', 60), // minutes
];

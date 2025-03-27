<?php

return [
    'secret' => env('JWT_SECRET', 'secret-key'),
    'algo' => env('JWT_ALGO', 'HS256'),
    'ttl' => env('JWT_TTL', 6),
];
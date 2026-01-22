<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'https://aquaguia.com.br',
        'https://www.aquaguia.com.br',
        'https://app.aquaguia.com.br',
    ],

    'allowed_origins_patterns' => [
        // Allow localhost with any port for development
        '#^https?://localhost(:\d+)?$#',
        // Allow any aquaguia subdomain
        '#^https://.*\.aquaguia\.com\.br$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [
        'X-RateLimit-Limit',
        'X-RateLimit-Remaining',
        'X-RateLimit-Reset',
        'ETag',
        'Cache-Control',
    ],

    'max_age' => 86400, // 24 hours

    'supports_credentials' => true,

];

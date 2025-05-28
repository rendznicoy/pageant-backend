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

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'api/csrf-cookie'], // Added api/csrf-cookie

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        env('FRONTEND_URL', 'https://pageant-scoring-vsu.up.railway.app'), // Added fallback
        'http://localhost:5173', // Keep for local development
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true, // This was 'credentials' => true (wrong property name)

];
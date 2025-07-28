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

    'paths' => ['api/*', 'tenant/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'http://localhost:3001',
        'https://onboard.avinertech.com',
        'http://demo.avinertech.local',
        'http://*.avinertech.local',
        'https://prototype.avinertech.com',
        'https://*.avinertech.com',
        'https://id-preview--68d3841c-9bcc-40d0-8d36-e5fdbb4e963f.lovable.app',
        'https://68d3841c-9bcc-40d0-8d36-e5fdbb4e963f.lovableproject.com',
        'https://*.lovable.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
]; 
<?php

return [
    'connection' => [
        'main' => [
            'host' => env('SSH_HOST', '154.80.3.24'),
            'username' => env('SSH_USERNAME', 'root1'),
            'password' => env('SSH_PASSWORD', '5Hassaan27901'),
            'port' => env('SSH_PORT', 22),
            'timeout' => env('SSH_TIMEOUT', 30),
        ],
    ],
];
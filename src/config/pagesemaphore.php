<?php

return [
    'websocket' => [
        // Websocket proto
        'proto' => env('WS_PROTO', 'ws'),

        // Websocket hostname
        'hostname' => env('WS_HOSTNAME', (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1')),

        // Websocket port
        'port' => env('WS_PORT', 8190),
    ]
];
<?php

return [
    'group_members' => [
        'Mark Fillartos',
        'Eleah Camille V. Carillo',
        'Kenrick Saballo',
    ],
    'rabbitmq' => [
        'host' => env('RABBITMQ_HOST', '127.0.0.1'),
        'port' => env('RABBITMQ_PORT', 5672),
        'user' => env('RABBITMQ_USER', 'guest'),
        'password' => env('RABBITMQ_PASSWORD', 'guest'),
        'vhost' => env('RABBITMQ_VHOST', '/'),
        'exchange' => env('RABBITMQ_EXCHANGE', 'school.events'),
        'queue' => env('RABBITMQ_QUEUE', 'enrollment.events'),
        'dead_letter_queue' => env('RABBITMQ_DEAD_LETTER_QUEUE', 'enrollment.events.dlq'),
        'retry_queue' => env('RABBITMQ_RETRY_QUEUE', 'enrollment.events.retry'),
    ],
];

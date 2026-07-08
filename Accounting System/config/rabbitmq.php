<?php

return [
    'host' => env('RABBITMQ_HOST', '127.0.0.1'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'guest'),
    'password' => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
    'exchange' => env('RABBITMQ_EXCHANGE', 'school.events'),
    'queue' => env('RABBITMQ_QUEUE', 'accounting.events'),
    'dead_letter_queue' => env('RABBITMQ_DEAD_LETTER_QUEUE', 'accounting.events.dlq'),
    'retry_queue' => env('RABBITMQ_RETRY_QUEUE', 'accounting.events.retry'),
];

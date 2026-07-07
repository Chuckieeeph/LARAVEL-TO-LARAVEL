<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqConsumer
{
    public function consume(string $queue, callable $handler): void
    {
        $connection = new AMQPStreamConnection(
            config('rabbitmq.host', env('RABBITMQ_HOST', '127.0.0.1')),
            (int) config('rabbitmq.port', env('RABBITMQ_PORT', 5672)),
            config('rabbitmq.user', env('RABBITMQ_USER', 'guest')),
            config('rabbitmq.password', env('RABBITMQ_PASSWORD', 'guest')),
            config('rabbitmq.vhost', env('RABBITMQ_VHOST', '/'))
        );

        $channel = $connection->channel();
        $channel->queue_declare($queue, false, true, false, false);
        $channel->basic_qos(null, 1, null);

        $channel->basic_consume(
            $queue,
            '',
            false,
            false,
            false,
            false,
            function (AMQPMessage $message) use ($handler): void {
                $handler($message);
            }
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}

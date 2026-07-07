<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMqPublisher
{
    protected function connection(): AMQPStreamConnection
    {
        $config = config('ems.rabbitmq');

        return new AMQPStreamConnection(
            $config['host'],
            $config['port'],
            $config['user'],
            $config['password'],
            $config['vhost']
        );
    }

    public function publish(string $queue, array $payload): void
    {
        $connection = $this->connection();
        $channel = $connection->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $message = new AMQPMessage(
            json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            [
                'content_type' => 'application/json',
                'delivery_mode' => 2,
            ]
        );

        $channel->basic_publish($message, '', $queue);

        $channel->close();
        $connection->close();
    }
}

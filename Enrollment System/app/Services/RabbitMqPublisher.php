<?php

namespace App\Services;

use Illuminate\Support\Str;
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

    public function publish(string $routingKey, array $payload): void
    {
        $connection = $this->connection();
        $channel = $connection->channel();

        $config = config('ems.rabbitmq');
        $this->declareExchange($channel, $config['exchange']);

        $message = new AMQPMessage(
            json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            [
                'content_type' => 'application/json',
                'delivery_mode' => 2,
            ]
        );

        $channel->basic_publish($message, $config['exchange'], $routingKey);

        $channel->close();
        $connection->close();
    }

    public function publishEvent(string $routingKey, string $eventType, array $payload, ?string $correlationId = null, ?array $actor = null): void
    {
        $this->publish($routingKey, [
            'event_id' => (string) Str::uuid(),
            'event_type' => $eventType,
            'event_version' => 1,
            'occurred_at' => now()->toISOString(),
            'correlation_id' => $correlationId ?: (string) Str::uuid(),
            'actor' => $actor ?? [
                'type' => auth()->check() ? 'user' : 'system',
                'id' => auth()->id(),
                'name' => auth()->user()?->name,
                'role' => auth()->user()?->role,
            ],
            'metadata' => [
                'producer' => config('app.name'),
                'routing_key' => $routingKey,
                'retry_count' => 0,
            ],
            'payload' => $payload,
        ]);
    }

    protected function declareExchange($channel, string $exchange): void
    {
        $channel->exchange_declare($exchange, 'topic', false, true, false);
    }
}

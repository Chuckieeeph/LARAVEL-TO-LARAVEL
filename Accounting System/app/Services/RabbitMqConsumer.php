<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

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
        $config = config('rabbitmq');
        $this->declareTopology($channel, $config, $queue);
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

    protected function declareTopology($channel, array $config, string $queue): void
    {
        $exchange = $config['exchange'];
        $dlx = $exchange.'.dlx';
        $retryExchange = $exchange.'.retry';

        $channel->exchange_declare($exchange, 'topic', false, true, false);
        $channel->exchange_declare($dlx, 'direct', false, true, false);
        $channel->exchange_declare($retryExchange, 'topic', false, true, false);

        $channel->queue_declare($queue, false, true, false, false, false, new AMQPTable([
            'x-dead-letter-exchange' => $dlx,
            'x-dead-letter-routing-key' => 'dead',
        ]));

        $channel->queue_declare($config['dead_letter_queue'], false, true, false, false);
        $channel->queue_declare($config['retry_queue'], false, true, false, false, false, new AMQPTable([
            'x-message-ttl' => 30000,
            'x-dead-letter-exchange' => $exchange,
        ]));

        $channel->queue_bind($queue, $exchange, 'student.#');
        $channel->queue_bind($queue, $exchange, 'enrollment.#');
        $channel->queue_bind($queue, $exchange, 'assessment.#');
        $channel->queue_bind($queue, $exchange, 'payment.#');
        $channel->queue_bind($config['dead_letter_queue'], $dlx, 'dead');
        $channel->queue_bind($config['retry_queue'], $retryExchange, 'student.#');
        $channel->queue_bind($config['retry_queue'], $retryExchange, 'enrollment.#');
        $channel->queue_bind($config['retry_queue'], $retryExchange, 'assessment.#');
        $channel->queue_bind($config['retry_queue'], $retryExchange, 'payment.#');
    }
}

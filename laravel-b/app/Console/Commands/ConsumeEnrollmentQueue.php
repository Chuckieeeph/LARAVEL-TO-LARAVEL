<?php

namespace App\Console\Commands;

use App\Services\AccountingEnrollmentProcessor;
use App\Services\RabbitMqConsumer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class ConsumeEnrollmentQueue extends Command
{
    protected $signature = 'rabbitmq:consume-enrollments {--queue=}';

    protected $description = 'Continuously consume enrollment messages from RabbitMQ and create accounting records.';

    public function handle(RabbitMqConsumer $consumer, AccountingEnrollmentProcessor $processor): int
    {
        $queue = $this->option('queue') ?: config('communication.rabbitmq_queue', 'enrollment_queue');
        $this->info("Listening on queue: {$queue}");

        $consumer->consume($queue, function (AMQPMessage $message) use ($processor): void {
            $deliveryTag = $message->getDeliveryTag();
            $channel = $message->getChannel();

            try {
                $payload = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $processor->process($payload);

                $channel->basic_ack($deliveryTag);
            } catch (Throwable $throwable) {
                Log::error('RabbitMQ enrollment consumer failed', [
                    'error' => $throwable->getMessage(),
                    'body' => $message->getBody(),
                ]);

                $channel->basic_nack($deliveryTag, false, false);
            }
        });

        return self::SUCCESS;
    }
}

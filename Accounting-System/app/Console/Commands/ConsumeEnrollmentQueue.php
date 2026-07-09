<?php

namespace App\Console\Commands;

use App\Services\AccountingEnrollmentProcessor;
use App\Services\CourseSyncProcessor;
use App\Services\EnrollmentActivityLogger;
use App\Services\RabbitMqConsumer;
use App\Services\SubjectSyncProcessor;
use App\Services\StudentSyncProcessor;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpAmqpLib\Message\AMQPMessage;
use Throwable;

class ConsumeEnrollmentQueue extends Command
{
    protected $signature = 'rabbitmq:consume-enrollments {--queue=}';

    protected $description = 'Continuously consume enrollment messages from RabbitMQ and create accounting records.';

    public function handle(
        RabbitMqConsumer $consumer,
        AccountingEnrollmentProcessor $enrollmentProcessor,
        StudentSyncProcessor $studentProcessor,
        CourseSyncProcessor $courseProcessor,
        SubjectSyncProcessor $subjectProcessor,
        EnrollmentActivityLogger $activityLogger
    ): int
    {
        $queue = $this->option('queue') ?: config('rabbitmq.queue', 'accounting.events');
        $this->info("Listening on queue: {$queue}");

        $consumer->consume($queue, function (AMQPMessage $message) use ($enrollmentProcessor, $studentProcessor, $courseProcessor, $subjectProcessor, $activityLogger): void {
            $deliveryTag = $message->getDeliveryTag();
            $channel = $message->getChannel();

            try {
                $payload = json_decode($message->getBody(), true, 512, JSON_THROW_ON_ERROR);
                $eventType = $payload['event_type'] ?? 'EnrollmentSubmitted';
                $activityLogger->logReceived($payload);

                if (in_array($eventType, ['StudentRegistered', 'StudentProfileUpdated', 'StudentArchived'], true)) {
                    $studentProcessor->process($payload);
                    $activityLogger->logProcessed($payload);
                } elseif (in_array($eventType, ['CourseCreated', 'CourseUpdated', 'CourseDeleted'], true)) {
                    $courseProcessor->process($payload);
                    $activityLogger->logProcessed($payload);
                } elseif (in_array($eventType, ['SubjectCreated', 'SubjectUpdated', 'SubjectDeleted'], true)) {
                    $subjectProcessor->process($payload);
                    $activityLogger->logProcessed($payload);
                } elseif ($eventType === 'EnrollmentSubmitted') {
                    $enrollmentProcessor->process($payload);
                    $activityLogger->logProcessed($payload);
                } else {
                    $activityLogger->logIgnored($payload);
                    Log::info('RabbitMQ enrollment consumer logged an event without domain processing', [
                        'event_type' => $eventType,
                        'routing_key' => $payload['metadata']['routing_key'] ?? null,
                    ]);
                }

                $channel->basic_ack($deliveryTag);
            } catch (Throwable $throwable) {
                $payload = isset($payload) && is_array($payload) ? $payload : json_decode($message->getBody(), true) ?? [];
                $metadata = $payload['metadata'] ?? [];
                $retryCount = (int) ($metadata['retry_count'] ?? 0);
                $routingKey = $metadata['routing_key'] ?? $this->routingKeyForEvent($payload['event_type'] ?? 'EnrollmentSubmitted');
                $activityLogger->logRetrying($payload, $throwable->getMessage());

                Log::error('RabbitMQ enrollment consumer failed', [
                    'error' => $throwable->getMessage(),
                    'body' => $message->getBody(),
                    'event_type' => $payload['event_type'] ?? null,
                    'retry_count' => $retryCount,
                ]);

                if ($retryCount < 3) {
                    $payload['metadata']['retry_count'] = $retryCount + 1;
                    $retryMessage = new AMQPMessage(
                        json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        [
                            'content_type' => 'application/json',
                            'delivery_mode' => 2,
                        ]
                    );

                    $channel->basic_publish(
                        $retryMessage,
                        config('rabbitmq.exchange', 'school.events').'.retry',
                        $routingKey
                    );

                    $channel->basic_ack($deliveryTag);

                    return;
                }

                $activityLogger->logFailed($payload, $throwable->getMessage());
                $channel->basic_nack($deliveryTag, false, false);
            }
        });

        return self::SUCCESS;
    }

    protected function routingKeyForEvent(string $eventType): string
    {
        return match ($eventType) {
            'StudentRegistered' => 'student.registered',
            'StudentProfileUpdated' => 'student.profile.updated',
            'StudentArchived' => 'student.archived',
            'EnrollmentSubmitted' => 'enrollment.submitted',
            'CourseCreated' => 'course.created',
            'CourseUpdated' => 'course.updated',
            'CourseDeleted' => 'course.deleted',
            'SubjectCreated' => 'subject.created',
            'SubjectUpdated' => 'subject.updated',
            'SubjectDeleted' => 'subject.deleted',
            default => Str::of($eventType)->snake()->replace('_', '.')->toString(),
        };
    }
}

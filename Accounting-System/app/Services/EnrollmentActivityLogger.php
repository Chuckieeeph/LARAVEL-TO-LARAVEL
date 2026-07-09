<?php

namespace App\Services;

use App\Models\EnrollmentActivityLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EnrollmentActivityLogger
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function log(array $payload, string $status = 'received', ?string $errorMessage = null): EnrollmentActivityLog
    {
        $eventId = (string) ($payload['event_id'] ?? Str::uuid());
        $eventType = (string) ($payload['event_type'] ?? 'UnknownEvent');
        $metadata = Arr::get($payload, 'metadata', []);

        $log = EnrollmentActivityLog::firstOrCreate(
            ['event_id' => $eventId],
            [
                'event_type' => $eventType,
                'routing_key' => Arr::get($metadata, 'routing_key'),
                'entity_type' => $this->entityTypeForEvent($eventType),
                'entity_identifier' => $this->entityIdentifierForEvent($payload, $eventType),
                'action' => $this->actionForEvent($eventType),
                'actor_name' => Arr::get($payload, 'actor.name'),
                'processing_status' => 'received',
                'error_message' => null,
                'payload' => $payload,
                'received_at' => now(),
            ]
        );

        $log->fill([
            'event_type' => $eventType,
            'routing_key' => Arr::get($metadata, 'routing_key'),
            'entity_type' => $this->entityTypeForEvent($eventType),
            'entity_identifier' => $this->entityIdentifierForEvent($payload, $eventType),
            'action' => $this->actionForEvent($eventType),
            'actor_name' => Arr::get($payload, 'actor.name'),
            'processing_status' => $status,
            'error_message' => $errorMessage,
            'payload' => $payload,
        ]);

        $log->processed_at = match ($status) {
            'received' => null,
            'processed', 'failed', 'retrying', 'ignored' => now(),
            default => $log->processed_at,
        };

        $log->save();

        return $log;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function logReceived(array $payload): EnrollmentActivityLog
    {
        return $this->log($payload, 'received');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function logProcessed(array $payload): EnrollmentActivityLog
    {
        return $this->log($payload, 'processed');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function logIgnored(array $payload): EnrollmentActivityLog
    {
        return $this->log($payload, 'ignored');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function logRetrying(array $payload, ?string $errorMessage = null): EnrollmentActivityLog
    {
        return $this->log($payload, 'retrying', $errorMessage);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function logFailed(array $payload, ?string $errorMessage = null): EnrollmentActivityLog
    {
        return $this->log($payload, 'failed', $errorMessage);
    }

    protected function entityTypeForEvent(string $eventType): ?string
    {
        return match ($eventType) {
            'StudentRegistered', 'StudentProfileUpdated', 'StudentArchived' => 'student',
            'CourseCreated', 'CourseUpdated', 'CourseDeleted' => 'course',
            'SubjectCreated', 'SubjectUpdated', 'SubjectDeleted' => 'subject',
            'EnrollmentSubmitted' => 'enrollment',
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function entityIdentifierForEvent(array $payload, string $eventType): ?string
    {
        return match ($eventType) {
            'StudentRegistered', 'StudentProfileUpdated', 'StudentArchived' => (string) Arr::get($payload, 'student_number'),
            'CourseCreated', 'CourseUpdated', 'CourseDeleted' => (string) Arr::get($payload, 'course.course_code'),
            'SubjectCreated', 'SubjectUpdated', 'SubjectDeleted' => (string) Arr::get($payload, 'subject.subject_code'),
            'EnrollmentSubmitted' => (string) Arr::get($payload, 'reference_number'),
            default => (string) (Arr::get($payload, 'reference_number') ?? Arr::get($payload, 'student_number') ?? Arr::get($payload, 'course.course_code') ?? Arr::get($payload, 'subject.subject_code') ?? ''),
        } ?: null;
    }

    protected function actionForEvent(string $eventType): ?string
    {
        return match ($eventType) {
            'StudentRegistered', 'CourseCreated', 'SubjectCreated' => 'created',
            'StudentProfileUpdated', 'CourseUpdated', 'SubjectUpdated' => 'updated',
            'StudentArchived', 'CourseDeleted', 'SubjectDeleted' => 'deleted',
            'EnrollmentSubmitted' => 'submitted',
            default => null,
        };
    }
}

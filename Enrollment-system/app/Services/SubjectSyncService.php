<?php

namespace App\Services;

use App\Models\Subject;

class SubjectSyncService
{
    public function __construct(protected RabbitMqPublisher $publisher)
    {
    }

    public function publishCreated(Subject $subject): void
    {
        $this->publish('subject.created', 'SubjectCreated', $subject);
    }

    public function publishUpdated(Subject $subject): void
    {
        $this->publish('subject.updated', 'SubjectUpdated', $subject);
    }

    public function publishDeleted(Subject $subject): void
    {
        $this->publish('subject.deleted', 'SubjectDeleted', $subject);
    }

    protected function publish(string $routingKey, string $eventType, Subject $subject): void
    {
        $subject->loadMissing('course');

        $this->publisher->publishEvent($routingKey, $eventType, [
            'subject' => $subject->only([
                'subject_code',
                'subject_name',
                'units',
                'semester',
            ]),
            'course' => $subject->course?->only([
                'course_code',
                'course_name',
                'department',
                'year_level',
            ]),
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Course;

class CourseSyncService
{
    public function __construct(protected RabbitMqPublisher $publisher)
    {
    }

    public function publishCreated(Course $course): void
    {
        $this->publish('course.created', 'CourseCreated', $course);
    }

    public function publishUpdated(Course $course): void
    {
        $this->publish('course.updated', 'CourseUpdated', $course);
    }

    public function publishDeleted(Course $course): void
    {
        $this->publish('course.deleted', 'CourseDeleted', $course);
    }

    protected function publish(string $routingKey, string $eventType, Course $course): void
    {
        $this->publisher->publishEvent($routingKey, $eventType, [
            'course' => $course->only([
                'course_code',
                'course_name',
                'department',
                'year_level',
            ]),
        ]);
    }
}

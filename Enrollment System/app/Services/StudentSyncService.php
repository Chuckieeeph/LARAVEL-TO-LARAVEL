<?php

namespace App\Services;

use App\Models\Student;

class StudentSyncService
{
    public function __construct(protected RabbitMqPublisher $publisher)
    {
    }

    public function publishCreated(Student $student): void
    {
        $this->publish('student.registered', 'StudentRegistered', $student);
    }

    public function publishUpdated(Student $student): void
    {
        $this->publish('student.profile.updated', 'StudentProfileUpdated', $student);
    }

    public function publishDeleted(Student $student): void
    {
        $this->publish('student.archived', 'StudentArchived', $student);
    }

    protected function publish(string $routingKey, string $eventType, Student $student): void
    {
        $this->publisher->publishEvent($routingKey, $eventType, [
            'student_number' => $student->student_number,
            'student' => $student->only([
                'first_name',
                'middle_name',
                'last_name',
                'gender',
                'birth_date',
                'email',
                'phone',
                'address',
            ]),
        ]);
    }
}

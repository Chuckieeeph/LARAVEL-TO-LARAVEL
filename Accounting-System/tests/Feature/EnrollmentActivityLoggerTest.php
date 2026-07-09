<?php

namespace Tests\Feature;

use App\Services\EnrollmentActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentActivityLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_logs_a_course_event_and_tracks_status_changes(): void
    {
        $payload = [
            'event_id' => '11111111-1111-1111-1111-111111111111',
            'event_type' => 'CourseCreated',
            'actor' => [
                'name' => 'Registrar User',
            ],
            'metadata' => [
                'routing_key' => 'course.created',
            ],
            'course' => [
                'course_code' => 'BSIT',
            ],
        ];

        app(EnrollmentActivityLogger::class)->logReceived($payload);
        app(EnrollmentActivityLogger::class)->logProcessed($payload);

        $this->assertDatabaseHas('enrollment_activity_logs', [
            'event_id' => '11111111-1111-1111-1111-111111111111',
            'event_type' => 'CourseCreated',
            'routing_key' => 'course.created',
            'entity_type' => 'course',
            'entity_identifier' => 'BSIT',
            'action' => 'created',
            'actor_name' => 'Registrar User',
            'processing_status' => 'processed',
        ]);
    }

    public function test_it_logs_an_ignored_event(): void
    {
        $payload = [
            'event_id' => '22222222-2222-2222-2222-222222222222',
            'event_type' => 'CourseDeleted',
            'metadata' => [
                'routing_key' => 'course.deleted',
            ],
            'course' => [
                'course_code' => 'BSIT',
            ],
        ];

        app(EnrollmentActivityLogger::class)->logIgnored($payload);

        $this->assertDatabaseHas('enrollment_activity_logs', [
            'event_id' => '22222222-2222-2222-2222-222222222222',
            'processing_status' => 'ignored',
        ]);
    }
}

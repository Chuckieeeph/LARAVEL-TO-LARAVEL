<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\EnrollmentService;
use App\Services\CourseSyncService;
use App\Services\RabbitMqPublisher;
use App\Services\StudentSyncService;
use App\Services\SubjectSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CommunicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_enrollment_service_creates_a_record_and_publishes_json(): void
    {
        $user = User::factory()->create(['role' => 'Registrar']);
        $student = Student::create([
            'student_number' => '2026-0001',
            'first_name' => 'Mark',
            'middle_name' => null,
            'last_name' => 'Fillartos',
            'gender' => 'Male',
            'email' => 'mark@example.com',
            'phone' => '09123456789',
            'address' => 'Sample Address',
            'birth_date' => now()->subYears(20),
        ]);
        $course = Course::create([
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'department' => 'College of Computer Studies',
            'year_level' => 4,
        ]);

        $subjectOne = Subject::create([
            'subject_code' => 'IT101',
            'subject_name' => 'Introduction to Computing',
            'units' => 3,
            'semester' => '1st Semester',
            'course_id' => $course->id,
        ]);
        $subjectTwo = Subject::create([
            'subject_code' => 'IT102',
            'subject_name' => 'Programming Fundamentals',
            'units' => 4,
            'semester' => '1st Semester',
            'course_id' => $course->id,
        ]);

        $publisher = Mockery::mock(RabbitMqPublisher::class);
        $publisher->shouldReceive('publishEvent')
            ->once()
            ->with(
                'enrollment.submitted',
                'EnrollmentSubmitted',
                Mockery::on(function (array $payload): bool {
                    return $payload['student_number'] === '2026-0001'
                        && $payload['reference_number'] !== ''
                        && $payload['total_units'] === 7
                        && $payload['enrollment_status'] === 'enrolled'
                        && in_array('Mark Fillartos', $payload['group_members'], true)
                        && in_array('Eleah Camille V. Carillo', $payload['group_members'], true)
                        && in_array('Kenrick Saballo', $payload['group_members'], true);
                })
            );

        $this->instance(RabbitMqPublisher::class, $publisher);

        $enrollment = app(EnrollmentService::class)->create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'semester' => '1st Semester',
            'school_year' => '2026-2027',
            'subject_ids' => [$subjectOne->id, $subjectTwo->id],
        ], $user->id);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'student_id' => $student->id,
            'course_id' => $course->id,
            'total_units' => 7,
            'status' => 'enrolled',
        ]);

        $this->assertSame(2, $enrollment->subjects()->count());
    }

    public function test_the_student_sync_service_publishes_student_payload(): void
    {
        $student = Student::create([
            'student_number' => '2026-0009',
            'first_name' => 'Jane',
            'middle_name' => 'D.',
            'last_name' => 'Cruz',
            'gender' => 'Female',
            'email' => 'jane@example.com',
            'phone' => '09171234567',
            'address' => 'Sample Address',
            'birth_date' => now()->subYears(19),
        ]);

        $publisher = Mockery::mock(RabbitMqPublisher::class);
        $publisher->shouldReceive('publishEvent')
            ->once()
            ->with(
                'student.registered',
                'StudentRegistered',
                Mockery::on(function (array $payload) use ($student): bool {
                    return $payload['student_number'] === $student->student_number
                        && $payload['student_number'] === $student->student_number
                        && $payload['student']['first_name'] === 'Jane'
                        && $payload['student']['last_name'] === 'Cruz';
                })
            );

        $this->instance(RabbitMqPublisher::class, $publisher);

        app(StudentSyncService::class)->publishCreated($student);
    }

    public function test_the_course_sync_service_publishes_course_payload(): void
    {
        $course = Course::create([
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'department' => 'College of Computer Studies',
            'year_level' => 4,
        ]);

        $publisher = Mockery::mock(RabbitMqPublisher::class);
        $publisher->shouldReceive('publishEvent')
            ->once()
            ->with(
                'course.created',
                'CourseCreated',
                Mockery::on(function (array $payload) use ($course): bool {
                    return $payload['course']['course_code'] === $course->course_code
                        && $payload['course']['course_name'] === $course->course_name
                        && $payload['course']['year_level'] === 4;
                })
            );

        $this->instance(RabbitMqPublisher::class, $publisher);

        app(CourseSyncService::class)->publishCreated($course);
    }

    public function test_the_subject_sync_service_publishes_subject_payload(): void
    {
        $course = Course::create([
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'department' => 'College of Computer Studies',
            'year_level' => 4,
        ]);
        $subject = Subject::create([
            'subject_code' => 'IT101',
            'subject_name' => 'Introduction to Computing',
            'units' => 3,
            'semester' => '1st Semester',
            'course_id' => $course->id,
        ]);

        $publisher = Mockery::mock(RabbitMqPublisher::class);
        $publisher->shouldReceive('publishEvent')
            ->once()
            ->with(
                'subject.created',
                'SubjectCreated',
                Mockery::on(function (array $payload) use ($subject): bool {
                    return $payload['subject']['subject_code'] === $subject->subject_code
                        && $payload['subject']['subject_name'] === $subject->subject_name
                        && $payload['course']['course_code'] === 'BSIT';
                })
            );

        $this->instance(RabbitMqPublisher::class, $publisher);

        app(SubjectSyncService::class)->publishCreated($subject);
    }
}

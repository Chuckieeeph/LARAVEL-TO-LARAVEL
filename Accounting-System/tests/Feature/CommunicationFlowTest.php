<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\FeeSchedule;
use App\Models\Enrollment;
use App\Models\LedgerEntry;
use App\Models\Subject;
use App\Services\AccountingEnrollmentProcessor;
use App\Services\CourseSyncProcessor;
use App\Services\SubjectSyncProcessor;
use App\Services\StudentSyncProcessor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class CommunicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_consumer_processor_creates_financial_records_and_logs_members(): void
    {
        Log::shouldReceive('info')->once();

        FeeSchedule::create([
            'course_code' => 'BSIT',
            'semester' => '1st Semester',
            'school_year' => '2026-2027',
            'per_unit_rate' => 350.00,
            'registration_fee' => 500.00,
            'miscellaneous_fee' => 1500.00,
            'laboratory_fee' => 1200.00,
            'other_fee' => 250.00,
            'is_active' => true,
        ]);

        $payload = [
            'event_type' => 'EnrollmentSubmitted',
            'reference_number' => 'EMS-20260707-ABC123',
            'student_number' => '2026-0001',
            'student' => [
                'first_name' => 'Mark',
                'middle_name' => null,
                'last_name' => 'Fillartos',
                'email' => 'mark@example.com',
                'status' => 'active',
            ],
            'course' => [
                'course_code' => 'BSIT',
                'course_name' => 'Bachelor of Science in Information Technology',
                'year_level' => 4,
            ],
            'semester' => '1st Semester',
            'school_year' => '2026-2027',
            'enrolled_subjects' => [
                ['subject_code' => 'IT101', 'subject_name' => 'Intro to Computing', 'units' => 3],
                ['subject_code' => 'IT102', 'subject_name' => 'Programming Fundamentals', 'units' => 4],
            ],
            'total_units' => 7,
            'enrollment_status' => 'enrolled',
            'group_members' => [
                'Mark Fillartos',
                'Eleah Camille V. Carillo',
                'Kenrick Saballo',
            ],
        ];

        $assessment = app(AccountingEnrollmentProcessor::class)->process($payload);

        $this->assertInstanceOf(Assessment::class, $assessment);
        $this->assertDatabaseHas('students', [
            'student_number' => '2026-0001',
            'first_name' => 'Mark',
            'last_name' => 'Fillartos',
            'course_code' => 'BSIT',
        ]);
        $this->assertDatabaseHas('courses', [
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('subjects', [
            'subject_code' => 'IT101',
            'subject_name' => 'Intro to Computing',
        ]);
        $this->assertDatabaseHas('enrollments', [
            'enrollment_reference_number' => 'EMS-20260707-ABC123',
            'student_number' => '2026-0001',
            'status' => 'enrolled',
            'total_units' => 7,
        ]);
        $this->assertDatabaseHas('financial_accounts', [
            'student_id' => $assessment->student_id,
            'status' => 'Pending Assessment',
        ]);
        $this->assertDatabaseHas('assessments', [
            'enrollment_reference_number' => 'EMS-20260707-ABC123',
            'total_units' => 7,
            'total_amount' => 5900.00,
            'status' => 'assessed',
        ]);
        $this->assertDatabaseHas('ledger_entries', [
            'reference_number' => 'EMS-20260707-ABC123',
            'entry_type' => 'assessment',
            'debit' => 5900.00,
            'credit' => 0.00,
            'balance' => 5900.00,
        ]);

        $this->assertSame(1, LedgerEntry::count());
        $this->assertSame(1, Enrollment::count());
        $this->assertSame(2, Subject::count());
    }

    public function test_the_student_sync_processor_upserts_a_student_record(): void
    {
        Log::shouldReceive('info')->once();

        $payload = [
            'event_type' => 'StudentRegistered',
            'student_number' => '2026-0002',
            'student' => [
                'first_name' => 'Jane',
                'middle_name' => 'D.',
                'last_name' => 'Cruz',
                'gender' => 'Female',
                'birth_date' => '2005-02-15',
                'email' => 'jane@example.com',
                'phone' => '09171234567',
                'address' => 'Sample Address',
                'status' => 'active',
            ],
        ];

        $student = app(StudentSyncProcessor::class)->process($payload);

        $this->assertSame('2026-0002', $student->student_number);
        $this->assertDatabaseHas('students', [
            'student_number' => '2026-0002',
            'first_name' => 'Jane',
            'last_name' => 'Cruz',
            'gender' => 'Female',
            'email' => 'jane@example.com',
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('financial_accounts', [
            'student_id' => $student->id,
            'status' => 'Pending Assessment',
        ]);
    }

    public function test_the_course_sync_processor_mirrors_course_changes(): void
    {
        Log::shouldReceive('info')->once();

        $course = app(CourseSyncProcessor::class)->process([
            'event_type' => 'CourseCreated',
            'course' => [
                'course_code' => 'BSBA',
                'course_name' => 'Bachelor of Science in Business Administration',
                'department' => 'College of Business',
                'year_level' => 4,
            ],
        ]);

        $this->assertSame('BSBA', $course->course_code);
        $this->assertDatabaseHas('courses', [
            'course_code' => 'BSBA',
            'course_name' => 'Bachelor of Science in Business Administration',
            'department' => 'College of Business',
            'status' => 'active',
        ]);
    }

    public function test_the_subject_sync_processor_mirrors_subject_changes(): void
    {
        Log::shouldReceive('info')->once();

        $course = Course::create([
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'department' => 'College of Computer Studies',
            'year_level' => 4,
            'status' => 'active',
        ]);

        $subject = app(SubjectSyncProcessor::class)->process([
            'event_type' => 'SubjectCreated',
            'subject' => [
                'subject_code' => 'IT101',
                'subject_name' => 'Introduction to Computing',
                'units' => 3,
                'semester' => '1st Semester',
                'course_id' => $course->id,
            ],
            'course' => [
                'course_code' => 'BSIT',
                'course_name' => 'Bachelor of Science in Information Technology',
                'department' => 'College of Computer Studies',
                'year_level' => 4,
            ],
        ]);

        $this->assertSame('IT101', $subject->subject_code);
        $this->assertDatabaseHas('subjects', [
            'subject_code' => 'IT101',
            'subject_name' => 'Introduction to Computing',
            'course_id' => $course->id,
            'status' => 'active',
        ]);
    }
}

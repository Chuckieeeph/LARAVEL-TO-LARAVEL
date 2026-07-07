<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\FeeSchedule;
use App\Models\LedgerEntry;
use App\Services\AccountingEnrollmentProcessor;
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
    }
}

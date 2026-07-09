<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\FeeSchedule;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatementOfAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_statement_api_returns_balances_and_payment_history(): void
    {
        $student = Student::create([
            'student_number' => '2026-0009',
            'first_name' => 'Ana',
            'middle_name' => null,
            'last_name' => 'Santos',
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'year_level' => 2,
            'email' => 'ana@example.com',
            'status' => 'active',
        ]);

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

        $assessment = Assessment::create([
            'student_id' => $student->id,
            'enrollment_reference_number' => 'EMS-20260709-STATEMENT',
            'course_code' => 'BSIT',
            'course_name' => 'Bachelor of Science in Information Technology',
            'semester' => '1st Semester',
            'school_year' => '2026-2027',
            'total_units' => 7,
            'per_unit_rate' => 350.00,
            'tuition_fee' => 2450.00,
            'registration_fee' => 500.00,
            'miscellaneous_fee' => 1500.00,
            'laboratory_fee' => 1200.00,
            'other_fee' => 250.00,
            'total_amount' => 5900.00,
            'status' => 'partially_paid',
            'assessed_at' => now(),
        ]);

        $payment = Payment::create([
            'assessment_id' => $assessment->id,
            'payment_reference' => 'OR-20260709-0001',
            'amount' => 2000.00,
            'payment_method' => 'Cash',
            'received_by' => null,
            'paid_at' => now(),
            'remarks' => 'Initial payment',
        ]);

        LedgerEntry::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'payment_id' => null,
            'entry_type' => 'assessment',
            'reference_number' => $assessment->enrollment_reference_number,
            'description' => 'Initial assessment',
            'debit' => 5900.00,
            'credit' => 0.00,
            'balance' => 5900.00,
            'meta' => [],
        ]);

        LedgerEntry::create([
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'payment_id' => $payment->id,
            'entry_type' => 'payment',
            'reference_number' => $payment->payment_reference,
            'description' => 'Partial payment',
            'debit' => 0.00,
            'credit' => 2000.00,
            'balance' => 3900.00,
            'meta' => [
                'payment_method' => 'Cash',
            ],
        ]);

        $response = $this->getJson("/api/students/{$student->id}/statement-of-account");

        $response->assertOk()
            ->assertJsonPath('student.student_number', '2026-0009')
            ->assertJsonPath('summary.assessment_count', 1)
            ->assertJsonPath('summary.payment_count', 1)
            ->assertJsonPath('summary.total_assessment_amount', 5900)
            ->assertJsonPath('summary.total_payments', 2000)
            ->assertJsonPath('summary.current_balance', 3900);
    }
}

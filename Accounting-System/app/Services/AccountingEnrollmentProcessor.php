<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\FinancialAccount;
use App\Models\FeeSchedule;
use App\Models\EnrollmentLog;
use App\Models\LedgerEntry;
use App\Services\EnrollmentSyncProcessor;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class AccountingEnrollmentProcessor
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function process(array $payload): Assessment
    {
        $data = Validator::make($payload, [
            'reference_number' => ['required', 'string', 'max:100'],
            'student_number' => ['required', 'string', 'max:50'],
            'student' => ['required', 'array'],
            'student.first_name' => ['required', 'string', 'max:255'],
            'student.last_name' => ['required', 'string', 'max:255'],
            'course' => ['required', 'array'],
            'course.course_code' => ['required', 'string', 'max:50'],
            'course.course_name' => ['required', 'string', 'max:255'],
            'course.department' => ['nullable', 'string', 'max:255'],
            'course.year_level' => ['nullable', 'integer', 'min:1'],
            'semester' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
            'enrolled_subjects' => ['required', 'array', 'min:1'],
            'enrolled_subjects.*.subject_code' => ['required', 'string', 'max:50'],
            'enrolled_subjects.*.subject_name' => ['required', 'string', 'max:255'],
            'enrolled_subjects.*.units' => ['required', 'integer', 'min:1'],
            'enrolled_subjects.*.semester' => ['nullable', 'string', 'max:100'],
            'total_units' => ['required', 'integer', 'min:1'],
            'enrollment_status' => ['required', 'string', 'max:50'],
        ])->validate();

        $log = EnrollmentLog::updateOrCreate(
            ['enrollment_reference_number' => $data['reference_number']],
            [
                'student_number' => $data['student_number'],
                'student_name' => trim(implode(' ', array_filter([
                    Arr::get($data, 'student.first_name'),
                    Arr::get($data, 'student.middle_name'),
                    Arr::get($data, 'student.last_name'),
                ]))),
                'course_code' => Arr::get($data, 'course.course_code'),
                'course_name' => Arr::get($data, 'course.course_name'),
                'semester' => $data['semester'],
                'school_year' => $data['school_year'],
                'total_units' => (int) $data['total_units'],
                'enrollment_status' => $data['enrollment_status'],
                'processing_status' => 'received',
                'error_message' => null,
                'payload' => $payload,
                'received_at' => now(),
                'processed_at' => null,
            ]
        );

        try {
            return DB::transaction(function () use ($payload, $log): Assessment {
                $enrollment = app(EnrollmentSyncProcessor::class)->process($payload);
                $enrollment->loadMissing(['student', 'course', 'subjects']);
                $student = $enrollment->student;

                FinancialAccount::firstOrCreate(
                    ['student_id' => $student->id],
                    [
                        'account_number' => 'FA-'.strtoupper(str_replace(' ', '', $student->student_number)),
                        'status' => 'Pending Assessment',
                        'balance' => 0,
                        'opened_at' => now(),
                    ]
                );

                $feeSchedule = FeeSchedule::query()
                    ->where('course_code', $enrollment->course?->course_code)
                    ->where('semester', $enrollment->semester)
                    ->where('school_year', $enrollment->school_year)
                    ->where('is_active', true)
                    ->first();

                if (! $feeSchedule) {
                    throw ValidationException::withMessages([
                        'course' => 'No active fee schedule exists for the enrolled course and term.',
                    ]);
                }

                $totalUnits = (int) $enrollment->total_units;
                $tuitionFee = round($totalUnits * (float) $feeSchedule->per_unit_rate, 2);
                $totalAmount = round(
                    $tuitionFee
                    + (float) $feeSchedule->registration_fee
                    + (float) $feeSchedule->miscellaneous_fee
                    + (float) $feeSchedule->laboratory_fee
                    + (float) $feeSchedule->other_fee,
                    2
                );

                $assessment = Assessment::updateOrCreate(
                    ['enrollment_reference_number' => $enrollment->enrollment_reference_number],
                    [
                        'student_id' => $student->id,
                        'course_code' => $enrollment->course?->course_code,
                        'course_name' => $enrollment->course?->course_name,
                        'semester' => $enrollment->semester,
                        'school_year' => $enrollment->school_year,
                        'total_units' => $totalUnits,
                        'per_unit_rate' => $feeSchedule->per_unit_rate,
                        'tuition_fee' => $tuitionFee,
                        'registration_fee' => $feeSchedule->registration_fee,
                        'miscellaneous_fee' => $feeSchedule->miscellaneous_fee,
                        'laboratory_fee' => $feeSchedule->laboratory_fee,
                        'other_fee' => $feeSchedule->other_fee,
                        'total_amount' => $totalAmount,
                        'status' => 'assessed',
                        'payload' => $payload,
                        'assessed_at' => now(),
                    ]
                );

                LedgerEntry::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'reference_number' => $assessment->enrollment_reference_number,
                        'entry_type' => 'assessment',
                    ],
                    [
                        'assessment_id' => $assessment->id,
                        'description' => 'Assessment generated from enrollment payload.',
                        'debit' => $totalAmount,
                        'credit' => 0,
                        'balance' => $totalAmount,
                        'meta' => [
                            'group_members' => Arr::get($payload, 'group_members', []),
                            'total_units' => $totalUnits,
                            'enrollment_reference_number' => $enrollment->enrollment_reference_number,
                        ],
                    ]
                );

                $log->update([
                    'assessment_id' => $assessment->id,
                    'processing_status' => 'processed',
                    'error_message' => null,
                    'processed_at' => now(),
                ]);

                Log::info('Accounting System assessment generated from RabbitMQ payload', [
                    'reference_number' => $assessment->enrollment_reference_number,
                    'student_number' => $student->student_number,
                    'group_members' => Arr::get($payload, 'group_members', []),
                    'total_units' => $totalUnits,
                    'total_amount' => $totalAmount,
                ]);

                return $assessment;
            });
        } catch (Throwable $throwable) {
            $log->update([
                'processing_status' => 'failed',
                'error_message' => $throwable->getMessage(),
                'processed_at' => now(),
            ]);

            throw $throwable;
        }
    }
}

<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\FeeSchedule;
use App\Models\LedgerEntry;
use App\Models\Student;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
            'semester' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
            'enrolled_subjects' => ['required', 'array', 'min:1'],
            'enrolled_subjects.*.units' => ['required', 'integer', 'min:1'],
            'total_units' => ['required', 'integer', 'min:1'],
            'enrollment_status' => ['required', 'string', 'max:50'],
        ])->validate();

        return DB::transaction(function () use ($data, $payload): Assessment {
            $student = Student::updateOrCreate(
                ['student_number' => $data['student_number']],
                [
                    'first_name' => Arr::get($data, 'student.first_name'),
                    'middle_name' => Arr::get($data, 'student.middle_name'),
                    'last_name' => Arr::get($data, 'student.last_name'),
                    'course_code' => Arr::get($data, 'course.course_code'),
                    'course_name' => Arr::get($data, 'course.course_name'),
                    'year_level' => (int) Arr::get($data, 'course.year_level', 1),
                    'email' => Arr::get($data, 'student.email'),
                    'status' => Arr::get($data, 'student.status', 'active'),
                ]
            );

            $feeSchedule = FeeSchedule::query()
                ->where('course_code', Arr::get($data, 'course.course_code'))
                ->where('semester', $data['semester'])
                ->where('school_year', $data['school_year'])
                ->where('is_active', true)
                ->first();

            if (! $feeSchedule) {
                throw ValidationException::withMessages([
                    'course' => 'No active fee schedule exists for the enrolled course and term.',
                ]);
            }

            $totalUnits = (int) $data['total_units'];
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
                ['enrollment_reference_number' => $data['reference_number']],
                [
                    'student_id' => $student->id,
                    'course_code' => Arr::get($data, 'course.course_code'),
                    'course_name' => Arr::get($data, 'course.course_name'),
                    'semester' => $data['semester'],
                    'school_year' => $data['school_year'],
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
                    ],
                ]
            );

            Log::info('Laravel B assessment generated from RabbitMQ payload', [
                'reference_number' => $assessment->enrollment_reference_number,
                'student_number' => $student->student_number,
                'group_members' => Arr::get($payload, 'group_members', []),
                'total_units' => $totalUnits,
                'total_amount' => $totalAmount,
            ]);

            return $assessment;
        });
    }
}

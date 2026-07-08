<?php

namespace App\Services;

use App\Models\FinancialAccount;
use App\Models\Student;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class StudentSyncProcessor
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function process(array $payload): Student
    {
        $data = Validator::make($payload, [
            'event_type' => ['required', 'string', 'in:StudentRegistered,StudentProfileUpdated,StudentArchived'],
            'student_number' => ['required', 'string', 'max:50'],
            'student' => ['required', 'array'],
            'student.first_name' => ['required', 'string', 'max:255'],
            'student.last_name' => ['required', 'string', 'max:255'],
            'student.middle_name' => ['nullable', 'string', 'max:255'],
            'student.gender' => ['nullable', 'string', 'max:20'],
            'student.birth_date' => ['nullable', 'date'],
            'student.email' => ['nullable', 'email', 'max:255'],
            'student.phone' => ['nullable', 'string', 'max:50'],
            'student.address' => ['nullable', 'string'],
            'student.status' => ['nullable', 'string', 'max:50'],
        ])->validate();

        $studentData = [
            'first_name' => $data['student']['first_name'],
            'middle_name' => $data['student']['middle_name'] ?? null,
            'last_name' => $data['student']['last_name'],
            'gender' => $data['student']['gender'] ?? null,
            'birth_date' => $data['student']['birth_date'] ?? null,
            'email' => $data['student']['email'] ?? null,
            'phone' => $data['student']['phone'] ?? null,
            'address' => $data['student']['address'] ?? null,
        ];

        if ($data['event_type'] === 'StudentArchived') {
            $studentData['status'] = 'deleted';
        } elseif (array_key_exists('status', $data['student'])) {
            $studentData['status'] = $data['student']['status'];
        }

        $student = Student::updateOrCreate(
            ['student_number' => $data['student_number']],
            $studentData
        );

        $financialAccount = FinancialAccount::firstOrCreate(
            ['student_id' => $student->id],
            [
                'account_number' => 'FA-'.str_replace(' ', '', Str::upper($student->student_number)),
                'status' => 'Pending Assessment',
                'balance' => 0,
                'opened_at' => now(),
            ]
        );

        if ($data['event_type'] === 'StudentArchived') {
            $financialAccount->update(['status' => 'Archived']);
        }

        Log::info('Accounting System student synced from Enrollment System payload', [
            'event_type' => $data['event_type'],
            'student_number' => $student->student_number,
        ]);

        return $student;
    }
}

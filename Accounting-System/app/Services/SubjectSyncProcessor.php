<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubjectSyncProcessor
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function process(array $payload): Subject
    {
        $data = Validator::make($payload, [
            'event_type' => ['required', 'string', 'in:SubjectCreated,SubjectUpdated,SubjectDeleted'],
            'subject' => ['required', 'array'],
            'subject.subject_code' => ['required', 'string', 'max:50'],
            'subject.subject_name' => ['required', 'string', 'max:255'],
            'subject.units' => ['required', 'integer', 'min:1'],
            'subject.semester' => ['required', 'string', 'max:100'],
            'course' => ['nullable', 'array'],
            'course.course_code' => ['nullable', 'string', 'max:50'],
            'course.course_name' => ['nullable', 'string', 'max:255'],
            'course.department' => ['nullable', 'string', 'max:255'],
            'course.year_level' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $courseId = null;

        if (! empty($data['course']['course_code'])) {
            $course = Course::updateOrCreate(
                ['course_code' => $data['course']['course_code']],
                [
                    'course_name' => $data['course']['course_name'] ?? $data['course']['course_code'],
                    'department' => $data['course']['department'] ?? null,
                    'year_level' => (int) ($data['course']['year_level'] ?? 1),
                    'status' => 'active',
                ]
            );

            $courseId = $course->id;
        }

        $subject = Subject::updateOrCreate(
            ['subject_code' => $data['subject']['subject_code']],
            [
                'subject_name' => $data['subject']['subject_name'],
                'units' => (int) $data['subject']['units'],
                'semester' => $data['subject']['semester'],
                'course_id' => $courseId,
                'status' => $data['event_type'] === 'SubjectDeleted' ? 'deleted' : 'active',
            ]
        );

        Log::info('Accounting System subject synced from Enrollment System payload', [
            'event_type' => $data['event_type'],
            'subject_code' => $subject->subject_code,
        ]);

        return $subject;
    }
}

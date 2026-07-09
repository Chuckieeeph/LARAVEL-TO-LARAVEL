<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CourseSyncProcessor
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function process(array $payload): Course
    {
        $data = Validator::make($payload, [
            'event_type' => ['required', 'string', 'in:CourseCreated,CourseUpdated,CourseDeleted'],
            'course' => ['required', 'array'],
            'course.course_code' => ['required', 'string', 'max:50'],
            'course.course_name' => ['required', 'string', 'max:255'],
            'course.department' => ['nullable', 'string', 'max:255'],
            'course.year_level' => ['nullable', 'integer', 'min:1'],
        ])->validate();

        $courseData = [
            'course_name' => $data['course']['course_name'],
            'department' => $data['course']['department'] ?? null,
            'year_level' => (int) ($data['course']['year_level'] ?? 1),
            'status' => $data['event_type'] === 'CourseDeleted' ? 'deleted' : 'active',
        ];

        $course = Course::updateOrCreate(
            ['course_code' => $data['course']['course_code']],
            $courseData
        );

        if ($data['event_type'] === 'CourseDeleted') {
            Subject::query()
                ->where('course_id', $course->id)
                ->update(['status' => 'deleted']);
        }

        Log::info('Accounting System course synced from Enrollment System payload', [
            'event_type' => $data['event_type'],
            'course_code' => $course->course_code,
        ]);

        return $course;
    }
}

<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class EnrollmentSyncProcessor
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function process(array $payload): Enrollment
    {
        $data = Validator::make($payload, [
            'reference_number' => ['required', 'string', 'max:100'],
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

        $student = Student::updateOrCreate(
            ['student_number' => $data['student_number']],
            [
                'first_name' => Arr::get($data, 'student.first_name'),
                'middle_name' => Arr::get($data, 'student.middle_name'),
                'last_name' => Arr::get($data, 'student.last_name'),
                'gender' => Arr::get($data, 'student.gender'),
                'birth_date' => Arr::get($data, 'student.birth_date'),
                'email' => Arr::get($data, 'student.email'),
                'phone' => Arr::get($data, 'student.phone'),
                'address' => Arr::get($data, 'student.address'),
                'course_code' => Arr::get($data, 'course.course_code'),
                'course_name' => Arr::get($data, 'course.course_name'),
                'year_level' => (int) Arr::get($data, 'course.year_level', 1),
                'status' => Arr::get($data, 'student.status', 'active'),
            ]
        );

        $course = Course::updateOrCreate(
            ['course_code' => Arr::get($data, 'course.course_code')],
            [
                'course_name' => Arr::get($data, 'course.course_name'),
                'department' => Arr::get($data, 'course.department'),
                'year_level' => (int) Arr::get($data, 'course.year_level', 1),
                'status' => 'active',
            ]
        );

        $mirroredSubjects = collect($data['enrolled_subjects'])->map(function (array $subjectData) use ($course): Subject {
            return Subject::updateOrCreate(
                ['subject_code' => $subjectData['subject_code']],
                [
                    'subject_name' => $subjectData['subject_name'],
                    'units' => (int) $subjectData['units'],
                    'semester' => $subjectData['semester'] ?? '1st Semester',
                    'course_id' => $course->id,
                    'status' => 'active',
                ]
            );
        });

        $enrollment = Enrollment::updateOrCreate(
            ['enrollment_reference_number' => $data['reference_number']],
            [
                'student_id' => $student->id,
                'student_number' => $student->student_number,
                'course_id' => $course->id,
                'semester' => $data['semester'],
                'school_year' => $data['school_year'],
                'status' => $data['enrollment_status'],
                'total_units' => (int) $data['total_units'],
                'created_by_name' => Arr::get($payload, 'actor.name'),
                'payload' => $payload,
            ]
        );

        $enrollment->subjects()->sync($mirroredSubjects->pluck('id')->all());

        return $enrollment;
    }
}

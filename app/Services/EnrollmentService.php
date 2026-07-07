<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnrollmentService
{
    public function __construct(protected RabbitMqPublisher $publisher)
    {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data, int $createdBy): Enrollment
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $student = Student::findOrFail($data['student_id']);
            $course = Course::findOrFail($data['course_id']);
            $subjects = Subject::query()
                ->whereIn('id', $data['subject_ids'])
                ->get();

            $totalUnits = (int) $subjects->sum('units');
            $reference = 'EMS-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));

            $enrollment = Enrollment::create([
                'student_id' => $student->id,
                'course_id' => $course->id,
                'semester' => $data['semester'],
                'school_year' => $data['school_year'],
                'status' => 'enrolled',
                'reference_number' => $reference,
                'total_units' => $totalUnits,
                'created_by' => $createdBy,
            ]);

            $enrollment->subjects()->sync($subjects->pluck('id'));

            $this->publisher->publish(config('ems.rabbitmq.queue', 'enrollment_queue'), [
                'reference_number' => $reference,
                'student_number' => $student->student_number,
                'student' => $student->only([
                    'id', 'student_number', 'first_name', 'middle_name', 'last_name', 'gender', 'birth_date', 'email', 'phone', 'address',
                ]),
                'course' => $course->only(['id', 'course_code', 'course_name', 'department', 'year_level']),
                'semester' => $enrollment->semester,
                'school_year' => $enrollment->school_year,
                'enrolled_subjects' => $subjects->map(fn (Subject $subject) => $subject->only(['id', 'subject_code', 'subject_name', 'units', 'semester']))->values()->all(),
                'total_units' => $totalUnits,
                'enrollment_status' => $enrollment->status,
                'group_members' => config('ems.group_members', []),
            ]);

            return $enrollment;
        });
    }
}

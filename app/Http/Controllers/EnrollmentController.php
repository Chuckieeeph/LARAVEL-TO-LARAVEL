<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use App\Services\EnrollmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(): View
    {
        $enrollments = Enrollment::with(['student', 'course'])->latest()->get();

        return view('shared.index', [
            'title' => 'Enrollments',
            'createUrl' => route('enrollments.create'),
            'columns' => [
                'reference_number' => 'Reference',
                'student_name' => 'Student',
                'course_name' => 'Course',
                'semester' => 'Semester',
                'school_year' => 'School Year',
                'total_units' => 'Units',
                'status' => 'Status',
            ],
            'rows' => $enrollments->map(fn (Enrollment $enrollment) => [
                'reference_number' => $enrollment->reference_number,
                'student_name' => trim($enrollment->student?->first_name.' '.$enrollment->student?->last_name),
                'course_name' => $enrollment->course?->course_name ?? '-',
                'semester' => $enrollment->semester,
                'school_year' => $enrollment->school_year,
                'total_units' => $enrollment->total_units,
                'status' => $enrollment->status,
                'show_url' => route('enrollments.show', $enrollment),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        $courses = Course::orderBy('course_name')->pluck('course_name', 'id')->all();
        $students = Student::orderBy('last_name')->get()->mapWithKeys(fn (Student $student) => [
            $student->id => $student->student_number.' - '.trim($student->first_name.' '.$student->last_name),
        ])->all();

        return view('shared.form', [
            'title' => 'Create Enrollment',
            'action' => route('enrollments.store'),
            'method' => 'POST',
            'backUrl' => route('enrollments.index'),
            'fields' => [
                ['name' => 'student_id', 'label' => 'Student', 'type' => 'select', 'options' => $students],
                ['name' => 'course_id', 'label' => 'Course', 'type' => 'select', 'options' => $courses],
                ['name' => 'semester', 'label' => 'Semester', 'type' => 'text'],
                ['name' => 'school_year', 'label' => 'School Year', 'type' => 'text', 'help' => 'Example: 2026-2027'],
                ['name' => 'subject_ids', 'label' => 'Subjects', 'type' => 'multiselect', 'options' => Subject::orderBy('subject_name')->pluck('subject_name', 'id')->all(), 'help' => 'Hold Ctrl or Cmd to select multiple subjects.'],
            ],
        ]);
    }

    public function store(StoreEnrollmentRequest $request, EnrollmentService $service): RedirectResponse
    {
        $enrollment = $service->create($request->validated(), (int) auth()->id());

        return redirect()->route('enrollments.show', $enrollment)->with('success', 'Enrollment saved and published to RabbitMQ.');
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'course', 'subjects']);

        return view('enrollments.show', compact('enrollment'));
    }
}

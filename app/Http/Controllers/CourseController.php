<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::latest()->get();

        return view('shared.index', [
            'title' => 'Courses',
            'createUrl' => route('courses.create'),
            'columns' => [
                'course_code' => 'Code',
                'course_name' => 'Course Name',
                'department' => 'Department',
                'year_level' => 'Year Level',
            ],
            'rows' => $courses->map(fn (Course $course) => [
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'department' => $course->department ?? '-',
                'year_level' => $course->year_level,
                'edit_url' => route('courses.edit', $course),
                'delete_url' => route('courses.destroy', $course),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        return view('shared.form', [
            'title' => 'Create Course',
            'action' => route('courses.store'),
            'method' => 'POST',
            'backUrl' => route('courses.index'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(StoreCourseRequest $request): RedirectResponse
    {
        Course::create($request->validated());

        return redirect()->route('courses.index')->with('success', 'Course created.');
    }

    public function edit(Course $course): View
    {
        return view('shared.form', [
            'title' => 'Edit Course',
            'action' => route('courses.update', $course),
            'method' => 'PUT',
            'backUrl' => route('courses.index'),
            'fields' => $this->fields($course),
        ]);
    }

    public function update(StoreCourseRequest $request, Course $course): RedirectResponse
    {
        $course->update($request->validated());

        return redirect()->route('courses.index')->with('success', 'Course updated.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return back()->with('success', 'Course deleted.');
    }

    protected function fields(?Course $course = null): array
    {
        return [
            ['name' => 'course_code', 'label' => 'Course Code', 'type' => 'text', 'value' => $course?->course_code],
            ['name' => 'course_name', 'label' => 'Course Name', 'type' => 'text', 'value' => $course?->course_name],
            ['name' => 'department', 'label' => 'Department', 'type' => 'text', 'value' => $course?->department],
            ['name' => 'year_level', 'label' => 'Year Level', 'type' => 'number', 'value' => $course?->year_level ?? 1],
        ];
    }
}

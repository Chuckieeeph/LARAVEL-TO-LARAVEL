<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Models\Student;
use App\Services\StudentNumberGenerator;
use App\Services\StudentSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::latest()->get();

        return view('shared.index', [
            'title' => 'Students',
            'createUrl' => route('students.create'),
            'columns' => [
                'student_number' => 'Student #',
                'full_name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
            ],
            'rows' => $students->map(fn (Student $student) => [
                'student_number' => $student->student_number,
                'full_name' => trim($student->first_name.' '.($student->middle_name ? $student->middle_name.' ' : '').$student->last_name),
                'email' => $student->email ?? '-',
                'phone' => $student->phone ?? '-',
                'edit_url' => route('students.edit', $student),
                'delete_url' => route('students.destroy', $student),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        return view('shared.form', [
            'title' => 'Create Student',
            'action' => route('students.store'),
            'method' => 'POST',
            'backUrl' => route('students.index'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(
        StoreStudentRequest $request,
        StudentSyncService $syncService,
        StudentNumberGenerator $numberGenerator
    ): RedirectResponse
    {
        $data = $request->validated();
        $data['student_number'] = $data['student_number'] ?: $numberGenerator->generate();

        $student = DB::transaction(fn (): Student => Student::create($data));
        $syncService->publishCreated($student);

        return redirect()->route('students.index')->with('success', 'Student created.');
    }

    public function edit(Student $student): View
    {
        return view('shared.form', [
            'title' => 'Edit Student',
            'action' => route('students.update', $student),
            'method' => 'PUT',
            'backUrl' => route('students.index'),
            'fields' => $this->fields($student),
        ]);
    }

    public function update(
        StoreStudentRequest $request,
        Student $student,
        StudentSyncService $syncService,
        StudentNumberGenerator $numberGenerator
    ): RedirectResponse
    {
        $data = $request->validated();
        $data['student_number'] = $data['student_number'] ?: $student->student_number ?: $numberGenerator->generate();
        $student->update($data);
        $syncService->publishUpdated($student);

        return redirect()->route('students.index')->with('success', 'Student updated.');
    }

    public function destroy(Student $student, StudentSyncService $syncService): RedirectResponse
    {
        $syncService->publishDeleted($student);
        $student->delete();

        return back()->with('success', 'Student deleted.');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function fields(?Student $student = null): array
    {
        return [
            ['name' => 'student_number', 'label' => 'Student Number', 'type' => 'text', 'value' => $student?->student_number],
            ['name' => 'first_name', 'label' => 'First Name', 'type' => 'text', 'value' => $student?->first_name],
            ['name' => 'middle_name', 'label' => 'Middle Name', 'type' => 'text', 'value' => $student?->middle_name],
            ['name' => 'last_name', 'label' => 'Last Name', 'type' => 'text', 'value' => $student?->last_name],
            ['name' => 'gender', 'label' => 'Gender', 'type' => 'text', 'value' => $student?->gender],
            ['name' => 'birth_date', 'label' => 'Birth Date', 'type' => 'date', 'value' => optional($student?->birth_date)->format('Y-m-d')],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'value' => $student?->email],
            ['name' => 'phone', 'label' => 'Phone', 'type' => 'text', 'value' => $student?->phone],
            ['name' => 'address', 'label' => 'Address', 'type' => 'textarea', 'value' => $student?->address],
        ];
    }
}

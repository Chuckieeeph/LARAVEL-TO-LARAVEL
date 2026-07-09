<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSubjectRequest;
use App\Models\Course;
use App\Models\Subject;
use App\Services\SubjectSyncService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(): View
    {
        $subjects = Subject::with('course')->latest()->get();

        return view('shared.index', [
            'title' => 'Subjects',
            'createUrl' => route('subjects.create'),
            'columns' => [
                'subject_code' => 'Code',
                'subject_name' => 'Subject Name',
                'course_name' => 'Course',
                'units' => 'Units',
                'semester' => 'Semester',
            ],
            'rows' => $subjects->map(fn (Subject $subject) => [
                'subject_code' => $subject->subject_code,
                'subject_name' => $subject->subject_name,
                'course_name' => $subject->course?->course_name ?? '-',
                'units' => $subject->units,
                'semester' => $subject->semester,
                'edit_url' => route('subjects.edit', $subject),
                'delete_url' => route('subjects.destroy', $subject),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        return view('shared.form', [
            'title' => 'Create Subject',
            'action' => route('subjects.store'),
            'method' => 'POST',
            'backUrl' => route('subjects.index'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(StoreSubjectRequest $request, SubjectSyncService $syncService): RedirectResponse
    {
        $subject = DB::transaction(fn (): Subject => Subject::create($request->validated()));
        $syncService->publishCreated($subject);

        return redirect()->route('subjects.index')->with('success', 'Subject created.');
    }

    public function edit(Subject $subject): View
    {
        return view('shared.form', [
            'title' => 'Edit Subject',
            'action' => route('subjects.update', $subject),
            'method' => 'PUT',
            'backUrl' => route('subjects.index'),
            'fields' => $this->fields($subject),
        ]);
    }

    public function update(StoreSubjectRequest $request, Subject $subject, SubjectSyncService $syncService): RedirectResponse
    {
        DB::transaction(fn () => $subject->update($request->validated()));
        $syncService->publishUpdated($subject);

        return redirect()->route('subjects.index')->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject, SubjectSyncService $syncService): RedirectResponse
    {
        DB::transaction(fn () => $subject->delete());
        $syncService->publishDeleted($subject);

        return back()->with('success', 'Subject deleted.');
    }

    protected function fields(?Subject $subject = null): array
    {
        return [
            ['name' => 'subject_code', 'label' => 'Subject Code', 'type' => 'text', 'value' => $subject?->subject_code],
            ['name' => 'subject_name', 'label' => 'Subject Name', 'type' => 'text', 'value' => $subject?->subject_name],
            ['name' => 'units', 'label' => 'Units', 'type' => 'number', 'value' => $subject?->units ?? 3],
            ['name' => 'semester', 'label' => 'Semester', 'type' => 'text', 'value' => $subject?->semester],
            ['name' => 'course_id', 'label' => 'Course', 'type' => 'select', 'value' => $subject?->course_id, 'options' => Course::orderBy('course_name')->pluck('course_name', 'id')->all()],
        ];
    }
}

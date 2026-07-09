<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeeScheduleRequest;
use App\Models\FeeSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FeeScheduleController extends Controller
{
    public function index(): View
    {
        $feeSchedules = FeeSchedule::latest()->get();

        return view('shared.index', [
            'title' => 'Fee Schedules',
            'createUrl' => route('fee-schedules.create'),
            'columns' => [
                'course_code' => 'Course',
                'semester' => 'Semester',
                'school_year' => 'School Year',
                'per_unit_rate' => 'Per Unit',
                'is_active' => 'Active',
            ],
            'rows' => $feeSchedules->map(fn (FeeSchedule $feeSchedule) => [
                'course_code' => $feeSchedule->course_code,
                'semester' => $feeSchedule->semester,
                'school_year' => $feeSchedule->school_year,
                'per_unit_rate' => number_format((float) $feeSchedule->per_unit_rate, 2),
                'is_active' => $feeSchedule->is_active ? 'Yes' : 'No',
                'edit_url' => route('fee-schedules.edit', $feeSchedule),
                'delete_url' => route('fee-schedules.destroy', $feeSchedule),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        return view('shared.form', [
            'title' => 'Create Fee Schedule',
            'action' => route('fee-schedules.store'),
            'method' => 'POST',
            'backUrl' => route('fee-schedules.index'),
            'fields' => $this->fields(),
        ]);
    }

    public function store(StoreFeeScheduleRequest $request): RedirectResponse
    {
        FeeSchedule::create($request->validated());

        return redirect()->route('fee-schedules.index')->with('success', 'Fee schedule created.');
    }

    public function edit(FeeSchedule $feeSchedule): View
    {
        return view('shared.form', [
            'title' => 'Edit Fee Schedule',
            'action' => route('fee-schedules.update', $feeSchedule),
            'method' => 'PUT',
            'backUrl' => route('fee-schedules.index'),
            'fields' => $this->fields($feeSchedule),
        ]);
    }

    public function update(StoreFeeScheduleRequest $request, FeeSchedule $feeSchedule): RedirectResponse
    {
        $feeSchedule->update($request->validated());

        return redirect()->route('fee-schedules.index')->with('success', 'Fee schedule updated.');
    }

    public function destroy(FeeSchedule $feeSchedule): RedirectResponse
    {
        $feeSchedule->delete();

        return back()->with('success', 'Fee schedule deleted.');
    }

    protected function fields(?FeeSchedule $feeSchedule = null): array
    {
        return [
            ['name' => 'course_code', 'label' => 'Course Code', 'type' => 'text', 'value' => $feeSchedule?->course_code],
            ['name' => 'semester', 'label' => 'Semester', 'type' => 'text', 'value' => $feeSchedule?->semester],
            ['name' => 'school_year', 'label' => 'School Year', 'type' => 'text', 'value' => $feeSchedule?->school_year],
            ['name' => 'per_unit_rate', 'label' => 'Per Unit Rate', 'type' => 'number', 'value' => $feeSchedule?->per_unit_rate ?? 0, 'step' => '0.01'],
            ['name' => 'registration_fee', 'label' => 'Registration Fee', 'type' => 'number', 'value' => $feeSchedule?->registration_fee ?? 0, 'step' => '0.01'],
            ['name' => 'miscellaneous_fee', 'label' => 'Miscellaneous Fee', 'type' => 'number', 'value' => $feeSchedule?->miscellaneous_fee ?? 0, 'step' => '0.01'],
            ['name' => 'laboratory_fee', 'label' => 'Laboratory Fee', 'type' => 'number', 'value' => $feeSchedule?->laboratory_fee ?? 0, 'step' => '0.01'],
            ['name' => 'other_fee', 'label' => 'Other Fee', 'type' => 'number', 'value' => $feeSchedule?->other_fee ?? 0, 'step' => '0.01'],
            ['name' => 'is_active', 'label' => 'Active', 'type' => 'select', 'value' => $feeSchedule?->is_active ? 1 : 0, 'options' => [1 => 'Active', 0 => 'Inactive']],
        ];
    }
}

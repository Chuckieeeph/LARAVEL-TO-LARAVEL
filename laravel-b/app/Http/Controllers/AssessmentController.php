<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function index(): View
    {
        $assessments = Assessment::with('student')->latest()->get();

        return view('shared.index', [
            'title' => 'Assessments',
            'createUrl' => null,
            'columns' => [
                'enrollment_reference_number' => 'Reference',
                'student_name' => 'Student',
                'course_name' => 'Course',
                'total_units' => 'Units',
                'total_amount' => 'Amount',
                'status' => 'Status',
            ],
            'rows' => $assessments->map(fn (Assessment $assessment) => [
                'enrollment_reference_number' => $assessment->enrollment_reference_number,
                'student_name' => trim($assessment->student?->first_name.' '.$assessment->student?->last_name),
                'course_name' => $assessment->course_name,
                'total_units' => $assessment->total_units,
                'total_amount' => number_format((float) $assessment->total_amount, 2),
                'status' => $assessment->status,
                'show_url' => route('assessments.show', $assessment),
            ])->all(),
        ]);
    }

    public function show(Assessment $assessment): View
    {
        $assessment->load(['student', 'payments', 'ledgerEntries']);

        return view('assessments.show', compact('assessment'));
    }
}

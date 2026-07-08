<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\StatementOfAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatementOfAccountController extends Controller
{
    public function show(Student $student, StatementOfAccountService $service): View
    {
        return view('statements.show', $service->build($student));
    }

    public function json(Student $student, StatementOfAccountService $service): JsonResponse
    {
        return response()->json($service->build($student));
    }

    public function download(Student $student, StatementOfAccountService $service)
    {
        $data = $service->build($student);

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('statements.show', $data)
                ->download($data['statement_reference'].'.pdf');
        }

        return response()
            ->view('statements.show', $data)
            ->header('X-Statement-Format', 'html-fallback');
    }
}

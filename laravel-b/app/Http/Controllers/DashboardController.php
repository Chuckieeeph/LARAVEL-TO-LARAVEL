<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\FeeSchedule;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'studentCount' => Student::count(),
            'assessmentCount' => Assessment::count(),
            'paymentCount' => Payment::count(),
            'feeScheduleCount' => FeeSchedule::count(),
            'recentAssessments' => Assessment::with('student')->latest()->limit(5)->get(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'studentCount' => Student::count(),
            'courseCount' => Course::count(),
            'subjectCount' => Subject::count(),
            'enrollmentCount' => Enrollment::count(),
            'recentEnrollments' => Enrollment::with(['student', 'course'])->latest()->limit(5)->get(),
        ]);
    }
}

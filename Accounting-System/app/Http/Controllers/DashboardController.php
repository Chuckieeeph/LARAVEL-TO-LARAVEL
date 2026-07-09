<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Course;
use App\Models\EnrollmentActivityLog;
use App\Models\Enrollment;
use App\Models\FeeSchedule;
use App\Models\EnrollmentLog;
use App\Models\Payment;
use App\Models\Student;
use App\Models\Subject;
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
            'courseCount' => Course::count(),
            'subjectCount' => Subject::count(),
            'syncedEnrollmentCount' => Enrollment::count(),
            'enrollmentLogCount' => EnrollmentLog::count(),
            'activityLogCount' => EnrollmentActivityLog::count(),
            'recentAssessments' => Assessment::with('student')->latest()->limit(5)->get(),
            'recentEnrollmentLogs' => EnrollmentLog::latest()->limit(5)->get(),
            'recentActivityLogs' => EnrollmentActivityLog::latest()->limit(8)->get(),
            'recentCourses' => Course::latest()->limit(5)->get(),
            'recentSubjects' => Subject::with('course')->latest()->limit(5)->get(),
            'recentEnrollments' => Enrollment::with(['student', 'course', 'subjects'])->latest()->limit(5)->get(),
        ]);
    }
}

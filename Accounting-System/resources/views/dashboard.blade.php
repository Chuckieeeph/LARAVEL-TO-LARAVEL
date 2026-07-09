@extends('layouts.app')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Students</div><div class="fs-2 fw-bold">{{ $studentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Assessments</div><div class="fs-2 fw-bold">{{ $assessmentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Payments</div><div class="fs-2 fw-bold">{{ $paymentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Fee Schedules</div><div class="fs-2 fw-bold">{{ $feeScheduleCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Courses</div><div class="fs-2 fw-bold">{{ $courseCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Subjects</div><div class="fs-2 fw-bold">{{ $subjectCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Mirrored Enrollments</div><div class="fs-2 fw-bold">{{ $syncedEnrollmentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Enrollment Logs</div><div class="fs-2 fw-bold">{{ $enrollmentLogCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Activity Logs</div><div class="fs-2 fw-bold">{{ $activityLogCount }}</div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-semibold">Recent Mirrored Enrollments</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Subjects</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentEnrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->enrollment_reference_number }}</td>
                        <td>{{ trim($enrollment->student?->first_name.' '.$enrollment->student?->last_name) }}</td>
                        <td>{{ $enrollment->course?->course_code ?? '-' }}</td>
                        <td>{{ $enrollment->subjects->pluck('subject_code')->join(', ') }}</td>
                        <td><span class="badge text-bg-secondary">{{ $enrollment->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No mirrored enrollments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold">Recent Mirrored Courses</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Department</th>
                    <th>Year Level</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentCourses as $course)
                    <tr>
                        <td>{{ $course->course_code }}</td>
                        <td>{{ $course->course_name }}</td>
                        <td>{{ $course->department ?? '-' }}</td>
                        <td>{{ $course->year_level }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No mirrored courses yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold">Recent Mirrored Subjects</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Course</th>
                    <th>Units</th>
                    <th>Semester</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentSubjects as $subject)
                    <tr>
                        <td>{{ $subject->subject_code }}</td>
                        <td>{{ $subject->subject_name }}</td>
                        <td>{{ $subject->course?->course_code ?? '-' }}</td>
                        <td>{{ $subject->units }}</td>
                        <td>{{ $subject->semester }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No mirrored subjects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold">Recent Assessments</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentAssessments as $assessment)
                    <tr>
                        <td>{{ $assessment->enrollment_reference_number }}</td>
                        <td>{{ trim($assessment->student?->first_name.' '.$assessment->student?->last_name) }}</td>
                        <td>{{ $assessment->course_name }}</td>
                        <td>{{ number_format((float) $assessment->total_amount, 2) }}</td>
                        <td><span class="badge text-bg-success">{{ $assessment->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No assessments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold">Recent Activity Logs</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Entity</th>
                    <th>Identifier</th>
                    <th>Status</th>
                    <th>Received</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentActivityLogs as $log)
                    <tr>
                        <td>{{ $log->event_type }}</td>
                        <td>{{ $log->entity_type ?? '-' }}</td>
                        <td>{{ $log->entity_identifier ?? '-' }}</td>
                        <td><span class="badge text-bg-secondary">{{ $log->processing_status }}</span></td>
                        <td>{{ optional($log->received_at)->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No activity logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm mt-4">
    <div class="card-header fw-semibold">Recent Enrollment Logs</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th>Received</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentEnrollmentLogs as $log)
                    <tr>
                        <td>{{ $log->enrollment_reference_number }}</td>
                        <td>{{ $log->student_name }}</td>
                        <td>{{ $log->course_name }}</td>
                        <td><span class="badge text-bg-secondary">{{ $log->processing_status }}</span></td>
                        <td>{{ optional($log->received_at)->format('M d, Y h:i A') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No enrollment logs yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Students</div><div class="fs-2 fw-bold">{{ $studentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Courses</div><div class="fs-2 fw-bold">{{ $courseCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Subjects</div><div class="fs-2 fw-bold">{{ $subjectCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Enrollments</div><div class="fs-2 fw-bold">{{ $enrollmentCount }}</div></div></div></div>
</div>

<div class="card shadow-sm">
    <div class="card-header fw-semibold">Recent Enrollments</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Units</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentEnrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->reference_number }}</td>
                        <td>{{ trim($enrollment->student?->first_name.' '.$enrollment->student?->last_name) }}</td>
                        <td>{{ $enrollment->course?->course_name }}</td>
                        <td>{{ $enrollment->total_units }}</td>
                        <td><span class="badge text-bg-success">{{ $enrollment->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No enrollments yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

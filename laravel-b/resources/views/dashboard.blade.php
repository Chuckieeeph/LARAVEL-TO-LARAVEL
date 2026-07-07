@extends('layouts.app')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Students</div><div class="fs-2 fw-bold">{{ $studentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Assessments</div><div class="fs-2 fw-bold">{{ $assessmentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Payments</div><div class="fs-2 fw-bold">{{ $paymentCount }}</div></div></div></div>
    <div class="col-md-3"><div class="card shadow-sm"><div class="card-body"><div class="text-muted">Fee Schedules</div><div class="fs-2 fw-bold">{{ $feeScheduleCount }}</div></div></div></div>
</div>

<div class="card shadow-sm">
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
@endsection

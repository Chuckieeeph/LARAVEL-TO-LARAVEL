@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Enrollment Record</h1>
    <a href="{{ route('enrollments.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">{{ $enrollment->reference_number }}</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Student:</strong> {{ trim($enrollment->student?->first_name.' '.$enrollment->student?->last_name) }}</div>
                    <div class="col-md-6"><strong>Student Number:</strong> {{ $enrollment->student?->student_number }}</div>
                    <div class="col-md-6"><strong>Course:</strong> {{ $enrollment->course?->course_name }}</div>
                    <div class="col-md-6"><strong>Semester:</strong> {{ $enrollment->semester }}</div>
                    <div class="col-md-6"><strong>School Year:</strong> {{ $enrollment->school_year }}</div>
                    <div class="col-md-6"><strong>Status:</strong> {{ $enrollment->status }}</div>
                    <div class="col-md-6"><strong>Total Units:</strong> {{ $enrollment->total_units }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Subjects</div>
            <ul class="list-group list-group-flush">
                @foreach ($enrollment->subjects as $subject)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $subject->subject_code }} - {{ $subject->subject_name }}</span>
                        <span>{{ $subject->units }} units</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

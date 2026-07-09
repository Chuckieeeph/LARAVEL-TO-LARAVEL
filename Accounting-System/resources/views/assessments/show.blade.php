@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Assessment Details</h1>
    <a href="{{ route('assessments.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">{{ $assessment->enrollment_reference_number }}</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Student:</strong> {{ trim($assessment->student?->first_name.' '.$assessment->student?->last_name) }}</div>
                    <div class="col-md-6"><strong>Student Number:</strong> {{ $assessment->student?->student_number }}</div>
                    <div class="col-md-6"><strong>Course:</strong> {{ $assessment->course_name }}</div>
                    <div class="col-md-6"><strong>Semester:</strong> {{ $assessment->semester }}</div>
                    <div class="col-md-6"><strong>School Year:</strong> {{ $assessment->school_year }}</div>
                    <div class="col-md-6"><strong>Status:</strong> {{ $assessment->status }}</div>
                    <div class="col-md-6"><strong>Total Units:</strong> {{ $assessment->total_units }}</div>
                    <div class="col-md-6"><strong>Total Amount:</strong> {{ number_format((float) $assessment->total_amount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Payments</div>
            <ul class="list-group list-group-flush">
                @forelse ($assessment->payments as $payment)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $payment->payment_reference }}</span>
                        <span>{{ number_format((float) $payment->amount, 2) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No payments recorded.</li>
                @endforelse
            </ul>
        </div>
        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Ledger Entries</div>
            <ul class="list-group list-group-flush">
                @forelse ($assessment->ledgerEntries as $entry)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $entry->entry_type }} - {{ $entry->reference_number }}</span>
                        <span>Balance: {{ number_format((float) $entry->balance, 2) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No ledger entries recorded.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

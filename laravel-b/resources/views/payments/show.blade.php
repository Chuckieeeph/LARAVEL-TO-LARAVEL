@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Payment Details</h1>
    <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6"><strong>Payment Reference:</strong> {{ $payment->payment_reference }}</div>
            <div class="col-md-6"><strong>Assessment:</strong> {{ $payment->assessment?->enrollment_reference_number }}</div>
            <div class="col-md-6"><strong>Student:</strong> {{ trim($payment->assessment?->student?->first_name.' '.$payment->assessment?->student?->last_name) }}</div>
            <div class="col-md-6"><strong>Amount:</strong> {{ number_format((float) $payment->amount, 2) }}</div>
            <div class="col-md-6"><strong>Method:</strong> {{ $payment->payment_method }}</div>
            <div class="col-md-6"><strong>Paid At:</strong> {{ optional($payment->paid_at)->format('Y-m-d H:i') }}</div>
            <div class="col-12"><strong>Remarks:</strong> {{ $payment->remarks ?? '-' }}</div>
        </div>
    </div>
</div>
@endsection

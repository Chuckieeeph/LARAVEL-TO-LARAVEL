@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h3 mb-0">Statement of Account</h1>
        <div class="text-muted small">{{ $statement_reference }}</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('statements.download', $student) }}" class="btn btn-outline-primary">Download</a>
        <button class="btn btn-primary" onclick="window.print()">Print</button>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Student Information</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6"><strong>Name:</strong> {{ trim($student->first_name.' '.($student->middle_name ? $student->middle_name.' ' : '').$student->last_name) }}</div>
                    <div class="col-md-6"><strong>Student Number:</strong> {{ $student->student_number }}</div>
                    <div class="col-md-6"><strong>Course:</strong> {{ $student->course_name ?? '-' }}</div>
                    <div class="col-md-6"><strong>Year Level:</strong> {{ $student->year_level }}</div>
                    <div class="col-md-6"><strong>Email:</strong> {{ $student->email ?? '-' }}</div>
                    <div class="col-md-6"><strong>Status:</strong> {{ $summary['account_status'] }}</div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Assessments</div>
            <div class="table-responsive">
                <table class="table mb-0 table-sm">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Semester</th>
                            <th>School Year</th>
                            <th class="text-end">Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assessments as $assessment)
                            <tr>
                                <td>{{ $assessment->enrollment_reference_number }}</td>
                                <td>{{ $assessment->semester }}</td>
                                <td>{{ $assessment->school_year }}</td>
                                <td class="text-end">{{ number_format((float) $assessment->total_amount, 2) }}</td>
                                <td>{{ $assessment->status }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No assessments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Payment History</div>
            <div class="table-responsive">
                <table class="table mb-0 table-sm">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Method</th>
                            <th>Paid At</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_reference }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ optional($payment->paid_at)->format('Y-m-d H:i') }}</td>
                                <td class="text-end">{{ number_format((float) $payment->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Ledger Entries</div>
            <div class="table-responsive">
                <table class="table mb-0 table-sm">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Reference</th>
                            <th>Description</th>
                            <th class="text-end">Debit</th>
                            <th class="text-end">Credit</th>
                            <th class="text-end">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ledger_entries as $entry)
                            <tr>
                                <td>{{ $entry->entry_type }}</td>
                                <td>{{ $entry->reference_number }}</td>
                                <td>{{ $entry->description }}</td>
                                <td class="text-end">{{ number_format((float) $entry->debit, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $entry->credit, 2) }}</td>
                                <td class="text-end">{{ number_format((float) $entry->balance, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No ledger entries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-header fw-semibold">Financial Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Assessments</span>
                    <strong>{{ $summary['assessment_count'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Payments</span>
                    <strong>{{ $summary['payment_count'] }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Assessment</span>
                    <strong>{{ number_format((float) $summary['total_assessment_amount'], 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Total Payments</span>
                    <strong>{{ number_format((float) $summary['total_payments'], 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>Current Balance</span>
                    <strong>{{ number_format((float) $summary['current_balance'], 2) }}</strong>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-semibold">Statement Metadata</div>
            <div class="card-body">
                <div class="mb-2"><strong>Generated:</strong> {{ $generated_at->format('Y-m-d H:i:s') }}</div>
                <div class="mb-2"><strong>Assessment Status:</strong> {{ $summary['latest_assessment_status'] }}</div>
                <div class="mb-2"><strong>Financial Account:</strong> {{ $financial_account?->account_number ?? 'Not linked' }}</div>
                <div><strong>Account Status:</strong> {{ $summary['account_status'] }}</div>
            </div>
        </div>
    </div>
</div>
@endsection

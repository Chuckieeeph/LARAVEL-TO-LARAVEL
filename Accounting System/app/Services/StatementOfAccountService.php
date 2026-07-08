<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StatementOfAccountService
{
    public function build(Student $student): array
    {
        $student->loadMissing('financialAccount');

        $assessments = $student->assessments()
            ->with(['payments', 'ledgerEntries'])
            ->orderByDesc('assessed_at')
            ->orderByDesc('id')
            ->get();

        $payments = Payment::query()
            ->whereHas('assessment', fn ($query) => $query->where('student_id', $student->id))
            ->with('assessment')
            ->orderByDesc('paid_at')
            ->orderByDesc('id')
            ->get();

        $ledgerEntries = $student->ledgerEntries()
            ->with(['assessment', 'payment'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $totalAssessmentAmount = (float) $assessments->sum('total_amount');
        $totalPayments = (float) $payments->sum('amount');
        $latestLedgerBalance = $this->latestLedgerBalance($ledgerEntries);
        $currentBalance = $student->financialAccount?->balance !== null
            ? (float) $student->financialAccount->balance
            : ($latestLedgerBalance ?? max(0, $totalAssessmentAmount - $totalPayments));

        $generatedAt = now();

        return [
            'statement_reference' => $this->statementReference($student, $generatedAt),
            'generated_at' => $generatedAt,
            'student' => $student,
            'financial_account' => $student->financialAccount,
            'summary' => [
                'assessment_count' => $assessments->count(),
                'payment_count' => $payments->count(),
                'total_assessment_amount' => $totalAssessmentAmount,
                'total_payments' => $totalPayments,
                'current_balance' => $currentBalance,
                'latest_assessment_status' => $assessments->first()?->status ?? 'no_assessment',
                'account_status' => $student->financialAccount?->status ?? 'unlinked',
            ],
            'assessments' => $assessments,
            'payments' => $payments,
            'ledger_entries' => $ledgerEntries,
            'latest_assessment' => $assessments->first(),
        ];
    }

    protected function latestLedgerBalance(Collection $ledgerEntries): ?float
    {
        $entry = $ledgerEntries->first();

        return $entry?->balance !== null ? (float) $entry->balance : null;
    }

    protected function statementReference(Student $student, \Illuminate\Support\Carbon $generatedAt): string
    {
        return Str::upper(sprintf(
            'SOA-%s-%s',
            $student->student_number,
            $generatedAt->format('YmdHis')
        ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Models\Assessment;
use App\Models\LedgerEntry;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with('assessment.student')->latest()->get();

        return view('shared.index', [
            'title' => 'Payments',
            'createUrl' => route('payments.create'),
            'columns' => [
                'payment_reference' => 'Reference',
                'student_name' => 'Student',
                'amount' => 'Amount',
                'payment_method' => 'Method',
                'paid_at' => 'Paid At',
            ],
            'rows' => $payments->map(fn (Payment $payment) => [
                'payment_reference' => $payment->payment_reference,
                'student_name' => trim($payment->assessment?->student?->first_name.' '.$payment->assessment?->student?->last_name),
                'amount' => number_format((float) $payment->amount, 2),
                'payment_method' => $payment->payment_method,
                'paid_at' => optional($payment->paid_at)->format('Y-m-d H:i'),
                'show_url' => route('payments.show', $payment),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        $assessments = Assessment::with('student')->latest()->get()->mapWithKeys(fn (Assessment $assessment) => [
            $assessment->id => $assessment->enrollment_reference_number.' - '.trim($assessment->student?->first_name.' '.$assessment->student?->last_name),
        ])->all();

        return view('shared.form', [
            'title' => 'Record Payment',
            'action' => route('payments.store'),
            'method' => 'POST',
            'backUrl' => route('payments.index'),
            'fields' => [
                ['name' => 'assessment_id', 'label' => 'Assessment', 'type' => 'select', 'options' => $assessments],
                ['name' => 'payment_reference', 'label' => 'Payment Reference', 'type' => 'text'],
                ['name' => 'amount', 'label' => 'Amount', 'type' => 'number', 'step' => '0.01'],
                ['name' => 'payment_method', 'label' => 'Payment Method', 'type' => 'text', 'value' => 'Cash'],
                ['name' => 'remarks', 'label' => 'Remarks', 'type' => 'textarea'],
            ],
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $payment = DB::transaction(function () use ($request): Payment {
            $assessment = Assessment::query()->with('student', 'payments')->findOrFail($request->integer('assessment_id'));
            $payment = Payment::create([
                'assessment_id' => $assessment->id,
                'payment_reference' => $request->string('payment_reference')->toString(),
                'amount' => (float) $request->input('amount'),
                'payment_method' => $request->string('payment_method')->toString(),
                'received_by' => auth()->id(),
                'paid_at' => now(),
                'remarks' => $request->input('remarks'),
            ]);

            $paidTotal = (float) $assessment->payments()->sum('amount') + (float) $payment->amount;
            $remaining = max(0, (float) $assessment->total_amount - $paidTotal);

            $assessment->update([
                'status' => $remaining <= 0 ? 'paid' : 'partially_paid',
            ]);

            $ledgerBalance = (float) $assessment->total_amount - $paidTotal;

            LedgerEntry::create([
                'student_id' => $assessment->student_id,
                'assessment_id' => $assessment->id,
                'payment_id' => $payment->id,
                'entry_type' => 'payment',
                'reference_number' => $payment->payment_reference,
                'description' => 'Payment recorded for assessment.',
                'debit' => 0,
                'credit' => $payment->amount,
                'balance' => $ledgerBalance,
                'meta' => [
                    'payment_method' => $payment->payment_method,
                ],
            ]);

            return $payment;
        });

        return redirect()->route('payments.show', $payment)->with('success', 'Payment recorded.');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['assessment.student']);

        return view('payments.show', compact('payment'));
    }
}

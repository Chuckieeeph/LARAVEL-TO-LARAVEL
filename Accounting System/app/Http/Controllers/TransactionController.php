<?php

namespace App\Http\Controllers;

use App\Models\LedgerEntry;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $entries = LedgerEntry::with(['student', 'assessment', 'payment'])->latest()->get();

        return view('shared.index', [
            'title' => 'Transaction History',
            'createUrl' => null,
            'columns' => [
                'entry_type' => 'Type',
                'reference_number' => 'Reference',
                'student_name' => 'Student',
                'debit' => 'Debit',
                'credit' => 'Credit',
                'balance' => 'Balance',
            ],
            'rows' => $entries->map(fn (LedgerEntry $entry) => [
                'entry_type' => $entry->entry_type,
                'reference_number' => $entry->reference_number,
                'student_name' => trim($entry->student?->first_name.' '.$entry->student?->last_name),
                'debit' => number_format((float) $entry->debit, 2),
                'credit' => number_format((float) $entry->credit, 2),
                'balance' => number_format((float) $entry->balance, 2),
            ])->all(),
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAccount extends Model
{
    protected $fillable = [
        'student_id',
        'account_number',
        'status',
        'balance',
        'opened_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'opened_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

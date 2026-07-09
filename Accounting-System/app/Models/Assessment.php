<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    protected $fillable = [
        'student_id',
        'enrollment_reference_number',
        'course_code',
        'course_name',
        'semester',
        'school_year',
        'total_units',
        'per_unit_rate',
        'tuition_fee',
        'registration_fee',
        'miscellaneous_fee',
        'laboratory_fee',
        'other_fee',
        'total_amount',
        'status',
        'payload',
        'assessed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'assessed_at' => 'datetime',
            'per_unit_rate' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'miscellaneous_fee' => 'decimal:2',
            'laboratory_fee' => 'decimal:2',
            'other_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}

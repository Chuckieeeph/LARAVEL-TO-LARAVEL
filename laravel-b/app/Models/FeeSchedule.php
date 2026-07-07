<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeSchedule extends Model
{
    protected $fillable = [
        'course_code',
        'semester',
        'school_year',
        'per_unit_rate',
        'registration_fee',
        'miscellaneous_fee',
        'laboratory_fee',
        'other_fee',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'per_unit_rate' => 'decimal:2',
            'registration_fee' => 'decimal:2',
            'miscellaneous_fee' => 'decimal:2',
            'laboratory_fee' => 'decimal:2',
            'other_fee' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}

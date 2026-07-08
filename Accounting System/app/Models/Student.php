<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_number',
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birth_date',
        'course_code',
        'course_name',
        'year_level',
        'email',
        'phone',
        'address',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function financialAccount(): HasOne
    {
        return $this->hasOne(FinancialAccount::class);
    }
}

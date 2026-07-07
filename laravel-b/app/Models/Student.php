<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'student_number',
        'first_name',
        'middle_name',
        'last_name',
        'course_code',
        'course_name',
        'year_level',
        'email',
        'status',
    ];

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class);
    }
}

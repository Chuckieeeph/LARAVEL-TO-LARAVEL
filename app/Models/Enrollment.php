<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'semester',
        'school_year',
        'status',
        'reference_number',
        'total_units',
        'created_by',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }
}

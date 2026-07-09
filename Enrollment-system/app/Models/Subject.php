<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Subject extends Model
{
    protected $fillable = [
        'subject_code',
        'subject_name',
        'units',
        'semester',
        'course_id',
    ];

    public function enrollments(): BelongsToMany
    {
        return $this->belongsToMany(Enrollment::class)->withTimestamps();
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}

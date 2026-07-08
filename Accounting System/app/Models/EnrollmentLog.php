<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnrollmentLog extends Model
{
    protected $fillable = [
        'assessment_id',
        'enrollment_reference_number',
        'student_number',
        'student_name',
        'course_code',
        'course_name',
        'semester',
        'school_year',
        'total_units',
        'enrollment_status',
        'processing_status',
        'error_message',
        'payload',
        'received_at',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'received_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}

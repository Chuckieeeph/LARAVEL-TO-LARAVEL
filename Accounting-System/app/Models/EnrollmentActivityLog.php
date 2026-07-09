<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrollmentActivityLog extends Model
{
    protected $fillable = [
        'event_id',
        'event_type',
        'routing_key',
        'entity_type',
        'entity_identifier',
        'action',
        'actor_name',
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
}

<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Str;

class StudentNumberGenerator
{
    public function generate(): string
    {
        do {
            $number = 'STU-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (Student::where('student_number', $number)->exists());

        return $number;
    }
}

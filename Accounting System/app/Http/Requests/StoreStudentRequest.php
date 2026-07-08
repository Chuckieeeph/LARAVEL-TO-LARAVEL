<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $studentId = $this->route('student')?->id;

        return [
            'student_number' => ['required', 'string', 'max:50', Rule::unique('students', 'student_number')->ignore($studentId)],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'course_code' => ['nullable', 'string', 'max:50'],
            'course_name' => ['nullable', 'string', 'max:255'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
        ];
    }
}

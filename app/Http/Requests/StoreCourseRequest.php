<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $courseId = $this->route('course')?->id;

        return [
            'course_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses', 'course_code')->ignore($courseId),
            ],
            'course_name' => ['required', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'year_level' => ['required', 'integer', 'min:1', 'max:6'],
        ];
    }
}

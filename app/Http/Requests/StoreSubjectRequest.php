<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subjectId = $this->route('subject')?->id;

        return [
            'subject_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'subject_code')->ignore($subjectId),
            ],
            'subject_name' => ['required', 'string', 'max:255'],
            'units' => ['required', 'integer', 'min:1', 'max:12'],
            'semester' => ['required', 'string', 'max:50'],
            'course_id' => ['nullable', 'exists:courses,id'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeeScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_code' => ['required', 'string', 'max:50'],
            'semester' => ['required', 'string', 'max:100'],
            'school_year' => ['required', 'string', 'max:20'],
            'per_unit_rate' => ['required', 'numeric', 'min:0'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'miscellaneous_fee' => ['required', 'numeric', 'min:0'],
            'laboratory_fee' => ['required', 'numeric', 'min:0'],
            'other_fee' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

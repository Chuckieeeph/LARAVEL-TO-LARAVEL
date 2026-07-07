<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'assessment_id' => ['required', 'exists:assessments,id'],
            'payment_reference' => ['required', 'string', 'max:100', Rule::unique('payments', 'payment_reference')],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'string', 'max:100'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommissionRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'commission_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
            'reason' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'commission_rate.required' => 'Commission rate is required',
            'commission_rate.numeric' => 'Commission rate must be a number',
            'commission_rate.max' => 'Commission rate cannot exceed 100%',
            'commission_rate.regex' => 'Commission rate can have maximum 2 decimal places',
        ];
    }

    public function attributes(): array
    {
        return [
            'commission_rate' => 'Commission Rate (%)',
            'reason' => 'Reason for Change',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'commission_rate' => floatval($this->commission_rate ?? 0),
        ]);
    }
}
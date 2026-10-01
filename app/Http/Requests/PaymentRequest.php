<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:jazzcash,bank_transfer,cod',
            'mobile_number' => 'required_if:payment_method,jazzcash|nullable|regex:/^(\+92|0|92)?3\d{9}$/',
            'transaction_reference' => 'required_if:payment_method,bank_transfer|nullable|string|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'mobile_number.required_if' => 'JazzCash mobile number is required.',
            'mobile_number.regex' => 'Please enter a valid Pakistani mobile number (03XXXXXXXXX).',
            'transaction_reference.required_if' => 'Transaction reference is required for bank transfer.',
            'transaction_reference.max' => 'Transaction reference must not exceed 100 characters.',
        ];
    }
}

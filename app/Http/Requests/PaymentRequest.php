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
        return auth()->check() || $this->is('webhooks/*');
    }

    protected function prepareForValidation(): void
    {
        // Normalize empty-string sent by JS (e.g. payment_id: "" or null) to real null
        // so `nullable|exists` does not run against "".
        if ($this->has('payment_id') && ($this->input('payment_id') === '' || $this->input('payment_id') === 0 || $this->input('payment_id') === '0')) {
            $this->merge(['payment_id' => null]);
        }
        if ($this->has('order_id') && ($this->input('order_id') === '' || $this->input('order_id') === 0 || $this->input('order_id') === '0')) {
            $this->merge(['order_id' => null]);
        }
        if ($this->has('payment_intent_id')) {
            $this->merge(['payment_intent_id' => trim((string) $this->input('payment_intent_id'))]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // Branch by intent: Stripe vs bank_transfer vs generic
        $isStripeRoute = $this->routeIs('payment.stripe.*') || $this->is('payment/stripe/*');

        if ($isStripeRoute) {
            if ($this->is('payment/stripe/confirm')) {
                return [
                    'payment_intent_id' => ['required', 'string', 'max:255', 'regex:/^pi_[a-zA-Z0-9_]+$/'],
                    'payment_id' => ['nullable', 'integer', 'exists:payments,id'],
                    'order_id' => ['nullable', 'integer', 'exists:orders,id'],
                ];
            }
            if ($this->is('payment/stripe/status/*')) {
                return [];
            }
            // create-intent / retry — only order_id is needed
            return [
                'order_id' => ['required', 'integer', 'exists:orders,id'],
            ];
        }

        $paymentMethod = $this->input('payment_method');
        $rules = [
            'order_id' => ['required', 'integer', 'exists:orders,id'],
            'payment_method' => ['required', 'string', 'in:card,bank_transfer,cod,jazzcash'],
        ];

        if ($paymentMethod === 'bank_transfer') {
            $rules['transaction_reference'] = ['nullable', 'string', 'max:100'];
            $rules['bank_payer_name'] = ['nullable', 'string', 'max:255'];
            $rules['bank_payment_app'] = ['nullable', 'string', 'max:255'];
            $rules['bank_payment_source'] = ['nullable', 'string', 'max:255'];
            $rules['bank_customer_account_number'] = ['nullable', 'string', 'max:100'];
            $rules['payment_slip'] = ['nullable', 'file', 'mimes:jpeg,png,jpg,pdf', 'max:5120'];
        } elseif ($paymentMethod === 'jazzcash') {
            $rules['mobile_number'] = ['nullable', 'string', 'max:20'];
        } elseif ($paymentMethod === 'card') {
            $rules['transaction_reference'] = ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'payment_intent_id.required' => 'Payment intent is required.',
            'payment_intent_id.regex' => 'Invalid payment intent format.',
            'transaction_reference.required_if' => 'Transaction reference is required for bank transfer.',
            'transaction_reference.max' => 'Transaction reference must not exceed 100 characters.',
            'order_id.required' => 'Order is required.',
            'payment_id.exists' => 'Payment not found.',
        ];
    }
}

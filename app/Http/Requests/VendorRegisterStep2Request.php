<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRegisterStep2Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data before validation
     * - Trim account holder name
     * - Remove spaces from account number and IBAN
     * - Uppercase IBAN
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'account_holder_name' => trim($this->account_holder_name ?? ''),
            'account_number'      => preg_replace('/\s+/', '', $this->account_number ?? ''),
            'iban'                => strtoupper(preg_replace('/\s+/', '', $this->iban ?? '')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            // Account holder name (as per bank record)
            'account_holder_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s\.\-\']+$/u', // Letters, spaces, dots, hyphens
            ],

            // Account number (10-20 digits)
            'account_number' => [
                'required',
                'string',
                'min:10',
                'max:20',
                'regex:/^[0-9]+$/', // Numbers only
                'unique:vendor_bank_details,account_number',
            ],

            // IBAN: Pakistani format (24 chars starting with PK)
            'iban' => [
                'nullable',
                'string',
                'starts_with:PK',
                'size:24', // Exactly 24 characters
                'regex:/^PK[0-9]{2}[A-Z]{4}[0-9]{16}$/', // PK + check digits + bank code + account number
                'unique:vendor_bank_details,iban',
            ],

            // Bank name: predefined list
            'bank_name' => [
                'required',
                'string',
                'in:hbl,ubl,mcb,allied,alfalah,meezan,standard_chartered,faysal,js,askari,bank_of_punjab',
            ],

            // Account type: savings, checking, or business
            'account_type' => [
                'required',
                'string',
                'in:current,savings,business',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            // Account holder name
            'account_holder_name.required' => 'Account holder name is required',
            'account_holder_name.min'      => 'Account holder name must be at least 3 characters',
            'account_holder_name.max'      => 'Account holder name must not exceed 100 characters',
            'account_holder_name.regex'    => 'Account holder name may only contain letters, spaces, dots, and hyphens',

            // Account number
            'account_number.required' => 'Account number is required',
            'account_number.min'      => 'Account number must be at least 10 digits',
            'account_number.max'      => 'Account number must not exceed 20 digits',
            'account_number.regex'    => 'Account number must contain only digits',
            'account_number.unique'   => 'This account number is already registered with another vendor',

            // IBAN
            'iban.starts_with'  => 'IBAN must start with PK (Pakistan)',
            'iban.size'         => 'IBAN must be exactly 24 characters long',
            'iban.regex'        => 'IBAN format invalid (example: PK36SCBL0000001123456702)',
            'iban.unique'       => 'This IBAN is already registered with another vendor',

            // Bank name
            'bank_name.required' => 'Bank name is required',
            'bank_name.in'       => 'Please select a valid bank from the list',

            // Account type
            'account_type.required' => 'Account type is required',
            'account_type.in'       => 'Please select: Current, Savings, or Business account',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'account_holder_name' => 'account holder name',
            'account_number'      => 'account number',
            'iban'                => 'IBAN',
            'bank_name'           => 'bank name',
            'account_type'        => 'account type',
        ];
    }
}
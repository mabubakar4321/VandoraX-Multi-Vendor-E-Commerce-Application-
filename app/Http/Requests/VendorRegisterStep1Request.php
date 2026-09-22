<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class VendorRegisterStep1Request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return true; // Public registration
    }

    /**
     * Prepare data before validation
     * - Trim whitespace from name
     * - Lowercase email
     * - Remove spaces from phone number
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email'        => strtolower(trim($this->email ?? '')),
            'phone_number' => preg_replace('/\s+/', '', $this->phone_number ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            // Name: letters, spaces, dots, hyphens (Unicode support)
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[\pL\s\.\-\']+$/u', // Supports all Unicode letters
            ],

            // Email: validate format locally; SMTP will validate delivery
            'email' => [
                'required',
                'email:rfc',
                'max:255',
                'unique:users,email',
            ],

            // Password: 8+ chars, mixed case, numbers, symbols, not compromised
            'password' => [
                'required',
                'confirmed', // Requires password_confirmation field
                Password::min(8)
                    ->mixedCase()      // Must have uppercase AND lowercase
                    ->numbers()        // Must have at least one number
                    ->symbols()        // Must have special character
                    ->uncompromised(), // Check against known breaches
            ],

            // Pakistani mobile format: 03XXXXXXXXX or +923XXXXXXXXX
            'phone_number' => [
                'required',
                'string',
                'regex:/^(\+92|0)?3[0-9]{9}$/',
                'unique:users,phone_number',
            ],

            // Terms acceptance (checkbox must be checked)
            'terms' => [
                'accepted',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            // Name validation
            'name.required'         => 'Full name is required',
            'name.max'              => 'Name must not exceed 255 characters',
            'name.regex'            => 'Name may only contain letters, spaces, dots, and hyphens.',

            // Email validation
            'email.required'        => 'Email address is required',
            'email.email'           => 'Please enter a valid email address',
            'email.unique'          => 'This email is already registered. Try logging in instead.',
            'email.dns'             => 'That email domain does not appear to exist. Please check and try again.',

            // Password validation
            'password.required'     => 'Password is required',
            'password.confirmed'    => 'The two passwords do not match',
            'password.min'          => 'Password must be at least 8 characters long',

            // Phone number validation
            'phone_number.required' => 'Phone number is required',
            'phone_number.regex'    => 'Enter a valid Pakistani mobile number (e.g., 03001234567 or +923001234567)',
            'phone_number.unique'   => 'This phone number is already registered',

            // Terms validation
            'terms.accepted'        => 'You must accept the Seller Agreement to continue',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'name'        => 'full name',
            'email'       => 'email address',
            'password'    => 'password',
            'phone_number' => 'phone number',
            'terms'       => 'seller agreement',
        ];
    }
}
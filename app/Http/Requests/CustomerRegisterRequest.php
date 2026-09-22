<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CustomerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower(trim($this->email ?? ''))]);
    }

    public function rules(): array
    {
        return [
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'regex:/^(\+92|0)?3[0-9]{9}$/', 'unique:users,phone_number'],
            'password'     => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'terms'        => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'       => 'An account with this email already exists.',
            'password.confirmed' => 'The two passwords do not match.',
            'terms.accepted'     => 'Please accept the terms and conditions.',
        ];
    }
}
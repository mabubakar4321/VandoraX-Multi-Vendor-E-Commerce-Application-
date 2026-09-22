<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    /** true = anyone may attempt this request (it's a public login form) */
    public function authorize(): bool
    {
        return true;
    }

    /** Normalise input BEFORE validation runs */
    protected function prepareForValidation(): void
    {
        $this->merge(['email' => strtolower(trim($this->email ?? ''))]);
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Email is required.',
            'email.email'       => 'Please enter a valid email address.',
            'password.required' => 'Password is required.',
        ];
    }
}
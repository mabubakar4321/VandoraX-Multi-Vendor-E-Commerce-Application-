<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Deliberately NOT 'exists:users,email' — see the enumeration note in 6.6
        return ['email' => ['required', 'email', 'max:255']];
    }
}
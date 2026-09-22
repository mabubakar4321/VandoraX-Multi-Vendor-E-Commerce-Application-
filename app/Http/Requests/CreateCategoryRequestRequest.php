<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'category_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[\pL\s\.\-&\']+$/u',
            ],
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'category_name.required' => 'Category name is required',
            'category_name.max' => 'Category name cannot exceed 100 characters',
            'category_name.regex' => 'Category name can only contain letters, spaces, dots, hyphens, and ampersands',
            'description.max' => 'Description cannot exceed 500 characters',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_name' => 'Category Name',
            'description' => 'Reason/Description',
        ];
    }

    public function prepareForValidation(): void
    {
        // Trim whitespace
        $this->merge([
            'category_name' => trim($this->category_name),
            'description' => trim($this->description ?? ''),
        ]);
    }
}
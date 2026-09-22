<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('admin');
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                'unique:categories,name,' . $categoryId,
                'regex:/^[\pL\s\.\-&\']+$/u',
            ],
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                'different:' . $categoryId,
            ],
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'commission_rate' => [
                'required',
                'numeric',
                'min:0',
                'max:100',
                'regex:/^\d+(\.\d{1,2})?$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required',
            'name.unique' => 'This category name already exists',
            'name.regex' => 'Category name can only contain letters, spaces, dots, hyphens, and ampersands',
            'icon.image' => 'Icon must be an image file',
            'icon.max' => 'Icon size must not exceed 2MB',
            'parent_id.different' => 'A category cannot be its own parent',
            'commission_rate.required' => 'Commission rate is required',
            'commission_rate.max' => 'Commission rate cannot exceed 100%',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Category Name',
            'description' => 'Description',
            'icon' => 'Category Icon',
            'parent_id' => 'Parent Category',
            'display_order' => 'Display Order',
            'is_active' => 'Active Status',
            'commission_rate' => 'Commission Rate (%)',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active'),
            'commission_rate' => floatval($this->commission_rate ?? 0),
        ]);
    }
}
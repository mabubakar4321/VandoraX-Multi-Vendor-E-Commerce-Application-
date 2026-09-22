<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'variant_name' => 'required|string|max:100',
            'variant_value' => 'required|string|max:100',
            'price' => 'required|numeric|min:1',
            'stock' => 'required|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:product_variants',
            'weight' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'variant_name.required' => 'Variant name is required',
            'variant_value.required' => 'Variant value is required',
            'price.required' => 'Variant price is required',
            'stock.required' => 'Stock quantity is required',
        ];
    }
}
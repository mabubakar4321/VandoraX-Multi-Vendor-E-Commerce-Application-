<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'base_price' => 'required|numeric|min:1|max:9999999999.99',
            'sku' => 'nullable|string|max:100|unique:products',
            'warranty' => 'nullable|string',
            'delivery_time_days' => 'required|integer|min:1',
            'status' => 'in:draft,published',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string',
            'images' => 'nullable|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,webp|max:5120',
            'variants' => 'nullable|array|max:20',
            'variants.*.variant_name' => 'required_with:variants|string|max:100',
            'variants.*.variant_value' => 'required_with:variants|string|max:100',
            'variants.*.price' => 'required_with:variants|numeric|min:1|max:9999999999.99',
            'variants.*.stock' => 'required_with:variants|integer|min:0',
            'variants.*.sku' => 'nullable|string|max:100|unique:product_variants,sku',
            'variants.*.weight' => 'nullable|string|max:50',
            'variants.*.dimensions' => 'nullable|string|max:100',
            'specifications' => 'nullable|array|max:30',
            'specifications.*.key' => 'required_with:specifications|string|max:100',
            'specifications.*.value' => 'required_with:specifications|string|max:1000',
            'dimensions' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'brand' => 'nullable|string|max:100',
            'color_options' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.unique' => 'This product name already exists',
            'description.required' => 'Product description is required',
            'category_id.required' => 'Please select a category',
            'base_price.required' => 'Product price is required',
            'base_price.min' => 'Price must be greater than 0',
            'base_price.max' => 'Price cannot exceed 9,999,999,999.99',
            'delivery_time_days.required' => 'Delivery time is required',
            'specifications.*.key.required_with' => 'Specification name is required',
            'specifications.*.value.required_with' => 'Specification value is required',
        ];
    }
}
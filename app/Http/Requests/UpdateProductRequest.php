<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        $productId = $this->route('product')->id;

        return [
            'name' => "required|string|max:255|unique:products,name,{$productId}",
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:categories,id',
            'base_price' => 'required|numeric|min:1|max:9999999999.99',
            'sku' => "nullable|string|max:100|unique:products,sku,{$productId}",
            'warranty' => 'nullable|string',
            'delivery_time_days' => 'required|integer|min:1',
            'status' => 'in:draft,published',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'keywords' => 'nullable|string',
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
            'base_price.max' => 'Price cannot exceed 9,999,999,999.99',
            'delivery_time_days.required' => 'Delivery time is required',
        ];
    }
}
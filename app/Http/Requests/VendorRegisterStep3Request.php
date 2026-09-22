<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VendorRegisterStep3Request extends FormRequest
{
    /**
     * Maximum file size in KB (5MB)
     */
    const MAX_FILE_SIZE_KB = 5120;

    /**
     * Determine if the user is authorized to make this request
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare data before validation
     * - Trim store name and description
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'store_name'        => trim($this->store_name ?? ''),
            'store_description' => trim($this->store_description ?? ''),
        ]);
    }

    /**
     * Get the validation rules that apply to the request
     */
    public function rules(): array
    {
        return [
            // Store name: 3-100 chars, unique
            'store_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[\pL\s\.\-0-9&]+$/u', // Letters, numbers, spaces, dots, hyphens, ampersand
                'unique:vendors,store_name',
            ],

            // Store description: optional, max 1000 chars
            'store_description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            // Category: must exist in database
            'category_id' => [
                'required',
                'integer',
                'exists:categories,id,is_active,1', // Only active categories
            ],

            // CNIC front: Image file (JPEG, PNG) or PDF
            'cnic_front' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:' . self::MAX_FILE_SIZE_KB,
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],

            // CNIC back: Image file (JPEG, PNG) or PDF
            'cnic_back' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:' . self::MAX_FILE_SIZE_KB,
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],

            // Bank Proof: Image (JPEG, PNG) or PDF
            'bank_proof' => [
                'required',
                'file',
                'mimes:jpeg,jpg,png,pdf',
                'max:' . self::MAX_FILE_SIZE_KB,
                'mimetypes:image/jpeg,image/png,application/pdf',
            ],

            // Tax Certificate: optional PDF
            'tax_certificate' => [
                'nullable',
                'file',
                'mimes:pdf',
                'max:' . self::MAX_FILE_SIZE_KB,
                'mimetypes:application/pdf',
            ],
        ];
    }

    /**
     * Custom error messages
     */
    public function messages(): array
    {
        return [
            // Store name
            'store_name.required' => 'Store name is required',
            'store_name.min'      => 'Store name must be at least 3 characters',
            'store_name.max'      => 'Store name must not exceed 100 characters',
            'store_name.regex'    => 'Store name may only contain letters, numbers, spaces, dots, hyphens, and ampersand',
            'store_name.unique'   => 'This store name is already taken. Please choose a different name',

            // Store description
            'store_description.max' => 'Store description must not exceed 1000 characters',

            // Category
            'category_id.required' => 'Please select a category for your store',
            'category_id.exists'   => 'The selected category does not exist or is inactive',

            // CNIC
            'cnic_front.required'  => 'CNIC front image is required (JPEG, PNG, or PDF)',
            'cnic_front.file'      => 'CNIC front must be a valid file',
            'cnic_front.mimes'     => 'CNIC front must be JPEG, PNG, or PDF format',
            'cnic_front.mimetypes' => 'CNIC front file format is invalid',
            'cnic_front.max'       => 'CNIC front file size must not exceed ' . (self::MAX_FILE_SIZE_KB / 1024) . 'MB',
            'cnic_back.required'   => 'CNIC back image is required (JPEG, PNG, or PDF)',
            'cnic_back.file'       => 'CNIC back must be a valid file',
            'cnic_back.mimes'      => 'CNIC back must be JPEG, PNG, or PDF format',
            'cnic_back.mimetypes'  => 'CNIC back file format is invalid',
            'cnic_back.max'        => 'CNIC back file size must not exceed ' . (self::MAX_FILE_SIZE_KB / 1024) . 'MB',

            // Tax Certificate
            'tax_certificate.file'      => 'Tax certificate must be a valid file',
            'tax_certificate.mimes'     => 'Tax certificate must be in PDF format',
            'tax_certificate.mimetypes' => 'Tax certificate file format is invalid',
            'tax_certificate.max'       => 'Tax certificate file size must not exceed ' . (self::MAX_FILE_SIZE_KB / 1024) . 'MB',

            // Bank Proof
            'bank_proof.required'   => 'Bank proof (statement or passbook) is required',
            'bank_proof.file'       => 'Bank proof must be a valid file',
            'bank_proof.mimes'      => 'Bank proof must be JPEG, PNG, or PDF format',
            'bank_proof.mimetypes'  => 'Bank proof file format is invalid',
            'bank_proof.max'        => 'Bank proof file size must not exceed ' . (self::MAX_FILE_SIZE_KB / 1024) . 'MB',
        ];
    }

    /**
     * Get custom attributes for validator errors
     */
    public function attributes(): array
    {
        return [
            'store_name'        => 'store name',
            'store_description' => 'store description',
            'category_id'       => 'category',
            'cnic_front'        => 'CNIC front image',
            'cnic_back'         => 'CNIC back image',
            'tax_certificate'   => 'tax certificate',
            'bank_proof'        => 'bank proof',
        ];
    }
}
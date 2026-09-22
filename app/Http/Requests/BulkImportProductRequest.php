<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkImportProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->hasRole('vendor');
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:csv,xlsx,xls|max:10240', // 10MB
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to import',
            'file.file' => 'The uploaded file must be a valid file',
            'file.mimes' => 'The file must be a CSV or Excel file',
            'file.max' => 'The file size must not exceed 10MB',
        ];
    }
}
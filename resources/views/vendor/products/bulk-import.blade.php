@extends('layouts.vendor')

@section('title', 'Bulk Import Products')

@section('content')
<div class="container mx-auto max-w-4xl px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('vendor.products.index') }}" class="text-indigo-600 hover:text-indigo-900 mb-4 inline-flex items-center">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Products
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Bulk Import Products</h1>
        <p class="text-gray-600 mt-1">Export, edit, and upload your complete product catalogue with CSV.</p>
    </div>

    <!-- Information Box -->
    <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-lg mb-8">
        <h3 class="font-semibold text-blue-900">How to Use</h3>
        <ol class="text-sm text-blue-800 mt-3 space-y-2 list-decimal list-inside">
            <li>Download your current product catalogue as a CSV file</li>
            <li>Edit the rows or add new products without changing the column names</li>
            <li>Keep JSON values valid in the specifications and variants columns</li>
            <li>Upload your CSV file</li>
            <li>Products, inventory, variants, specifications, and existing image paths will be imported</li>
        </ol>
    </div>

    <!-- CSV Template Download -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Step 1: Download Product CSV</h2>
        
        <div class="mb-6">
            <p class="text-gray-600 mb-4">Download all of your vendor products with complete supported details. If your catalogue is empty, the download contains the headers for a new import.</p>
            <a href="{{ route('vendor.products.import-template') }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Download Product CSV
            </a>
        </div>

        <div class="border-t pt-6">
            <h3 class="font-semibold text-gray-900 mb-3">CSV columns:</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                <div>✓ name, description, category_id, base_price</div>
                <div>✓ stock, sku, warranty, delivery_time_days</div>
                <div>✓ status, moderation_status, is_visible</div>
                <div>✓ dimensions, weight, material, brand, color_options</div>
                <div>✓ specifications_json - product specifications</div>
                <div>✓ variants_json - variant prices, stock, and SKUs</div>
                <div>✓ image_paths - existing storage paths separated by |</div>
                <div>✓ Images must already exist in storage to be attached</div>
            </div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Step 2: Upload CSV File</h2>

        <form method="POST" action="{{ route('vendor.products.bulk-import.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- File Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">CSV File *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-600">
                        <label for="file" class="font-medium text-indigo-600 hover:text-indigo-500 cursor-pointer">
                            Click to select
                        </label>
                        or drag and drop CSV file here
                    </p>
                    <p class="text-xs text-gray-500">Maximum file size: 10MB</p>
                    <input id="file" type="file" name="file" accept=".csv,.xlsx,.xls" required class="hidden">
                </div>
                @error('file') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                Upload & Preview
            </button>
        </form>
    </div>

    <!-- Import Progress (if showing preview) -->
    @if(session('preview_data'))
    <div class="bg-white rounded-lg shadow p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Step 3: Review & Confirm</h2>

        <div class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Total Products</p>
                    <p class="text-2xl font-bold text-blue-600">{{ session('preview_data')['total'] }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Valid</p>
                    <p class="text-2xl font-bold text-green-600">{{ session('preview_data')['valid'] }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded">
                    <p class="text-sm text-gray-600">Errors</p>
                    <p class="text-2xl font-bold text-red-600">{{ session('preview_data')['errors'] }}</p>
                </div>
            </div>
        </div>

        <!-- Preview Table -->
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left">Product Name</th>
                        <th class="px-4 py-2 text-left">Price</th>
                        <th class="px-4 py-2 text-left">Stock</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('preview_data')['rows'] as $row)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $row['name'] }}</td>
                        <td class="px-4 py-2">₨{{ number_format($row['base_price']) }}</td>
                        <td class="px-4 py-2">{{ $row['stock'] }}</td>
                        <td class="px-4 py-2">
                            @if($row['valid'])
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Valid</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Error</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Confirm Import Form -->
        <form method="POST" action="{{ route('vendor.products.bulk-import.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="hidden" name="import_session" value="{{ session('import_id') }}">
            
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    ✓ Confirm & Import
                </button>
                <a href="{{ route('vendor.products.bulk-import') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-3 px-6 rounded-lg text-center">
                    ← Cancel
                </a>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
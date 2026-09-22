@extends('layouts.vendor')

@section('title', 'Request New Category')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Request New Category</h1>
        <p class="text-gray-600 mt-2">If you don't find your category, request it here</p>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <form method="POST" action="{{ route('vendor.categories.request.store') }}" class="space-y-6">
            @csrf

            <!-- Category Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Category Name *</label>
                <input type="text" name="category_name" value="{{ old('category_name') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('category_name') border-red-500 @enderror"
                    placeholder="e.g., Electronic Gadgets">
                @error('category_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description/Reason -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Reason/Description *</label>
                <textarea name="description" rows="5" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('description') border-red-500 @enderror"
                    placeholder="Explain why you need this category and how it will benefit your business...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-600 p-4 rounded">
                <p class="text-sm text-blue-900">
                    <strong>Note:</strong> Your category request will be reviewed by our admin team. You'll receive an email notification once it's approved or rejected. This usually takes 1-2 business days.
                </p>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium">
                    Submit Request
                </button>
                <a href="{{ route('vendor.categories.requests') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-6 py-2 rounded-lg font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
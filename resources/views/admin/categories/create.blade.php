@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ isset($category) ? 'Edit Category' : 'Create New Category' }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <form method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <!-- Category Name -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Category Name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('name') border-red-500 @enderror"
                    placeholder="Enter category name">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Description</label>
                <textarea name="description" rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('description') border-red-500 @enderror"
                    placeholder="Enter category description">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Icon Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Category Icon</label>
                <input type="file" name="icon" accept="image/*"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('icon') border-red-500 @enderror">
                @if(isset($category) && $category->icon)
                    <p class="text-sm text-gray-600 mt-2">Current: <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-12 mt-2 inline-block rounded"></p>
                @endif
                @error('icon')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Category (for subcategories) -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Parent Category (for subcategories)</label>
                <select name="parent_id"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('parent_id') border-red-500 @enderror">
                    <option value="">-- None (Main Category) --</option>
                    @foreach($mainCategories as $main)
                        <option value="{{ $main->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $main->id)>
                            {{ $main->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Commission Rate -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Commission Rate (%) *</label>
                    <div class="flex items-center">
                        <input type="number" name="commission_rate" value="{{ old('commission_rate', $category->commission_rate ?? 10) }}" step="0.01" min="0" max="100" required
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none @error('commission_rate') border-red-500 @enderror"
                            placeholder="10.00">
                        <span class="ml-2 text-gray-600 text-sm">%</span>
                    </div>
                    @error('commission_rate')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Display Order -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $category->display_order ?? 0) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))
                    class="w-4 h-4 text-indigo-600 rounded">
                <label for="is_active" class="ml-3 text-sm font-medium text-gray-900">
                    Active Category
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-6 border-t">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium">
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-6 py-2 rounded-lg font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
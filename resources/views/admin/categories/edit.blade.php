@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')

@section('content')
<div class="container mx-auto max-w-2xl px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">{{ isset($category) ? 'Edit Category' : 'Create New Category' }}</h1>
    </div>

    <div class="bg-white rounded-lg shadow p-8">
        <form id="category-form" method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-6">
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
                <button type="{{ isset($category) ? 'button' : 'submit' }}" id="update-category-button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium">
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-6 py-2 rounded-lg font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@if(isset($category))
<div id="update-category-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="update-category-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-forest/60 backdrop-blur-sm" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-5">
        <div class="w-full max-w-md overflow-hidden border border-mist bg-white shadow-2xl" style="border-radius: 4px;">
            <div class="flex items-start gap-4 border-b border-mist px-6 py-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-marigold-100 text-marigold-600" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-8.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 7.5-7.5z"></path>
                    </svg>
                </div>
                <div>
                    <h2 id="update-category-title" class="text-xl font-semibold text-forest">Save changes?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-graphite">Update <strong class="font-semibold text-forest">{{ $category->name }}</strong> with the information you entered?</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 bg-paper px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" class="border border-mist bg-white px-5 py-2.5 text-sm font-semibold text-forest transition-colors hover:bg-mist" data-modal-close>
                    Keep editing
                </button>
                <button type="button" id="confirm-update-category" class="bg-forest px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-forest-700">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const form = document.getElementById('category-form');
        const updateButton = document.getElementById('update-category-button');
        const modal = document.getElementById('update-category-modal');
        const confirmButton = document.getElementById('confirm-update-category');

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        updateButton.addEventListener('click', () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            confirmButton.focus();
        });

        document.querySelectorAll('[data-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmButton.addEventListener('click', () => {
            form.submit();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endif
@endsection
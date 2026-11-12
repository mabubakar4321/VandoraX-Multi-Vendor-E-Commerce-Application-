@extends('layouts.admin')

@section('title', isset($category) ? 'Edit Category' : 'Create Category')

@section('content')
<div class="mx-auto w-full max-w-3xl space-y-8">
    <div>
        <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">← Back to categories</a>
        <p class="mt-5 text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Catalog management</p>
        <h1 class="mt-2 text-3xl leading-tight">{{ isset($category) ? 'Edit category' : 'Create category' }}</h1>
        <p class="mt-2 text-sm text-graphite/70">Set the category details, hierarchy, and marketplace commission.</p>
    </div>

    <section class="border border-mist bg-white p-5 sm:p-6 lg:p-8" style="border-radius: 2px;">
        <form id="category-form" method="POST" action="{{ isset($category) ? route('admin.categories.update', $category) : route('admin.categories.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($category))
                @method('PUT')
            @endif

            <!-- Category Name -->
            <div>
                <label for="category-name" class="field-label">Category name *</label>
                <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required
                    id="category-name" class="field @error('name') field-invalid @enderror"
                    placeholder="Enter category name">
                @error('name')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="category-description" class="field-label">Description</label>
                <textarea name="description" rows="4"
                    id="category-description" class="field @error('description') field-invalid @enderror"
                    placeholder="Enter category description">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Icon Upload -->
            <div>
                <label for="category-icon" class="field-label">Category icon</label>
                <input type="file" name="icon" accept="image/*"
                    id="category-icon" class="field @error('icon') field-invalid @enderror">
                @if(isset($category) && $category->icon)
                    <div class="mt-3 flex items-center gap-3 text-sm text-graphite/70"><img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="h-12 w-12 rounded-full border border-mist object-cover">Current category icon</div>
                @endif
                @error('icon')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Category (for subcategories) -->
            <div>
                <label for="parent-category" class="field-label">Parent category <span class="font-normal text-graphite/60">(leave empty for a main category)</span></label>
                <select name="parent_id" id="parent-category"
                    class="field @error('parent_id') field-invalid @enderror">
                    <option value="">-- None (Main Category) --</option>
                    @foreach($mainCategories as $main)
                        <option value="{{ $main->id }}" @selected(old('parent_id', $category->parent_id ?? '') == $main->id)>
                            {{ $main->name }}
                        </option>
                    @endforeach
                </select>
                @error('parent_id')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>

            <!-- Commission Rate -->
            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="commission-rate" class="field-label">Commission rate (%) *</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="commission_rate" value="{{ old('commission_rate', $category->commission_rate ?? 10) }}" step="0.01" min="0" max="100" required
                            id="commission-rate" class="field @error('commission_rate') field-invalid @enderror"
                            placeholder="10.00">
                    </div>
                    @error('commission_rate')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Display Order -->
                <div>
                    <label for="display-order" class="field-label">Display order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', $category->display_order ?? 0) }}" min="0"
                        id="display-order" class="field">
                </div>
            </div>

            <!-- Active Status -->
            <div class="flex items-center gap-3 border-t border-mist pt-5">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $category->is_active ?? true))
                    class="h-4 w-4 accent-forest-500">
                <label for="is_active" class="text-sm font-medium text-forest">
                    Active Category
                </label>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col-reverse gap-3 border-t border-mist pt-6 sm:flex-row sm:gap-4">
                <button type="{{ isset($category) ? 'button' : 'submit' }}" id="update-category-button" class="btn-primary px-6 py-2.5">
                    {{ isset($category) ? 'Update Category' : 'Create Category' }}
                </button>
                <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center justify-center border border-mist px-6 py-2.5 text-center text-sm font-semibold text-forest transition-colors hover:bg-paper">
                    Cancel
                </a>
            </div>
        </form>
    </section>
</div>

@if(isset($category))
<div id="update-category-modal" class="fixed inset-0 z-50 hidden overflow-y-auto p-4" aria-labelledby="update-category-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-forest/60 backdrop-blur-sm" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center">
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

@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
                <p class="text-gray-600 mt-1">Manage your product categories</p>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Category
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a2 2 0 012-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Total Categories</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Active</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Main Categories</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['main'] }}</p>
                </div>
            </div>
        </div>

        <a href="{{ route('admin.categories.tree') }}" class="block bg-white rounded-lg shadow p-6 transition-shadow hover:shadow-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            <div class="flex items-center">
                <div class="bg-orange-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-gray-600 text-sm">Subcategories</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $statistics['sub'] }}</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="q" value="{{ $query }}" placeholder="Search categories..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                Search
            </button>
            <a href="{{ route('admin.categories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 px-6 py-2 rounded-lg">
                Reset
            </a>
            <a href="{{ route('admin.categories.tree') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Tree View
            </a>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Name</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Commission</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Products</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($category->icon)
                                <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="w-8 h-8 rounded mr-3">
                            @else
                                <div class="w-8 h-8 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 012 12V7a2 2 0 012-2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                <p class="text-sm text-gray-600">{{ $category->slug }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $category->isMainCategory() ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $category->isMainCategory() ? 'Main' : 'Sub' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-gray-900">{{ $category->commission_rate }}%</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-600">{{ $category->products()->count() }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $category->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline delete-category-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="text-red-600 hover:text-red-900 text-sm font-medium delete-category-button"
                                    data-category-name="{{ $category->name }}">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No categories found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(method_exists($categories, 'links'))
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<div id="delete-category-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="delete-category-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-forest/60 backdrop-blur-sm" data-modal-close></div>

    <div class="relative flex min-h-full items-center justify-center p-5">
        <div class="w-full max-w-md overflow-hidden border border-mist bg-white shadow-2xl" style="border-radius: 4px;">
            <div class="flex items-start gap-4 border-b border-mist px-6 py-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-vermilion-100 text-vermilion" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3m-4 0h14"></path>
                    </svg>
                </div>
                <div>
                    <h2 id="delete-category-title" class="text-xl font-semibold text-forest">Delete category?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-graphite">You are about to remove <strong id="delete-category-name" class="font-semibold text-forest"></strong>. This action cannot be undone.</p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 bg-paper px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" class="border border-mist bg-white px-5 py-2.5 text-sm font-semibold text-forest transition-colors hover:bg-mist" data-modal-close>
                    Keep category
                </button>
                <button type="button" id="confirm-delete-category" class="bg-vermilion px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-vermilion/90">
                    Delete category
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('delete-category-modal');
        const categoryName = document.getElementById('delete-category-name');
        const confirmButton = document.getElementById('confirm-delete-category');
        let selectedForm = null;

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            selectedForm = null;
        };

        document.querySelectorAll('.delete-category-button').forEach((button) => {
            button.addEventListener('click', () => {
                selectedForm = button.closest('.delete-category-form');
                categoryName.textContent = button.dataset.categoryName;
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                confirmButton.focus();
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmButton.addEventListener('click', () => {
            if (selectedForm) {
                selectedForm.submit();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endsection
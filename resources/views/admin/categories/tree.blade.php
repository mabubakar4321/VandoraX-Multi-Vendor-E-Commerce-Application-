@extends('layouts.admin')

@section('title', 'Category Tree')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">Category Hierarchy</h1>
            <a href="{{ route('admin.categories.index') }}" class="text-indigo-600 hover:text-indigo-900">
                Back to Categories
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        @foreach($tree as $category)
            <div class="mb-4">
                <div class="bg-gray-50 p-4 rounded-lg border-l-4 border-indigo-600">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $category['name'] }}</h3>
                            <p class="text-sm text-gray-600">Commission: {{ $category['commission_rate'] }}% | Products: {{ $category['product_count'] }}</p>
                        </div>
                        <a href="{{ route('admin.categories.edit', $category['id']) }}" class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </a>
                    </div>

                    @if(count($category['subcategories']) > 0)
                        <div class="mt-4 space-y-2">
                            @foreach($category['subcategories'] as $subcategory)
                                <div class="ml-6 p-3 bg-white rounded border-l-2 border-purple-500">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $subcategory['name'] }}</p>
                                            <p class="text-xs text-gray-600">Commission: {{ $subcategory['commission_rate'] }}% | Products: {{ $subcategory['product_count'] }}</p>
                                        </div>
                                        <a href="{{ route('admin.categories.edit', $subcategory['id']) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
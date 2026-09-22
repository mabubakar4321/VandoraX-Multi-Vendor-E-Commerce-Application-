@extends('layouts.vendor')

@section('title', isset($product) ? 'Edit Product' : 'Create Product')

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
        <h1 class="text-3xl font-bold text-gray-900">{{ isset($product) ? 'Edit Product' : 'Create New Product' }}</h1>
    </div>

    <form method="POST" 
        action="{{ isset($product) ? route('vendor.products.update', $product) : route('vendor.products.store') }}" 
        class="bg-white rounded-lg shadow p-8 space-y-6">
        @csrf
        @if(isset($product)) @method('PUT') @endif

        <!-- Basic Information -->
        <div class="border-b pb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Basic Information</h2>

            <!-- Product Name -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Product Name *</label>
                <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    placeholder="e.g., iPhone 15 Pro Max">
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description *</label>
                <textarea name="description" rows="6" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    placeholder="Detailed product description...">{{ old('description', $product->description ?? '') }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Category & Subcategory -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category *</label>
                    <select name="category_id" id="category_id" required onchange="loadSubcategories(this.value)"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Subcategory</label>
                    <select name="subcategory_id" id="subcategory_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="">Select Subcategory (if any)</option>
                    </select>
                    @error('subcategory_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Pricing & Inventory -->
        <div class="border-b pb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Pricing & Inventory</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Base Price -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Base Price (PKR) *</label>
                    <input type="number" name="base_price" value="{{ old('base_price', $product->base_price ?? '') }}" 
                        step="0.01" min="0" max="9999999999.99" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="1000">
                    @error('base_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Stock Quantity -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stock Quantity *</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->getTotalStock() ?? 0) }}" 
                        min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="100">
                    @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- SKU -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="Auto-generated">
                    @error('sku') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="border-b pb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Additional Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Warranty -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Warranty</label>
                    <input type="text" name="warranty" value="{{ old('warranty', $product->warranty ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="e.g., 1 Year Manufacturer Warranty">
                    @error('warranty') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Delivery Time -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Time (Days)</label>
                    <input type="number" name="delivery_time_days" value="{{ old('delivery_time_days', $product->delivery_time_days ?? 2) }}" 
                        min="1" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="2">
                    @error('delivery_time_days') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- SEO & Metadata -->
        <div class="border-b pb-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">SEO & Metadata</h2>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title ?? '') }}" maxlength="60"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    placeholder="SEO title (60 chars max)">
                <small class="text-gray-500">Keep it under 60 characters</small>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="3" maxlength="160"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    placeholder="SEO description (160 chars max)">{{ old('meta_description', $product->meta_description ?? '') }}</textarea>
                <small class="text-gray-500">Keep it under 160 characters</small>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Keywords (Comma Separated)</label>
                <input type="text" name="keywords" value="{{ old('keywords', $product->keywords ?? '') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    placeholder="keyword1, keyword2, keyword3">
            </div>
        </div>

        <!-- Status -->
        <div class="pb-6">
            <label class="flex items-center">
                <input type="radio" name="status" value="draft" @checked(old('status', $product->status ?? 'draft') === 'draft')
                    class="w-4 h-4 text-indigo-600">
                <span class="ml-3 text-sm font-medium text-gray-900">Save as Draft</span>
            </label>
            <label class="flex items-center mt-2">
                <input type="radio" name="status" value="published" @checked(old('status', $product->status ?? '') === 'published')
                    class="w-4 h-4 text-indigo-600">
                <span class="ml-3 text-sm font-medium text-gray-900">Publish Now</span>
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 border-t pt-6">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                {{ isset($product) ? 'Update Product' : 'Create Product' }}
            </button>
            <a href="{{ route('vendor.products.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-900 font-bold py-3 px-6 rounded-lg">
                Cancel
            </a>
        </div>
    </form>

    <section class="mt-8 rounded-lg bg-white p-8 shadow">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Product Images</h2>
            <p class="mt-1 text-sm text-gray-600">Upload up to 10 JPG, PNG, or WebP images. Each image must be 5 MB or smaller.</p>
        </div>

        <form method="POST" action="{{ route('vendor.images.upload', $product) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple required
                class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-700">
            @error('images') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            @error('images.*') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            <button type="submit" class="rounded-lg bg-indigo-600 px-6 py-3 font-bold text-white hover:bg-indigo-700">
                Upload Images
            </button>
        </form>

        @if($product->images->isNotEmpty())
            <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($product->images as $image)
                    <div class="rounded-lg border border-gray-200 p-3">
                        <img src="{{ $image->getThumbnailUrl() }}" alt="{{ $product->name }} image" class="aspect-square w-full rounded object-cover">
                        <form method="POST" action="{{ route('vendor.images.delete', [$product, $image]) }}" class="mt-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full rounded border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50" onclick="return confirm('Delete this image?')">
                                Delete
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="mt-6 text-sm text-gray-500">No product images uploaded yet.</p>
        @endif
    </section>
</div>

<script>
function loadSubcategories(categoryId) {
    if (!categoryId) {
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Subcategory</option>';
        return;
    }
    
    fetch(`{{ route('vendor.categories.subcategories', ['category' => '__CATEGORY__']) }}`.replace('__CATEGORY__', categoryId))
        .then(response => response.json())
        .then(data => {
            let options = '<option value="">Select Subcategory</option>';
            data.data.forEach(sub => {
                options += `<option value="${sub.id}">${sub.name}</option>`;
            });
            document.getElementById('subcategory_id').innerHTML = options;
        });
}
</script>
@endsection
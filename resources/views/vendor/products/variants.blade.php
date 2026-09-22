@extends('layouts.vendor')

@section('title', 'Manage Variants - ' . $product->name)

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
        <h1 class="text-3xl font-bold text-gray-900">Manage Variants</h1>
        <p class="text-gray-600 mt-1">{{ $product->name }}</p>
    </div>

    <!-- Add Variant Form -->
    <div class="bg-white rounded-lg shadow p-8 mb-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Add New Variant</h2>

        <form method="POST" action="{{ route('vendor.products.variants.store', $product) }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Variant Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Variant Type *</label>
                    <input type="text" name="variant_name" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="e.g., Storage, Color, Size">
                    @error('variant_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Variant Value -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Variant Value *</label>
                    <input type="text" name="variant_value" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="e.g., 128GB, Blue, Large">
                    @error('variant_value') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Variant Price -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Price (PKR) *</label>
                    <input type="number" name="price" step="0.01" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="150000">
                    @error('price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Variant Stock -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Stock *</label>
                    <input type="number" name="stock" min="0" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                        placeholder="50">
                    @error('stock') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Variant SKU -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Variant SKU</label>
                <input type="text" name="sku"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                    placeholder="Optional variant-specific SKU">
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                + Add Variant
            </button>
        </form>
    </div>

    <!-- Existing Variants -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Type</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Value</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stock</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">SKU</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($product->variants as $variant)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $variant->variant_name }}</td>
                    <td class="px-6 py-4 text-gray-900">{{ $variant->variant_value }}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">₨{{ number_format($variant->price) }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $variant->stock > 5 ? 'bg-green-100 text-green-800' : ($variant->stock > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $variant->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $variant->sku ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <button type="button" class="delete-variant-btn text-red-600 hover:text-red-900 font-medium text-sm"
                                data-variant-id="{{ $variant->id }}"
                                data-variant-name="{{ $variant->variant_name }}: {{ $variant->variant_value }}"
                                data-variant-delete-route="{{ route('vendor.products.variants.destroy', [$product, $variant]) }}">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No variants added yet. Create your first variant above.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Delete Variant Confirmation Modal --}}
<div id="delete-variant-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="delete-variant-modal-title">
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-2xl">
        {{-- Modal Header --}}
        <div class="border-b border-gray-200 px-6 py-5">
            <h2 id="delete-variant-modal-title" class="text-lg font-semibold text-gray-900">Delete Variant</h2>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-6">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6V9m0 4a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-[15px] text-gray-700">
                Are you sure you want to delete this variant:
                <span id="delete-variant-name" class="block font-semibold text-gray-900 mt-2"></span>?
                This action cannot be undone.
            </p>
        </div>

        {{-- Modal Footer --}}
        <div class="border-t border-gray-200 flex gap-3 px-6 py-4">
            <button type="button" id="delete-variant-cancel"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" id="delete-variant-confirm"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
    // Delete variant confirmation modal
    const deleteVariantModal = document.getElementById('delete-variant-modal');
    const deleteVariantName = document.getElementById('delete-variant-name');
    const deleteVariantConfirmBtn = document.getElementById('delete-variant-confirm');
    const deleteVariantCancelBtn = document.getElementById('delete-variant-cancel');
    let currentDeleteVariantRoute = null;

    const closeDeleteVariantModal = () => {
        deleteVariantModal.classList.add('hidden');
        deleteVariantModal.classList.remove('flex');
        currentDeleteVariantRoute = null;
    };

    document.querySelectorAll('.delete-variant-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const variantName = btn.dataset.variantName;
            const deleteRoute = btn.dataset.variantDeleteRoute;

            deleteVariantName.textContent = variantName;
            currentDeleteVariantRoute = deleteRoute;

            deleteVariantModal.classList.remove('hidden');
            deleteVariantModal.classList.add('flex');
            deleteVariantConfirmBtn.focus();
        });
    });

    deleteVariantConfirmBtn.addEventListener('click', async () => {
        if (currentDeleteVariantRoute) {
            deleteVariantConfirmBtn.disabled = true;
            deleteVariantConfirmBtn.textContent = 'Deleting...';

            try {
                const response = await fetch(currentDeleteVariantRoute, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    // Reload page to show updated variants
                    window.location.reload();
                } else {
                    alert('Error deleting variant. Please try again.');
                    deleteVariantConfirmBtn.disabled = false;
                    deleteVariantConfirmBtn.textContent = 'Delete';
                    closeDeleteVariantModal();
                }
            } catch (error) {
                alert('Error: ' + error.message);
                deleteVariantConfirmBtn.disabled = false;
                deleteVariantConfirmBtn.textContent = 'Delete';
                closeDeleteVariantModal();
            }
        }
    });

    deleteVariantCancelBtn.addEventListener('click', closeDeleteVariantModal);
    deleteVariantModal.addEventListener('click', (event) => {
        if (event.target === deleteVariantModal) closeDeleteVariantModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !deleteVariantModal.classList.contains('hidden')) closeDeleteVariantModal();
    });
</script>
@endsection
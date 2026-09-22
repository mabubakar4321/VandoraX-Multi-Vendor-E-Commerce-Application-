@extends('layouts.vendor')

@section('title', $product->name)

@section('content')
<div class="container mx-auto max-w-5xl px-4 py-8">
    <a href="{{ route('vendor.products.index') }}" class="mb-6 inline-flex items-center text-indigo-600 hover:text-indigo-900">
        &larr; Back to Products
    </a>

    <div class="space-y-8 rounded-lg bg-white p-8 shadow">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 pb-6">
            <div>
                <p class="text-sm text-gray-500">Product details</p>
                <h1 class="mt-1 text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-2 text-gray-600">SKU: {{ $product->sku ?: 'Not assigned' }}</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('vendor.products.edit', $product) }}" class="rounded bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">Edit product</a>
                <button type="button" class="delete-product-show-btn rounded bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700"
                        data-product-id="{{ $product->id }}"
                        data-product-name="{{ $product->name }}"
                        data-product-delete-route="{{ route('vendor.products.destroy', $product) }}">
                    Delete product
                </button>
            </div>
        </div>

        @if($product->moderation_status === 'rejected')
            <div class="border-l-4 border-red-500 bg-red-50 p-5">
                <h2 class="font-bold text-red-800">Product rejected by admin</h2>
                <p class="mt-2 text-red-700">{{ $product->moderation_reason ?: 'No reason was provided.' }}</p>
                <p class="mt-3 text-sm text-red-700">You can edit the product and submit it again, or delete it.</p>
            </div>
        @elseif($product->moderation_status === 'flagged')
            <div class="border-l-4 border-orange-500 bg-orange-50 p-5">
                <h2 class="font-bold text-orange-800">Product flagged by admin</h2>
                <p class="mt-2 text-orange-700">{{ $product->moderation_reason ?: 'No reason was provided.' }}</p>
            </div>
        @elseif($product->moderation_status === 'pending')
            <div class="border-l-4 border-yellow-500 bg-yellow-50 p-5 text-yellow-800">This product is waiting for admin approval.</div>
        @endif

        @if($product->images->isNotEmpty())
            <div class="mt-8 grid grid-cols-2 gap-4 md:grid-cols-4">
                @foreach($product->images as $image)
                    <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }} image" class="aspect-square w-full rounded-lg border border-gray-200 object-cover">
                @endforeach
            </div>
        @else
            <p class="mt-8 text-gray-500">No images uploaded for this product.</p>
        @endif

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Product overview</h2>
            <div class="mt-5 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div><p class="text-sm text-gray-500">Category</p><p class="mt-1 font-medium text-gray-900">{{ $product->category?->name ?? 'Not selected' }}</p></div>
                <div><p class="text-sm text-gray-500">Subcategory</p><p class="mt-1 font-medium text-gray-900">{{ $product->subcategory?->name ?? 'Not selected' }}</p></div>
                <div><p class="text-sm text-gray-500">Price</p><p class="mt-1 font-medium text-gray-900">Rs {{ number_format($product->base_price, 2) }}</p></div>
                <div><p class="text-sm text-gray-500">Status</p><p class="mt-1 font-medium text-gray-900">{{ ucfirst($product->status) }}</p></div>
                <div><p class="text-sm text-gray-500">Visibility</p><p class="mt-1 font-medium text-gray-900">{{ $product->is_visible ? 'Visible' : 'Hidden' }}</p></div>
                <div><p class="text-sm text-gray-500">Delivery time</p><p class="mt-1 font-medium text-gray-900">{{ $product->delivery_time_days }} days</p></div>
                <div><p class="text-sm text-gray-500">Total stock</p><p class="mt-1 font-medium text-gray-900">{{ $product->getTotalStock() }}</p></div>
                <div><p class="text-sm text-gray-500">Created</p><p class="mt-1 font-medium text-gray-900">{{ $product->created_at?->format('M d, Y H:i') }}</p></div>
                <div><p class="text-sm text-gray-500">Last updated</p><p class="mt-1 font-medium text-gray-900">{{ $product->updated_at?->format('M d, Y H:i') }}</p></div>
            </div>
        </section>

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Description and product information</h2>
            <p class="mt-4 whitespace-pre-line text-gray-900">{{ $product->description }}</p>
            <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div><p class="text-sm text-gray-500">Warranty</p><p class="mt-1 text-gray-900">{{ $product->warranty ?: 'Not provided' }}</p></div>
                <div><p class="text-sm text-gray-500">Slug</p><p class="mt-1 break-all text-gray-900">{{ $product->slug }}</p></div>
                <div><p class="text-sm text-gray-500">Base SKU</p><p class="mt-1 text-gray-900">{{ $product->sku ?: 'Not assigned' }}</p></div>
            </div>
        </section>

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Variants</h2>
            @if($product->variants->isEmpty())
                <p class="mt-4 text-gray-500">No variants added.</p>
            @else
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b border-gray-200 text-gray-500"><tr><th class="px-3 py-3">Type</th><th class="px-3 py-3">Value</th><th class="px-3 py-3">Price</th><th class="px-3 py-3">Stock</th><th class="px-3 py-3">SKU</th><th class="px-3 py-3">Details</th></tr></thead>
                        <tbody>
                            @foreach($product->variants as $variant)
                                <tr class="border-b border-gray-100"><td class="px-3 py-3">{{ $variant->variant_name }}</td><td class="px-3 py-3 font-medium">{{ $variant->variant_value }}</td><td class="px-3 py-3">Rs {{ number_format($variant->price, 2) }}</td><td class="px-3 py-3">{{ $variant->stock }}</td><td class="px-3 py-3">{{ $variant->sku ?: '-' }}</td><td class="px-3 py-3">{{ $variant->weight ?: '-' }}{{ $variant->dimensions ? ' / ' . $variant->dimensions : '' }}</td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Specifications</h2>
            @if($product->specifications->isEmpty())
                <p class="mt-4 text-gray-500">No specifications added.</p>
            @else
                <dl class="mt-4 grid gap-4 sm:grid-cols-2">
                    @foreach($product->specifications as $specification)
                        <div class="rounded border border-gray-200 p-4"><dt class="text-sm text-gray-500">{{ $specification->spec_key }}</dt><dd class="mt-1 text-gray-900">{{ $specification->spec_value }}</dd></div>
                    @endforeach
                </dl>
            @endif
        </section>

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">SEO metadata</h2>
            <div class="mt-4 grid gap-6 sm:grid-cols-2">
                <div><p class="text-sm text-gray-500">Meta title</p><p class="mt-1 text-gray-900">{{ $product->meta_title ?: 'Not provided' }}</p></div>
                <div><p class="text-sm text-gray-500">Keywords</p><p class="mt-1 text-gray-900">{{ $product->keywords ?: 'Not provided' }}</p></div>
                <div class="sm:col-span-2"><p class="text-sm text-gray-500">Meta description</p><p class="mt-1 whitespace-pre-line text-gray-900">{{ $product->meta_description ?: 'Not provided' }}</p></div>
            </div>
        </section>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div id="delete-product-show-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="delete-product-show-modal-title">
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-2xl">
        {{-- Modal Header --}}
        <div class="border-b border-gray-200 px-6 py-5">
            <h2 id="delete-product-show-modal-title" class="text-lg font-semibold text-gray-900">Delete Product</h2>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-6">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6V9m0 4a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-[15px] text-gray-700">
                Are you sure you want to delete
                <span id="delete-product-show-name" class="font-semibold text-gray-900"></span>
                permanently? This action cannot be undone.
            </p>
        </div>

        {{-- Modal Footer --}}
        <div class="border-t border-gray-200 flex gap-3 px-6 py-4">
            <button type="button" id="delete-product-show-cancel"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" id="delete-product-show-confirm"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors">
                Delete
            </button>
        </div>
    </div>
</div>

<script>
    // Delete confirmation modal
    let deleteShowModal = null;
    let deleteShowProductName = null;
    let deleteShowConfirmBtn = null;
    let deleteShowCancelBtn = null;
    let currentDeleteShowRoute = null;

    // Initialize modal elements after DOM is ready
    function initDeleteShowModal() {
        deleteShowModal = document.getElementById('delete-product-show-modal');
        deleteShowProductName = document.getElementById('delete-product-show-name');
        deleteShowConfirmBtn = document.getElementById('delete-product-show-confirm');
        deleteShowCancelBtn = document.getElementById('delete-product-show-cancel');

        if (!deleteShowModal || !deleteShowProductName || !deleteShowConfirmBtn || !deleteShowCancelBtn) {
            console.error('Delete modal elements not found');
            return;
        }

        // Attach event listeners
        document.querySelectorAll('.delete-product-show-btn').forEach((btn) => {
            btn.addEventListener('click', showDeleteShowModal);
        });

        deleteShowConfirmBtn.addEventListener('click', confirmDeleteShow);
        deleteShowCancelBtn.addEventListener('click', closeDeleteShowModal);
        deleteShowModal.addEventListener('click', function(event) {
            if (event.target === deleteShowModal) closeDeleteShowModal();
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && deleteShowModal && !deleteShowModal.classList.contains('hidden')) {
                closeDeleteShowModal();
            }
        });
    }

    function showDeleteShowModal(e) {
        const btn = e.currentTarget;
        const productName = btn.dataset.productName;
        const deleteRoute = btn.dataset.productDeleteRoute;

        deleteShowProductName.textContent = productName;
        currentDeleteShowRoute = deleteRoute;

        deleteShowModal.classList.remove('hidden');
        deleteShowModal.classList.add('flex');
        deleteShowConfirmBtn.focus();
    }

    function closeDeleteShowModal() {
        deleteShowModal.classList.add('hidden');
        deleteShowModal.classList.remove('flex');
        currentDeleteShowRoute = null;
        deleteShowConfirmBtn.disabled = false;
        deleteShowConfirmBtn.textContent = 'Delete';
    }

    async function confirmDeleteShow() {
        if (!currentDeleteShowRoute) return;

        deleteShowConfirmBtn.disabled = true;
        deleteShowConfirmBtn.textContent = 'Deleting...';

        try {
            // Use POST with _method=DELETE for better compatibility
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch(currentDeleteShowRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (response.ok || response.status === 204 || response.status === 200) {
                // Delete successful - redirect to products page
                setTimeout(() => {
                    window.location.href = '{{ route("vendor.products.index") }}';
                }, 500);
            } else {
                const text = await response.text();
                console.error('Response status:', response.status, 'Body:', text);
                alert('Error deleting product. Status: ' + response.status);
                deleteShowConfirmBtn.disabled = false;
                deleteShowConfirmBtn.textContent = 'Delete';
                closeDeleteShowModal();
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Error: ' + error.message);
            deleteShowConfirmBtn.disabled = false;
            deleteShowConfirmBtn.textContent = 'Delete';
            closeDeleteShowModal();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeleteShowModal);
    } else {
        initDeleteShowModal();
    }
</script>
@endsection

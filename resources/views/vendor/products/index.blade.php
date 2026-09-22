@extends('layouts.vendor')

@section('title', 'My Products')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <p class="text-sm font-medium text-forest-500">Seller Centre</p>
        <h1 class="mt-1 text-[32px] leading-tight">My Products</h1>
        <p class="mt-2 text-[15px] text-graphite">Manage your product catalog</p>
    </div>
</div>

{{-- Product Tools Section --}}
<div class="mt-6 flex flex-wrap items-center gap-3 border border-mist bg-white px-4 py-4 mb-8" style="border-radius: 2px;">
    <p class="mr-2 text-[15px] font-medium text-forest">Product tools</p>
    <a href="{{ route('vendor.products.create') }}" class="border border-forest px-4 py-2 text-[14px] font-medium text-forest transition hover:bg-forest hover:text-white" style="border-radius: 2px;">
        + Add product
    </a>
    <a href="{{ route('vendor.products.bulk-import') }}" class="border border-mist px-4 py-2 text-[14px] font-medium text-forest transition hover:border-forest" style="border-radius: 2px;">
        📥 Bulk import
    </a>
    <a href="{{ route('vendor.products.import-template') }}" class="border border-mist px-4 py-2 text-[14px] font-medium text-forest transition hover:border-forest" style="border-radius: 2px;">
        📋 Import template
    </a>
</div>

<!-- Statistics -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm">Total Products</p>
        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm">Published</p>
        <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['published'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm">Drafts</p>
        <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['draft'] }}</p>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-gray-600 text-sm">Low Stock</p>
        <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['low_stock'] }}</p>
    </div>
</div>

<!-- Search & Filter -->
<div class="bg-white rounded-lg shadow mb-6 p-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name/SKU..."
            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">

        <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            <option value="">All Status</option>
            <option value="draft" @selected(request('status') == 'draft')>Draft</option>
            <option value="published" @selected(request('status') == 'published')>Published</option>
            <option value="archived" @selected(request('status') == 'archived')>Archived</option>
        </select>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
            Search
        </button>
    </form>
</div>

<!-- Products Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Category</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Stock</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($product->images->first())
                                <button type="button" class="product-image-trigger mr-3" data-image-url="{{ $product->images->first()->getImageUrl() }}" data-product-url="{{ route('vendor.products.show', $product) }}" data-product-name="{{ $product->name }}" aria-label="View {{ $product->name }} image">
                                    <img src="{{ $product->images->first()->getThumbnailUrl() }}" alt="{{ $product->name }}" class="h-12 w-12 rounded object-cover">
                                </button>
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
                                @if($product->moderation_status === 'rejected')
                                    <p class="mt-1 text-xs font-medium text-red-600">Rejected by admin</p>
                                    <p class="text-xs text-red-600">{{ $product->moderation_reason }}</p>
                                @elseif($product->moderation_status === 'flagged')
                                    <p class="mt-1 text-xs font-medium text-orange-600">Flagged by admin</p>
                                    <p class="text-xs text-orange-600">{{ $product->moderation_reason }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $product->category->name }}
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                        ₨{{ number_format($product->base_price) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $product->getTotalStock() > 5 ? 'bg-green-100 text-green-800' : ($product->getTotalStock() > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $product->getTotalStock() }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block rounded-full px-3 py-1 text-sm font-medium {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ ucfirst($product->status) }}</span>
                        <span class="mt-1 inline-block rounded-full px-3 py-1 text-xs font-medium {{ $product->moderation_status === 'approved' ? 'bg-green-100 text-green-800' : ($product->moderation_status === 'rejected' ? 'bg-red-100 text-red-800' : ($product->moderation_status === 'flagged' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">Admin: {{ ucfirst($product->moderation_status) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('vendor.products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                Edit
                            </a>
                            <a href="{{ route('vendor.products.show', $product) }}" class="text-blue-600 hover:text-blue-900 font-medium text-sm">
                                View product
                            </a>
                            <button type="button" class="delete-product-btn text-red-600 hover:text-red-900 font-medium text-sm"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-product-delete-route="{{ route('vendor.products.destroy', $product) }}">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No products found. <a href="{{ route('vendor.products.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first product</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>

<div id="product-image-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4" role="dialog" aria-modal="true" aria-labelledby="product-image-modal-title">
    <div class="relative max-h-full w-full max-w-3xl rounded-lg bg-white p-5 shadow-xl">
        <button type="button" id="product-image-modal-close" class="absolute right-3 top-3 rounded-full bg-gray-100 px-3 py-1 text-xl text-gray-700 hover:bg-gray-200" aria-label="Close image preview">&times;</button>
        <h2 id="product-image-modal-title" class="pr-10 text-lg font-bold text-gray-900"></h2>
        <img id="product-image-modal-image" src="" alt="" class="mt-4 max-h-[65vh] w-full rounded object-contain">
        <a id="product-image-modal-link" href="#" class="mt-5 inline-block rounded bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">View product</a>
    </div>
</div>

<script>
    const imageModal = document.getElementById('product-image-modal');
    const imageModalImage = document.getElementById('product-image-modal-image');
    const imageModalTitle = document.getElementById('product-image-modal-title');
    const imageModalLink = document.getElementById('product-image-modal-link');
    const closeImageModal = () => {
        imageModal.classList.add('hidden');
        imageModal.classList.remove('flex');
        imageModalImage.src = '';
    };

    document.querySelectorAll('.product-image-trigger').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            imageModalImage.src = trigger.dataset.imageUrl;
            imageModalImage.alt = trigger.dataset.productName;
            imageModalTitle.textContent = trigger.dataset.productName;
            imageModalLink.href = trigger.dataset.productUrl;
            imageModal.classList.remove('hidden');
            imageModal.classList.add('flex');
        });
    });

    document.getElementById('product-image-modal-close').addEventListener('click', closeImageModal);
    imageModal.addEventListener('click', (event) => {
        if (event.target === imageModal) closeImageModal();
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeImageModal();
    });

    // Delete confirmation modal
    let deleteModal = null;
    let deleteProductName = null;
    let deleteConfirmBtn = null;
    let deleteCancelBtn = null;
    let currentDeleteRoute = null;
    let currentDeleteProductId = null;

    // Initialize modal elements after DOM is ready
    function initDeleteModal() {
        deleteModal = document.getElementById('delete-product-modal');
        deleteProductName = document.getElementById('delete-product-name');
        deleteConfirmBtn = document.getElementById('delete-product-confirm');
        deleteCancelBtn = document.getElementById('delete-product-cancel');

        if (!deleteModal || !deleteProductName || !deleteConfirmBtn || !deleteCancelBtn) {
            console.error('Delete modal elements not found');
            return;
        }

        // Attach event listeners
        document.querySelectorAll('.delete-product-btn').forEach((btn) => {
            btn.addEventListener('click', showDeleteModal);
        });

        deleteConfirmBtn.addEventListener('click', confirmDelete);
        deleteCancelBtn.addEventListener('click', closeDeleteModal);
        deleteModal.addEventListener('click', function(event) {
            if (event.target === deleteModal) closeDeleteModal();
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && deleteModal && !deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }
        });
    }

    function showDeleteModal(e) {
        const btn = e.currentTarget;
        const productName = btn.dataset.productName;
        const deleteRoute = btn.dataset.productDeleteRoute;
        const productId = btn.dataset.productId;

        deleteProductName.textContent = productName;
        currentDeleteRoute = deleteRoute;
        currentDeleteProductId = productId;

        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
        deleteConfirmBtn.focus();
    }

    function closeDeleteModal() {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
        currentDeleteRoute = null;
        currentDeleteProductId = null;
        deleteConfirmBtn.disabled = false;
        deleteConfirmBtn.textContent = 'Delete';
    }

    async function confirmDelete() {
        if (!currentDeleteRoute) return;

        deleteConfirmBtn.disabled = true;
        deleteConfirmBtn.textContent = 'Deleting...';

        try {
            // Use POST with _method=DELETE for better compatibility
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch(currentDeleteRoute, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });

            if (response.ok || response.status === 204 || response.status === 200) {
                // Delete successful - reload the page
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } else {
                const text = await response.text();
                console.error('Response status:', response.status, 'Body:', text);
                alert('Error deleting product. Status: ' + response.status);
                deleteConfirmBtn.disabled = false;
                deleteConfirmBtn.textContent = 'Delete';
                closeDeleteModal();
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Error: ' + error.message);
            deleteConfirmBtn.disabled = false;
            deleteConfirmBtn.textContent = 'Delete';
            closeDeleteModal();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDeleteModal);
    } else {
        initDeleteModal();
    }
</script>

{{-- Delete Confirmation Modal --}}
<div id="delete-product-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="delete-product-modal-title">
    <div class="relative w-full max-w-md rounded-lg bg-white shadow-2xl">
        {{-- Modal Header --}}
        <div class="border-b border-mist px-6 py-5">
            <h2 id="delete-product-modal-title" class="text-lg font-semibold text-forest">Delete Product</h2>
        </div>

        {{-- Modal Body --}}
        <div class="px-6 py-6">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-vermilion-100">
                <svg class="h-6 w-6 text-vermilion" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-6V9m0 4a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <p class="text-[15px] text-graphite">
                Are you sure you want to delete
                <span id="delete-product-name" class="font-semibold text-forest"></span>?
                This action cannot be undone.
            </p>
        </div>

        {{-- Modal Footer --}}
        <div class="border-t border-mist flex gap-3 px-6 py-4">
            <button type="button" id="delete-product-cancel"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-medium text-forest border border-mist hover:bg-paper transition-colors">
                Cancel
            </button>
            <button type="button" id="delete-product-confirm"
                    class="flex-1 rounded px-4 py-2.5 text-[14px] font-semibold text-white bg-vermilion hover:bg-vermilion/90 transition-colors">
                Delete
            </button>
        </div>
    </div>
</div>

@endsection
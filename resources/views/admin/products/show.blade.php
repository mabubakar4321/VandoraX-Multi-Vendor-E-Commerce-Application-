@extends('layouts.admin')

@section('title', 'Product Details - ' . $product->name)

@section('content')
<div class="container mx-auto max-w-6xl px-4 py-8">
    <a href="{{ route('admin.products.index') }}" class="mb-6 inline-flex items-center text-indigo-600 hover:text-indigo-900">&larr; Back to all products</a>

    <div class="space-y-8 rounded-lg bg-white p-4 shadow sm:p-6 lg:p-8">
        <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-200 pb-6">
            <div class="min-w-0">
                <p class="text-sm text-gray-500">Admin product review</p>
                <h1 class="mt-1 break-words text-3xl font-bold text-gray-900">{{ $product->name }}</h1>
                <p class="mt-2 text-gray-600">SKU: {{ $product->sku ?: 'Not assigned' }}</p>
                <p class="mt-2 break-words text-sm text-gray-600">Vendor: {{ $product->vendor?->store_name ?? 'Unknown' }} ({{ $product->vendor?->user?->email ?? 'No email' }})</p>
            </div>
            <span class="rounded-full px-4 py-2 text-sm font-semibold {{ $product->moderation_status === 'approved' ? 'bg-green-100 text-green-800' : ($product->moderation_status === 'rejected' ? 'bg-red-100 text-red-800' : ($product->moderation_status === 'flagged' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800')) }}">
                {{ ucfirst($product->moderation_status) }}
            </span>
        </div>

        @if($product->images->isNotEmpty())
            <div class="grid grid-cols-2 gap-4 md:grid-cols-5">
                @foreach($product->images as $image)
                    <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }} image" class="aspect-square w-full rounded-lg border border-gray-200 object-cover">
                @endforeach
            </div>
        @else
            <p class="text-gray-500">No images uploaded.</p>
        @endif

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Product information</h2>
        <div class="mt-5 grid min-w-0 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div><p class="text-sm text-gray-500">Category</p><p class="mt-1 font-medium">{{ $product->category?->name ?? 'Not selected' }}</p></div>
                <div><p class="text-sm text-gray-500">Subcategory</p><p class="mt-1 font-medium">{{ $product->subcategory?->name ?? 'Not selected' }}</p></div>
                <div><p class="text-sm text-gray-500">Price</p><p class="mt-1 font-medium">Rs {{ number_format($product->base_price, 2) }}</p></div>
                <div><p class="text-sm text-gray-500">Vendor status</p><p class="mt-1 font-medium">{{ ucfirst($product->status) }}</p></div>
                <div><p class="text-sm text-gray-500">Visibility</p><p class="mt-1 font-medium">{{ $product->is_visible ? 'Visible' : 'Hidden' }}</p></div>
                <div><p class="text-sm text-gray-500">Delivery time</p><p class="mt-1 font-medium">{{ $product->delivery_time_days }} days</p></div>
                <div><p class="text-sm text-gray-500">Total stock</p><p class="mt-1 font-medium">{{ $product->getTotalStock() }}</p></div>
                <div><p class="text-sm text-gray-500">Warranty</p><p class="mt-1 font-medium">{{ $product->warranty ?: 'Not provided' }}</p></div>
                <div><p class="text-sm text-gray-500">Created</p><p class="mt-1 font-medium">{{ $product->created_at?->format('M d, Y H:i') }}</p></div>
            </div>
            <p class="mt-6 whitespace-pre-line text-gray-900">{{ $product->description }}</p>
        </section>

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Variants</h2>
            @if($product->variants->isEmpty())
                <p class="mt-3 text-gray-500">No variants added.</p>
            @else
                <div class="mt-4 overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b"><tr><th class="px-3 py-3">Type</th><th class="px-3 py-3">Value</th><th class="px-3 py-3">Price</th><th class="px-3 py-3">Stock</th><th class="px-3 py-3">SKU</th></tr></thead><tbody>
                    @foreach($product->variants as $variant)
                        <tr class="border-b border-gray-100"><td class="px-3 py-3">{{ $variant->variant_name }}</td><td class="px-3 py-3">{{ $variant->variant_value }}</td><td class="px-3 py-3">Rs {{ number_format($variant->price, 2) }}</td><td class="px-3 py-3">{{ $variant->stock }}</td><td class="px-3 py-3">{{ $variant->sku ?: '-' }}</td></tr>
                    @endforeach
                </tbody></table></div>
            @endif
        </section>

        <section class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-bold text-gray-900">Specifications and SEO</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach($product->specifications as $specification)
                    <div class="rounded border border-gray-200 p-4"><p class="text-sm text-gray-500">{{ $specification->spec_key }}</p><p class="mt-1">{{ $specification->spec_value }}</p></div>
                @endforeach
                <div><p class="text-sm text-gray-500">Meta title</p><p class="mt-1">{{ $product->meta_title ?: 'Not provided' }}</p></div>
                <div><p class="text-sm text-gray-500">Keywords</p><p class="mt-1">{{ $product->keywords ?: 'Not provided' }}</p></div>
                <div class="sm:col-span-2"><p class="text-sm text-gray-500">Meta description</p><p class="mt-1">{{ $product->meta_description ?: 'Not provided' }}</p></div>
            </div>
        </section>

        @if($product->moderation_reason)
            <div class="border-t border-gray-200 pt-6"><p class="text-sm font-semibold text-gray-500">Moderation reason</p><p class="mt-1 text-gray-900">{{ $product->moderation_reason }}</p></div>
        @endif

        <section class="flex flex-wrap gap-3 border-t border-gray-200 pt-6">
            @if($product->moderation_status !== 'approved')
                <form method="POST" action="{{ route('admin.products.approve', $product) }}">@csrf<button class="rounded bg-green-600 px-5 py-2 font-medium text-white hover:bg-green-700">Approve product</button></form>
            @endif
            <form id="reject-form" method="POST" action="{{ route('admin.products.reject', $product) }}">@csrf<input type="hidden" name="reason"><button type="button" onclick="openModerationModal('reject-form', 'Reject product', 'Reason for rejecting this product:')" class="rounded bg-red-600 px-5 py-2 font-medium text-white hover:bg-red-700">Reject product</button></form>
            <form id="flag-form" method="POST" action="{{ route('admin.products.flag', $product) }}">@csrf<input type="hidden" name="reason"><button type="button" onclick="openModerationModal('flag-form', 'Flag product', 'Reason for flagging this product:')" class="rounded bg-orange-500 px-5 py-2 font-medium text-white hover:bg-orange-600">Flag product</button></form>
        </section>
    </div>
</div>
<div id="moderation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="moderation-modal-title">
    <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4"><h2 id="moderation-modal-title" class="text-xl font-bold text-gray-900"></h2><button type="button" onclick="closeModerationModal()" class="text-2xl leading-none text-gray-500" aria-label="Close">&times;</button></div>
        <label for="moderation-reason" id="moderation-modal-label" class="mt-5 block text-sm font-medium text-gray-700"></label>
        <textarea id="moderation-reason" rows="4" maxlength="1000" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3" required></textarea>
        <div class="mt-5 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button type="button" onclick="closeModerationModal()" class="rounded-lg bg-gray-100 px-5 py-2 font-medium text-gray-700">Cancel</button><button type="button" onclick="submitModerationForm()" class="rounded-lg bg-indigo-600 px-5 py-2 font-medium text-white">Submit</button></div>
    </div>
</div>
<script>
let activeModerationForm = null;
function openModerationModal(formId, title, label) {
    activeModerationForm = document.getElementById(formId);
    document.getElementById('moderation-modal-title').textContent = title;
    document.getElementById('moderation-modal-label').textContent = label;
    document.getElementById('moderation-reason').value = '';
    const modal = document.getElementById('moderation-modal');
    modal.classList.remove('hidden'); modal.classList.add('flex');
    document.getElementById('moderation-reason').focus();
}
function closeModerationModal() {
    const modal = document.getElementById('moderation-modal');
    modal.classList.add('hidden'); modal.classList.remove('flex'); activeModerationForm = null;
}
function submitModerationForm() {
    const reason = document.getElementById('moderation-reason').value.trim();
    if (!reason || !activeModerationForm) return;
    activeModerationForm.querySelector('input[name="reason"]').value = reason;
    activeModerationForm.submit();
}
</script>
@endsection

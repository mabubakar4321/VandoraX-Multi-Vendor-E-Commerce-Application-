    @extends('layouts.admin')

@section('title', 'Product Moderation')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Product Moderation</h1>
        <p class="text-gray-600 mt-1">View products listed by every vendor</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Total Products</p>
            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Pending review</p>
            <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Rejected</p>
            <p class="text-3xl font-bold text-red-600 mt-2">{{ $stats['rejected'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-600 text-sm">Approved</p>
            <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['approved'] }}</p>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name/vendor..." 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            
            <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">All Status</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                <option value="flagged" @selected(request('status') == 'flagged')>Flagged</option>
            </select>

            <select name="category" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                Filter
            </button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vendor</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Category</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Price</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                        <p class="text-sm text-gray-600">{{ $product->sku }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-gray-900">{{ $product->vendor?->user?->name ?? 'Unknown vendor' }}</p>
                        <p class="text-sm text-gray-600">{{ $product->vendor?->store_name ?? 'Unknown store' }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-600">
                        {{ $product->category?->name ?? 'Uncategorized' }}
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900">
                        ₨{{ number_format($product->base_price) }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ 
                            $product->moderation_status === 'approved' ? 'bg-green-100 text-green-800' :
                            ($product->moderation_status === 'rejected' ? 'bg-red-100 text-red-800' :
                            ($product->moderation_status === 'flagged' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800'))
                        }}">
                            {{ ucfirst($product->moderation_status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">
                                Details
                            </a>
                            @if($product->moderation_status !== 'approved')
                                <form method="POST" action="{{ route('admin.products.approve', $product) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-900 font-medium text-sm">Approve</button>
                                </form>
                            @endif
                            <form id="reject-form-{{ $product->id }}" method="POST" action="{{ route('admin.products.reject', $product) }}" class="inline">
                                @csrf
                                <input type="hidden" name="reason">
                                <button type="button" onclick="openModerationModal('reject-form-{{ $product->id }}', 'Reject product', 'Reason for rejecting this product:')" class="text-red-600 hover:text-red-900 font-medium text-sm">Reject</button>
                            </form>
                            <form id="flag-form-{{ $product->id }}" method="POST" action="{{ route('admin.products.flag', $product) }}" class="inline">
                                @csrf
                                <input type="hidden" name="reason">
                                <button type="button" onclick="openModerationModal('flag-form-{{ $product->id }}', 'Flag product', 'Reason for flagging this product:')" class="text-orange-600 hover:text-orange-900 font-medium text-sm">Flag</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        No products found
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

<div id="moderation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 p-4" role="dialog" aria-modal="true" aria-labelledby="moderation-modal-title">
    <div class="w-full max-w-lg rounded-lg bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between gap-4">
            <h2 id="moderation-modal-title" class="text-xl font-bold text-gray-900"></h2>
            <button type="button" onclick="closeModerationModal()" class="text-2xl leading-none text-gray-500 hover:text-gray-900" aria-label="Close">&times;</button>
        </div>
        <label for="moderation-reason" id="moderation-modal-label" class="mt-5 block text-sm font-medium text-gray-700"></label>
        <textarea id="moderation-reason" rows="4" maxlength="1000" class="mt-2 w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-indigo-500" required></textarea>
        <div class="mt-5 flex justify-end gap-3">
            <button type="button" onclick="closeModerationModal()" class="rounded-lg bg-gray-100 px-5 py-2 font-medium text-gray-700 hover:bg-gray-200">Cancel</button>
            <button type="button" onclick="submitModerationForm()" class="rounded-lg bg-indigo-600 px-5 py-2 font-medium text-white hover:bg-indigo-700">Submit</button>
        </div>
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
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('moderation-reason').focus();
}

function closeModerationModal() {
    const modal = document.getElementById('moderation-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    activeModerationForm = null;
}

function submitModerationForm() {
    const reason = document.getElementById('moderation-reason').value.trim();
    if (!reason || !activeModerationForm) return;
    activeModerationForm.querySelector('input[name="reason"]').value = reason;
    activeModerationForm.submit();
}

document.getElementById('moderation-modal').addEventListener('click', (event) => {
    if (event.target.id === 'moderation-modal') closeModerationModal();
});
</script>
@endsection
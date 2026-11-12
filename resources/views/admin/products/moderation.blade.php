@extends('layouts.admin')

@section('title', 'All Products — Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Catalog management</p>
            <h1 class="mt-2 text-3xl leading-tight">All products</h1>
            <p class="mt-2 text-sm text-graphite/70">Review products listed by vendors and manage their moderation status.</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn-dark w-full px-4 py-2.5 sm:w-auto">Back to dashboard</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        @foreach ([['Total products', 'total', 'border-forest-500'], ['Pending review', 'pending', 'border-marigold'], ['Approved', 'approved', 'border-indigo-500'], ['Rejected', 'rejected', 'border-vermilion'], ['Flagged', 'flagged', 'border-graphite']] as [$label, $key, $border])
            <div class="border-t-2 {{ $border }} bg-white p-4 shadow-sm" style="border-radius: 2px;">
                <p class="text-sm text-graphite/70">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl text-forest">{{ number_format($stats[$key]) }}</p>
            </div>
        @endforeach
    </div>

    <section class="border border-mist bg-white p-4 sm:p-5" style="border-radius: 2px;">
        <form method="GET" class="grid min-w-0 gap-3 sm:grid-cols-2 xl:grid-cols-[minmax(0,1fr)_minmax(10rem,0.65fr)_minmax(10rem,0.65fr)_auto_auto]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product or vendor..." aria-label="Search products" class="field">

            <select name="status" aria-label="Filter by moderation status" class="field">
                <option value="">All statuses</option>
                <option value="pending" @selected(request('status') == 'pending')>Pending</option>
                <option value="approved" @selected(request('status') == 'approved')>Approved</option>
                <option value="rejected" @selected(request('status') == 'rejected')>Rejected</option>
                <option value="flagged" @selected(request('status') == 'flagged')>Flagged</option>
            </select>

            <select name="category" aria-label="Filter by category" class="field">
                <option value="">All categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="btn-primary w-full px-5 py-2.5 xl:w-auto">Apply filters</button>
            @if(request()->anyFilled(['search', 'status', 'category']))
                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center justify-center border border-mist px-5 py-2.5 text-sm font-semibold text-forest transition-colors hover:bg-paper">Reset</a>
            @endif
        </form>
    </section>

    <section class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
        <div class="border-b border-mist px-5 py-4 sm:px-6">
            <h2 class="text-xl">Product directory</h2>
            <p class="mt-1 text-sm text-graphite/65">Product details, vendor information, and moderation actions.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65">
                    <tr>
                        <th class="px-5 py-3 sm:px-6">Product</th>
                        <th class="px-5 py-3 sm:px-6">Vendor</th>
                        <th class="px-5 py-3 sm:px-6">Category</th>
                        <th class="px-5 py-3 sm:px-6">Price</th>
                        <th class="px-5 py-3 sm:px-6">Status</th>
                        <th class="px-5 py-3 sm:px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse($products as $product)
                        <tr class="hover:bg-paper/70">
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex min-w-0 items-center gap-3">
                                    @if($product->images->isNotEmpty())
                                        <img src="{{ $product->images->first()->getThumbnailUrl() }}" alt="" class="h-11 w-11 shrink-0 border border-mist object-cover" style="border-radius: 2px;">
                                    @else
                                        <div class="flex h-11 w-11 shrink-0 items-center justify-center bg-paper text-graphite/45" aria-hidden="true">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 5h16v14H4zM4 15l4-4 4 4 3-3 5 5M9 9h.01"/></svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="break-words font-semibold text-forest">{{ $product->name }}</p>
                                        <p class="mt-1 text-xs text-graphite/60">SKU: {{ $product->sku ?: 'Not assigned' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-forest">{{ $product->vendor?->user?->name ?? 'Unknown vendor' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $product->vendor?->store_name ?? 'Unknown store' }}</p>
                            </td>
                            <td class="px-5 py-4 text-graphite sm:px-6">{{ $product->category?->name ?? 'Uncategorized' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-forest sm:px-6">Rs {{ number_format($product->base_price, 2) }}</td>
                            <td class="px-5 py-4 sm:px-6">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $product->moderation_status === 'approved' ? 'bg-forest-100 text-forest' : ($product->moderation_status === 'rejected' ? 'bg-vermilion-100 text-vermilion' : ($product->moderation_status === 'flagged' ? 'bg-marigold-100 text-forest' : 'bg-paper text-graphite')) }}">
                                    {{ ucfirst($product->moderation_status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <div class="flex flex-wrap gap-x-3 gap-y-2">
                                    <a href="{{ route('admin.products.show', $product) }}" class="font-semibold text-forest-500 hover:text-forest">Details</a>
                                    @if($product->moderation_status !== 'approved')
                                        <form method="POST" action="{{ route('admin.products.approve', $product) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="font-semibold text-forest-500 hover:text-forest">Approve</button>
                                        </form>
                                    @endif
                                    <form id="reject-form-{{ $product->id }}" method="POST" action="{{ route('admin.products.reject', $product) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="reason">
                                        <button type="button" onclick="openModerationModal('reject-form-{{ $product->id }}', 'Reject product', 'Reason for rejecting this product:')" class="font-semibold text-vermilion hover:text-vermilion/80">Reject</button>
                                    </form>
                                    <form id="flag-form-{{ $product->id }}" method="POST" action="{{ route('admin.products.flag', $product) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="reason">
                                        <button type="button" onclick="openModerationModal('flag-form-{{ $product->id }}', 'Flag product', 'Reason for flagging this product:')" class="font-semibold text-graphite hover:text-forest">Flag</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <p class="font-semibold text-forest">No products found</p>
                                <p class="mt-1 text-sm text-graphite/65">Try adjusting your search or filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="border-t border-mist px-5 py-4 sm:px-6">{{ $products->links() }}</div>
        @endif
    </section>
</div>

<div id="moderation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-forest/60 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="moderation-modal-title">
    <div class="w-full max-w-lg overflow-hidden border border-mist bg-white shadow-2xl" style="border-radius: 2px;">
        <div class="flex items-start justify-between gap-4 border-b border-mist px-6 py-5">
            <h2 id="moderation-modal-title" class="text-xl"></h2>
            <button type="button" onclick="closeModerationModal()" class="text-2xl leading-none text-graphite/60 hover:text-forest" aria-label="Close">&times;</button>
        </div>
        <div class="px-6 py-5">
            <label for="moderation-reason" id="moderation-modal-label" class="block text-sm font-medium text-forest"></label>
            <textarea id="moderation-reason" rows="4" maxlength="1000" class="field mt-2" required></textarea>
            <p class="mt-1.5 text-[13px] text-graphite/65">Enter a reason to include in the moderation record.</p>
        </div>
        <div class="flex flex-col-reverse gap-3 bg-paper px-6 py-4 sm:flex-row sm:justify-end">
            <button type="button" onclick="closeModerationModal()" class="border border-mist bg-white px-5 py-2.5 text-sm font-semibold text-forest transition-colors hover:bg-mist">Cancel</button>
            <button type="button" onclick="submitModerationForm()" class="btn-primary w-full px-5 py-2.5 sm:w-auto">Submit</button>
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
    document.body.classList.add('overflow-hidden');
    document.getElementById('moderation-reason').focus();
}

function closeModerationModal() {
    const modal = document.getElementById('moderation-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
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

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !document.getElementById('moderation-modal').classList.contains('hidden')) {
        closeModerationModal();
    }
});
</script>
@endsection

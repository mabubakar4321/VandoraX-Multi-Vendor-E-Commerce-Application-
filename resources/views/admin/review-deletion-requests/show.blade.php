@extends('layouts.admin')

@section('title', 'Review Deletion Request')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 max-w-5xl">
    <div class="flex items-center justify-between gap-4 mb-6">
        <div>
            <a href="{{ route('admin.review-deletion-requests.index') }}" class="text-sm font-medium text-forest-light hover:text-forest">&larr; All deletion requests</a>
            <h1 class="mt-2 text-2xl font-bold text-forest">Review deletion request</h1>
        </div>
        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $reviewDeletionRequest->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($reviewDeletionRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($reviewDeletionRequest->status) }}</span>
    </div>

    @if (session('success'))<div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @if (session('error'))<div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ session('error') }}</div>@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <section class="rounded-xl border border-mist bg-white p-5 sm:p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-graphite">Customer review</p>
                <h2 class="mt-2 text-lg font-bold text-forest">{{ $reviewDeletionRequest->review?->review_title ?: 'Review without title' }}</h2>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-graphite">{{ $reviewDeletionRequest->review?->review_text ?? 'The review has already been removed.' }}</p>
                @if ($reviewDeletionRequest->review)
                    <div class="mt-5 grid grid-cols-2 gap-4 border-t border-mist pt-4 text-sm">
                        <div><p class="text-xs text-graphite">Product</p><p class="font-semibold text-forest">{{ $reviewDeletionRequest->review->product?->name ?? 'N/A' }}</p></div>
                        <div><p class="text-xs text-graphite">Customer</p><p class="font-semibold text-forest">{{ $reviewDeletionRequest->review->customer?->name ?? 'Anonymous' }}</p></div>
                    </div>
                @endif
            </section>

            <section class="rounded-xl border border-red-100 bg-red-50/40 p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Vendor explanation</p>
                <p class="mt-3 whitespace-pre-line text-sm leading-6 text-gray-800">{{ $reviewDeletionRequest->reason }}</p>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="rounded-xl border border-mist bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-graphite">Request details</p>
                <p class="mt-3 text-sm text-graphite">Vendor</p>
                <p class="font-semibold text-forest">{{ $reviewDeletionRequest->vendor?->store_name ?? 'Unknown vendor' }}</p>
                <p class="mt-3 text-sm text-graphite">Submitted</p>
                <p class="font-semibold text-forest">{{ $reviewDeletionRequest->created_at->format('M d, Y g:i A') }}</p>
            </section>

            @if ($reviewDeletionRequest->status === 'pending')
                <section class="rounded-xl border border-mist bg-white p-5 shadow-sm space-y-3">
                    <form method="POST" action="{{ route('admin.review-deletion-requests.approve', $reviewDeletionRequest) }}" onsubmit="return confirm('Delete this review permanently and recalculate ratings?')">
                        @csrf
                        <button class="w-full rounded-md bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Approve &amp; delete review</button>
                    </form>
                    <form method="POST" action="{{ route('admin.review-deletion-requests.reject', $reviewDeletionRequest) }}">
                        @csrf
                        <label for="admin_notes" class="block text-sm font-semibold text-forest">Reason if rejecting</label>
                        <textarea id="admin_notes" name="admin_notes" required minlength="5" rows="4" class="mt-2 w-full rounded-md border-mist text-sm focus:border-forest-light focus:ring-forest-light" placeholder="Explain the decision...">{{ old('admin_notes') }}</textarea>
                        @error('admin_notes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        <button class="mt-3 w-full rounded-md bg-forest px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest-dark">Reject request</button>
                    </form>
                </section>
            @elseif ($reviewDeletionRequest->admin_notes)
                <section class="rounded-xl border border-mist bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-wide text-graphite">Admin notes</p>
                    <p class="mt-3 whitespace-pre-line text-sm text-graphite">{{ $reviewDeletionRequest->admin_notes }}</p>
                </section>
            @endif
        </aside>
    </div>
</div>
@endsection

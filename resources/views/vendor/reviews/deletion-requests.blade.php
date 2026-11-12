@extends('layouts.vendor')

@section('title', 'Request Review Deletion')

@section('content')
<div class="w-full max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.14em] text-forest-light">Review system</p>
        <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-forest">Request review deletion</h1>
        <p class="mt-2 text-sm text-graphite">Select a review, verify its details, and explain why an admin should remove it.</p>
    </div>

    @if (session('success'))
        <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="mb-5 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
    @endif

    <div class="rounded-xl border border-mist bg-white p-5 sm:p-7 shadow-sm">
        <h2 class="text-lg font-bold text-forest">Submit a deletion request</h2>

        @if ($reviews->isEmpty())
            <p class="mt-5 rounded-lg bg-paper px-4 py-5 text-sm text-graphite">You do not have any reviews available for a deletion request.</p>
        @else
            <form method="POST" id="deletion-request-form" class="mt-5">
                @csrf
                <label for="review_id" class="block text-sm font-semibold text-forest">Review to report <span class="text-red-600">*</span></label>
                <select id="review_id" required class="mt-2 w-full rounded-lg border-mist text-sm focus:border-forest-light focus:ring-forest-light">
                    <option value="">Choose a review</option>
                    @foreach ($reviews as $review)
                        <option value="{{ $review->id }}"
                            data-action="{{ route('vendor.reviews.deletion-requests.store', $review) }}"
                            data-customer="{{ $review->customer?->name ?? 'Anonymous' }}"
                            data-product="{{ $review->product?->name ?? 'Product unavailable' }}"
                            data-review-id="{{ $review->id }}"
                            data-description="{{ $review->review_text }}">
                            #{{ $review->id }} — {{ Str::limit($review->product?->name ?? 'Product unavailable', 55) }} — {{ $review->customer?->name ?? 'Anonymous' }}
                        </option>
                    @endforeach
                </select>

                <div id="review-details" class="hidden mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4 rounded-lg border border-mist bg-paper p-4">
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-graphite">Customer name</p><p id="detail-customer" class="mt-1 text-sm font-semibold text-forest"></p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wide text-graphite">Review ID</p><p id="detail-review-id" class="mt-1 text-sm font-semibold text-forest"></p></div>
                    <div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-graphite">Product details</p><p id="detail-product" class="mt-1 text-sm font-semibold text-forest"></p></div>
                    <div class="sm:col-span-2"><p class="text-xs font-semibold uppercase tracking-wide text-graphite">Review description</p><p id="detail-description" class="mt-1 whitespace-pre-line text-sm text-graphite"></p></div>
                </div>

                <div class="mt-5">
                    <label for="reason" class="block text-sm font-semibold text-forest">Why should this review be deleted? <span class="text-red-600">*</span></label>
                    <textarea id="reason" name="reason" rows="6" required minlength="20" maxlength="3000"
                        class="mt-2 w-full rounded-lg border-mist text-sm focus:border-forest-light focus:ring-forest-light"
                        placeholder="Explain the policy violation, inaccurate claim, abuse, or evidence...">{{ old('reason') }}</textarea>
                    @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit" id="submit-request" disabled class="rounded-md bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50">Submit deletion request</button>
                </div>
            </form>
        @endif
    </div>

    <div class="mt-6 rounded-xl border border-mist bg-white shadow-sm overflow-hidden">
        <div class="border-b border-mist px-5 py-4 sm:px-7">
            <h2 class="text-lg font-bold text-forest">Already submitted requests</h2>
        </div>
        @forelse ($requests as $deletionRequest)
            <div class="border-b border-mist px-5 py-4 last:border-b-0 sm:px-7">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-forest">
                            Review #{{ $deletionRequest->review_id ?? 'deleted' }} · {{ $deletionRequest->review?->product?->name ?? 'Product unavailable' }}
                        </p>
                        <p class="mt-1 text-xs text-graphite">Customer: {{ $deletionRequest->review?->customer?->name ?? 'Anonymous' }} · Submitted {{ $deletionRequest->created_at->format('M d, Y') }}</p>
                        <p class="mt-2 text-sm text-graphite">{{ Str::limit($deletionRequest->reason, 220) }}</p>
                    </div>
                    <span class="w-fit rounded-full px-2.5 py-1 text-xs font-semibold {{ $deletionRequest->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($deletionRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($deletionRequest->status) }}</span>
                </div>
            </div>
        @empty
            <p class="px-5 py-8 text-sm text-graphite sm:px-7">You have not submitted any review deletion requests yet.</p>
        @endforelse
    </div>
</div>

@if (!$reviews->isEmpty())
<script>
    const reviewSelect = document.getElementById('review_id');
    const requestForm = document.getElementById('deletion-request-form');
    const details = document.getElementById('review-details');
    const submitButton = document.getElementById('submit-request');

    reviewSelect.addEventListener('change', () => {
        const option = reviewSelect.options[reviewSelect.selectedIndex];
        const hasReview = Boolean(reviewSelect.value);

        submitButton.disabled = !hasReview;
        details.classList.toggle('hidden', !hasReview);

        if (!hasReview) {
            requestForm.removeAttribute('action');
            return;
        }

        requestForm.action = option.dataset.action;
        document.getElementById('detail-customer').textContent = option.dataset.customer;
        document.getElementById('detail-review-id').textContent = `#${option.dataset.reviewId}`;
        document.getElementById('detail-product').textContent = option.dataset.product;
        document.getElementById('detail-description').textContent = option.dataset.description;
    });
</script>
@endif
@endsection

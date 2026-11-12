@extends('layouts.vendor')

@section('title', 'Request Review Deletion')

@section('content')
<div class="w-full max-w-3xl mx-auto px-4 sm:px-6 py-6 sm:py-8">
    <a href="{{ route('vendor.reviews.show', $review) }}" class="text-sm font-medium text-forest-light hover:text-forest">&larr; Back to review</a>
    <div class="mt-4 bg-white rounded-xl border border-mist shadow-sm p-5 sm:p-7">
        <h1 class="text-xl sm:text-2xl font-bold text-forest">Request review deletion</h1>
        <p class="mt-2 text-sm text-graphite">Explain clearly why this review should be removed. An administrator will inspect the review and your explanation before deciding.</p>

        <div class="mt-6 rounded-lg bg-paper border border-mist p-4">
            <p class="text-xs uppercase tracking-wide text-graphite/70">Review</p>
            <p class="mt-1 font-semibold text-forest">{{ $review->product->name ?? 'Product review' }}</p>
            <p class="mt-2 text-sm text-graphite">{{ Str::limit($review->review_text, 300) }}</p>
        </div>

        <form method="POST" action="{{ route('vendor.reviews.deletion-requests.store', $review) }}" class="mt-6">
            @csrf
            <label for="reason" class="block text-sm font-semibold text-forest">Reason for deletion <span class="text-red-600">*</span></label>
            <textarea id="reason" name="reason" rows="7" required minlength="20" maxlength="3000"
                class="mt-2 w-full rounded-lg border-mist text-sm focus:border-forest-light focus:ring-forest-light"
                placeholder="Describe the policy violation, inaccurate claim, abuse, or other evidence...">{{ old('reason') }}</textarea>
            @error('reason')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            <div class="mt-5 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                <a href="{{ route('vendor.reviews.show', $review) }}" class="inline-flex justify-center rounded-md border border-mist bg-white px-5 py-2.5 text-sm font-medium text-forest">Cancel</a>
                <button type="submit" class="inline-flex justify-center rounded-md bg-red-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700">Submit deletion request</button>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.vendor')

@section('title', 'Review Detail')

@section('content')
<div class="w-full px-4 sm:px-6 py-6 sm:py-8 max-w-3xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Review Detail</h1>
            <p class="text-sm text-gray-500 mt-1">
                <a href="{{ route('vendor.reviews.index') }}" class="text-emerald-600 hover:underline">&larr; Back to all reviews</a>
            </p>
        </div>
        <span class="text-xs sm:text-sm text-gray-500">Posted {{ $review->created_at->diffForHumans() }}</span>
    </div>

    @if (session('success'))
        <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if (session('info'))
        <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">{{ session('info') }}</div>
    @endif

    {{-- Review card --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
        <div class="flex flex-col gap-3 mb-4">
            <div class="flex items-center gap-1">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= $review->product_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                <span class="ml-2 text-sm text-gray-600">{{ $review->product_rating }}/5</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($review->is_verified_purchase)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Verified purchase</span>
                @endif
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ ucfirst($review->status) }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold shrink-0">
                {{ strtoupper(substr($review->customer->name ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900">{{ $review->customer->name ?? 'Anonymous' }}</p>
                <p class="text-xs text-gray-500">on <a href="{{ route('storefront.products.show', $review->product) }}" class="text-emerald-600 hover:underline">{{ $review->product->name ?? 'product' }}</a></p>
            </div>
        </div>

        @if ($review->review_title)
            <h3 class="text-base font-semibold text-gray-900 mt-3 mb-1">{{ $review->review_title }}</h3>
        @endif
        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->review_text }}</p>

        @if ($review->images->count())
            <div class="flex gap-2 flex-wrap mt-4">
                @foreach ($review->images as $img)
                    <img src="{{ $img->getImageUrl() }}" alt="Review photo" class="w-16 h-16 sm:w-20 sm:h-20 object-cover rounded border border-gray-200">
                @endforeach
            </div>
        @endif

        <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
            <span>Helpful: <strong>{{ $review->is_helpful_count }}</strong></span>
            <span>Unhelpful: <strong>{{ $review->is_unhelpful_count }}</strong></span>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-red-100 p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-base font-semibold text-gray-900">Report this review for deletion</h2>
                <p class="text-sm text-gray-500 mt-1">If this review violates marketplace guidelines, submit the details to an administrator for review.</p>
            </div>
            <a href="{{ route('vendor.reviews.deletion-requests.create', $review) }}"
               class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-red-600 text-white text-sm font-medium hover:bg-red-700">
                Request review deletion
            </a>
        </div>
    </div>

    {{-- Your reply --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 sm:p-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">{{ $review->response ? 'Update your reply' : 'Reply to this customer' }}</h2>

        @if ($review->response)
            <div class="bg-gray-50 rounded-lg p-3 sm:p-4 mb-4 border-l-4 border-emerald-500">
                <p class="text-xs uppercase tracking-wide text-emerald-700 font-semibold mb-1">Your published reply</p>
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $review->response->response_text }}</p>
            </div>
        @endif

        @if ($review->status === 'approved')
            <form method="POST" action="{{ $review->response ? route('vendor.reviews.respond.update', $review) : route('vendor.reviews.respond', $review) }}">
                @csrf
                @if ($review->response) @method('PUT') @endif
                <label class="block text-sm font-medium text-gray-700 mb-2" for="response_text">Your response <span class="text-red-500">*</span></label>
                <textarea id="response_text" name="response_text" rows="4" required minlength="10" maxlength="2000"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    placeholder="Thank the customer, address the concern, and explain what you've done to improve.">{{ old('response_text', $review->response->response_text ?? '') }}</textarea>
                @error('response_text')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror

                <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3">
                    @if ($review->response)
                        <form method="POST" action="{{ route('vendor.reviews.respond.destroy', $review) }}" onsubmit="return confirm('Remove your reply?')" class="w-full sm:w-auto">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-red-600 hover:text-red-900">Remove reply</button>
                        </form>
                    @endif
                    <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">
                        {{ $review->response ? 'Update reply' : 'Send reply' }}
                    </button>
                </div>
            </form>
        @else
            <p class="text-sm text-gray-500">You can reply to this review once it is approved for public display.</p>
        @endif
    </div>
</div>
@endsection

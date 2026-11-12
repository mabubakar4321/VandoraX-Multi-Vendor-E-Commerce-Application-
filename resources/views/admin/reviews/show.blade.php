@extends('layouts.admin')

@section('title', 'Review Details')

@section('content')
<div class="px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Review Details</h1>
        <a href="{{ route('admin.reviews.index') }}" class="text-sm text-emerald-600 hover:text-emerald-900">&larr; Back to Reviews</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Review Card --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">{{ $review->review_title ?? 'No Title' }}</h2>
                    <p class="text-sm text-gray-500 mt-1">Posted {{ $review->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
                @php
                    $statusColors = [
                        'approved' => 'bg-green-100 text-green-800',
                        'pending' => 'bg-yellow-100 text-yellow-800',
                        'rejected' => 'bg-red-100 text-red-800',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$review->status] ?? 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($review->status) }}
                </span>
            </div>

            <div class="flex items-center gap-1 mb-4">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-5 h-5 {{ $i <= $review->product_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                <span class="ml-2 text-sm text-gray-600">Product Rating</span>
            </div>

            <div class="prose prose-sm max-w-none text-gray-700 mb-6">
                {{ $review->review_text }}
            </div>

            @if ($review->images->count() > 0)
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Attached Photos</h3>
                    <div class="flex gap-2 flex-wrap">
                        @foreach ($review->images as $image)
                            <img src="{{ Storage::url($image->image_url) }}" alt="Review photo" class="w-20 h-20 object-cover rounded border border-gray-200">
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($review->response)
                <div class="bg-gray-50 rounded p-4 border-l-4 border-emerald-500">
                    <h3 class="text-sm font-bold text-emerald-700 uppercase mb-1">Vendor Response</h3>
                    <p class="text-sm text-gray-700">{{ $review->response->response_text }}</p>
                    <p class="text-xs text-gray-500 mt-2">{{ $review->response->vendor->store_name }} &middot; {{ $review->response->created_at->format('M d, Y') }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Product Info --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Product</h3>
                <p class="text-sm text-gray-700 font-medium">{{ $review->product->name ?? 'N/A' }}</p>
                <p class="text-xs text-gray-500 mt-1">Vendor: {{ $review->vendor->store_name ?? 'N/A' }}</p>
                @if ($review->product)
                    <a href="{{ route('storefront.products.show', $review->product) }}" target="_blank" class="inline-block mt-3 text-xs text-emerald-600 hover:text-emerald-900">View Product &rarr;</a>
                @endif
            </div>

            {{-- Customer Info --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Customer</h3>
                <p class="text-sm text-gray-700 font-medium">{{ $review->customer->name ?? 'Anonymous' }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $review->customer->email ?? 'N/A' }}</p>
                @if ($review->is_verified_purchase)
                    <span class="inline-flex items-center mt-2 px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">Verified Purchase</span>
                @endif
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Actions</h3>
                <div class="space-y-2">
                    @if ($review->status !== 'approved')
                        <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">Approve Review</button>
                        </form>
                    @endif

                    @if ($review->status !== 'pending')
                        <form method="POST" action="{{ route('admin.reviews.flag', $review) }}">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-md hover:bg-yellow-600">Flag for Review</button>
                        </form>
                    @endif

                    @if ($review->status !== 'rejected')
                        <form method="POST" action="{{ route('admin.reviews.reject', $review) }}" id="reject-form">
                            @csrf
                            <input type="hidden" name="reason" id="reject-reason" value="">
                            <button type="button" onclick="promptReject()" class="w-full px-4 py-2 bg-orange-500 text-white text-sm font-medium rounded-md hover:bg-orange-600">Reject Review</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('Delete this review permanently? This cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">Delete Permanently</button>
                    </form>
                </div>
            </div>

            {{-- Stats --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Engagement</h3>
                <div class="grid grid-cols-2 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $review->is_helpful_count }}</div>
                        <div class="text-xs text-gray-500">Helpful</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ $review->is_unhelpful_count }}</div>
                        <div class="text-xs text-gray-500">Unhelpful</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function promptReject() {
    const reason = prompt('Enter rejection reason:');
    if (reason && reason.trim().length > 0) {
        document.getElementById('reject-reason').value = reason;
        document.getElementById('reject-form').submit();
    }
}
</script>
@endpush
@endsection
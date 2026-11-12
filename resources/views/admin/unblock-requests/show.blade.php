@extends('layouts.admin')

@section('title', 'Review Unblock Request')

@section('content')
<div class="px-6 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Unblock Request Review</h1>
        <a href="{{ route('admin.unblock-requests.index') }}" class="text-sm text-emerald-600 hover:text-emerald-900">&larr; Back to Requests</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Request Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $unblockRequest->vendor->store_name ?? 'Unknown Vendor' }}</h2>
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$unblockRequest->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($unblockRequest->status) }}
                    </span>
                </div>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Vendor Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unblockRequest->vendor->user->email ?? 'N/A' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Submitted</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $unblockRequest->created_at->format('M d, Y \a\t g:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Request Details</dt>
                        <dd class="mt-1 text-sm text-gray-900 whitespace-pre-line bg-gray-50 p-3 rounded">{{ $unblockRequest->reason }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Bad Reviews History --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bad Reviews ({{ $badReviews->count() }})</h3>
                @forelse ($badReviews as $badReview)
                <div class="border-b border-gray-100 pb-4 mb-4 last:border-0 last:pb-0 last:mb-0">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900">{{ $badReview->review->product->name ?? 'Unknown Product' }}</span>
                        <span class="text-xs text-gray-500">{{ $badReview->flagged_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex items-center gap-1 mb-1">
                        @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-3 h-3 {{ $i <= $badReview->review_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-700">{{ Str::limit($badReview->review->review_text ?? '', 200) }}</p>
                </div>
                @empty
                <p class="text-sm text-gray-500">No bad reviews found.</p>
                @endforelse
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            @if ($unblockRequest->status === 'pending')
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Decision</h3>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.unblock-requests.approve', $unblockRequest) }}">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700" onclick="return confirm('Approve this unblock request? The vendor will be able to sell again.')">
                            Approve & Unblock
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.unblock-requests.reject', $unblockRequest) }}" id="reject-form">
                        @csrf
                        <input type="hidden" name="reason" id="reject-reason" value="">
                        <button type="button" onclick="promptReject()" class="w-full px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700">
                            Reject Request
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Decision</h3>
                <p class="text-sm text-gray-700">This request has been {{ $unblockRequest->status }}.</p>
                @if ($unblockRequest->admin_response)
                <p class="text-sm text-gray-500 mt-2">Comment: {{ $unblockRequest->admin_response }}</p>
                @endif
                <p class="text-xs text-gray-400 mt-2">Reviewed by {{ $unblockRequest->reviewer->name ?? 'Admin' }} on {{ $unblockRequest->reviewed_at->format('M d, Y') }}</p>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-sm font-medium text-gray-900 uppercase mb-3">Vendor Stats</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Bad Reviews</span>
                        <span class="font-medium text-gray-900">{{ $unblockRequest->vendor->bad_reviews_count ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Average Rating</span>
                        <span class="font-medium text-gray-900">{{ number_format($unblockRequest->vendor->average_rating ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Products</span>
                        <span class="font-medium text-gray-900">{{ $unblockRequest->vendor->products->count() }}</span>
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
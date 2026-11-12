@extends('layouts.vendor')

@section('title', 'Reviews & Reputation')

@section('content')
<div class="w-full px-4 sm:px-6 py-6 sm:py-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Reviews &amp; Reputation</h1>
            <p class="text-sm text-gray-500 mt-1">Manage customer reviews, respond to feedback, and request unblocking.</p>
        </div>
        {{-- Prominent CTA: request unblock / view unblock history --}}
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
            @if ($pendingUnblock)
                <a href="{{ route('vendor.unblock.show', $pendingUnblock) }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-100 hover:bg-amber-200 text-amber-800 text-sm font-semibold rounded-lg border border-amber-200 shadow-sm transition-colors w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Pending Request
                </a>
            @endif
            <a href="{{ route('vendor.unblock.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-marigold hover:bg-marigold-600 text-forest text-sm font-semibold rounded-lg shadow-sm transition-colors w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Request Unblock
            </a>
            <a href="{{ route('vendor.unblock.index') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 shadow-sm transition-colors w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                History
            </a>
        </div>
    </div>

    {{-- Block state banner (only when actually blocked) --}}
    @if ($vendor->isBlocked())
        <div class="mb-6 border-l-4 border-red-500 bg-red-50 rounded-lg p-4 sm:p-5">
            <h3 class="text-sm font-semibold text-red-800">Your store is currently blocked</h3>
            <div class="mt-2 text-sm text-red-700 space-y-2">
                <p>You have <strong>{{ $vendor->bad_reviews_count }}</strong> negative review(s). The auto-block threshold is <strong>{{ $threshold }}</strong>.</p>
                @if ($pendingUnblock)
                    <p><a href="{{ route('vendor.unblock.show', $pendingUnblock) }}" class="text-emerald-700 font-medium hover:underline">You have a pending unblock request</a> — track its status in Unblock History.</p>
                @else
                    <p><a href="{{ route('vendor.unblock.create') }}" class="text-emerald-700 font-medium hover:underline">Submit an unblock request</a> to get your store reinstated.</p>
                @endif
            </div>
        </div>
    @endif

    {{-- Quick stats: 2 cols on mobile, 4 on md+ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs uppercase tracking-wide text-gray-500">Pending reviews</p>
            <p class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1">{{ $pendingCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs uppercase tracking-wide text-gray-500">Bad reviews (1–2★)</p>
            <p class="text-2xl sm:text-3xl font-bold text-red-600 mt-1">{{ $badReviewCount }}</p>
            @if ($badReviewCount > 0)
                <p class="text-[11px] text-gray-400 mt-0.5">{{ $badReviewCount }}/{{ $threshold }} to threshold</p>
            @endif
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs uppercase tracking-wide text-gray-500">Awaiting reply</p>
            <p class="text-2xl sm:text-3xl font-bold text-amber-600 mt-1">{{ $unrespondedCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4">
            <p class="text-[11px] sm:text-xs uppercase tracking-wide text-gray-500">Store rating</p>
            <p class="text-2xl sm:text-3xl font-bold text-emerald-600 mt-1">{{ number_format($vendor->average_rating ?? 0, 1) }}★</p>
        </div>
    </div>

    {{-- Filters: stacks on mobile, 4-col on md+ --}}
    <form method="GET" class="bg-white rounded-xl shadow-sm border border-gray-200 p-3 sm:p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">All Statuses</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Rating</label>
                <select name="rating" class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">All Ratings</option>
                    @for ($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Replied?</label>
                <select name="responded" class="w-full border-gray-300 rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    <option value="">All</option>
                    <option value="yes" {{ request('responded') === 'yes' ? 'selected' : '' }}>You've replied</option>
                    <option value="no" {{ request('responded') === 'no' ? 'selected' : '' }}>Needs your reply</option>
                </select>
            </div>
            <div class="flex items-end gap-2 sm:justify-end">
                <button type="submit" class="flex-1 sm:flex-none px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-md hover:bg-emerald-700">Filter</button>
                <a href="{{ route('vendor.reviews.index') }}" class="flex-1 sm:flex-none px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 text-center">Reset</a>
            </div>
        </div>
    </form>

    {{-- Reviews: responsive card list on mobile, table on sm+ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        {{-- Desktop table (sm+) --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reply</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($review->product->name ?? 'N/A', 40) }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="text-sm text-gray-900">{{ $review->customer->name ?? 'Anonymous' }}</div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->product_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @if ($review->response)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Replied</span>
                                @elseif ($review->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Needs reply</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $review->created_at->format('M d, Y') }}</td>
                            <td class="px-4 py-4 text-right text-sm">
                                <div class="inline-flex items-center gap-3">
                                    <a href="{{ route('vendor.reviews.show', $review) }}" class="font-medium text-emerald-600 hover:text-emerald-900">View &amp; Reply</a>
                                    <a href="{{ route('vendor.reviews.deletion-requests.create', $review) }}" class="font-medium text-red-600 hover:text-red-800">Request deletion</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-gray-500">No reviews found matching your criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards (sm-) --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse ($reviews as $review)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ Str::limit($review->product->name ?? 'N/A', 36) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $review->customer->name ?? 'Anonymous' }}</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-1">
                            <a href="{{ route('vendor.reviews.show', $review) }}" class="text-xs font-medium text-emerald-600 whitespace-nowrap">View &amp; Reply →</a>
                            <a href="{{ route('vendor.reviews.deletion-requests.create', $review) }}" class="text-xs font-medium text-red-600 whitespace-nowrap">Request deletion</a>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-3.5 h-3.5 {{ $i <= $review->product_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            @endfor
                        </div>
                        <span class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                    </div>
                    @if ($review->response)
                        <span class="inline-flex items-center px-2 py-0.5 mt-2 rounded-full text-[11px] font-medium bg-green-100 text-green-800">Replied</span>
                    @elseif ($review->status === 'approved')
                        <span class="inline-flex items-center px-2 py-0.5 mt-2 rounded-full text-[11px] font-medium bg-amber-100 text-amber-800">Needs reply</span>
                    @endif
                </div>
            @empty
                <div class="px-4 py-12 text-center text-gray-500 text-sm">No reviews found matching your criteria.</div>
            @endforelse
        </div>
    </div>

    @if ($reviews instanceof \Illuminate\Pagination\LengthAwarePaginator && $reviews->hasPages())
        <div class="flex justify-center sm:justify-end mb-8">
            {{ $reviews->links() }}
        </div>
    @endif

    {{-- Your unblock requests history: responsive card + table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-base font-semibold text-gray-900">Your unblock requests</h2>
            @if ($canRequestUnblock)
                <a href="{{ route('vendor.unblock.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-medium rounded-md">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    New Request
                </a>
            @endif
        </div>

        {{-- Mobile cards --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse ($recentRequests as $req)
                <div class="p-4">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-sm text-gray-900">{{ $req->created_at->format('M d, Y') }}</span>
                        @php
                            $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($req->status) }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">
                        @if ($req->status === 'rejected' && $req->admin_response)
                            {{ Str::limit($req->admin_response, 80) }}
                        @elseif ($req->status === 'approved')
                            Store reinstated
                        @else
                            Under review
                        @endif
                    </p>
                    <a href="{{ route('vendor.unblock.show', $req) }}" class="inline-block mt-2 text-xs font-medium text-emerald-600">View details →</a>
                </div>
            @empty
                <div class="px-4 py-10 text-center text-gray-500 text-sm">No unblock requests yet.</div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Decision</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($recentRequests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = ['pending' => 'bg-yellow-100 text-yellow-800', 'approved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$req->status] ?? 'bg-gray-100 text-gray-800' }}">{{ ucfirst($req->status) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if ($req->status === 'rejected' && $req->admin_response)
                                    {{ Str::limit($req->admin_response, 60) }}
                                @elseif ($req->status === 'approved')
                                    Store reinstated
                                @else
                                    Under review
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <a href="{{ route('vendor.unblock.show', $req) }}" class="text-emerald-600 hover:text-emerald-900 font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No unblock requests yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

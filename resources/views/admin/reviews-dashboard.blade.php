@extends('layouts.admin')

@section('title', 'Review System')

@section('content')
<div class="px-6 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Review System</h1>
        <p class="text-sm text-gray-500 mt-1">Central hub for review moderation, vendor reputation, and unblock requests.</p>
    </div>

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Total reviews</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalReviews }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $approvedReviews }} approved · {{ $rejectedReviews }} rejected</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Pending moderation</p>
            <p class="text-3xl font-bold text-amber-600 mt-1">{{ $pendingReviews }}</p>
            <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="text-xs text-emerald-600 hover:underline mt-1 inline-block">Review queue &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Unblock requests</p>
            <p class="text-3xl font-bold {{ $pendingUnblockRequests ? 'text-amber-600' : 'text-gray-900' }} mt-1">{{ $pendingUnblockRequests }}</p>
            <a href="{{ route('admin.unblock-requests.index', ['status' => 'pending']) }}" class="text-xs text-emerald-600 hover:underline mt-1 inline-block">{{ $totalUnblockRequests }} total &rarr;</a>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500">Auto-block threshold</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $threshold }}</p>
            <p class="text-xs text-gray-400 mt-1">bad reviews per vendor</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Vendors at risk --}}
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900">Vendors at risk (bad reviews)</h2>
                <span class="text-xs text-gray-500">Threshold: {{ $threshold }}</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($atRiskVendors as $v)
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between gap-4 mb-2">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $v->store_name }}</p>
                                <p class="text-xs text-gray-500">{{ $v->user->name ?? 'Unknown' }}</p>
                            </div>
                            @if ($v->is_over_threshold)
                                <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Over limit</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full {{ $v->is_over_threshold ? 'bg-red-500' : 'bg-amber-500' }}" style="width: {{ $v->progress_pct }}%"></div>
                            </div>
                            <span class="text-xs font-medium text-gray-600 w-10 text-right">{{ $v->bad_reviews_count }}/{{ $threshold }}</span>
                        </div>
                    </div>
                @empty
                    <p class="px-6 py-10 text-center text-gray-500 text-sm">No vendors currently have bad reviews. 🎉</p>
                @endforelse
            </div>
        </div>

        {{-- Recent unblock requests --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Recent unblock requests</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse ($recentUnblockRequests as $req)
                    <div class="px-6 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $req->vendor->store_name ?? 'N/A' }}</p>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                {{ $req->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : ($req->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($req->status) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">{{ $req->created_at->format('M d, Y') }}</p>
                    </div>
                @empty
                    <p class="px-6 py-8 text-center text-gray-500 text-sm">No unblock requests yet.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent reviews --}}
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Latest reviews</h2>
            <a href="{{ route('admin.reviews.index') }}" class="text-xs text-emerald-600 hover:underline">Manage all &rarr;</a>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <tbody class="divide-y divide-gray-100">
                @forelse ($recentReviews as $review)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm">
                            <p class="font-medium text-gray-900">{{ Str::limit($review->product->name ?? 'N/A', 36) }}</p>
                            <p class="text-xs text-gray-500">{{ $review->vendor->store_name ?? 'N/A' }}</p>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $review->product_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                @endfor
                            </div>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-600">{{ $review->customer->name ?? 'Anonymous' }}</td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $review->status === 'approved' ? 'bg-green-100 text-green-800' : ($review->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($review->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right text-sm">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="text-emerald-600 hover:underline">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 text-sm">No reviews yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Quick links --}}
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">All reviews</a>
        <a href="{{ route('admin.reviews.index', ['status' => 'pending']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Pending queue</a>
        <a href="{{ route('admin.unblock-requests.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Unblock requests</a>
        <a href="{{ route('admin.unblock-requests.index', ['status' => 'pending']) }}" class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">Pending unblocks</a>
    </div>
</div>
@endsection

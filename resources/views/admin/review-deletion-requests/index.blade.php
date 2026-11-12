@extends('layouts.admin')

@section('title', 'Review Delete Requests')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-forest">Review deletion requests</h1>
            <p class="mt-1 text-sm text-graphite">Inspect vendor explanations before removing a customer review.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <select name="status" class="rounded-md border-mist text-sm focus:border-forest-light focus:ring-forest-light">
                <option value="">All statuses</option>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-md bg-forest px-4 py-2 text-sm font-medium text-white hover:bg-forest-dark">Filter</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-mist">
                <thead class="bg-paper">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-graphite">Review</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-graphite">Vendor</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-graphite">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-graphite">Submitted</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-graphite">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse ($requests as $deletionRequest)
                        <tr class="hover:bg-paper/60">
                            <td class="px-5 py-4">
                                <p class="text-sm font-semibold text-forest">{{ Str::limit($deletionRequest->review?->product?->name ?? 'Review unavailable', 42) }}</p>
                                <p class="mt-1 text-xs text-graphite">{{ Str::limit($deletionRequest->reason, 90) }}</p>
                            </td>
                            <td class="px-5 py-4 text-sm text-graphite">{{ $deletionRequest->vendor?->store_name ?? 'Unknown vendor' }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $deletionRequest->status === 'pending' ? 'bg-amber-100 text-amber-800' : ($deletionRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($deletionRequest->status) }}</span>
                            </td>
                            <td class="px-5 py-4 text-sm text-graphite">{{ $deletionRequest->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-right"><a href="{{ route('admin.review-deletion-requests.show', $deletionRequest) }}" class="font-semibold text-forest-light hover:text-forest">Review request &rarr;</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-12 text-center text-sm text-graphite">No review deletion requests found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())
            <div class="border-t border-mist px-5 py-4">{{ $requests->links() }}</div>
        @endif
    </div>
</div>
@endsection

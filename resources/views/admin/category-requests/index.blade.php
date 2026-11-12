@extends('layouts.admin')

@section('title', 'Category Requests')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Catalog management</p>
            <h1 class="mt-2 text-3xl leading-tight">Category requests</h1>
            <p class="mt-2 text-sm text-graphite/70">Review proposed categories submitted by marketplace vendors.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="btn-dark w-full px-4 py-2.5 sm:w-auto">Manage categories</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="{{ route('admin.category-requests.index', ['status' => 'pending']) }}" class="border-t-2 border-marigold bg-white p-4 shadow-sm transition-shadow hover:shadow-md {{ $status === 'pending' ? 'ring-1 ring-marigold' : '' }}" style="border-radius: 2px;">
            <p class="text-sm text-graphite/70">Pending</p>
            <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['pending']) }}</p>
        </a>
        <a href="{{ route('admin.category-requests.index', ['status' => 'approved']) }}" class="border-t-2 border-forest-500 bg-white p-4 shadow-sm transition-shadow hover:shadow-md {{ $status === 'approved' ? 'ring-1 ring-forest-500' : '' }}" style="border-radius: 2px;">
            <p class="text-sm text-graphite/70">Approved</p>
            <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['approved']) }}</p>
        </a>
        <a href="{{ route('admin.category-requests.index', ['status' => 'rejected']) }}" class="border-t-2 border-vermilion bg-white p-4 shadow-sm transition-shadow hover:shadow-md {{ $status === 'rejected' ? 'ring-1 ring-vermilion' : '' }}" style="border-radius: 2px;">
            <p class="text-sm text-graphite/70">Rejected</p>
            <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['rejected']) }}</p>
        </a>
        <div class="border-t-2 border-indigo-500 bg-white p-4 shadow-sm" style="border-radius: 2px;">
            <p class="text-sm text-graphite/70">Total requests</p>
            <p class="mt-2 font-display text-3xl text-forest">{{ number_format($statistics['total']) }}</p>
        </div>
    </div>

    <div class="border border-mist bg-white p-4 sm:p-5" style="border-radius: 2px;">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="hidden" name="status" value="{{ $status }}">
            <input type="search" name="q" value="{{ $query }}" placeholder="Search category, vendor, or email..." aria-label="Search category requests" class="field min-w-0 sm:flex-1">
            <button type="submit" class="btn-primary px-6 py-2.5 sm:w-auto">Search</button>
            @if($query)
                <a href="{{ route('admin.category-requests.index', ['status' => $status]) }}" class="inline-flex items-center justify-center border border-mist px-6 py-2.5 text-center text-sm font-semibold text-forest hover:bg-paper">Reset</a>
            @endif
        </form>
    </div>

    <div class="flex flex-wrap gap-2">
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $filter => $label)
            <a href="{{ route('admin.category-requests.index', ['status' => $filter, 'q' => $query]) }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold transition-colors {{ $status === $filter ? 'bg-forest text-white' : 'border border-mist bg-white text-graphite hover:bg-paper' }}" style="border-radius: 2px;">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <section class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
        <div class="border-b border-mist px-5 py-4 sm:px-6"><h2 class="text-xl">Request inbox</h2><p class="mt-1 text-sm text-graphite/65">{{ ucfirst($status) }} vendor category requests</p></div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65">
                    <tr>
                        <th class="px-5 py-3 sm:px-6">Vendor</th>
                        <th class="px-5 py-3 sm:px-6">Category</th>
                        <th class="px-5 py-3 sm:px-6">Requested</th>
                        <th class="px-5 py-3 sm:px-6">Status</th>
                        <th class="px-5 py-3 text-right sm:px-6">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse($requests as $categoryRequest)
                        <tr class="hover:bg-paper/70">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-semibold text-forest">{{ optional($categoryRequest->vendor->user)->name ?: 'Unknown vendor' }}</p>
                                <p class="mt-1 break-all text-xs text-graphite/60">{{ optional($categoryRequest->vendor->user)->email ?: 'No email' }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-semibold text-forest">{{ $categoryRequest->category_name }}</p>
                                @if($categoryRequest->description)
                                    <p class="mt-1 text-xs text-graphite/65">{{ Str::limit($categoryRequest->description, 70) }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-graphite sm:px-6">
                                {{ optional($categoryRequest->requested_at)->format('M d, Y') ?: 'Unknown' }}
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $categoryRequest->isPending() ? 'bg-marigold-100 text-forest' : ($categoryRequest->isApproved() ? 'bg-forest-100 text-forest' : 'bg-vermilion-100 text-vermilion') }}">
                                    {{ ucfirst($categoryRequest->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right sm:px-6">
                                <a href="{{ route('admin.category-requests.show', $categoryRequest) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">Review request →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <p class="font-semibold text-forest">No {{ $status }} requests found</p>
                                <p class="mt-1 text-sm text-graphite/65">@if($query)Try another search term or clear your search to see all {{ $status }} requests.@else There are no {{ $status }} vendor category requests to review right now.@endif</p>
                                @if($query)
                                    <a href="{{ route('admin.category-requests.index', ['status' => $status]) }}" class="mt-4 inline-flex items-center justify-center border border-mist px-4 py-2 text-sm font-semibold text-forest transition-colors hover:bg-paper">Clear search</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if(method_exists($requests, 'links'))
        <div class="mt-6">{{ $requests->appends(['status' => $status, 'q' => $query])->links() }}</div>
    @endif
</div>
@endsection

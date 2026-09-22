@extends('layouts.vendor')

@section('title', 'All Categories — VandoraX')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4 mb-8">
    <div>
        <p class="text-sm font-medium text-forest-500">Seller Centre</p>
        <h1 class="mt-1 text-[32px] leading-tight">All Categories</h1>
        <p class="mt-2 text-[15px] text-graphite">View admin-approved categories and your category requests</p>
    </div>
    <a href="{{ route('vendor.categories.request.create') }}" class="btn-primary w-auto px-5">
        Request a category
    </a>
</div>

{{-- Statistics --}}
<div class="mt-8 grid gap-4 sm:grid-cols-3">
    <div class="border border-mist bg-white p-5" style="border-radius: 2px;">
        <p class="text-sm text-graphite">Admin-Approved</p>
        <p class="mt-2 font-display text-3xl text-forest">{{ $approvedCategories->count() }}</p>
    </div>
    <div class="border border-mist bg-white p-5" style="border-radius: 2px;">
        <p class="text-sm text-graphite">Your Requests</p>
        <p class="mt-2 font-display text-3xl text-forest">{{ $vendorRequests->count() }}</p>
    </div>
    <div class="border border-mist bg-white p-5" style="border-radius: 2px;">
        <p class="text-sm text-graphite">Approved by Admin</p>
        <p class="mt-2 font-display text-3xl text-forest">{{ $vendorRequests->where('status', 'approved')->count() }}</p>
    </div>
</div>

{{-- Approved Categories Section --}}
<div class="mt-10 border border-mist bg-white p-6 sm:p-8" style="border-radius: 2px;">
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
        <div>
            <h2 class="text-[20px] leading-tight text-forest">Admin-Approved Categories</h2>
            <p class="mt-1 text-[15px] text-graphite">Categories you can use for your products</p>
        </div>
        <span class="text-[13px] text-graphite/70">{{ $approvedCategories->count() }} total</span>
    </div>

    @if ($approvedCategories->isEmpty())
        <p class="text-[15px] text-graphite/70">No approved categories are available yet.</p>
    @else
        <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($approvedCategories as $category)
                <div class="border border-mist px-4 py-4 hover:bg-paper transition-colors" style="border-radius: 2px;">
                    <p class="font-medium text-forest">{{ $category->name }}</p>
                    @if ($category->parent)
                        <p class="mt-1 text-[13px] text-graphite/70">Parent: {{ $category->parent->name }}</p>
                    @endif
                    @if ($category->children->count() > 0)
                        <p class="mt-1 text-[13px] text-marigold-600 font-medium">{{ $category->children->count() }} subcategories</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Your Category Requests Section --}}
<div class="mt-10 border border-mist bg-white overflow-hidden" style="border-radius: 2px;">
    <div class="border-b border-mist bg-paper px-6 py-4">
        <h2 class="text-[20px] leading-tight text-forest">Your Category Requests</h2>
        <p class="mt-1 text-[15px] text-graphite">Track categories you've requested</p>
    </div>

    @if ($vendorRequests->isEmpty())
        <div class="px-6 py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-graphite/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="mt-4 text-[15px] text-graphite">You haven't submitted any category requests yet.</p>
            <a href="{{ route('vendor.categories.request.create') }}" class="mt-4 inline-block text-marigold font-medium hover:text-marigold/80">
                Request your first category →
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[650px] text-left">
                <thead class="border-b border-mist bg-paper">
                    <tr>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Category</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Status</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Requested</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Response</th>
                        <th class="px-5 py-4 text-sm font-semibold text-forest">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @foreach ($vendorRequests as $request)
                        <tr class="hover:bg-paper/50 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-medium text-forest">{{ $request->category_name }}</p>
                                @if ($request->description)
                                    <p class="mt-1 text-sm text-graphite/70">{{ Str::limit($request->description, 60) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if ($request->status === 'approved')
                                    <span class="bg-forest-100 px-2.5 py-1 text-xs font-semibold text-forest-500" style="border-radius: 2px;">Approved</span>
                                @elseif ($request->status === 'rejected')
                                    <span class="bg-vermilion-100 px-2.5 py-1 text-xs font-semibold text-vermilion" style="border-radius: 2px;">Rejected</span>
                                @else
                                    <span class="bg-marigold-100 px-2.5 py-1 text-xs font-semibold text-marigold-600" style="border-radius: 2px;">Pending</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-graphite">{{ optional($request->requested_at)->format('M d, Y') }}</td>
                            <td class="px-5 py-4 text-sm text-graphite">{{ optional($request->approved_at)->format('M d, Y') ?? '—' }}</td>
                            <td class="px-5 py-4">
                                <a href="{{ route('vendor.categories.request.show', $request) }}" class="text-sm font-semibold text-forest underline decoration-mist underline-offset-4 hover:decoration-forest">
                                    View details
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection

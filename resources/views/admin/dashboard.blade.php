@extends('layouts.admin')
@section('title', 'Admin — VandoraX')

@section('content')
@php
    // Dashboard statistics
    $pending_vendors = \App\Models\Vendor::where('status', 'pending')->count();
    $active_vendors = \App\Models\Vendor::where('status', 'active')->count();
    $customers = \App\Models\User::role('customer')->count();
    
    // Category requests
    $pending_category_requests = \App\Models\VendorCategoryRequest::where('status', 'pending')->count();
@endphp

    <h1 class="text-[32px] leading-tight">Today on VandoraX</h1>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">

        {{-- Vendor Approvals --}}
        <div class="border-t-2 border-marigold bg-white p-6 lg:row-span-2" style="border-radius: 2px;">
            <p class="text-[15px] font-medium text-forest">Vendor Approvals</p>
            <p class="tabular mt-3 font-display text-[64px] leading-none text-forest">{{ $pending_vendors }}</p>
            <p class="mt-3 max-w-prose text-sm leading-relaxed text-graphite">
                @if ($pending_vendors === 0)
                    Nothing in the queue. Every vendor application has been dealt with.
                @else
                    {{ $pending_vendors === 1 ? 'One seller is' : "$pending_vendors sellers are" }}
                    waiting on a decision. Review their documents and approve or reject.
                @endif
            </p>
            @if ($pending_vendors > 0)
                <a href="{{ route('admin.vendors.pending') }}" class="btn-primary mt-6 inline-block w-auto px-5">
                    Review vendor applications
                </a>
            @endif
        </div>

        <div class="divide-y divide-mist border border-mist bg-white lg:col-span-2" style="border-radius: 2px;">
            @foreach ([
                ['Sellers trading', $active_vendors, 'Approved and able to list'],
                ['Customers registered', $customers, 'All time'],
            ] as [$label, $figure, $caption])
                <div class="flex items-baseline justify-between gap-6 px-6 py-5">
                    <div>
                        <p class="text-[15px] font-medium text-forest">{{ $label }}</p>
                        <p class="mt-0.5 text-[13px] text-graphite/70">{{ $caption }}</p>
                    </div>
                    <p class="tabular font-display text-[28px] leading-none text-forest">{{ $figure }}</p>
                </div>
            @endforeach
        </div>

        {{-- Category Requests Section --}}
        <div class="border-t-2 border-indigo-600 bg-white p-6 lg:row-span-2" style="border-radius: 2px;">
            <p class="text-[15px] font-medium text-forest">Category Requests</p>
            <p class="tabular mt-3 font-display text-[64px] leading-none text-forest">{{ $pending_category_requests }}</p>
            <p class="mt-3 max-w-prose text-sm leading-relaxed text-graphite">
                @if ($pending_category_requests === 0)
                    All category requests have been reviewed.
                @else
                    {{ $pending_category_requests === 1 ? 'One seller has' : "$pending_category_requests sellers have" }}
                    requested new categories. Review and approve or reject them.
                @endif
            </p>
            @if ($pending_category_requests > 0)
                <a href="{{ route('admin.category-requests.index') }}" class="btn-primary mt-6 inline-block w-auto px-5">
                    Review category requests
                </a>
            @else
                <a href="{{ route('admin.category-requests.index') }}" class="btn-secondary mt-6 inline-block w-auto px-5">
                    View all requests
                </a>
            @endif
        </div>

        <div class="border border-mist bg-white px-6 py-8 lg:col-span-2" style="border-radius: 2px;">
            <h2 class="text-[20px] leading-tight">Management Tools</h2>
            <ul class="mt-4 space-y-2">
                <li>
                    <a href="{{ route('admin.categories.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                        → Manage Categories
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories.tree') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                        → View Category Tree
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.category-requests.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                        → All Category Requests
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.products.index') }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                        → All Products
                    </a>
                </li>
            </ul>
        </div>
    </div>

    {{-- Recent Pending Requests Quick View --}}
    @if ($pending_category_requests > 0)
    <div class="mt-10">
        <h2 class="text-[20px] leading-tight mb-4">Recent Category Requests (Pending)</h2>
        <div class="bg-white border border-mist rounded overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Vendor</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Category Request</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Requested</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(\App\Models\VendorCategoryRequest::pending()->with(['vendor', 'vendor.user'])->latest('requested_at')->take(5)->get() as $request)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-gray-900">{{ $request->vendor->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $request->vendor->user->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $request->category_name }}</p>
                            @if($request->description)
                            <p class="text-sm text-gray-600">{{ Str::limit($request->description, 60) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $request->requested_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.category-requests.show', $request) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                Review →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                            No pending requests
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
@endsection
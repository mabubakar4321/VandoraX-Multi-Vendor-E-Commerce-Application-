@extends('layouts.admin')
@section('title', 'Vendors — Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Marketplace people</p><h1 class="mt-2 text-3xl leading-tight">Vendors</h1><p class="mt-2 text-sm text-graphite/70">Review every seller, their store, activity, and account status.</p></div><a href="{{ route('admin.dashboard') }}" class="btn-dark w-auto px-4 py-2.5">Back to dashboard</a></div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ([['All vendors', 'total', 'border-forest-500'], ['Pending', 'pending', 'border-marigold'], ['Active', 'active', 'border-indigo-500'], ['Blocked', 'blocked', 'border-vermilion'], ['Suspended', 'suspended', 'border-graphite']] as [$label, $key, $border])
            <div class="border-t-2 {{ $border }} bg-white p-4 shadow-sm" style="border-radius: 2px;"><p class="text-sm text-graphite/70">{{ $label }}</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($key === 'total' ? $vendors->total() : $counts->get($key, 0)) }}</p></div>
        @endforeach
    </div>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;"><div class="border-b border-mist px-5 py-4 sm:px-6"><h2 class="text-xl">Vendor directory</h2></div><div class="overflow-x-auto"><table class="w-full min-w-[980px] text-left text-sm"><thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-5 py-3 sm:px-6">Vendor</th><th class="px-5 py-3 sm:px-6">Store</th><th class="px-5 py-3 sm:px-6">Category</th><th class="px-5 py-3 sm:px-6">Products</th><th class="px-5 py-3 sm:px-6">Orders</th><th class="px-5 py-3 sm:px-6">Status</th><th class="px-5 py-3 text-right sm:px-6">Action</th></tr></thead><tbody class="divide-y divide-mist">
        @forelse($vendors as $vendor)
            <tr class="hover:bg-paper/70"><td class="px-5 py-4 sm:px-6"><p class="font-semibold text-forest">{{ $vendor->user->name ?? 'Vendor' }}</p><p class="mt-1 break-all text-xs text-graphite/60">{{ $vendor->user->email ?? '' }}</p></td><td class="px-5 py-4 font-medium text-forest sm:px-6">{{ $vendor->store_name }}</td><td class="px-5 py-4 text-graphite sm:px-6">{{ $vendor->category->name ?? 'Unassigned' }}</td><td class="px-5 py-4 text-graphite sm:px-6">{{ number_format($vendor->products_count) }}</td><td class="px-5 py-4 text-graphite sm:px-6">{{ number_format($vendor->orders_count) }}</td><td class="px-5 py-4 sm:px-6"><span class="inline-flex rounded-full bg-mist px-2.5 py-1 text-xs font-semibold capitalize text-forest">{{ $vendor->status }}</span></td><td class="px-5 py-4 text-right sm:px-6"><div class="flex justify-end gap-3"><a href="{{ route('admin.vendors.detail', $vendor) }}" class="font-semibold text-forest hover:text-indigo-900">View details</a><a href="{{ route('admin.vendors.manage', $vendor) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">Edit</a></div></td></tr>
        @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-graphite/65">No vendors found.</td></tr>
        @endforelse
    </tbody></table></div>@if($vendors->hasPages())<div class="border-t border-mist px-5 py-4 sm:px-6">{{ $vendors->links() }}</div>@endif</div>
</div>
@endsection

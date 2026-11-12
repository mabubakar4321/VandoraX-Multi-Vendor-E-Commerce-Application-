@extends('layouts.admin')
@section('title', 'Customers — Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">People</p><h1 class="mt-2 text-3xl leading-tight">Customers</h1><p class="mt-2 text-sm text-graphite/70">Review customer accounts, activity, orders, and contact information.</p></div><a href="{{ route('admin.dashboard') }}" class="btn-dark w-auto px-4 py-2.5">Back to dashboard</a></div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([['Total customers', 'total', 'border-forest-500'], ['Active', 'active', 'border-marigold'], ['Inactive', 'inactive', 'border-graphite'], ['Suspended', 'suspended', 'border-vermilion']] as [$label, $key, $border])
            <div class="border-t-2 {{ $border }} bg-white p-4 shadow-sm" style="border-radius: 2px;"><p class="text-sm text-graphite/70">{{ $label }}</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($counts[$key]) }}</p></div>
        @endforeach
    </div>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;"><div class="border-b border-mist px-5 py-4 sm:px-6"><h2 class="text-xl">Customer directory</h2></div><div class="overflow-x-auto"><table class="w-full min-w-[820px] text-left text-sm"><thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-5 py-3 sm:px-6">Customer</th><th class="px-5 py-3 sm:px-6">Phone</th><th class="px-5 py-3 sm:px-6">Orders</th><th class="px-5 py-3 sm:px-6">Status</th><th class="px-5 py-3 sm:px-6">Joined</th><th class="px-5 py-3 text-right sm:px-6">Action</th></tr></thead><tbody class="divide-y divide-mist">
        @forelse($customers as $customer)
            <tr class="hover:bg-paper/70"><td class="px-5 py-4 sm:px-6"><p class="font-semibold text-forest">{{ $customer->name }}</p><p class="mt-1 break-all text-xs text-graphite/60">{{ $customer->email }}</p></td><td class="px-5 py-4 text-graphite sm:px-6">{{ $customer->phone_number ?: 'Not provided' }}</td><td class="px-5 py-4 text-graphite sm:px-6">{{ number_format($customer->orders_count) }}</td><td class="px-5 py-4 sm:px-6"><span class="inline-flex rounded-full bg-mist px-2.5 py-1 text-xs font-semibold capitalize text-forest">{{ $customer->status }}</span></td><td class="whitespace-nowrap px-5 py-4 text-graphite sm:px-6">{{ $customer->created_at->format('M d, Y') }}</td><td class="px-5 py-4 text-right sm:px-6"><a href="{{ route('admin.customers.show', $customer) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">Review account →</a></td></tr>
        @empty
            <tr><td colspan="6" class="px-6 py-12 text-center text-graphite/65">No customer accounts found.</td></tr>
        @endforelse
    </tbody></table></div>@if($customers->hasPages())<div class="border-t border-mist px-5 py-4 sm:px-6">{{ $customers->links() }}</div>@endif</div>
</div>
@endsection

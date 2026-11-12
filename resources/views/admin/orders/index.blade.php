@extends('layouts.admin')
@section('title', 'Orders — Admin')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Sales monitoring</p><h1 class="mt-2 text-3xl leading-tight">All orders</h1><p class="mt-2 text-sm text-graphite/70">Monitor customers, vendors, fulfillment, and payments from one place.</p></div>
        <a href="{{ route('admin.dashboard') }}" class="btn-dark w-auto px-4 py-2.5">Back to dashboard</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ([['Pending', 'pending', 'border-marigold'], ['Confirmed', 'confirmed', 'border-forest-500'], ['Processing', 'processing', 'border-indigo-500'], ['Shipped', 'shipped', 'border-blue-500'], ['Delivered', 'delivered', 'border-graphite']] as [$label, $status, $border])
            <div class="border-t-2 {{ $border }} bg-white p-4 shadow-sm" style="border-radius: 2px;"><p class="text-sm text-graphite/70">{{ $label }}</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($counts->get($status, 0)) }}</p></div>
        @endforeach
    </div>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
        <div class="border-b border-mist px-5 py-4 sm:px-6">
            <h2 class="text-xl">Customer orders</h2>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="mt-4 grid min-w-0 gap-3 sm:grid-cols-[minmax(0,1fr)_11rem_auto_auto]">
                <label class="sr-only" for="admin-order-search">Search orders</label>
                <input id="admin-order-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search by order number, customer name/email, or vendor store name..."
                       class="w-full min-w-0 border border-mist px-4 py-2.5 text-sm" style="border-radius: 2px;">
                <label class="sr-only" for="admin-order-status">Filter by status</label>
                <select id="admin-order-status" name="status" class="w-full border border-mist px-4 py-2.5 text-sm capitalize sm:w-44" style="border-radius: 2px;">
                    <option value="">All statuses</option>
                    @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn-dark !w-auto px-6 py-2.5 text-sm whitespace-nowrap">Search</button>
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 text-sm text-graphite/60 hover:text-graphite whitespace-nowrap">Clear</a>
                @endif
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[920px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-5 py-3 sm:px-6">Order</th><th class="px-5 py-3 sm:px-6">Customer</th><th class="px-5 py-3 sm:px-6">Vendors</th><th class="px-5 py-3 sm:px-6">Order status</th><th class="px-5 py-3 sm:px-6">Payment</th><th class="px-5 py-3 text-right sm:px-6">Total</th><th class="px-5 py-3 text-right sm:px-6">Action</th></tr></thead>
                <tbody class="divide-y divide-mist">
                    @forelse($orders as $order)
                        <tr class="hover:bg-paper/70"><td class="px-5 py-4 sm:px-6"><p class="font-semibold text-forest">{{ $order->order_number }}</p><p class="mt-1 text-xs text-graphite/60">{{ $order->created_at->format('M d, Y') }}</p></td><td class="px-5 py-4 sm:px-6"><p class="font-medium text-forest">{{ $order->customer->name ?? 'Customer' }}</p><p class="mt-1 text-xs text-graphite/60">{{ $order->customer->email ?? '' }}</p></td><td class="px-5 py-4 text-graphite sm:px-6">{{ $order->vendorOrders->count() }}</td><td class="px-5 py-4 sm:px-6"><span class="inline-flex rounded-full bg-mist px-2.5 py-1 text-xs font-semibold capitalize text-forest">{{ $order->effective_status }}</span></td><td class="px-5 py-4 sm:px-6"><span class="capitalize text-graphite">{{ $order->payment_status }}</span></td><td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-forest sm:px-6">Rs {{ number_format($order->total_amount) }}</td><td class="px-5 py-4 text-right sm:px-6"><a href="{{ route('admin.orders.show', $order) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">View details →</a></td></tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-12 text-center text-graphite/65">No orders found{{ request('search') ? ' matching "' . request('search') . '"' : '' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())<div class="border-t border-mist px-5 py-4 sm:px-6">{{ $orders->links() }}</div>@endif
    </div>
</div>
@endsection

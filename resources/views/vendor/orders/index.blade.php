@extends('layouts.vendor')
@section('title', 'Orders — Seller Centre')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Sales</p>
            <h1 class="mt-2 text-3xl leading-tight">Orders</h1>
            <p class="mt-2 text-sm text-graphite/70">Review customer orders containing your products and manage their status.</p>
        </div>
        <a href="{{ route('vendor.dashboard') }}" class="btn-dark w-auto px-4 py-2.5">Back to dashboard</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        @foreach ([
            ['Pending', 'pending', 'border-marigold'],
            ['Confirmed', 'confirmed', 'border-forest-500'],
            ['Processing', 'processing', 'border-indigo-500'],
            ['Shipped', 'shipped', 'border-blue-500'],
            ['Delivered', 'delivered', 'border-graphite'],
        ] as [$label, $status, $border])
            <div class="border-t-2 {{ $border }} bg-white p-4 shadow-sm" style="border-radius: 2px;">
                <p class="text-sm text-graphite/70">{{ $label }}</p>
                <p class="mt-2 font-display text-3xl text-forest">{{ number_format($counts->get($status, 0)) }}</p>
            </div>
        @endforeach
    </div>

    <!-- Status Filter Tabs -->
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('vendor.orders.index') }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition
                @if(request('status') === null) bg-forest text-white @else bg-white text-graphite border border-mist hover:bg-paper @endif">
            All
        </a>
        @foreach(['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
            <a href="{{ route('vendor.orders.index', ['status' => $status]) }}"
               class="px-4 py-2 rounded-lg text-sm font-semibold transition capitalize
                    @if(request('status') === $status) bg-forest text-white @else bg-white text-graphite border border-mist hover:bg-paper @endif">
                {{ ucfirst($status) }}
            </a>
        @endforeach
    </div>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
        <div class="border-b border-mist px-5 py-4 sm:px-6">
            <h2 class="text-xl">Customer orders</h2>
            @if(request('status'))
                <p class="mt-1 text-sm text-graphite/70">Showing: <strong class="capitalize">{{ request('status') }}</strong> sub-orders</p>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[760px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65">
                    <tr>
                        <th class="px-5 py-3 sm:px-6">Order</th>
                        <th class="px-5 py-3 sm:px-6">Customer</th>
                        <th class="px-5 py-3 sm:px-6">Date</th>
                        <th class="px-5 py-3 sm:px-6">Status</th>
                        <th class="px-5 py-3 text-right sm:px-6">Your total</th>
                        <th class="px-5 py-3 text-right sm:px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse($orders as $vendorOrder)
                        @php $order = $vendorOrder->order; @endphp
                        <tr class="hover:bg-paper/70">
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-semibold text-forest">{{ $order->order_number ?? '—' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $vendorOrder->vendor_order_number }}</p>
                            </td>
                            <td class="px-5 py-4 sm:px-6">
                                <p class="font-medium text-forest">{{ $order->customer->name ?? 'Customer' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $order->customer->email ?? '' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-graphite sm:px-6">{{ $order->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full @if($vendorOrder->status === 'pending') bg-yellow-100 text-yellow-800 @elseif($vendorOrder->status === 'confirmed') bg-blue-100 text-blue-800 @elseif($vendorOrder->status === 'processing') bg-indigo-100 text-indigo-800 @elseif($vendorOrder->status === 'shipped') bg-purple-100 text-purple-800 @elseif($vendorOrder->status === 'delivered') bg-green-100 text-green-800 @elseif($vendorOrder->status === 'cancelled') bg-red-100 text-red-800 @else bg-mist text-forest @endif px-2.5 py-1 text-xs font-semibold capitalize">
                                    {{ $vendorOrder->status }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-forest sm:px-6">Rs {{ number_format($vendorOrder->vendor_amount) }}</td>
                            <td class="px-5 py-4 text-right sm:px-6">
                                <a href="{{ route('vendor.orders.show', $vendorOrder) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">Details →</a>
                                <a href="{{ route('vendor.orders.print', $vendorOrder) }}" class="ml-3 font-semibold text-graphite/60 hover:text-graphite" title="Print invoice">🖨</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-graphite/65">No sub-orders found{{ request('status') ? ' with status "' . request('status') . '"' : '' }}.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="border-t border-mist px-5 py-4 sm:px-6">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Total Admin Collected Amount — Admin')
@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Finance</p>
            <h1 class="mt-2 text-3xl leading-tight">Total Admin Collected Amount</h1>
            <p class="mt-2 text-sm text-graphite/70">All amounts collected by admin from vendor orders as commission. Vendor, order, customer, and collected amount on one page.</p>
        </div>
        <a href="{{ route('admin.payments.index') }}" class="btn-dark w-auto px-4 py-2.5">Back to payments</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-4">
        <div class="border-t-2 border-forest bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Total collected (commission)</p><p class="mt-2 font-display text-2xl text-forest">Rs {{ number_format($stats['total_collected'], 2) }}</p><p class="text-xs text-graphite/60">From {{ $stats['total_orders'] }} vendor order(s)</p></div>
        <div class="border-t-2 border-marigold bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Released</p><p class="mt-2 font-display text-2xl text-forest">{{ $stats['released_collections'] }}</p><p class="text-xs text-graphite/60">Credited to vendors</p></div>
        <div class="border-t-2 border-vermilion bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Pending</p><p class="mt-2 font-display text-2xl text-forest">{{ $stats['pending_collections'] }}</p><p class="text-xs text-graphite/60">Awaiting release</p></div>
        <div class="border-t-2 border-indigo-500 bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Avg commission rate</p><p class="mt-2 font-display text-2xl text-forest">{{ number_format($stats['average_rate'], 2) }}%</p></div>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 bg-white border border-mist p-4" style="border-radius:2px">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search vendor / store / customer / order" class="min-w-0 flex-1 border border-mist px-3 py-2 text-sm" style="border-radius:2px">
        <select name="status" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px"><option value="">All statuses</option><option value="released" @selected(request('status')==='released')>Released</option><option value="pending" @selected(request('status')==='pending')>Pending</option><option value="paid" @selected(request('status')==='paid')>Paid</option></select>
        <select name="vendor_id" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px"><option value="">All vendors</option>@foreach($vendors as $v)<option value="{{ $v->id }}" @selected((string)request('vendor_id')===(string)$v->id)>{{ $v->store_name ?? $v->user->name ?? 'Vendor #'.$v->id }}</option>@endforeach</select>
        <button type="submit" class="btn-primary px-5 py-2">Filter</button>
        <a href="{{ route('admin.admin-collections.index') }}" class="border border-mist px-5 py-2 text-sm" style="border-radius:2px">Clear</a>
    </form>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius:2px">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1300px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65">
                    <tr>
                        <th class="px-5 py-3">Vendor details</th>
                        <th class="px-5 py-3">Order details</th>
                        <th class="px-5 py-3">Customer details</th>
                        <th class="px-5 py-3 text-right">Admin collected</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse($collections as $c)
                        @php $vo = $c->vendorOrder; $vendor = $vo?->vendor; $vendorUser = $vendor?->user; $order = $vo?->order; $customer = $order?->customer; @endphp
                        <tr class="hover:bg-paper/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-forest">{{ $vendor->store_name ?? $vendorUser->name ?? 'Vendor #'.$vo->vendor_id }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $vendorUser->email ?? '' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">Vendor #{{ $vendor->id ?? $vo->vendor_id }} · {{ $vendor->bankDetails?->bank_name ?? 'No bank' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-mono text-xs break-all font-semibold text-forest">{{ $order->order_number ?? '—' }}</p>
                                <p class="mt-1 font-mono text-xs break-all text-graphite/60">{{ $vo->vendor_order_number ?? '' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $order?->created_at?->format('M d, Y') }} · {{ $vo->status ?? '' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">Gross Rs {{ number_format($c->order_amount, 2) }} · Rate {{ number_format($c->commission_rate, 2) }}%</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-forest">{{ $customer->name ?? 'Customer' }}</p>
                                <p class="mt-1 text-xs break-all text-graphite/60">{{ $customer->email ?? '' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $order?->address?->phone_number ?? '' }} · {{ $order?->address?->city ?? '' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <p class="font-bold text-forest">Rs {{ number_format($c->commission_amount, 2) }}</p>
                                <p class="mt-1 text-xs text-graphite/60">Vendor payout Rs {{ number_format($c->vendor_payout, 2) }}</p>
                                <p class="mt-1 text-xs text-graphite/60">Released: {{ $c->released_at?->format('M d, Y') ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize @if($c->status==='released') bg-green-100 text-green-800 @elseif($c->status==='paid') bg-blue-100 text-blue-800 @else bg-yellow-100 text-yellow-800 @endif">{{ $c->status }}</span></td>
                            <td class="px-5 py-4 text-right">
                                @if($vo?->order?->payment)<a href="{{ route('admin.payments.show', $vo->order->payment) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">Payment →</a><br>@endif
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-graphite/60 hover:text-indigo-600">Order →</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-graphite/65">No admin collections yet. Commissions are recorded when admin clicks Release Amount on a payment.</td></tr>
                    @endforelse
                </tbody>
                @if($collections->isNotEmpty())
                <tfoot class="bg-paper font-semibold border-t-2 border-mist">
                    <tr><td colspan="3" class="px-5 py-3 text-right">Page totals</td><td class="px-5 py-3 text-right">Rs {{ number_format($collections->sum('commission_amount'), 2) }}</td><td colspan="2"></td></tr>
                </tfoot>
                @endif
            </table>
        </div>
        @if($collections->hasPages())<div class="border-t border-mist px-5 py-4">{{ $collections->links() }}</div>@endif
    </div>
</div>
@endsection

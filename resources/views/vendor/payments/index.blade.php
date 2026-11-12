@extends('layouts.vendor')
@section('title', 'Payments — Seller Centre')
@section('content')
<div class="space-y-8">
    <div>
        <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Finance</p>
        <h1 class="mt-2 text-3xl leading-tight">Payments</h1>
        <p class="mt-2 text-sm text-graphite/70">Track payouts released by admin after deducting commission. Each row is linked to its order. Amount credited is net after commission.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-4">
        <div class="border-t-2 border-forest bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Total credited</p><p class="mt-2 font-display text-2xl text-forest">Rs {{ number_format($stats['completed'], 2) }}</p><p class="text-xs text-graphite/60">{{ $stats['count'] }} payout(s)</p></div>
        <div class="border-t-2 border-marigold bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Pending payouts</p><p class="mt-2 font-display text-2xl text-forest">Rs {{ number_format($stats['pending'], 2) }}</p><p class="text-xs text-graphite/60">Already released, processing</p></div>
        <div class="border-t-2 border-vermilion bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Total gross</p><p class="mt-2 font-display text-2xl text-forest">Rs {{ number_format($stats['total'] + $stats['commission_total'], 2) }}</p><p class="text-xs text-graphite/60">Before commission</p></div>
        <div class="border-t-2 border-indigo-500 bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Commission deducted</p><p class="mt-2 font-display text-2xl text-vermilion">Rs {{ number_format($stats['commission_total'], 2) }}</p></div>
    </div>

    <section class="border border-amber-200 bg-amber-50 p-4" style="border-radius:2px">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-lg font-semibold text-amber-900">Held in escrow</h2><p class="mt-1 text-sm text-amber-800">These customer payments are visible for tracking only. They are not part of your available or withdrawable balance until an admin releases them.</p></div>
            <strong class="text-xl text-amber-900">Rs {{ number_format($stats['held'], 2) }}</strong>
        </div>
        @if($heldPayments->isNotEmpty())
            <div class="mt-4 overflow-x-auto"><table class="w-full min-w-[720px] text-left text-sm"><thead class="border-b border-amber-200 text-xs uppercase tracking-wider text-amber-800"><tr><th class="px-3 py-2">Order</th><th class="px-3 py-2">Customer</th><th class="px-3 py-2">Payment</th><th class="px-3 py-2 text-right">Your amount held</th><th class="px-3 py-2">Status</th></tr></thead><tbody class="divide-y divide-amber-200">@foreach($heldPayments as $held)<tr><td class="px-3 py-3"><p class="font-semibold">{{ $held->order->order_number ?? '—' }}</p><p class="text-xs text-amber-800">{{ $held->order->created_at?->format('M d, Y') }}</p></td><td class="px-3 py-3">{{ $held->order->customer->name ?? 'Customer' }}</td><td class="px-3 py-3"><p>{{ $held->order->payment->payment_gateway ?? 'Payment' }}</p><p class="text-xs text-amber-800">Payment #{{ $held->order->payment->id ?? '—' }}</p></td><td class="px-3 py-3 text-right font-semibold">Rs {{ number_format($held->vendor_amount, 2) }}</td><td class="px-3 py-3"><span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-900">Pending – Awaiting Admin Release</span></td></tr>@endforeach</tbody></table></div>
        @else
            <p class="mt-4 text-sm text-amber-800">No customer payments are currently held for your store.</p>
        @endif
    </section>

    <section class="border border-mist bg-white p-4" style="border-radius:2px">
        <h2 class="text-sm font-semibold">Your payout account</h2>
        @if($vendor->bankDetails)
            <div class="mt-3 grid gap-3 sm:grid-cols-3 text-sm">
                <div><p class="text-graphite/60">Bank</p><p class="font-semibold text-forest">{{ $vendor->bankDetails->bank_name }}</p></div>
                <div><p class="text-graphite/60">Account holder</p><p class="font-semibold text-forest">{{ $vendor->bankDetails->account_holder_name }}</p></div>
                <div><p class="text-graphite/60">Account</p><p class="font-mono text-forest">{{ $vendor->bankDetails->maskedAccountNumber() }}</p></div>
                <div><p class="text-graphite/60">IBAN</p><p class="font-mono text-xs break-all text-forest">{{ $vendor->bankDetails->iban ? '****' . substr($vendor->bankDetails->iban, -4) : 'Not set' }}</p></div>
                <div><p class="text-graphite/60">Type</p><p class="text-forest capitalize">{{ $vendor->bankDetails->account_type ?? '—' }}</p></div>
                <div><p class="text-graphite/60">Verified</p><span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $vendor->bankDetails->is_verified ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">{{ $vendor->bankDetails->is_verified ? 'Verified' : 'Pending verification' }}</span></div>
            </div>
        @else
            <p class="mt-2 text-sm text-graphite/70">No bank details on file. Contact admin to configure payouts.</p>
        @endif
    </section>

    <form method="GET" class="flex flex-wrap gap-3 bg-white border border-mist p-4" style="border-radius:2px">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order / payout / transaction ID" class="flex-1 min-w-[220px] border border-mist px-3 py-2 text-sm" style="border-radius:2px">
        <select name="status" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px"><option value="">All statuses</option>@foreach(['completed','pending','processing','failed'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
        <button type="submit" class="btn-primary px-5 py-2">Filter</button>
        <a href="{{ route('vendor.payments.index') }}" class="border border-mist px-5 py-2 text-sm" style="border-radius:2px">Clear</a>
    </form>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius:2px">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1100px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65">
                    <tr>
                        <th class="px-5 py-3">Order details</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Payment breakdown</th>
                        <th class="px-5 py-3 text-right">Released to you</th>
                        <th class="px-5 py-3">Payment status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-mist">
                    @forelse($payouts as $payout)
                        @php
                            $vo = $payout->vendorOrder;
                            $order = $vo?->order;
                            $customer = $order?->customer;
                        @endphp
                        <tr class="hover:bg-paper/70">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-forest">{{ $order->order_number ?? '—' }}</p>
                                <p class="mt-1 font-mono text-xs break-all text-graphite/60">{{ $vo->vendor_order_number ?? '' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $order?->created_at?->format('M d, Y') }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $order?->items()->count() ?? 0 }} item(s) · Linked payment #{{ $payout->payment_id }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-medium text-forest">{{ $customer->name ?? 'Customer' }}</p>
                                <p class="mt-1 text-xs break-all text-graphite/60">{{ $customer->email ?? '' }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $order?->address?->phone_number ?? '' }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="space-y-1 text-xs">
                                    <div class="flex justify-between gap-4"><span class="text-graphite/60">Gross</span><span class="font-semibold">Rs {{ number_format($payout->gross_amount, 2) }}</span></div>
                                    <div class="flex justify-between gap-4"><span class="text-graphite/60">Admin commission</span><span class="font-semibold text-vermilion">- Rs {{ number_format($payout->commission_amount, 2) }}</span></div>
                                    <div class="flex justify-between gap-4 border-t border-mist pt-1"><span class="font-semibold">Net</span><span class="font-bold text-forest">Rs {{ number_format($payout->amount, 2) }}</span></div>
                                    <div class="text-graphite/60">Method: {{ ucfirst(str_replace('_',' ', $payout->payout_method)) }} · {{ $payout->payment->payment_gateway ?? '' }}</div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <p class="font-bold text-forest">Rs {{ number_format($payout->amount, 2) }}</p>
                                <p class="mt-1 font-mono text-xs break-all text-graphite/60">{{ $payout->transaction_id }}</p>
                                <p class="mt-1 text-xs text-graphite/60">{{ $payout->processed_at?->format('M d, Y H:i') ?? $payout->created_at->format('M d, Y') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold capitalize @if($payout->status==='completed') bg-green-100 text-green-800 @elseif($payout->status==='pending') bg-yellow-100 text-yellow-800 @elseif($payout->status==='processing') bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">{{ $payout->status }}</span>
                                <p class="mt-1 text-xs text-graphite/60">Escrow: {{ $payout->payment?->escrow_status ?? '—' }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('vendor.payments.show', $payout) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">View →</a>
                                <form method="POST" action="{{ route('vendor.payments.destroy', $payout) }}" class="mt-2" onsubmit="return confirm('Delete this payment detail from your payment history? The linked payment and escrow records will not be deleted.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-600 hover:text-red-800">Delete</button>
                                </form>
                                @if($vo)<br><a href="{{ route('vendor.orders.show', $vo) }}" class="text-xs text-graphite/60 hover:text-indigo-600">Order →</a>@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-12 text-center text-graphite/65">No payouts yet. When admin clicks Release Amount on a payment, you will see the credited amount here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payouts->hasPages())<div class="border-t border-mist px-5 py-4">{{ $payouts->links() }}</div>@endif
    </div>
</div>
@endsection

@extends('layouts.admin')
@section('title', 'Payments — Admin')
@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div><p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Finance</p><h1 class="mt-2 text-3xl leading-tight">Payments & escrow</h1><p class="mt-2 text-sm text-graphite/70">Monitor all transactions, escrow holds, and settlements. Search by transaction ID or order number.</p></div>
        <a href="{{ route('admin.payments.export', request()->query()) }}" class="btn-dark w-auto px-4 py-2.5">Export CSV</a>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="border-t-2 border-forest bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Total payments</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($stats['total']) }}</p><p class="mt-1 text-xs text-graphite/60">Rs {{ number_format($stats['revenue']) }} collected</p></div>
        <div class="border-t-2 border-marigold bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Pending</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($stats['pending']) }}</p><p class="mt-1 text-xs text-graphite/60">Rs {{ number_format($stats['pending_amount']) }} awaiting</p></div>
        <div class="border-t-2 border-vermilion bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Failed / Refunded</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($stats['failed'] + $stats['refunded']) }}</p><p class="mt-1 text-xs text-graphite/60">{{ $stats['failed'] }} failed, {{ $stats['refunded'] }} refunded</p></div>
        <div class="border-t-2 border-indigo-500 bg-white p-4 shadow-sm" style="border-radius:2px"><p class="text-sm text-graphite/70">Escrow held</p><p class="mt-2 font-display text-3xl text-forest">{{ number_format($stats['held_escrow']) }}</p><p class="mt-1 text-xs text-graphite/60">Funds in {{ config('escrow.account.bank_name') }} escrow</p></div>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 bg-white border border-mist p-4" style="border-radius:2px">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search transaction / order / customer" class="min-w-0 flex-1 border border-mist px-3 py-2 text-sm" style="border-radius:2px">
        <select name="status" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px"><option value="">All statuses</option>@foreach(['pending','completed','failed','refunded'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
        <select name="gateway" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px"><option value="">All gateways</option><option value="stripe" @selected(request('gateway')==='stripe')>Stripe</option><option value="bank_transfer" @selected(request('gateway')==='bank_transfer')>Bank Transfer</option></select>
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px">
        <button type="submit" class="btn-primary px-5 py-2">Filter</button>
        <a href="{{ route('admin.payments.index') }}" class="border border-mist px-5 py-2 text-sm" style="border-radius:2px">Clear</a>
    </form>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius:2px">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-5 py-3">Payment</th><th class="px-5 py-3">Order / Customer</th><th class="px-5 py-3">Gateway</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Escrow</th><th class="px-5 py-3 text-right">Amount</th><th class="px-5 py-3 text-right">Action</th></tr></thead>
                <tbody class="divide-y divide-mist">
                    @forelse($payments as $p)
                    <tr class="hover:bg-paper/70"><td class="px-5 py-4"><p class="font-mono text-xs break-all">{{ $p->transaction_id }}</p><p class="text-xs text-graphite/60">{{ $p->created_at->format('M d, Y H:i') }}</p></td><td class="px-5 py-4"><p class="font-semibold text-forest">{{ $p->order->order_number ?? '—' }}</p><p class="text-xs text-graphite/60">{{ $p->order->customer->name ?? '' }} · {{ $p->order->customer->email ?? '' }}</p></td><td class="px-5 py-4 capitalize text-graphite">{{ $p->payment_gateway === 'stripe' ? 'Stripe Card' : str_replace('_',' ', $p->payment_gateway) }}</td><td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize @if($p->status==='completed') bg-green-100 text-green-800 @elseif($p->status==='pending') bg-yellow-100 text-yellow-800 @elseif($p->status==='failed') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">{{ $p->status }}</span></td><td class="px-5 py-4"><span class="rounded-full bg-mist px-2.5 py-1 text-xs capitalize">{{ $p->escrow_status ?? '—' }}</span></td><td class="px-5 py-4 text-right font-semibold">Rs {{ number_format($p->amount) }}</td><td class="px-5 py-4 text-right"><a href="{{ route('admin.payments.show',$p) }}" class="font-semibold text-indigo-600">View →</a></td></tr>
                    @empty<tr><td colspan="7" class="px-6 py-12 text-center text-graphite/65">No payments found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())<div class="border-t border-mist px-5 py-4">{{ $payments->links() }}</div>@endif
    </div>

    <div class="border border-mist bg-white p-5" style="border-radius:2px">
        <h3 class="font-semibold">Escrow account — designated marketplace holding</h3>
        <p class="mt-1 text-sm text-graphite/70">Funds from Stripe and bank transfers are held in the escrow account until order fulfillment. Withdrawal requires admin approval.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-3 text-sm"><div><p class="text-graphite/60">Account holder</p><p class="font-semibold">{{ config('escrow.account.holder') }}</p></div><div><p class="text-graphite/60">Bank / IBAN</p><p class="font-semibold">{{ config('escrow.account.bank_name') }} · {{ config('escrow.account.iban') }}</p></div><div><p class="text-graphite/60">Release rule</p><p class="font-semibold">Admin approval required — delivery does not release funds</p></div></div>
    </div>
</div>
@endsection

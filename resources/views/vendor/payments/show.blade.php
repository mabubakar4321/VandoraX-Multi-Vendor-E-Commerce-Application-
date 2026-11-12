@extends('layouts.vendor')
@section('title', 'Payment Details — Seller Centre')
@section('content')
@php $vo = $payout->vendorOrder; $order = $vo?->order; @endphp
<div class="space-y-8">
    <div><a href="{{ route('vendor.payments.index') }}" class="text-sm font-semibold text-indigo-600">← Back to payments</a><h1 class="mt-3 text-2xl font-bold break-all">Payout {{ $payout->transaction_id }}</h1><p class="text-sm text-graphite/60">Order {{ $order->order_number ?? '—' }} · Vendor order {{ $vo->vendor_order_number ?? '' }} · {{ $payout->created_at->format('F d, Y H:i A') }}</p></div>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="border border-mist bg-white p-5" style="border-radius:2px">
            <h2 class="text-lg font-semibold">Order details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                @if($vo)
                <div class="flex justify-between gap-2"><dt class="text-graphite/60">Order number</dt><dd class="font-semibold text-forest break-all">{{ $order->order_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-graphite/60">Vendor order</dt><dd class="font-mono text-xs break-all">{{ $vo->vendor_order_number ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-graphite/60">Order status</dt><dd class="capitalize">{{ $order->status ?? '—' }} / {{ $vo->status ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-graphite/60">Items</dt><dd>{{ $order?->items()->count() ?? 0 }}</dd></div>
                <div class="flex justify-between"><dt class="text-graphite/60">Order total</dt><dd class="font-semibold">Rs {{ number_format($order->total_amount ?? 0, 2) }}</dd></div>
                @else
                <p class="text-graphite/60">No linked vendor order for this payout.</p>
                @endif
            </dl>
            @if($vo)<a href="{{ route('vendor.orders.show', $vo) }}" class="mt-4 inline-flex text-sm font-semibold text-indigo-600">View order →</a>@endif
        </section>
        <section class="border border-mist bg-white p-5" style="border-radius:2px">
            <h2 class="text-lg font-semibold">Customer details</h2>
            <dl class="mt-4 space-y-3 text-sm">
                <div><dt class="text-graphite/60">Name</dt><dd class="font-semibold text-forest">{{ $order?->customer?->name ?? 'Customer' }}</dd></div>
                <div><dt class="text-graphite/60">Email</dt><dd class="break-all">{{ $order?->customer?->email ?? '—' }}</dd></div>
                <div><dt class="text-graphite/60">Phone</dt><dd>{{ $order?->address?->phone_number ?? '—' }}</dd></div>
                <div><dt class="text-graphite/60">Delivery address</dt><dd class="text-xs leading-5">{{ $order?->address?->address_line_1 }}, {{ $order?->address?->city }}, {{ $order?->address?->province }} {{ $order?->address?->postal_code }}</dd></div>
            </dl>
        </section>
    </div>

    <section class="border border-mist bg-white p-5" style="border-radius:2px">
        <h2 class="text-lg font-semibold">Payment details</h2>
        <div class="mt-4 grid gap-4 sm:grid-cols-3 text-sm">
            <div class="bg-paper p-4" style="border-radius:2px"><p class="text-graphite/60">Original order amount (your share: gross)</p><p class="mt-2 font-display text-xl text-forest">Rs {{ number_format($payout->gross_amount, 2) }}</p><p class="text-xs text-graphite/60">VendorOrder subtotal</p></div>
            <div class="bg-red-50 p-4" style="border-radius:2px"><p class="text-graphite/60">Admin commission (deducted)</p><p class="mt-2 font-display text-xl text-vermilion">- Rs {{ number_format($payout->commission_amount, 2) }}</p><p class="text-xs text-graphite/60">Rate: {{ $vo && $vo->subtotal>0 ? round(($payout->commission_amount / $vo->subtotal)*100, 2) : 0 }}%</p></div>
            <div class="bg-green-50 p-4 border border-green-200" style="border-radius:2px"><p class="text-graphite/60">Amount credited to your account</p><p class="mt-2 font-display text-2xl text-forest">Rs {{ number_format($payout->amount, 2) }}</p><p class="text-xs text-graphite/60">Transaction {{ $payout->transaction_id }}</p></div>
        </div>
        <dl class="mt-6 space-y-3 text-sm">
            <div class="flex justify-between"><dt class="text-graphite/60">Payment gateway</dt><dd class="capitalize">{{ $payout->payment?->payment_gateway ?? '—' }} ({{ $payout->payment?->payment_method ?? '' }})</dd></div>
            <div class="flex justify-between"><dt class="text-graphite/60">Transaction ID</dt><dd class="font-mono text-xs break-all">{{ $payout->payment?->transaction_id ?? '—' }}</dd></div>
            <div class="flex justify-between"><dt class="text-graphite/60">Payout status</dt><dd><span class="px-2 py-1 text-xs font-semibold rounded-full capitalize @if($payout->status==='completed') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">{{ $payout->status }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-graphite/60">Released at</dt><dd>{{ $payout->processed_at?->format('F d, Y H:i A') ?? $payout->created_at->format('F d, Y H:i A') }}</dd></div>
        </dl>
    </section>

    <section class="border border-mist bg-white p-5" style="border-radius:2px">
        <h2 class="text-lg font-semibold">Bank details — amount credited</h2>
        @php $bank = $payout->vendor?->bankDetails; @endphp
        @if($bank)
            <div class="mt-4 grid gap-4 sm:grid-cols-2 text-sm">
                <div><p class="text-graphite/60">Bank</p><p class="font-semibold text-forest">{{ $bank->bank_name }}</p></div>
                <div><p class="text-graphite/60">Account holder</p><p class="font-semibold text-forest">{{ $bank->account_holder_name }}</p></div>
                <div><p class="text-graphite/60">Account number</p><p class="font-mono text-forest">{{ $bank->maskedAccountNumber() }}</p></div>
                <div><p class="text-graphite/60">IBAN</p><p class="font-mono text-xs break-all">****{{ $bank->iban ? substr($bank->iban, -4) : '----' }}</p></div>
                <div><p class="text-graphite/60">Credited amount</p><p class="font-bold text-forest">Rs {{ number_format($payout->amount, 2) }}</p></div>
                <div><p class="text-graphite/60">Method</p><p class="capitalize">{{ $payout->payout_method }} · {{ $bank->account_type ?? '' }}</p></div>
            </div>
        @else
            <p class="mt-2 text-sm text-graphite/70">No bank details found for this vendor. Ask admin to configure them.</p>
        @endif
        <p class="mt-4 text-xs text-graphite/60">This payout is linked to Order {{ $order->order_number ?? '' }} and VendorOrder {{ $vo->vendor_order_number ?? '' }}. Amount shown is after commission deduction.</p>
    </section>
</div>
@endsection

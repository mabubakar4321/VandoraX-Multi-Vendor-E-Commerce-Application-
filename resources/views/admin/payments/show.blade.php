@extends('layouts.admin')
@section('title', 'Payment Details — Admin')
@section('content')
@php $p = $payment; @endphp
<div class="space-y-8">
    <div class="min-w-0"><a href="{{ route('admin.payments.index') }}" class="text-sm font-semibold text-indigo-600">← Back to payments</a><h1 class="mt-3 break-all text-2xl font-bold">Payment #{{ $p->id }} — {{ $p->transaction_id }}</h1><p class="break-words text-sm text-graphite/60">{{ $p->created_at->format('F d, Y H:i A') }} · {{ $p->order->order_number ?? '' }}</p></div>

    @if(session('success'))<div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3" style="border-radius:2px">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3" style="border-radius:2px">{{ session('error') }}</div>@endif

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="min-w-0 border border-mist bg-white p-5" style="border-radius:2px"><h2 class="text-lg font-semibold">Payment record</h2><dl class="mt-4 space-y-3 text-sm"><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Status</dt><dd class="font-semibold capitalize">{{ $p->status }}</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Gateway</dt><dd class="font-semibold">{{ $p->payment_gateway }}</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Method</dt><dd>{{ $p->payment_method }}</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Amount</dt><dd class="font-semibold">Rs {{ number_format($p->amount) }} {{ $p->currency }}</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Escrow</dt><dd class="capitalize"><span class="px-2 py-1 text-xs font-semibold rounded-full @if($p->escrow_status==='held') bg-yellow-100 text-yellow-800 @elseif($p->escrow_status==='released') bg-green-100 text-green-800 @else bg-mist text-forest @endif">{{ $p->escrow_status ?? '—' }}</span></dd></div>@if($p->paid_at)<div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Paid at</dt><dd>{{ $p->paid_at->format('M d, Y H:i') }}</dd></div>@endif @if($p->released_at)<div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Released at</dt><dd>{{ $p->released_at->format('M d, Y H:i') }}</dd></div>@endif @if($p->refunded_at)<div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Refunded</dt><dd class="break-words">{{ $p->refunded_at->format('M d, Y') }} — {{ $p->refund_reason }}</dd></div>@endif</dl></section>
        <section class="min-w-0 border border-mist bg-white p-5" style="border-radius:2px"><h2 class="text-lg font-semibold">Order</h2><dl class="mt-4 space-y-3 text-sm"><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Order number</dt><dd class="font-semibold">{{ $p->order->order_number }}</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Customer</dt><dd class="break-words">{{ $p->order->customer->name }} ({{ $p->order->customer->email }})</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Order status</dt><dd class="capitalize">{{ $p->order->status }} / {{ $p->order->payment_status }}</dd></div><div class="flex flex-wrap justify-between gap-x-3 gap-y-1"><dt class="text-graphite/60">Total</dt><dd class="font-semibold">Rs {{ number_format($p->order->total_amount) }}</dd></div></dl><a href="{{ route('admin.orders.show',$p->order) }}" class="mt-4 inline-flex text-sm font-semibold text-indigo-600">View order →</a></section>
    </div>

    @if($p->escrowAccount)
    <section class="border border-mist bg-white p-5" style="border-radius:2px"><h2 class="text-lg font-semibold">Escrow ledger</h2><p class="text-sm text-graphite/60 mt-1">Designated account: {{ config('escrow.account.holder') }} — {{ config('escrow.account.bank_name') }} {{ config('escrow.account.iban') }}</p><div class="mt-4 flex flex-wrap gap-4 text-sm"><span>Status: <strong class="capitalize">{{ $p->escrowAccount->status }}</strong></span><span>Amount: <strong>Rs {{ number_format($p->escrowAccount->amount) }}</strong></span><span>Held: {{ $p->escrowAccount->held_at?->format('M d, Y') }}</span>@if($p->escrowAccount->released_at)<span>Released: {{ $p->escrowAccount->released_at->format('M d, Y') }}</span>@endif</div></section>
    @endif

    <section class="border border-mist bg-white p-5" style="border-radius:2px">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div><h2 class="text-lg font-semibold">Refund history</h2><p class="mt-1 text-sm text-graphite/60">Every Stripe refund transaction for this payment is recorded here.</p></div>
            <strong class="text-lg text-vermilion">Refunded: Rs {{ number_format((float) $p->refunded_amount, 2) }}</strong>
        </div>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-4 py-2">Transaction</th><th class="px-4 py-2">Amount</th><th class="px-4 py-2">Status</th><th class="px-4 py-2">Reason</th><th class="px-4 py-2">Date</th></tr></thead>
                <tbody class="divide-y divide-mist">
                    @forelse($p->refunds as $refund)
                        <tr><td class="break-all px-4 py-3 font-mono text-xs">{{ $refund->transaction_id }}</td><td class="px-4 py-3">Rs {{ number_format($refund->amount, 2) }}</td><td class="px-4 py-3 capitalize">{{ $refund->status }}</td><td class="px-4 py-3">{{ $refund->reason ?: '—' }}</td><td class="px-4 py-3">{{ $refund->created_at?->format('M d, Y H:i') }}</td></tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-6 text-center text-graphite/60">No refund transactions recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- Vendor breakdown: gross / commission / net per vendor_order --}}
    <section class="border border-mist bg-white p-5" style="border-radius:2px">
        <h2 class="text-lg font-semibold">Vendor breakdown</h2>
        <p class="mt-1 text-sm text-graphite/60">Commission is auto-calculated at checkout; Release Amount deducts it and credits net to vendor.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-4 py-2">Vendor</th><th class="px-4 py-2">Vendor order</th><th class="px-4 py-2 text-right">Gross</th><th class="px-4 py-2 text-right">Commission</th><th class="px-4 py-2 text-right">Vendor receives</th><th class="px-4 py-2">Status</th></tr></thead>
                <tbody class="divide-y divide-mist">
                    @forelse($vendorBreakdown as $vo)
                    <tr><td class="px-4 py-3"><p class="font-semibold text-forest">{{ $vo->vendor->store_name ?? $vo->vendor->user->name ?? 'Vendor #'.$vo->vendor_id }}</p><p class="text-xs text-graphite/60">ID {{ $vo->vendor_id }}</p></td><td class="px-4 py-3 font-mono text-xs break-all">{{ $vo->vendor_order_number }}</td><td class="px-4 py-3 text-right">Rs {{ number_format($vo->subtotal,2) }}</td><td class="px-4 py-3 text-right text-vermilion">- Rs {{ number_format($vo->commission_amount,2) }}</td><td class="px-4 py-3 text-right font-bold text-forest">Rs {{ number_format($vo->vendor_amount,2) }}</td><td class="px-4 py-3"><span class="px-2 py-1 text-xs font-semibold rounded-full capitalize @if($vo->status==='pending') bg-yellow-100 text-yellow-800 @elseif($vo->status==='confirmed') bg-blue-100 text-blue-800 @else bg-mist text-forest @endif">{{ $vo->status }}</span></td></tr>
                    @empty<tr><td colspan="6" class="px-4 py-8 text-center text-graphite/60">No vendor orders</td></tr>@endforelse
                </tbody>
                @if($vendorBreakdown->isNotEmpty())
                <tfoot class="bg-paper font-semibold"><tr><td colspan="2" class="px-4 py-3 text-right">Totals</td><td class="px-4 py-3 text-right">Rs {{ number_format($vendorBreakdown->sum('subtotal'),2) }}</td><td class="px-4 py-3 text-right">Rs {{ number_format($vendorBreakdown->sum('commission_amount'),2) }}</td><td class="px-4 py-3 text-right">Rs {{ number_format($vendorBreakdown->sum('vendor_amount'),2) }}</td><td></td></tr></tfoot>
                @endif
            </table>
        </div>
        @php $alreadyReleased = \App\Models\VendorPayout::where('payment_id',$p->id)->exists(); @endphp
        @if($alreadyReleased)
            <div class="mt-4 rounded bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">This payment has already been released. Vendor payout(s) recorded — duplicate release blocked.</div>
        @endif
    </section>

    <section class="border border-mist bg-white p-5" style="border-radius:2px"><h2 class="text-lg font-semibold">Actions</h2>
        <div class="mt-4 flex flex-wrap gap-3">
            @if($p->escrow_status==='held' && in_array($p->status, ['completed','pending']))
            <form id="release-amount-form" method="POST" action="{{ route('admin.payments.release-escrow',$p) }}" class="hidden">@csrf</form>
            <button type="button" id="open-release-modal" class="bg-forest-500 text-white px-6 py-2.5 text-sm font-semibold hover:bg-forest-600" style="border-radius:2px">Release Amount</button>
            <p class="w-full text-xs text-graphite/60">Credits Rs {{ number_format($vendorBreakdown->sum('vendor_amount'),2) }} to vendor(s) after deducting Rs {{ number_format($vendorBreakdown->sum('commission_amount'),2) }} commission. Customer payment will show Confirmed.</p>
            @if($p->status==='pending')<p class="w-full text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2" style="border-radius:2px">Note: This payment is currently Pending (Stripe did not confirm yet). Clicking Release Amount will mark it completed and release funds.</p>@endif
            @elseif($p->escrow_status==='released')
            <span class="px-4 py-2.5 text-sm font-semibold rounded bg-green-100 text-green-800">Released — vendor(s) already credited</span>
            @endif
            @if($p->status === 'completed' && $p->escrow_status!=='released')
            <form method="POST" action="{{ route('admin.payments.refund',$p) }}" class="flex w-full flex-col items-stretch gap-2 sm:flex-row sm:flex-wrap sm:items-end">@csrf<input type="number" step="0.01" min="0.01" max="{{ max(0, (float) $p->amount - (float) $p->refunded_amount) }}" name="amount" placeholder="Refund amount" required class="w-full min-w-0 border border-mist px-3 py-2 text-sm sm:w-auto" style="border-radius:2px"><input type="text" name="reason" placeholder="Refund details / reason" required class="w-full min-w-0 border border-mist px-3 py-2 text-sm sm:w-auto" style="border-radius:2px"><button type="submit" class="bg-vermilion text-white px-5 py-2.5 text-sm font-semibold" style="border-radius:2px">Refund via {{ $p->payment_gateway==='stripe'?'Stripe':'payment method' }}</button></form>
            @elseif($p->status === 'pending')
            <p class="w-full text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2" style="border-radius:2px">Refund is available after Stripe confirms this payment. Pending payments have not been captured by Stripe yet.</p>
            @endif
            <a href="{{ route('invoice.view',$p->order) }}" target="_blank" class="border border-mist px-5 py-2.5 text-sm font-semibold" style="border-radius:2px">View invoice</a>
            <a href="{{ route('invoice.download',$p->order) }}" class="border border-mist px-5 py-2.5 text-sm font-semibold" style="border-radius:2px">Download PDF</a>
        </div>
        @if($p->gateway_response)<details class="mt-4"><summary class="cursor-pointer text-sm font-semibold">Gateway response (JSON)</summary><pre class="mt-2 overflow-auto bg-paper p-3 text-xs">{{ json_encode($p->gateway_response, JSON_PRETTY_PRINT) }}</pre></details>@endif
    </section>
</div>

<div id="release-amount-modal" class="fixed inset-0 z-50 hidden overflow-y-auto p-4" aria-labelledby="release-amount-title" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-forest/60 backdrop-blur-sm" data-release-close></div>

    <div class="relative flex min-h-full items-center justify-center">
        <div class="w-full max-w-md overflow-hidden border border-mist bg-white shadow-2xl" style="border-radius: 4px;">
            <div class="flex items-start gap-4 border-b border-mist px-6 py-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-forest-100 text-forest" aria-hidden="true">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M12 15h.01M4.93 4.93a10 10 0 1014.14 0 10 10 0 00-14.14 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 id="release-amount-title" class="text-xl font-semibold text-forest">Release amount?</h2>
                    <p class="mt-1 text-sm leading-relaxed text-graphite">
                        This will deduct admin commission of <strong class="font-semibold text-vermilion">Rs {{ number_format($vendorBreakdown->sum('commission_amount'),2) }}</strong>
                        and credit <strong class="font-semibold text-forest">Rs {{ number_format($vendorBreakdown->sum('vendor_amount'),2) }}</strong>
                        to vendor(s). This cannot be undone.
                    </p>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 bg-paper px-6 py-4 sm:flex-row sm:justify-end">
                <button type="button" class="border border-mist bg-white px-5 py-2.5 text-sm font-semibold text-forest transition-colors hover:bg-mist" data-release-close>
                    Cancel
                </button>
                <button type="button" id="confirm-release-amount" class="bg-forest-500 px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-forest-600">
                    Release amount
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('release-amount-modal');
        const confirmButton = document.getElementById('confirm-release-amount');
        const form = document.getElementById('release-amount-form');

        const closeModal = () => {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        };

        document.getElementById('open-release-modal').addEventListener('click', () => {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            confirmButton.focus();
        });

        document.querySelectorAll('[data-release-close]').forEach((button) => {
            button.addEventListener('click', closeModal);
        });

        confirmButton.addEventListener('click', () => {
            form.classList.remove('hidden');
            form.submit();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    })();
</script>
@endsection

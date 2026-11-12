@extends('layouts.app')
@section('title', 'Payment History')
@section('content')
<div class="mx-auto max-w-6xl space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div><h1 class="text-3xl">Payment History</h1><p class="mt-2 text-sm text-graphite/70">Track card and bank-transfer payments, search by transaction ID or order number, filter by status.</p></div>
        <a href="{{ route('order.index') }}" class="text-sm font-semibold text-indigo-600">My orders →</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 bg-white border border-mist p-4" style="border-radius:2px">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order / transaction ID" class="flex-1 min-w-[220px] border border-mist px-3 py-2 text-sm" style="border-radius:2px">
        <select name="status" class="border border-mist px-3 py-2 text-sm" style="border-radius:2px"><option value="">All statuses</option>@foreach(['pending','completed','failed','refunded'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
        <button type="submit" class="bg-forest text-white px-5 py-2 text-sm font-semibold" style="border-radius:2px">Filter</button>
        <a href="{{ route('payment.history') }}" class="border border-mist px-5 py-2 text-sm" style="border-radius:2px">Clear</a>
    </form>

    <div class="overflow-hidden border border-mist bg-white" style="border-radius:2px">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[820px] text-left text-sm">
                <thead class="bg-paper text-xs uppercase tracking-wider text-graphite/65"><tr><th class="px-5 py-3">Order</th><th class="px-5 py-3">Gateway</th><th class="px-5 py-3">Transaction</th><th class="px-5 py-3">Status</th><th class="px-5 py-3">Escrow</th><th class="px-5 py-3 text-right">Amount</th><th class="px-5 py-3">Receipt</th></tr></thead>
                <tbody class="divide-y divide-mist">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-paper/70">
                        <td class="px-5 py-4"><a class="font-semibold text-indigo-600" href="{{ route('order.show', $payment->order) }}">{{ $payment->order->order_number }}</a><p class="mt-1 text-xs text-graphite/60">{{ $payment->created_at->format('M d, Y') }} · {{ $payment->order->vendorOrders->count() }} vendor(s)</p></td>
                        <td class="px-5 py-4 capitalize text-sm">{{ $payment->payment_gateway === 'stripe' ? 'Stripe Card' : str_replace('_',' ', $payment->payment_method) }}</td>
                        <td class="max-w-[200px] break-all px-5 py-4 font-mono text-xs">{{ $payment->transaction_id }}</td>
                        <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize @if($payment->status==='completed') bg-green-100 text-green-800 @elseif($payment->status==='pending') bg-yellow-100 text-yellow-800 @elseif($payment->status==='failed') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">{{ $payment->status }}</span>
                            @if($payment->status==='failed' && $payment->gateway_response['failure_message'] ?? false)<p class="mt-1 text-xs text-red-600">{{ $payment->gateway_response['failure_message'] }}</p>@endif
                            @if($payment->status==='failed')<button onclick="retryStripePayment({{ $payment->order->id }})" class="mt-2 text-xs font-semibold text-indigo-600">Retry payment →</button>@endif
                        </td>
                        <td class="px-5 py-4"><span class="rounded-full bg-mist px-2.5 py-1 text-xs capitalize">{{ $payment->escrow_status ?? '—' }}</span></td>
                        <td class="px-5 py-4 text-right font-semibold">Rs {{ number_format($payment->amount) }}</td>
                        <td class="px-5 py-4"><a href="{{ route('invoice.download', $payment->order) }}" class="font-semibold text-indigo-600">Download PDF</a><br><a href="{{ route('invoice.view', $payment->order) }}" class="text-xs text-graphite/60">View</a></td>
                    </tr>
                    @empty<tr><td colspan="7" class="px-6 py-12 text-center text-graphite/65">No payments found.</td></tr>@endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())<div class="border-t border-mist px-5 py-4">{{ $payments->links() }}</div>@endif
    </div>
</div>
@push('scripts')
<script>
function retryStripePayment(orderId){
    if(!confirm('Retry card payment for this order?')) return;
    fetch('{{ url('payment/stripe/retry') }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','Content-Type':'application/json'},body:JSON.stringify({order_id:orderId})})
    .then(r=>r.json()).then(d=>{ if(d.success){ alert('Retry ready — you will be redirected to checkout to complete payment.'); window.location.href='{{ url('checkout') }}'; } else alert(d.message||'Retry failed'); })
    .catch(()=>alert('Retry failed'));
}
</script>
@endpush
@endsection

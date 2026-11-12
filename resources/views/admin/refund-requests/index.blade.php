@extends('layouts.admin')
@section('title', 'Request for Refund — Admin')
@section('content')
<div class="space-y-7">
    <div><p class="text-xs font-semibold uppercase tracking-[.18em] text-graphite/60">Finance review</p><h1 class="mt-2 text-3xl">Request for Refund</h1><p class="mt-2 text-sm text-graphite/70">Review seller refund appeals with the complete customer, order, and return context.</p></div>
    <form class="panel flex flex-col gap-3 p-4 sm:flex-row" method="GET"><select class="field sm:w-56" name="status"><option value="">All statuses</option>@foreach(['pending','approved','rejected'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select><button class="btn-dark !w-auto">Filter requests</button></form>
    <div class="grid gap-4">
        @forelse ($refundRequests as $request)
            <a class="panel block p-5 transition hover:border-forest" href="{{ route('admin.refund-requests.show', $request) }}">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold text-forest">{{ $request->appeal_number }}</p><p class="mt-1 text-sm">{{ $request->orderItem->product->name ?? 'Product' }} · {{ $request->customer->name ?? 'Customer' }}</p><p class="mt-1 text-xs text-graphite/60">Seller: {{ $request->vendor->store_name ?? 'Seller' }} · {{ $request->created_at?->format('M d, Y H:i') }}</p></div><div class="text-left sm:text-right"><span class="status-pill status-{{ $request->status }}">{{ ucfirst($request->status) }}</span><p class="mt-2 font-semibold">₨{{ number_format($request->requested_amount, 2) }}</p></div></div>
            </a>
        @empty
            <div class="panel p-10 text-center text-graphite/65">No seller refund requests found.</div>
        @endforelse
    </div>
    {{ $refundRequests->links() }}
</div>
@endsection

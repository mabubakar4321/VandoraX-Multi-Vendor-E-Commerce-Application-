@extends('layouts.vendor')
@section('title', 'Refund Appeals — Seller Centre')
@section('content')
<div class="space-y-7">
    <div class="space-y-5">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[.18em] text-graphite/60">Seller finance</p>
            <h1 class="mt-2 text-3xl leading-tight sm:text-4xl">All Refund Appeals</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-graphite/70">Review every refund appeal submitted by your store, including the customer, order, return, requested amount, and admin response.</p>
        </div>
        <a class="btn-primary w-full text-center sm:w-auto" href="{{ route('vendor.refund-appeals.create') }}">Create new appeal</a>
    </div>

    <div class="grid gap-5">
        @forelse ($appeals as $appeal)
            <article class="panel overflow-hidden">
                <div class="flex flex-col gap-4 border-b border-mist bg-paper/60 p-5 sm:flex-row sm:items-start sm:justify-between">
                    <div class="flex min-w-0 gap-4">
                        <div class="h-16 w-16 shrink-0 overflow-hidden rounded border border-mist bg-white">
                            @if ($appeal->orderItem->product?->images?->first())
                                <img class="h-full w-full object-contain" src="{{ asset('storage/'.$appeal->orderItem->product->images->first()->image_path) }}" alt="{{ $appeal->orderItem->product->name }}">
                            @else
                                <div class="flex h-full items-center justify-center text-xs text-graphite/50">No image</div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[.14em] text-graphite/60">{{ $appeal->appeal_number }}</p>
                            <h2 class="mt-1 truncate text-lg text-forest">{{ $appeal->orderItem->product->name ?? 'Product' }}</h2>
                            <p class="mt-1 text-sm text-graphite/65">Customer: {{ $appeal->customer->name ?? '—' }} · Order: {{ $appeal->order->order_number ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="shrink-0 sm:text-right">
                        <span class="status-pill status-{{ $appeal->status }}">{{ ucfirst($appeal->status) }}</span>
                        <p class="mt-2 text-xl font-bold text-forest">₨{{ number_format($appeal->requested_amount, 2) }}</p>
                        <p class="text-xs text-graphite/60">Requested refund</p>
                    </div>
                </div>

                <div class="grid gap-5 p-5 md:grid-cols-2 xl:grid-cols-3">
                    <section>
                        <h3 class="font-semibold text-forest">Customer and order</h3>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div><dt class="text-graphite/60">Customer email</dt><dd class="break-all font-medium">{{ $appeal->customer->email ?? '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Order date</dt><dd class="font-medium">{{ $appeal->order->created_at?->format('M d, Y') ?? '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Product variant</dt><dd class="font-medium">{{ $appeal->orderItem->variant?->getDisplayName() ?: '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Quantity</dt><dd class="font-medium">{{ $appeal->orderItem->quantity }}</dd></div>
                        </dl>
                    </section>
                    <section>
                        <h3 class="font-semibold text-forest">Return details</h3>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div><dt class="text-graphite/60">Return number</dt><dd class="font-medium">{{ $appeal->returnRequest->return_number ?? '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Return status</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $appeal->returnRequest->status ?? '—') }}</dd></div>
                            <div><dt class="text-graphite/60">Customer reason</dt><dd class="font-medium capitalize">{{ str_replace('_', ' ', $appeal->returnRequest->reason ?? '—') }}</dd></div>
                            <div><dt class="text-graphite/60">Submitted</dt><dd class="font-medium">{{ $appeal->created_at?->format('M d, Y H:i') }}</dd></div>
                        </dl>
                    </section>
                    <section>
                        <h3 class="font-semibold text-forest">Seller refund details</h3>
                        <dl class="mt-3 space-y-2 text-sm">
                            <div><dt class="text-graphite/60">Refund method</dt><dd class="font-medium">{{ $appeal->refund_method ?: '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Reason</dt><dd class="whitespace-pre-line font-medium">{{ $appeal->reason }}</dd></div>
                        </dl>
                    </section>
                </div>

                <div class="grid gap-5 border-t border-mist p-5 md:grid-cols-2">
                    <div><h3 class="font-semibold text-forest">Your complete appeal</h3><p class="mt-2 whitespace-pre-line text-sm text-graphite/75">{{ $appeal->seller_explanation }}</p></div>
                    <div><h3 class="font-semibold text-forest">Refund processing details</h3><p class="mt-2 whitespace-pre-line text-sm text-graphite/75">{{ $appeal->refund_details ?: 'No additional processing details provided.' }}</p></div>
                </div>

                @if ($appeal->status !== 'pending' || $appeal->admin_notes)
                    <div class="border-t border-mist bg-forest/[.03] p-5">
                        <h3 class="font-semibold text-forest">Admin response</h3>
                        <p class="mt-2 whitespace-pre-line text-sm text-graphite/75">{{ $appeal->admin_notes ?: 'No admin notes were added.' }}</p>
                        @if ($appeal->reviewed_at)<p class="mt-2 text-xs text-graphite/60">Reviewed {{ $appeal->reviewed_at->format('M d, Y H:i') }}</p>@endif
                    </div>
                @endif
            </article>
        @empty
            <div class="panel p-10 text-center">
                <h2 class="text-lg font-semibold text-forest">No refund appeals yet</h2>
                <p class="mt-2 text-sm text-graphite/65">Submit an appeal when you need the admin team to review a customer refund.</p>
                <a class="btn-primary mt-5 inline-flex" href="{{ route('vendor.refund-appeals.create') }}">Create first appeal</a>
            </div>
        @endforelse
    </div>
    {{ $appeals->links() }}
</div>
@endsection

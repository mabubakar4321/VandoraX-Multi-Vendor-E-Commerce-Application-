@extends('layouts.vendor')

@section('title', 'Return '.$return->return_number)

@section('content')
<div class="space-y-6">
    <div>
        <a href="{{ route('vendor.returns.index') }}" class="text-sm font-semibold text-forest">← Back to returns</a>
        <h1 class="mt-4 text-3xl">{{ $return->return_number }}</h1>
        <p class="mt-2 text-sm text-graphite/65">
            {{ $return->orderItem->product->name ?? 'Product' }}
            · {{ $return->orderItem->order->customer->name ?? 'Customer' }}
        </p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_21rem]">
        <main class="space-y-6">
            @php
                $order = $return->orderItem->order;
                $item = $return->orderItem;
                $product = $item->product;
                $returnedQuantity = $return->returnItems->sum('quantity') ?: $item->quantity;
                $refundTotal = $return->refund_amount !== null
                    ? (float) $return->refund_amount
                    : ($return->returnItems->sum('refund_amount') ?: ((float) $item->price_at_purchase * $returnedQuantity));
            @endphp

            <section class="panel p-5">
                <div class="flex flex-col gap-5 sm:flex-row">
                    <div class="grid shrink-0 grid-cols-3 gap-2 sm:w-56">
                        @forelse ($product->images as $image)
                            <a href="{{ asset('storage/'.$image->image_path) }}" target="_blank" rel="noopener">
                                <img class="h-24 w-full rounded border border-mist object-contain sm:h-28" src="{{ asset('storage/'.$image->image_path) }}" alt="{{ $image->alt_text ?: $product->name }}">
                            </a>
                        @empty
                            <div class="col-span-3 flex h-28 items-center justify-center rounded bg-paper text-xs text-graphite/60">No product image</div>
                        @endforelse
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-[.16em] text-graphite/60">Returned product</p>
                        <h2 class="mt-1 text-xl">{{ $product->name }}</h2>
                        <p class="mt-2 text-sm text-graphite/70">{{ $product->description ?: 'No product description available.' }}</p>
                        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                            <div><dt class="text-graphite/60">SKU</dt><dd class="font-semibold">{{ $product->sku ?: '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Category</dt><dd class="font-semibold">{{ $product->category->name ?? '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Variant</dt><dd class="font-semibold">{{ $item->variant?->getDisplayName() ?: '—' }}</dd></div>
                            <div><dt class="text-graphite/60">Unit price</dt><dd class="font-semibold">₨{{ number_format($item->price_at_purchase, 2) }}</dd></div>
                            <div><dt class="text-graphite/60">Purchased quantity</dt><dd class="font-semibold">{{ $item->quantity }}</dd></div>
                            <div><dt class="text-graphite/60">Return quantity</dt><dd class="font-semibold text-vermilion">{{ $returnedQuantity }}</dd></div>
                            <div><dt class="text-graphite/60">Item total</dt><dd class="font-semibold">₨{{ number_format($item->total_amount, 2) }}</dd></div>
                            <div><dt class="text-graphite/60">Return refund</dt><dd class="font-semibold">₨{{ number_format($refundTotal, 2) }}</dd></div>
                        </dl>
                    </div>
                </div>
            </section>

            <section class="panel p-5">
                <h2 class="text-xl">Order and customer details</h2>
                <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-graphite/60">Order number</dt><dd class="font-semibold">{{ $order->order_number }}</dd></div>
                    <div><dt class="text-graphite/60">Order date</dt><dd class="font-semibold">{{ $order->created_at?->format('M d, Y H:i') }}</dd></div>
                    <div><dt class="text-graphite/60">Customer</dt><dd class="font-semibold">{{ $order->customer->name ?? '—' }}</dd></div>
                    <div><dt class="text-graphite/60">Customer email</dt><dd class="break-all font-semibold">{{ $order->customer->email ?? '—' }}</dd></div>
                    <div><dt class="text-graphite/60">Order status</dt><dd class="font-semibold capitalize">{{ str_replace('_', ' ', $order->status) }}</dd></div>
                    <div><dt class="text-graphite/60">Payment status</dt><dd class="font-semibold capitalize">{{ str_replace('_', ' ', $order->payment_status) }}</dd></div>
                    <div><dt class="text-graphite/60">Payment method</dt><dd class="font-semibold capitalize">{{ $order->payment?->payment_method ? str_replace('_', ' ', $order->payment->payment_method) : '—' }}</dd></div>
                    <div><dt class="text-graphite/60">Escrow status</dt><dd class="font-semibold capitalize">{{ $order->payment?->escrow_status ? str_replace('_', ' ', $order->payment->escrow_status) : '—' }}</dd></div>
                    <div><dt class="text-graphite/60">Vendor order status</dt><dd class="font-semibold capitalize">{{ $item->vendorOrder?->status ? str_replace('_', ' ', $item->vendorOrder->status) : '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-graphite/60">Delivery address</dt><dd class="mt-1 whitespace-pre-line font-semibold">{{ $order->address?->full_name }}{{ $order->address ? "\n".$order->address->getFullAddress() : '' }}{{ $order->address?->phone_number ? "\n".$order->address->phone_number : '' }}</dd></div>
                </dl>
            </section>

            <section class="panel p-5">
                <h2 class="text-xl">Other items in this order</h2>
                <div class="mt-4 divide-y divide-mist">
                    @foreach ($order->items as $orderItem)
                        <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded border border-mist bg-paper">
                                @if ($orderItem->product?->images?->first())
                                    <img class="h-full w-full object-contain" src="{{ asset('storage/'.$orderItem->product->images->first()->image_path) }}" alt="{{ $orderItem->product->name }}">
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold">{{ $orderItem->product->name ?? 'Product' }}</p>
                                <p class="text-xs text-graphite/60">Qty {{ $orderItem->quantity }} · {{ $orderItem->status }}</p>
                            </div>
                            <p class="text-sm font-semibold">₨{{ number_format($orderItem->total_amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex justify-between border-t border-mist pt-4 text-sm font-semibold">
                    <span>Order total</span>
                    <span>₨{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </section>

            <section class="panel p-5">
                <h2 class="text-xl">Customer request</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div>
                        <dt class="text-graphite/60">Reason</dt>
                        <dd class="font-semibold capitalize">{{ str_replace('_', ' ', $return->reason) }}</dd>
                    </div>
                    <div>
                        <dt class="text-graphite/60">Description</dt>
                        <dd class="mt-1 whitespace-pre-line">{{ $return->description }}</dd>
                    </div>
                </dl>

                @if ($return->photos->isNotEmpty())
                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach ($return->photos as $photo)
                            <a href="{{ asset('storage/'.$photo->path) }}" target="_blank" rel="noopener">
                                <img
                                    class="h-32 w-full rounded border border-mist object-contain"
                                    src="{{ asset('storage/'.$photo->path) }}"
                                    alt="Return evidence"
                                >
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>

            @if ($return->status === 'requested')
                <section class="panel p-5">
                    <h2 class="text-xl">Approve return</h2>
                    <form class="mt-4 space-y-4" method="POST" action="{{ route('vendor.returns.approve', $return) }}">
                        @csrf
                        <textarea class="field" name="return_shipping_address" rows="3" required placeholder="Return shipping address"></textarea>
                        <textarea class="field" name="return_instructions" rows="3" placeholder="Packing and return instructions"></textarea>
                        <input class="field" type="url" name="return_label_url" placeholder="Prepaid label URL (optional)">
                        <button class="btn-primary">Approve and send instructions</button>
                    </form>

                    <form class="mt-4 border-t border-mist pt-4" method="POST" action="{{ route('vendor.returns.reject', $return) }}">
                        @csrf
                        <textarea class="field" name="rejection_reason" rows="3" required placeholder="Reason for rejection"></textarea>
                        <button class="btn-secondary mt-3">Reject return</button>
                    </form>
                </section>
            @endif

            @if ($return->status === 'shipped_back')
                <section class="panel p-5">
                    <h2 class="text-xl">Inspect received item</h2>
                    <form class="mt-4 space-y-4" method="POST" action="{{ route('vendor.returns.receive', $return) }}">
                        @csrf
                        <textarea class="field" name="inspection_notes" rows="4" placeholder="Record the condition and inspection result"></textarea>
                        <button class="btn-primary">Mark received</button>
                    </form>
                </section>
            @endif

            @if ($return->status === 'received')
                <section class="panel p-5">
                    <h2 class="text-xl">Approve refund</h2>
                    <p class="mt-2 text-sm text-graphite/70">
                        Refund amount: ₨{{ number_format($refundTotal, 2) }}.
                        Held escrow is used first; otherwise a vendor account debit is recorded.
                    </p>
                    <form class="mt-4" method="POST" action="{{ route('vendor.returns.refund', $return) }}">
                        @csrf
                        <button class="btn-primary">Process refund</button>
                    </form>
                </section>
            @endif
        </main>

        <aside class="panel h-fit p-5">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 text-emerald-950">
                <p class="text-xs font-bold uppercase tracking-[.16em] text-emerald-700">Vendor refund amount</p>
                <p class="mt-2 text-sm text-emerald-800">Total to refund to the customer</p>
                <p class="mt-1 text-3xl font-bold tracking-tight">₨{{ number_format($refundTotal, 2) }}</p>
                <p class="mt-2 text-xs text-emerald-700">This amount is based on the returned quantity and purchase price.</p>
            </div>
            <h2 class="text-lg">Timeline</h2>
            <div class="mt-4 space-y-3 text-sm">
                @foreach ($return->statusHistory->sortBy('changed_at') as $event)
                    <div>
                        <p class="font-semibold capitalize text-forest">{{ str_replace('_', ' ', $event->new_status) }}</p>
                        <p class="text-xs text-graphite/60">{{ $event->changed_at?->format('M d, Y H:i') }}</p>
                    </div>
                @endforeach
            </div>
        </aside>
    </div>
</div>
@endsection

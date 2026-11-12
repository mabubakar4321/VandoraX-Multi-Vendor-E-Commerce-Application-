@extends('layouts.vendor')
@section('title', 'Order Details — Seller Centre')

@section('content')
@php
    $payment = $order->payment;
    $isPending = $vendorOrder->status === 'pending';
    $isConfirmed = $vendorOrder->status === 'confirmed';
    $isProcessing = $vendorOrder->status === 'processing';
    $isShipped = $vendorOrder->status === 'shipped';
    $paymentMethod = $payment?->payment_method;
    $paymentLabel = match ($paymentMethod) {
        'bank_transfer' => 'Bank Transfer',
        'wallet', 'jazzcash' => 'JazzCash',
        'card' => 'Card Payment',
        default => $payment ? ucfirst(str_replace('_', ' ', $paymentMethod ?? 'Unknown')) : 'Cash on Delivery',
    };
@endphp

<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('vendor.orders.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">← Back to orders</a>
            <p class="mt-5 text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Order details</p>
            <h1 class="mt-2 break-all text-3xl leading-tight">{{ $order->order_number }}</h1>
            <p class="mt-2 text-sm text-graphite/70">Sub-order: {{ $vendorOrder->vendor_order_number }} · Placed {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
        </div>
        <div class="flex flex-col items-start sm:items-end gap-2">
            <span class="inline-flex w-fit rounded-full @if($isPending) bg-yellow-100 text-yellow-800 @elseif($isConfirmed) bg-blue-100 text-blue-800 @elseif($isProcessing) bg-indigo-100 text-indigo-800 @elseif($isShipped) bg-purple-100 text-purple-800 @elseif($vendorOrder->status === 'delivered') bg-green-100 text-green-800 @elseif($vendorOrder->status === 'cancelled') bg-red-100 text-red-800 @else bg-mist text-forest @endif px-3 py-1.5 text-sm font-semibold capitalize">{{ $vendorOrder->status }}</span>
            <a href="{{ route('vendor.orders.print', $vendorOrder) }}" class="text-xs text-graphite/60 hover:text-forest transition">🖨 Print invoice</a>
        </div>
    </div>

    <!-- Status Action Buttons -->
    @if($isPending)
        <div class="border border-marigold/50 bg-marigold-100 p-5" style="border-radius: 2px;">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-forest">This sub-order is waiting for your decision.</p>
                    <p class="mt-1 text-sm text-graphite">Confirm to accept fulfillment, or reject if you cannot complete it.</p>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <form method="POST" action="{{ route('vendor.orders.status', $vendorOrder) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn-primary w-full px-5 py-2.5 sm:w-auto">Confirm order</button>
                    </form>
                    <form method="POST" action="{{ route('vendor.orders.status', $vendorOrder) }}">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" class="inline-flex w-full items-center justify-center bg-vermilion px-5 py-2.5 text-sm font-semibold text-white hover:bg-vermilion/90 sm:w-auto" style="border-radius: 2px;">Reject order</button>
                    </form>
                </div>
            </div>
        </div>
    @elseif($isConfirmed)
        <div class="border border-blue-200 bg-blue-50 p-5" style="border-radius: 2px;">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-forest">Order confirmed. Ready to start processing.</p>
                    <p class="mt-1 text-sm text-graphite">Move this sub-order to processing once packaging begins.</p>
                </div>
                <form method="POST" action="{{ route('vendor.orders.status', $vendorOrder) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="processing">
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-sm font-semibold text-white hover:bg-indigo-700" style="border-radius: 2px;">Start processing</button>
                </form>
            </div>
        </div>
    @elseif($isProcessing)
        <div class="border border-indigo-200 bg-indigo-50 p-5" style="border-radius: 2px;">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-forest">Order is being processed. Awaiting shipment.</p>
                    <p class="mt-1 text-sm text-graphite">Mark as shipped when the package is handed to the courier.</p>
                </div>
                <form method="POST" action="{{ route('vendor.orders.status', $vendorOrder) }}" class="flex flex-col gap-2 sm:flex-row">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="shipped">
                    <input type="text" name="tracking_number" placeholder="Tracking number" required class="border border-mist px-3 py-2 text-sm rounded w-full sm:w-48" style="border-radius: 2px;">
                    <input type="url" name="tracking_url" placeholder="Tracking URL" required class="border border-mist px-3 py-2 text-sm rounded w-full sm:w-56" style="border-radius: 2px;">
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-purple-600 text-sm font-semibold text-white hover:bg-purple-700 whitespace-nowrap" style="border-radius: 2px;">Mark shipped</button>
                </form>
            </div>
        </div>
    @elseif($isShipped)
        <div class="border border-purple-200 bg-purple-50 p-5" style="border-radius: 2px;">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="font-semibold text-forest">Order shipped. Awaiting delivery confirmation.</p>
                    @if($vendorOrder->tracking_number)
                        <p class="mt-1 text-sm text-graphite">Tracking: {{ $vendorOrder->tracking_number }}
                            @if($vendorOrder->tracking_url)
                                · <a href="{{ $vendorOrder->tracking_url }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:underline">Open tracking link</a>
                            @endif
                        </p>
                    @endif
                </div>
                <form method="POST" action="{{ route('vendor.orders.status', $vendorOrder) }}">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="delivered">
                    <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 bg-green-600 text-sm font-semibold text-white hover:bg-green-700" style="border-radius: 2px;">Mark delivered</button>
                </form>
            </div>
        </div>
    @endif

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 p-4 rounded" style="border-radius: 2px;">
            <p class="text-green-800 text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 p-4 rounded" style="border-radius: 2px;">
            <p class="text-red-800 text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="space-y-6 xl:col-span-2">
            <section class="overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
                <div class="border-b border-mist px-5 py-4 sm:px-6"><h2 class="text-xl">Products in this sub-order</h2><p class="mt-1 text-sm text-graphite/65">Items sold by your store.</p></div>
                <div class="divide-y divide-mist">
                    @forelse($items as $item)
                        <div class="flex flex-col gap-4 px-5 py-5 sm:flex-row sm:px-6">
                            <div class="h-20 w-20 shrink-0 overflow-hidden bg-paper" style="border-radius: 2px;">
                                @if($item->product?->mainImage())<img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}" alt="{{ $item->product->name }}" class="h-full w-full object-cover">@else<div class="flex h-full items-center justify-center text-xs text-graphite/50">No image</div>@endif
                            </div>
                            <div class="min-w-0 flex-1"><h3 class="break-words font-semibold text-forest">{{ $item->product->name ?? 'Product' }}</h3>@if($item->variant)<p class="mt-1 text-sm text-graphite">Variant: {{ $item->variant->variant_value }}</p>@endif<div class="mt-3 flex flex-wrap gap-x-6 gap-y-1 text-sm text-graphite"><span>Quantity: <strong class="text-forest">{{ $item->quantity }}</strong></span><span>Unit price: <strong class="text-forest">Rs {{ number_format($item->price_at_purchase) }}</strong></span></div></div>
                            <div class="shrink-0 text-left sm:text-right"><p class="font-display text-xl text-forest">Rs {{ number_format($item->total_amount) }}</p><p class="mt-1 text-xs text-graphite/60">Line total</p></div>
                        </div>
                    @empty
                        <p class="px-6 py-8 text-sm text-graphite/65">No products found for this vendor order.</p>
                    @endforelse
                </div>
                <div class="flex flex-wrap justify-between gap-3 border-t border-mist bg-paper px-5 py-4 font-semibold text-forest sm:px-6"><span>Your order subtotal</span><span>Rs {{ number_format($vendorOrder->subtotal) }}</span></div>
            </section>

            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Customer and delivery details</h2>
                <div class="mt-5 grid gap-6 md:grid-cols-2">
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Customer</p><p class="mt-2 font-semibold text-forest">{{ $order->customer->name ?? 'Customer' }}</p><p class="mt-1 break-all text-sm text-graphite">{{ $order->customer->email ?? 'No email' }}</p></div>
                    <div><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Phone</p><p class="mt-2 text-forest">{{ $order->address->phone_number ?? 'Not provided' }}</p></div>
                    <div class="md:col-span-2"><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Delivery address</p><p class="mt-2 text-forest">{{ $order->address->full_name ?? '' }}</p><p class="mt-1 text-sm leading-6 text-graphite">{{ $order->address->address_line_1 ?? '' }}@if($order->address?->address_line_2), {{ $order->address->address_line_2 }}@endif<br>{{ $order->address->city ?? '' }}, {{ $order->address->province ?? '' }} {{ $order->address->postal_code ?? '' }}<br>{{ $order->address->country ?? '' }}</p></div>
                    @if($order->address?->delivery_instructions)<div class="md:col-span-2 border-t border-mist pt-4"><p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Delivery instructions</p><p class="mt-2 text-sm text-graphite">{{ $order->address->delivery_instructions }}</p></div>@endif
                </div>
            </section>

            <!-- Status History -->
            @if($order->relationLoaded('statusHistory') && $order->statusHistory->isNotEmpty())
                <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                    <h2 class="text-xl">Status history</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($order->statusHistory as $history)
                            <div class="flex items-start gap-3">
                                <div class="w-2 h-2 rounded-full bg-forest mt-2 shrink-0"></div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-forest capitalize">{{ $history->new_status }}</p>
                                    @if($history->notes)<p class="text-xs text-graphite mt-0.5">{{ $history->notes }}</p>@endif
                                    <p class="text-xs text-graphite/50 mt-0.5">{{ $history->changed_at?->diffForHumans() }} by {{ $history->changer?->name ?? 'System' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        <aside class="space-y-6">
            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Payment details</h2>
                <div class="mt-5 space-y-4 text-sm">
                    <div class="flex flex-wrap justify-between gap-2"><span class="text-graphite/70">Method</span><strong class="text-forest">{{ $paymentLabel }}</strong></div>
                    <div class="flex flex-wrap justify-between gap-2"><span class="text-graphite/70">Order total</span><strong class="text-forest">Rs {{ number_format($order->total_amount) }}</strong></div>
                    <div class="flex flex-wrap items-center justify-between gap-2"><span class="text-graphite/70">Payment status</span><strong class="capitalize text-forest">{{ $order->payment_status }}</strong></div>
                    @if($payment && $paymentMethod === 'bank_transfer')
                        @foreach ([['Customer name', $payment->bank_payer_name], ['Payment app/platform', $payment->bank_payment_app], ['Payment source', $payment->bank_payment_source], ['Customer account number', $payment->bank_customer_account_number]] as [$label, $value])
                            <div class="border-t border-mist pt-3"><p class="text-xs text-graphite/60">{{ $label }}</p><p class="mt-1 break-words font-medium text-forest">{{ $value ?: 'Not provided' }}</p></div>
                        @endforeach
                    @endif
                    @if($payment && $payment->payment_slip_path)
                        <button type="button" onclick="openPaymentSlip()" class="btn-dark mt-2 w-full px-4 py-2.5">View payment slip</button>
                        @if($payment->status !== 'completed')
                            <form method="POST" action="{{ route('vendor.orders.payment.verify', $vendorOrder) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-primary w-full px-4 py-2.5 mt-2">Mark payment verified</button>
                            </form>
                        @else
                            <p class="text-center text-xs font-semibold text-forest-500 mt-1">Payment verified</p>
                        @endif
                    @endif
                </div>
            </section>

            <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
                <h2 class="text-xl">Order information</h2>
                <dl class="mt-5 space-y-4 text-sm">
                    <div><dt class="text-graphite/60">Vendor order number</dt><dd class="mt-1 break-all font-mono text-forest">{{ $vendorOrder->vendor_order_number }}</dd></div>
                    <div><dt class="text-graphite/60">Sub-order status</dt><dd class="mt-1 capitalize text-forest">{{ $vendorOrder->status }}</dd></div>
                    <div><dt class="text-graphite/60">Items in your order</dt><dd class="mt-1 text-forest">{{ $items->sum('quantity') }}</dd></div>
                    <div><dt class="text-graphite/60">Your payout</dt><dd class="mt-1 font-bold text-forest">Rs {{ number_format($vendorOrder->vendor_amount) }}</dd></div>
                </dl>
            </section>
        </aside>
    </div>
</div>

@if($payment && $payment->payment_slip_path)
    <div id="paymentSlipModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-forest-900/80 p-4" role="dialog" aria-modal="true" aria-label="Payment slip preview" onclick="closePaymentSlip(event)">
        <div class="relative flex max-h-[92vh] w-full max-w-4xl flex-col overflow-hidden bg-white p-3 shadow-2xl sm:p-5" style="border-radius: 2px;" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between gap-4 border-b border-mist pb-3">
                <h2 class="text-xl">Payment slip preview</h2>
                <button type="button" onclick="closePaymentSlip()" class="text-2xl leading-none text-graphite hover:text-forest" aria-label="Close payment slip">&times;</button>
            </div>
            <div class="mt-4 min-h-0 overflow-auto bg-paper p-2 text-center sm:p-4">
                <img src="{{ asset('storage/' . $payment->payment_slip_path) }}" alt="Payment slip" class="mx-auto max-h-[72vh] max-w-full object-contain">
            </div>
        </div>
    </div>
@endif

<script>
function openPaymentSlip() {
    const modal = document.getElementById('paymentSlipModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }
}

function closePaymentSlip(event) {
    if (event && event.target !== event.currentTarget) return;
    const modal = document.getElementById('paymentSlipModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }
}

document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closePaymentSlip();
});
</script>
@endsection

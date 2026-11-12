@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
@php
    $displayStatus = $order->effective_status;
    $hasCOD = !$order->payment;
    $isReleased = $order->payment?->escrow_status === 'released';
    $paymentMethodLabel = $order->payment
        ? (
            $order->payment->payment_gateway === 'stripe'
                ? 'Stripe'
                : ($order->payment->payment_method === 'wallet'
                ? 'JazzCash'
                : ($order->payment->payment_method === 'bank_transfer'
                    ? 'Bank Transfer'
                    : ucfirst(str_replace('_', ' ', $order->payment->payment_method))))
        )
        : 'Cash on Delivery';
    $refundedAmount = (float) ($order->payment?->refunded_amount ?? 0);
@endphp

<div class="orders-detail-page">
    <div class="orders-detail-shell">
        <header class="orders-detail-header">
            <div>
                <h1>Order Details</h1>
                <p>{{ $order->order_number }}</p>
            </div>
            <a href="{{ route('order.index') }}" class="back-link">← Back to Orders</a>
        </header>

        <div class="orders-detail-grid">
            <main class="orders-detail-main">
                <section class="detail-panel status-panel">
                    <h2>Order Status</h2>
                    <div class="status-stack">
                        <span class="status-pill status-{{ $displayStatus }}">
                            <span class="status-dot"></span>
                            Order: {{ ucfirst($displayStatus) }}
                        </span>
                        <span class="status-pill {{ $hasCOD ? 'status-cod' : ($order->payment_status === 'completed' ? 'status-paid' : 'status-pending') }}">
                            @if($hasCOD)
                                Payment: Not Paid (COD)
                            @elseif($order->payment_status === 'completed' && $isReleased)
                                Payment: Confirmed
                            @elseif($order->payment_status === 'completed')
                                Payment: Paid
                            @elseif($order->payment_status === 'pending')
                                Payment: Pending
                            @else
                                Payment: {{ ucfirst($order->payment_status) }}
                            @endif
                        </span>
                    </div>

                    @if($order->payment_status === 'completed' && $isReleased)
                        <p class="status-message success-message">Admin has confirmed your payment and released funds to the vendor. Your order is now confirmed.</p>
                    @elseif($order->payment_status === 'completed' && !$isReleased)
                        <p class="status-message info-message">Payment received — awaiting admin confirmation and release to the vendor.</p>
                    @endif

                    <p class="placed-date">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                </section>

                <section class="detail-panel address-panel">
                    <h2>Delivery Address</h2>
                    <div class="address-block">
                        <p class="address-name">{{ $order->address->full_name }}</p>
                        <p>{{ $order->address->address_line_1 }}</p>
                        @if($order->address->address_line_2)
                            <p>{{ $order->address->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->address->city }}, {{ $order->address->state_province }} {{ $order->address->postal_code }}</p>
                        <p>{{ $order->address->country }}</p>
                        <p class="address-phone">{{ $order->address->phone }}</p>
                    </div>

                    @if($order->address->delivery_instructions)
                        <div class="delivery-instructions">
                            <h3>Delivery Instructions</h3>
                            <p>{{ $order->address->delivery_instructions }}</p>
                        </div>
                    @endif
                </section>

                @foreach($itemsByVendor as $vendorId => $items)
                    @php
                        $vendorOrder = isset($order->vendorOrders)
                            ? $order->vendorOrders()->where('vendor_id', $vendorId)->first()
                            : null;
                        $vendorDeliveryDays = $items->first()->product?->delivery_time_days;
                        $estimatedDelivery = $vendorDeliveryDays
                            ? now()->addDays($vendorDeliveryDays)->format('M d, Y')
                            : null;
                    @endphp

                    <section class="vendor-order-card">
                        <header class="vendor-order-header">
                            <div>
                                <h3>{{ $items->first()->product->vendor->user->name }}</h3>
                                <p>Sub-order: <span>{{ $vendorOrder?->vendor_order_number ?? '—' }}</span></p>
                            </div>
                            @if($vendorOrder)
                                <div class="vendor-meta">
                                    @if($estimatedDelivery)
                                        <span class="delivery-meta">Est. delivery: {{ $estimatedDelivery }}</span>
                                    @endif
                                    <span class="status-pill status-{{ $vendorOrder->status }}">
                                        <span class="status-dot"></span>
                                        {{ ucfirst($vendorOrder->status) }}
                                    </span>
                                </div>
                            @endif
                        </header>

                        @if($vendorOrder && in_array($vendorOrder->status, ['shipped', 'delivered'], true))
                            <div class="mx-4 mt-4 border border-purple-200 bg-purple-50 p-4 sm:mx-6" style="border-radius: 2px;">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-sm font-bold uppercase tracking-[0.12em] text-purple-900">Your order was shipped by the vendor</p>
                                        <p class="mt-1 text-sm text-purple-800">Please wait for delivery. You can use the tracking details below to follow your shipment.</p>
                                    </div>
                                    @if($vendorOrder->shipped_at)
                                        <p class="shrink-0 text-xs font-medium text-purple-700">Shipped {{ $vendorOrder->shipped_at->format('M d, Y') }}</p>
                                    @endif
                                </div>
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div class="bg-white/70 p-3">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-700">Tracking number</p>
                                        <p class="mt-1 break-all font-semibold text-forest">{{ $vendorOrder->tracking_number }}</p>
                                    </div>
                                    <div class="bg-white/70 p-3">
                                        <p class="text-xs font-semibold uppercase tracking-wider text-purple-700">Tracking URL</p>
                                        @if($vendorOrder->tracking_url)
                                            <a href="{{ $vendorOrder->tracking_url }}" target="_blank" rel="noopener noreferrer" class="mt-1 block break-all font-semibold text-indigo-700 hover:underline">
                                                Track your shipment →
                                            </a>
                                        @else
                                            <p class="mt-1 text-sm text-purple-800">Tracking link not provided yet.</p>
                                        @endif
                                    </div>
                                </div>
                                <form class="customer-shipping-details-form mt-4 border-t border-purple-200 pt-4" onsubmit="openShippingDetailsPopup(event, this)">
                                    <p class="text-sm font-semibold text-purple-900">Add shipping details</p>
                                    <p class="mt-1 text-xs text-purple-800">Enter the shipping details you want to review in a popup.</p>
                                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                        <label class="block">
                                            <span class="text-xs font-semibold uppercase tracking-wider text-purple-700">Tracking number</span>
                                            <input name="customer_tracking_number" type="text" required class="mt-1 w-full border border-purple-200 bg-white px-3 py-2 text-sm text-forest" placeholder="Enter tracking number" style="border-radius: 2px;">
                                        </label>
                                        <label class="block">
                                            <span class="text-xs font-semibold uppercase tracking-wider text-purple-700">Tracking URL</span>
                                            <input name="customer_tracking_url" type="url" required class="mt-1 w-full border border-purple-200 bg-white px-3 py-2 text-sm text-forest" placeholder="https://..." style="border-radius: 2px;">
                                        </label>
                                    </div>
                                    <button type="submit" class="mt-3 inline-flex items-center justify-center bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800" style="border-radius: 2px;">Submit shipping details</button>
                                </form>
                            </div>
                        @endif

                        <div class="vendor-items-list">
                            @foreach($items as $item)
                                <article class="vendor-item-row">
                                    <div class="product-media">
                                        @if($item->product->mainImage())
                                            <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}" alt="{{ $item->product->name }}">
                                        @else
                                            <div class="product-media-fallback">No image</div>
                                        @endif
                                    </div>

                                    <div class="product-copy">
                                        <h4>
                                            <a href="{{ route('storefront.products.show', $item->product) }}">
                                                {{ $item->product->name }}
                                            </a>
                                        </h4>
                                        @if($item->variant)
                                            <p>{{ $item->variant->variant_value }}</p>
                                        @endif
                                        <div class="product-meta-row">
                                            <span>Unit Price:</span>
                                            <strong>₨{{ number_format($item->price_at_purchase) }}</strong>
                                        </div>
                                        <div class="product-meta-row">
                                            <span>Quantity:</span>
                                            <strong>{{ $item->quantity }}</strong>
                                        </div>
                                        <div class="product-meta-row">
                                            <span>Status:</span>
                                            <strong>{{ ucfirst($item->status) }}</strong>
                                        </div>
                                        @php
                                            $deliveredAt = $item->vendorOrder?->delivered_at
                                                ?? ($item->vendorOrder?->status === 'delivered' ? $item->vendorOrder?->updated_at : null);
                                            $returnOpen = $item->returns->first(fn ($r) => !in_array($r->status, ['rejected'], true));
                                            $returnEligible = $deliveredAt && !$item->return_requested && !$returnOpen;
                                        @endphp
                                        @if($returnOpen)
                                            <a href="{{ route('returns.show', $returnOpen) }}" class="return-link">View return: {{ ucfirst(str_replace('_', ' ', $returnOpen->status)) }}</a>
                                        @elseif($returnEligible)
                                            <a href="{{ route('returns.create', [$order, $item]) }}" class="return-link">Request return</a>
                                        @endif
                                    </div>

                                    <div class="product-total-box">
                                        <strong>₨{{ number_format($item->total_amount) }}</strong>
                                        <span>{{ $item->quantity }} × ₨{{ number_format($item->price_at_purchase) }}</span>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="vendor-subtotal">
                            <span>Vendor Subtotal</span>
                            <strong>₨{{ number_format($items->sum('total_amount')) }}</strong>
                        </div>
                    </section>
                @endforeach
            </main>

            <aside class="orders-detail-sidebar">
                <section class="detail-panel summary-panel">
                    <h2>Order Summary</h2>
                    <div class="summary-list">
                        <div>
                            <span>Subtotal</span>
                            <strong>₨{{ number_format($order->total_amount) }}</strong>
                        </div>
                        <div>
                            <span>Shipping</span>
                            <strong class="text-success">Free</strong>
                        </div>
                        <div>
                            <span>Tax</span>
                            <strong>Included</strong>
                        </div>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <strong>₨{{ number_format($order->total_amount) }}</strong>
                    </div>
                </section>

                <section class="detail-panel info-panel">
                    <h2>Order Information</h2>
                    <dl class="info-list">
                        <div>
                            <dt>Order Number</dt>
                            <dd>{{ $order->order_number }}</dd>
                        </div>
                        <div>
                            <dt>Order Date</dt>
                            <dd>{{ $order->created_at->format('F d, Y g:i A') }}</dd>
                        </div>
                        <div>
                            <dt>Total Items</dt>
                            <dd>{{ $order->items()->count() }}</dd>
                        </div>
                        <div>
                            <dt>Vendors</dt>
                            <dd>{{ $order->vendor_count }}</dd>
                        </div>
                        <div>
                            <dt>Payment Method</dt>
                            <dd>{{ $paymentMethodLabel }}</dd>
                        </div>
                        @if ($order->payment && $refundedAmount > 0)
                            <div>
                                <dt>Refunded Amount</dt>
                                <dd class="refund-value">₨{{ number_format($refundedAmount, 2) }}</dd>
                            </div>
                            <div>
                                <dt>Refund Status</dt>
                                <dd class="refund-value">Refunded to {{ $paymentMethodLabel }}</dd>
                            </div>
                        @endif
                    </dl>
                </section>

                <section class="detail-panel actions-panel">
                    <h2>Actions</h2>
                    <div class="actions-stack">
                        @if($order->payment && $order->payment->payment_gateway === 'stripe' && in_array($order->payment_status, ['pending', 'failed']))
                            <button type="button" class="action-btn action-btn-primary" data-retry-payment onclick="retryCardPayment(event)">
                                {{ $order->payment_status === 'failed' ? 'Retry Card Payment' : 'Complete Card Payment' }}
                            </button>
                        @endif

                        @if($order->status === 'pending' && $order->canCancel())
                            <form method="POST" action="{{ route('order.cancel', $order) }}" onsubmit="return confirm('Cancel this order? Any payment will be refunded.');">
                                @csrf
                                <button type="submit" class="action-btn action-btn-secondary danger-btn">Cancel Order</button>
                            </form>
                        @endif

                        <button type="button" class="action-btn action-btn-secondary" onclick="printOrder()">Print Order</button>
                        <a href="{{ route('storefront.products.index') }}" class="action-btn action-btn-primary">Continue Shopping</a>
                        <a href="{{ route('invoice.view', $order) }}" target="_blank" class="action-btn action-btn-secondary">View Invoice</a>
                        <a href="{{ route('invoice.download', $order) }}" class="action-btn action-btn-secondary">Download Invoice PDF</a>
                        <a href="{{ route('payment.history') }}" class="action-btn action-btn-secondary">Payment History</a>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</div>

<div id="shipping-details-popup" class="fixed inset-0 z-50 hidden items-center justify-center bg-forest/60 px-4 py-6" role="dialog" aria-modal="true" aria-labelledby="shipping-details-popup-title">
    <div class="w-full max-w-lg bg-white p-6 shadow-2xl" style="border-radius: 4px;">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-purple-700">Shipping details</p>
                <h2 id="shipping-details-popup-title" class="mt-1 text-xl font-bold text-forest">Your shipping detail</h2>
            </div>
            <button type="button" onclick="closeShippingDetailsPopup()" class="text-2xl leading-none text-graphite/60 hover:text-forest" aria-label="Close popup">&times;</button>
        </div>
        <div class="mt-5 grid gap-3 sm:grid-cols-2">
            <div class="border border-mist bg-paper p-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Tracking number</p>
                <p id="shipping-popup-number" class="mt-1 break-all font-semibold text-forest"></p>
            </div>
            <div class="border border-mist bg-paper p-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-graphite/60">Tracking URL</p>
                <a id="shipping-popup-url" href="#" target="_blank" rel="noopener noreferrer" class="mt-1 block break-all font-semibold text-indigo-700 hover:underline">Open tracking link →</a>
            </div>
        </div>
        <label class="mt-4 block">
            <span class="text-sm font-semibold text-forest">Description</span>
            <textarea id="shipping-popup-description" rows="4" class="mt-1 w-full border border-mist px-3 py-2 text-sm text-forest" placeholder="Add a description about these shipping details..." style="border-radius: 2px;"></textarea>
        </label>
        <p class="mt-3 text-xs text-graphite/60">This popup is for review only. These details will not be saved to the order.</p>
        <div class="mt-5 flex justify-end">
            <button type="button" onclick="closeShippingDetailsPopup()" class="bg-forest px-5 py-2.5 text-sm font-semibold text-white hover:bg-forest/90" style="border-radius: 2px;">Close</button>
        </div>
    </div>
</div>

<style>
    .orders-detail-page {
        background: #F5F7F4;
        min-height: 100vh;
        padding: 28px 0 56px;
        color: #4A5551;
    }

    .orders-detail-shell {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
    }

    .orders-detail-header {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
        background: rgba(255,255,255,0.78);
        border: 1px solid #E4EAE5;
        border-radius: 6px;
        padding: 28px 24px;
        margin-bottom: 24px;
        box-shadow: 0 10px 24px rgba(14,42,34,0.04);
    }

    .orders-detail-header h1 {
        margin: 0;
        color: #0E2A22;
        font-size: clamp(2rem, 2.7vw, 2.9rem);
        line-height: 1.1;
        font-family: "Bricolage Grotesque", sans-serif;
    }

    .orders-detail-header p {
        margin: 10px 0 0;
        color: #4A5551;
        font-size: 15px;
        font-weight: 600;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        color: #1E6F5C;
        text-decoration: none;
        font-size: 14px;
        font-weight: 700;
        border: 1px solid #D6E3DD;
        background: #F8FBF9;
        padding: 10px 12px;
        border-radius: 6px;
        transition: all .2s ease;
    }

    .back-link:hover {
        border-color: #1E6F5C;
        background: #EEF6F3;
    }

    .orders-detail-grid {
        display: grid;
        grid-template-columns: minmax(0, 2fr) minmax(280px, 0.9fr);
        gap: 24px;
    }

    .orders-detail-main,
    .orders-detail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .detail-panel,
    .vendor-order-card {
        background: #FFFFFF;
        border: 1px solid #E4EAE5;
        border-radius: 8px;
        box-shadow: 0 10px 24px rgba(14,42,34,0.03);
    }

    .detail-panel {
        padding: 22px 20px;
    }

    .detail-panel h2,
    .vendor-order-header h3,
    .product-copy h4 {
        margin: 0;
        color: #0E2A22;
        font-family: "Bricolage Grotesque", sans-serif;
    }

    .detail-panel h2 {
        font-size: 1.05rem;
        margin-bottom: 18px;
    }

    .status-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.01em;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-pending { color: #866300; background: #FDF3D4; }
    .refund-value { color: #1E6F5C; font-weight: 700; }
    .status-confirmed,
    .status-processing { color: #175C4C; background: #DCEEE7; }
    .status-shipped { color: #5A4A8A; background: #EDE8FA; }
    .status-delivered,
    .status-paid,
    .status-cancelled {
        color: #176147;
        background: #DDF2E8;
    }
    .status-cancelled { color: #9B342A; background: #F7E4E1; }
    .status-cod { color: #8A5813; background: #FFF0D7; }

    .status-message {
        margin: 16px 0 0;
        font-size: 13px;
        line-height: 1.6;
    }

    .success-message {
        color: #1B704A;
        font-weight: 600;
    }

    .info-message {
        color: #1E6F5C;
    }

    .placed-date {
        margin: 16px 0 0;
        font-size: 13px;
        color: #6B7671;
    }

    .address-block {
        color: #4A5551;
        line-height: 1.8;
        font-size: 14px;
    }

    .address-name {
        margin: 0 0 4px;
        font-weight: 700;
        color: #0E2A22;
    }

    .address-phone {
        margin-top: 8px;
        color: #0E2A22;
        font-weight: 700;
    }

    .delivery-instructions {
        margin-top: 18px;
        border-top: 1px solid #E4EAE5;
        padding-top: 18px;
    }

    .delivery-instructions h3 {
        margin: 0 0 8px;
        font-size: 0.9rem;
        color: #0E2A22;
        font-family: "Bricolage Grotesque", sans-serif;
    }

    .delivery-instructions p {
        margin: 0;
        color: #4A5551;
        line-height: 1.7;
        font-size: 14px;
    }

    .vendor-order-card {
        overflow: hidden;
    }

    .vendor-order-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        background: #F7FAF8;
        padding: 18px 20px;
        border-bottom: 1px solid #E4EAE5;
    }

    .vendor-order-header h3 {
        font-size: 1.06rem;
        margin-bottom: 6px;
    }

    .vendor-order-header p {
        margin: 0;
        font-size: 12px;
        color: #6B7671;
    }

    .vendor-order-header p span {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        color: #0E2A22;
    }

    .vendor-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .delivery-meta {
        color: #6B7671;
        font-size: 12px;
    }

    .vendor-items-list {
        padding: 0 0 0;
    }

    .vendor-item-row {
        display: grid;
        grid-template-columns: 80px minmax(0, 1fr) auto;
        gap: 16px;
        padding: 18px 20px;
        border-bottom: 1px solid #E4EAE5;
    }

    .product-media {
        width: 80px;
        height: 80px;
        border-radius: 8px;
        overflow: hidden;
        background: #F5F7F4;
        border: 1px solid #E4EAE5;
    }

    .product-media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .product-media-fallback {
        width: 100%;
        height: 100%;
        display: grid;
        place-items: center;
        font-size: 10px;
        font-weight: 700;
        color: #1E6F5C;
        background: linear-gradient(135deg, #EAF4EF, #F5F7F4);
    }

    .product-copy {
        min-width: 0;
    }

    .product-copy h4 {
        font-size: 1.02rem;
        margin-bottom: 6px;
    }

    .product-copy h4 a {
        color: #0E2A22;
        text-decoration: none;
    }

    .product-copy h4 a:hover {
        color: #1E6F5C;
    }

    .product-copy p {
        margin: 0;
        color: #6B7671;
        font-size: 13px;
    }

    .product-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 9px;
        color: #4A5551;
        font-size: 13px;
    }

    .product-meta-row strong {
        color: #0E2A22;
        font-weight: 700;
    }

    .product-total-box {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        text-align: right;
        gap: 6px;
        min-width: 120px;
    }

    .product-total-box strong {
        color: #0E2A22;
        font-size: 1.05rem;
        font-family: "Bricolage Grotesque", sans-serif;
    }

    .product-total-box span {
        color: #6B7671;
        font-size: 12px;
    }

    .vendor-subtotal {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        background: #F7FAF8;
        font-weight: 700;
        color: #0E2A22;
    }

    .summary-panel h2 {
        margin-bottom: 14px;
    }

    .summary-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        color: #4A5551;
        font-size: 14px;
    }

    .summary-list > div,
    .summary-total {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .summary-total {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid #E4EAE5;
        font-size: 1.05rem;
        color: #0E2A22;
    }

    .text-success {
        color: #1E6F5C;
    }

    .info-panel h2 {
        margin-bottom: 12px;
    }

    .info-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin: 0;
    }

    .info-list div {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-list dt {
        color: #6B7671;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .info-list dd {
        margin: 0;
        font-size: 14px;
        color: #0E2A22;
    }

    .actions-panel h2 {
        margin-bottom: 12px;
    }

    .actions-stack {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        min-height: 44px;
        padding: 11px 14px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all .2s ease;
    }

    .action-btn-primary {
        background: #0E2A22;
        color: #fff;
    }

    .action-btn-primary:hover {
        background: #14392E;
    }

    .action-btn-secondary {
        background: #fff;
        color: #0E2A22;
        border-color: #C8D6CE;
    }

    .action-btn-secondary:hover {
        border-color: #1E6F5C;
        background: #F8FBF9;
    }

    .danger-btn {
        color: #9B342A;
    }

    .actions-stack form {
        margin: 0;
    }

    @media (max-width: 980px) {
        .orders-detail-grid {
            grid-template-columns: 1fr;
        }

        .orders-detail-sidebar {
            order: 2;
        }

        .orders-detail-main {
            order: 1;
        }
    }

    @media (max-width: 640px) {
        .orders-detail-page {
            padding-top: 18px;
        }

        .orders-detail-header {
            display: block;
            padding: 22px 18px;
        }

        .back-link {
            margin-top: 14px;
            width: fit-content;
        }

        .detail-panel,
        .vendor-order-header,
        .vendor-item-row,
        .vendor-subtotal {
            padding-left: 16px;
            padding-right: 16px;
        }

        .vendor-item-row {
            grid-template-columns: 1fr;
        }

        .product-media {
            width: 72px;
            height: 72px;
        }

        .product-total-box {
            align-items: flex-start;
            text-align: left;
        }

        .vendor-order-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .vendor-meta {
            justify-content: flex-start;
        }
    }
</style>

<script>
    function printOrder() {
        window.print();
    }

    function openShippingDetailsPopup(event, form) {
        event.preventDefault();

        const number = form.querySelector('[name="customer_tracking_number"]').value.trim();
        const url = form.querySelector('[name="customer_tracking_url"]').value.trim();
        const popup = document.getElementById('shipping-details-popup');
        const popupNumber = document.getElementById('shipping-popup-number');
        const popupUrl = document.getElementById('shipping-popup-url');

        popupNumber.textContent = number;
        popupUrl.href = url;
        popup.classList.remove('hidden');
        popup.classList.add('flex');
        document.getElementById('shipping-popup-description').focus();
    }

    function closeShippingDetailsPopup() {
        const popup = document.getElementById('shipping-details-popup');
        popup.classList.add('hidden');
        popup.classList.remove('flex');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeShippingDetailsPopup();
        }
    });

    async function retryCardPayment(event) {
        const button = event && event.currentTarget ? event.currentTarget : document.querySelector('[data-retry-payment]');
        if (!button) return;

        if (!confirm('Retry / complete card payment for this order?')) return;

        const orderId = @json($order->id);
        button.disabled = true;
        button.style.opacity = '0.7';

        try {
            const res = await fetch('{{ url('payment/stripe/retry') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ order_id: orderId })
            });

            const data = await res.json();

            if (data.success) {
                if (typeof Stripe === 'undefined') {
                    await new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.src = 'https://js.stripe.com/v3/';
                        script.onload = resolve;
                        script.onerror = reject;
                        document.head.appendChild(script);
                    });
                }

                const stripe = Stripe(data.publishable_key);
                const elements = stripe.elements({ clientSecret: data.client_secret });
                const mountId = 'stripe-order-retry-mount';
                let mount = document.getElementById(mountId);

                if (!mount) {
                    mount = document.createElement('div');
                    mount.id = mountId;
                    mount.style.minHeight = '120px';
                    mount.style.marginTop = '12px';
                    mount.style.padding = '12px';
                    mount.style.border = '1px solid #E4EAE5';
                    mount.style.borderRadius = '8px';
                    button.parentNode.insertBefore(mount, button.nextSibling);
                }

                const paymentElement = elements.create('payment');
                paymentElement.mount('#' + mountId);
                paymentElement.on('change', function (e) {
                    if (e.error) {
                        alert(e.error.message);
                    }
                });

                button.textContent = 'Pay Now';
                button.onclick = async function () {
                    button.disabled = true;
                    button.textContent = 'Processing...';
                    const result = await stripe.confirmPayment({ elements, redirect: 'if_required' });

                    if (result.error) {
                        alert(result.error.message || 'Payment failed. Please try again.');
                        button.disabled = false;
                        button.textContent = 'Retry Card Payment';
                    } else {
                        alert('Payment successful! Redirecting...');
                        setTimeout(function () {
                            window.location.href = data.redirect;
                        }, 1500);
                    }
                };
            } else {
                alert(data.message || 'Could not start card payment.');
                button.disabled = false;
                button.style.opacity = '1';
            }
        } catch (error) {
            alert('Network error. Please try again.');
            button.disabled = false;
            button.style.opacity = '1';
        }
    }
</script>
@endsection

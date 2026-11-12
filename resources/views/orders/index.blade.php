@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="orders-page">
    <section class="orders-hero">
        <div>
            <p class="orders-eyebrow">Your VandoraX account</p>
            <h1>My orders</h1>
            <p class="orders-intro">Track your purchases, review payment status, and quickly revisit every order.</p>
        </div>
        <a href="{{ route('storefront.products.index') }}" class="orders-shop-link">
            <span>Continue shopping</span>
            <span aria-hidden="true">→</span>
        </a>
    </section>

    @if($orders->count() === 0)
        <section class="orders-empty panel">
            <div class="orders-empty-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 5h2l1.5 10.5a2 2 0 0 0 2 1.7h7.8a2 2 0 0 0 2-1.6L20 8H6"/>
                    <path d="M10 20h.01M17 20h.01M9 8l2 2 4-4"/>
                </svg>
            </div>
            <p class="orders-eyebrow">Nothing here yet</p>
            <h2>Your next favourite is waiting</h2>
            <p>Explore products from independent vendors and place your first order.</p>
            <a href="{{ route('storefront.products.index') }}" class="orders-primary-action">Start shopping</a>
        </section>
    @else
        <div class="orders-summary">
            <div>
                <strong>{{ $orders->total() }}</strong>
                <span>{{ Str::plural('order', $orders->total()) }} in your account</span>
            </div>
            <span class="orders-page-label">Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }}</span>
        </div>

        <div class="orders-list">
            @foreach($orders as $order)
                @php
                    $displayStatus = $order->effective_status;
                    $items = $order->items;
                    $previewItems = $items->take(3);
                    $hasCOD = !$order->payment;
                    $isReleased = $order->payment?->escrow_status === 'released';
                @endphp
                <article class="order-card">
                    <div class="order-card-top">
                        <div class="order-heading">
                            <span class="order-number">{{ $order->order_number }}</span>
                            <h2>Order placed {{ $order->created_at->format('M d, Y') }}</h2>
                            <p>{{ $items->count() }} {{ Str::plural('item', $items->count()) }} · {{ $order->created_at->format('g:i A') }}</p>
                        </div>
                        <div class="order-total">
                            <span>Total</span>
                            <strong>₨{{ number_format($order->total_amount) }}</strong>
                        </div>
                    </div>

                    <div class="order-statuses">
                        <span class="status-pill status-{{ $displayStatus }}">
                            <span class="status-dot"></span>{{ ucfirst($displayStatus) }}
                        </span>
                        <span class="status-pill {{ $hasCOD ? 'status-cod' : ($order->payment_status === 'completed' ? 'status-paid' : 'status-pending') }}">
                            @if($hasCOD)
                                COD payment
                            @elseif($order->payment_status === 'completed' && $isReleased)
                                Payment confirmed
                            @elseif($order->payment_status === 'completed')
                                Payment received
                            @elseif($order->payment_status === 'pending')
                                Payment pending
                            @else
                                {{ ucfirst($order->payment_status) }}
                            @endif
                        </span>
                    </div>

                    <div class="order-items" aria-label="Order items">
                        @foreach($previewItems as $item)
                            @php $product = $item->product; @endphp
                            <div class="order-item">
                                @if($product?->mainImage())
                                    <img src="{{ asset('storage/' . $product->mainImage()->image_path) }}" alt="{{ $product->name }}">
                                @else
                                    <div class="order-item-placeholder" aria-hidden="true">
                                        <span>VX</span>
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $product?->name ?? 'Product unavailable' }}</strong>
                                    <span>Quantity {{ $item->quantity }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if($items->count() > 3)
                            <span class="more-items">+{{ $items->count() - 3 }} more</span>
                        @endif
                    </div>

                    <div class="order-card-footer">
                        <a href="{{ route('order.show', $order) }}" class="orders-primary-action">View order details <span aria-hidden="true">→</span></a>
                        @if($displayStatus === 'pending')
                            <div class="order-actions">
                                <a href="{{ route('order.edit', $order) }}" class="orders-secondary-action">Edit order</a>
                                @if($order->canCancel())
                                    <form method="POST" action="{{ route('order.cancel', $order) }}" onsubmit="return confirm('Cancel this order? Any payment will be refunded.');">
                                        @csrf
                                        <button type="submit" class="orders-secondary-action orders-danger-action">Cancel</button>
                                    </form>
                                @endif
                                <button type="button" onclick="deleteOrder({{ $order->id }})" class="orders-secondary-action orders-danger-action">Delete</button>
                            </div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>

        <nav class="orders-pagination" aria-label="Orders pagination">
            {{ $orders->links() }}
        </nav>
    @endif
</div>

<div id="deleteModal" class="delete-modal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="delete-dialog">
        <div class="delete-icon" aria-hidden="true">!</div>
        <p class="orders-eyebrow">Permanent action</p>
        <h2 id="deleteModalTitle">Delete this order?</h2>
        <p>This order and its items will be permanently removed from your order history. This cannot be undone.</p>
        <div class="delete-actions">
            <button type="button" onclick="closeDeleteModal()" class="orders-secondary-action">Keep order</button>
            <button type="button" id="confirmDeleteBtn" onclick="confirmDelete()" class="orders-danger-button">Delete order</button>
        </div>
    </div>
</div>

<style>
    .orders-page { color: #4A5551; }
    .orders-hero { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; padding:8px 0 32px; border-bottom:1px solid #E4EAE5; }
    .orders-eyebrow { margin:0 0 8px; color:#1E6F5C; font-size:11px; font-weight:700; letter-spacing:.14em; text-transform:uppercase; }
    .orders-hero h1 { margin:0; color:#0E2A22; font-size:clamp(2rem,4vw,3.2rem); line-height:1; }
    .orders-intro { max-width:540px; margin:14px 0 0; color:#6B7671; font-size:15px; line-height:1.6; }
    .orders-shop-link { display:inline-flex; align-items:center; gap:12px; flex:none; color:#0E2A22; font-size:14px; font-weight:700; text-decoration:none; border-bottom:2px solid #F2B705; padding:10px 0; }
    .orders-shop-link:hover { color:#1E6F5C; }
    .orders-summary { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:24px 0 16px; }
    .orders-summary strong { display:block; color:#0E2A22; font-family:"Bricolage Grotesque",sans-serif; font-size:22px; }
    .orders-summary span { color:#6B7671; font-size:13px; }
    .orders-page-label { text-align:right; }
    .orders-list { display:grid; gap:16px; }
    .order-card, .orders-empty { border:1px solid #E4EAE5; background:#fff; border-radius:4px; box-shadow:0 8px 24px rgba(14,42,34,.05); }
    .order-card { padding:24px; }
    .order-card-top { display:flex; justify-content:space-between; gap:20px; }
    .order-number { display:inline-block; margin-bottom:9px; color:#1E6F5C; font-size:12px; font-weight:700; letter-spacing:.08em; }
    .order-heading h2 { margin:0; color:#0E2A22; font-size:18px; }
    .order-heading p { margin:6px 0 0; color:#7A8580; font-size:13px; }
    .order-total { flex:none; text-align:right; }
    .order-total span { display:block; color:#7A8580; font-size:12px; }
    .order-total strong { display:block; margin-top:5px; color:#0E2A22; font-size:22px; }
    .order-statuses { display:flex; flex-wrap:wrap; gap:8px; margin:20px 0; }
    .status-pill { display:inline-flex; align-items:center; gap:7px; border-radius:999px; padding:7px 11px; font-size:12px; font-weight:700; }
    .status-dot { width:7px; height:7px; border-radius:50%; background:currentColor; }
    .status-pending { color:#866300; background:#FDF3D4; }
    .status-confirmed, .status-processing { color:#175C4C; background:#DCEEE7; }
    .status-shipped { color:#5A4A8A; background:#EDE8FA; }
    .status-delivered, .status-paid { color:#176147; background:#DDF2E8; }
    .status-cancelled { color:#9B342A; background:#F7E4E1; }
    .status-cod { color:#8A5813; background:#FFF0D7; }
    .order-items { display:flex; flex-wrap:wrap; gap:12px; padding:16px 0; border-top:1px solid #E4EAE5; border-bottom:1px solid #E4EAE5; }
    .order-item { display:flex; align-items:center; gap:10px; min-width:190px; flex:1 1 190px; }
    .order-item img, .order-item-placeholder { width:52px; height:52px; flex:none; border-radius:3px; object-fit:cover; background:#F5F7F4; }
    .order-item-placeholder { display:grid; place-items:center; color:#1E6F5C; font-size:11px; font-weight:800; }
    .order-item strong, .order-item span { display:block; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .order-item strong { color:#0E2A22; font-size:13px; }
    .order-item span { margin-top:4px; color:#7A8580; font-size:12px; }
    .more-items { align-self:center; color:#1E6F5C; font-size:12px; font-weight:700; }
    .order-card-footer { display:flex; align-items:center; justify-content:space-between; gap:16px; padding-top:20px; }
    .orders-primary-action, .orders-secondary-action, .orders-danger-button { display:inline-flex; align-items:center; justify-content:center; gap:8px; border-radius:2px; font-size:13px; font-weight:700; text-decoration:none; cursor:pointer; transition:all .2s ease; }
    .orders-primary-action { padding:11px 15px; color:#0E2A22; background:#F2B705; border:1px solid #F2B705; }
    .orders-primary-action:hover { background:#D9A404; border-color:#D9A404; }
    .order-actions { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:8px; }
    .orders-secondary-action { padding:10px 13px; color:#0E2A22; background:#fff; border:1px solid #C8D6CE; }
    .orders-secondary-action:hover { border-color:#1E6F5C; background:#F5F7F4; }
    .orders-danger-action { color:#9B342A; }
    .orders-pagination { padding-top:28px; }
    .orders-pagination nav { display:flex; justify-content:center; }
    .orders-pagination svg { width:17px; height:17px; }
    .orders-empty { padding:64px 24px; text-align:center; }
    .orders-empty-icon { display:grid; place-items:center; width:64px; height:64px; margin:0 auto 20px; border-radius:50%; color:#1E6F5C; background:#DCEEE7; }
    .orders-empty-icon svg { width:30px; height:30px; stroke-width:1.5; }
    .orders-empty h2 { margin:0; color:#0E2A22; font-size:25px; }
    .orders-empty > p:not(.orders-eyebrow) { margin:10px auto 24px; color:#6B7671; font-size:14px; }
    .delete-modal { position:fixed; inset:0; z-index:50; display:grid; place-items:center; padding:20px; visibility:hidden; opacity:0; background:rgba(8,24,19,.58); backdrop-filter:blur(5px); transition:opacity .2s ease, visibility .2s ease; }
    .delete-modal.show { visibility:visible; opacity:1; }
    .delete-dialog { width:min(100%,420px); padding:30px; text-align:center; border:1px solid #E4EAE5; border-radius:5px; background:#fff; box-shadow:0 24px 70px rgba(8,24,19,.25); }
    .delete-icon { display:grid; place-items:center; width:44px; height:44px; margin:0 auto 18px; border-radius:50%; color:#9B342A; background:#F7E4E1; font-size:24px; font-weight:800; }
    .delete-dialog h2 { margin:0; color:#0E2A22; font-size:24px; }
    .delete-dialog p:not(.orders-eyebrow) { margin:10px 0 24px; color:#6B7671; font-size:14px; line-height:1.6; }
    .delete-actions { display:flex; gap:10px; }
    .delete-actions > * { flex:1; }
    .orders-danger-button { padding:11px 13px; color:#fff; background:#B23A2E; border:1px solid #B23A2E; }
    .orders-danger-button:hover { background:#922F26; }
    @media (max-width:640px) {
        .orders-hero { display:block; padding-bottom:24px; }
        .orders-shop-link { margin-top:20px; }
        .orders-summary { align-items:flex-start; flex-direction:column; padding-top:20px; }
        .orders-page-label { text-align:left; }
        .order-card { padding:18px; }
        .order-card-top { display:block; }
        .order-total { margin-top:16px; text-align:left; }
        .order-total strong { font-size:20px; }
        .order-item { min-width:100%; }
        .order-card-footer { align-items:stretch; flex-direction:column; }
        .orders-primary-action { width:100%; }
        .order-actions { justify-content:stretch; }
        .order-actions > *, .order-actions form { flex:1 1 0; }
        .order-actions .orders-secondary-action { width:100%; }
        .delete-dialog { padding:24px 20px; }
    }
</style>

<script>
let orderToDelete = null;

function deleteOrder(orderId) {
    orderToDelete = orderId;
    const modal = document.getElementById('deleteModal');
    modal.classList.add('show');
    modal.setAttribute('aria-hidden', 'false');
    document.getElementById('confirmDeleteBtn').focus();
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.remove('show');
    modal.setAttribute('aria-hidden', 'true');
    orderToDelete = null;
    const button = document.getElementById('confirmDeleteBtn');
    button.disabled = false;
    button.textContent = 'Delete order';
}

function confirmDelete() {
    if (!orderToDelete) return;

    const button = document.getElementById('confirmDeleteBtn');
    button.disabled = true;
    button.textContent = 'Deleting...';

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/orders/${orderToDelete}`;
    form.innerHTML = `<input type="hidden" name="_token" value="{{ csrf_token() }}"><input type="hidden" name="_method" value="DELETE">`;
    document.body.appendChild(form);
    form.submit();
}

document.addEventListener('click', (event) => {
    if (event.target === document.getElementById('deleteModal')) closeDeleteModal();
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeDeleteModal();
});
</script>
@endsection

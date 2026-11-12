@extends('layouts.storefront')

@section('title', 'Shopping Cart - VandoraX')

@push('styles')
<style>
    .container {
        max-width: 1400px;
        /* margin: 0 auto; */
        padding: 40px 24px;
    }

    /* Centers the cart page in the viewport regardless of the layout wrapper */
    .cart-page {
        box-sizing: border-box;
        width: 100%;
        max-width: 1380px;
        padding-top: 20px;
        padding-bottom: 60px;
    }

    .page-title {
        font-family: 'Playfair Display', serif;
        font-size: clamp(32px, 6vw, 48px);
        font-weight: 900;
        margin-bottom: 40px;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -1px;
    }

    .cart-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 32px;
    }

    /* ===== EMPTY CART ===== */
    .empty-cart {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 24px;
        background: linear-gradient(135deg, rgba(22, 33, 62, 0.5) 0%, rgba(26, 45, 92, 0.3) 100%);
        border-radius: 16px;
        border: 1px solid var(--border-light);
    }

    .empty-cart h2 {
        font-family: 'Playfair Display', serif;
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 16px;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .empty-cart p {
        font-size: 16px;
        color: var(--text-secondary);
        margin-bottom: 32px;
        font-weight: 300;
    }

    .btn-primary {
        display: inline-block;
        padding: 14px 42px;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        border: none;
        color: var(--primary);
        text-decoration: none;
        font-size: 15px;
        font-weight: 700;
        letter-spacing: 0.5px;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 8px 25px rgba(255, 165, 0, 0.35);
    }

    .btn-primary:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 35px rgba(255, 165, 0, 0.45);
    }

    /* ===== CART ITEMS ===== */
    .cart-items {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .vendor-section {
        background: var(--primary-light);
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-light);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    }

    .vendor-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .vendor-name {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }

    .vendor-subtotal {
        font-size: 16px;
        font-weight: 700;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .cart-item {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
        display: grid;
        grid-template-columns: 100px 1fr 120px;
        gap: 20px;
        align-items: start;
        transition: background 0.3s ease;
    }

    .cart-item:hover {
        background: rgba(255, 165, 0, 0.03);
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .item-image {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        overflow: hidden;
        background: linear-gradient(135deg, var(--border-light) 0%, var(--border-medium) 100%);
    }

    .item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        font-weight: 700;
        font-size: 15px;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .item-variant {
        font-size: 13px;
        color: var(--text-muted);
        margin-bottom: 8px;
    }

    .item-price {
        font-size: 16px;
        font-weight: 700;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .item-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        border: 1px solid var(--border-medium);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.03);
    }

    .quantity-control button {
        width: 32px;
        height: 32px;
        background: transparent;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .quantity-control button:hover {
        color: var(--accent);
    }

    .quantity-control span {
        padding: 0 16px;
        font-weight: 700;
        color: var(--text-primary);
        min-width: 40px;
        text-align: center;
    }

    .item-total {
        font-weight: 700;
        font-size: 14px;
        color: var(--text-secondary);
    }

    .btn-remove {
        font-size: 12px;
        color: var(--error);
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .btn-remove:hover {
        opacity: 0.7;
    }

    /* ===== CART SUMMARY ===== */
    .cart-summary {
        background: var(--primary-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        overflow: hidden;
        height: fit-content;
        position: sticky;
        top: 100px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    }

    .summary-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
    }

    .summary-header h3 {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .summary-content {
        padding: 24px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        font-size: 14px;
    }

    .summary-row:last-of-type {
        margin-bottom: 0;
    }

    .summary-label {
        color: var(--text-secondary);
        font-weight: 500;
    }

    .summary-value {
        color: var(--text-primary);
        font-weight: 700;
    }

    .summary-divider {
        border-top: 1px solid var(--border-light);
        margin: 16px 0;
    }

    .summary-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 18px;
        font-weight: 800;
        background: linear-gradient(135deg, rgba(255, 165, 0, 0.1) 0%, rgba(255, 165, 0, 0.05) 100%);
        padding: 16px;
        border-radius: 10px;
        border: 1px solid var(--border-medium);
        margin-top: 16px;
    }

    .summary-total .total-value {
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .summary-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 24px;
    }

    .btn-checkout {
        padding: 14px 20px;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: var(--primary);
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
    }

    .btn-secondary {
        padding: 14px 20px;
        background: transparent;
        color: var(--accent);
        border: 1px solid var(--border-medium);
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-secondary:hover {
        background: rgba(255, 165, 0, 0.1);
        border-color: var(--accent);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .cart-grid {
            grid-template-columns: 1fr;
        }

        .cart-summary {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 24px 16px;
        }

        .cart-item {
            grid-template-columns: 80px 1fr;
            gap: 16px;
        }

        .item-actions {
            grid-column: 1 / -1;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            margin-top: 12px;
        }

        .item-image {
            width: 80px;
            height: 80px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 20px 12px;
        }

        .cart-item {
            padding: 16px 12px;
            grid-template-columns: 70px 1fr;
            gap: 12px;
        }

        .item-image {
            width: 70px;
            height: 70px;
            border-radius: 8px;
        }

        .summary-actions {
            gap: 10px;
        }
    }

    /* ===== Custom popup (replaces browser alert/confirm) ===== */
    .vx-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(8, 24, 19, 0.58);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .vx-box {
        width: 100%;
        max-width: 430px;
        text-align: center;
        background: #ffffff;
        border: 1px solid #e4eae5;
        border-radius: 10px;
        padding: 32px 28px 26px;
        box-shadow: 0 24px 70px rgba(8, 24, 19, 0.24);
        opacity: 0;
        transform: scale(0.94) translateY(10px);
        transition: opacity 0.25s ease, transform 0.25s ease;
    }
    .vx-icon {
        display: grid;
        place-items: center;
        width: 58px;
        height: 58px;
        margin: 0 auto 18px;
        border-radius: 50%;
        background: #fdf3d4;
        color: #0e2a22;
        font-size: 30px;
        line-height: 1;
    }
    .vx-title {
        margin: 0 0 9px;
        color: #0e2a22;
        font-family: var(--font-display), sans-serif;
        font-size: 24px;
        font-weight: 800;
        line-height: 1.2;
    }
    .vx-msg {
        max-width: 320px;
        margin: 0 auto 26px;
        color: #4a5551;
        font-size: 14px;
        line-height: 1.6;
    }
    .vx-actions {
        display: flex;
        gap: 10px;
    }
    .vx-btn {
        flex: 1;
        min-height: 44px;
        padding: 11px 16px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }
    .vx-btn:hover { transform: translateY(-1px); }
    .vx-btn.primary {
        background: #f2b705;
        color: #0e2a22;
        border-color: #f2b705;
    }
    .vx-btn.primary:hover { background: #d9a404; border-color: #d9a404; }
    .vx-btn.danger {
        background: #b23a2e;
        color: #ffffff;
        border-color: #b23a2e;
    }
    .vx-btn.danger:hover { background: #922f26; border-color: #922f26; }
    .vx-btn.ghost {
        background: #ffffff;
        color: #0e2a22;
        border-color: #c8d6ce;
    }
    .vx-btn.ghost:hover { background: #f5f7f4; border-color: #1e6f5c; }

    @media (max-width: 480px) {
        .vx-box {
            padding: 28px 20px 22px;
        }

        .vx-actions {
            flex-direction: column-reverse;
        }

        .vx-btn {
            width: 100%;
        }
    }

    /* Storefront cart theme */
    .cart-page {
        min-width: 0;
        overflow-x: clip;
        position: relative;
        max-width: 1200px;
        margin: 0 auto;
        padding: 8px 0 64px;
        color: #0e2a22;
    }

    .cart-page .page-title {
        max-width: 100%;
        overflow-wrap: anywhere;
        margin: 0 0 32px;
        color: #0e2a22;
        font-family: var(--font-display), sans-serif;
        font-size: clamp(2rem, 5vw, 3rem);
        letter-spacing: -0.04em;
        background: none;
        -webkit-text-fill-color: currentColor;
    }

    .cart-page .cart-grid {
        grid-template-columns: minmax(0, 1fr) minmax(280px, 340px);
        gap: 24px;
        align-items: start;
    }

    .cart-page .vendor-section,
    .cart-page .cart-summary {
        min-width: 0;
        background: #fff;
        border: 1px solid #e4eae5;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.06);
    }

    .cart-page .vendor-section { overflow: hidden; }

    .cart-page .vendor-header,
    .cart-page .summary-header {
        padding: 16px 20px;
        background: #f5f7f4;
        border-bottom: 1px solid #e4eae5;
    }

    .cart-page .vendor-header {
        min-width: 0;
        gap: 12px;
        flex-wrap: wrap;
        background: linear-gradient(135deg, #0e2a22, #14392e);
    }

    .cart-page .vendor-info {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px 12px;
    }

    .cart-page .vendor-name,
    .cart-page .summary-header h3 {
        overflow-wrap: anywhere;
        color: #0e2a22;
        font-family: var(--font-display), sans-serif;
        font-size: 1rem;
        font-weight: 800;
    }

    .cart-page .vendor-header .vendor-name,
    .cart-page .vendor-header .vendor-rating {
        color: #fff;
    }

    .cart-page .vendor-rating,
    .cart-page .vendor-subtotal {
        font-size: 0.82rem;
        font-weight: 700;
    }

    .cart-page .vendor-subtotal {
        color: #f2b705;
    }

    .cart-page .vendor-verified {
        padding: 4px 8px;
        border-radius: 999px;
        background: rgba(242, 183, 5, 0.16);
        color: #f2b705;
        font-size: 0.68rem;
        font-weight: 700;
    }

    .cart-page .cart-item {
        grid-template-columns: 88px minmax(0, 1fr) auto;
        gap: 16px;
        min-width: 0;
        padding: 18px 20px;
        border-bottom-color: #e4eae5;
    }

    .cart-page .item-image {
        width: 88px;
        height: 88px;
        border-radius: 12px;
        background: #f5f7f4;
    }

    .cart-page .item-name,
    .cart-page .item-price {
        color: #0e2a22;
    }

    .cart-page .item-name {
        min-width: 0;
        margin-bottom: 6px;
        font-size: 0.95rem;
        overflow-wrap: anywhere;
    }

    .cart-page .item-name,
    .cart-page .item-variant,
    .cart-page .item-price,
    .cart-page .item-total {
        overflow-wrap: anywhere;
        word-break: break-word;
    }

    .cart-page .item-actions {
        min-width: 0;
        max-width: 100%;
    }

    .cart-page .quantity-control {
        flex-shrink: 0;
    }

    .cart-page .summary-content,
    .cart-page .summary-row,
    .cart-page .summary-total {
        min-width: 0;
    }

    .cart-page .summary-row > *,
    .cart-page .summary-total > * {
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .cart-page .summary-actions > * {
        max-width: 100%;
        min-width: 0;
    }

    .cart-page .cart-items,
    .cart-page .cart-summary {
        max-height: calc(100vh - 170px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #c8d6ce transparent;
    }

    .cart-page .cart-summary {
        align-self: start;
        position: static;
        width: auto;
        height: fit-content;
    }

    .cart-page .item-variant,
    .cart-page .item-total,
    .cart-page .summary-label {
        color: rgba(14, 42, 34, 0.68);
    }

    .cart-page .item-price {
        font-size: 0.9rem;
        background: none;
        -webkit-text-fill-color: currentColor;
    }

    .cart-page .quantity-control {
        border-color: #c8d6ce;
        background: #fff;
    }

    .cart-page .quantity-control button,
    .cart-page .quantity-control span {
        color: #0e2a22;
    }

    .cart-page .quantity-control button:hover { color: #1e6f5c; }

    .cart-page .btn-remove {
        color: #b23a2e;
    }

    .cart-page .summary-content {
        padding: 20px;
    }

    .cart-page .summary-value {
        color: #0e2a22;
    }

    .cart-page .summary-row {
        margin-bottom: 14px;
    }

    .cart-page .summary-divider {
        border-color: #e4eae5;
    }

    .cart-page .summary-total {
        margin-top: 14px;
        padding: 14px;
        border-color: #c8d6ce;
        background: rgba(242, 183, 5, 0.08);
        color: #0e2a22;
    }

    .cart-page .summary-total .total-value {
        color: #0e2a22;
        background: none;
        -webkit-text-fill-color: currentColor;
    }

    .cart-page .btn-checkout {
        background: #f2b705;
        color: #0e2a22;
        box-shadow: 0 8px 18px rgba(242, 183, 5, 0.2);
    }

    .cart-page .btn-secondary {
        padding: 12px 16px;
        background: #fff;
        border-color: #c8d6ce;
        color: #0e2a22;
    }

    .cart-page .btn-secondary:hover {
        background: #f5f7f4;
        border-color: #1e6f5c;
    }

    .cart-page .empty-cart {
        background: #fff;
        border-color: #e4eae5;
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.06);
    }

    .cart-page .empty-cart h2 {
        color: #0e2a22;
        background: none;
        -webkit-text-fill-color: currentColor;
    }

    .cart-page .empty-cart p {
        color: rgba(14, 42, 34, 0.68);
    }

    @media (max-width: 1024px) {
        .cart-page .cart-grid { grid-template-columns: 1fr; }
        .cart-page .cart-items,
        .cart-page .cart-summary {
            position: static;
            width: auto;
            z-index: auto;
            max-height: none;
            overflow: visible;
        }
    }

    @media (max-width: 640px) {
        .cart-page {
            width: 100%;
            padding: 0 0 48px;
        }
        .cart-page .page-title { margin-bottom: 24px; }
        .cart-page .vendor-header {
            align-items: flex-start;
            flex-direction: column;
            gap: 8px;
        }
        .cart-page .cart-item {
            grid-template-columns: 68px minmax(0, 1fr);
            padding: 16px;
        }
        .cart-page .item-image { width: 68px; height: 68px; }
        .cart-page .item-actions {
            grid-column: 1 / -1;
            width: 100%;
            align-items: center;
            flex-direction: row;
            justify-content: space-between;
            margin-top: 4px;
        }
        .cart-page .quantity-control button {
            width: 30px;
        }
        .cart-page .quantity-control span {
            padding: 0 10px;
        }
    }
</style>
@endpush

@section('content')
<div class="cart-page" id="cartPage">
<h1 class="page-title">Shopping Cart</h1>

@if($cart->getTotalItems() == 0)
    <div class="empty-cart">
        <h2>Your Cart is Empty</h2>
        <p>Start shopping to add items to your cart and discover premium products.</p>
        <a href="{{ route('storefront.products.index') }}" class="btn-primary">Continue Shopping</a>
    </div>
@else
    <div class="cart-grid">
        <!-- Cart Items -->
        <div class="cart-items">
            @foreach($itemsByVendor as $vendorId => $items)
                @php
                    $vendor = $items->first()->product->vendor;
                    $vendorName = $vendor->store_name ?? $vendor->user->name;
                    $vendorRating = $vendor->average_rating ?? 0;
                    $isVerified = $vendor->is_verified ?? false;
                @endphp
                <div class="vendor-section">
                    <div class="vendor-header">
                        <div class="vendor-info">
                            <div class="vendor-name">{{ $vendorName }}</div>
                            @if($vendorRating > 0)
                                <div class="vendor-rating">⭐ {{ number_format($vendorRating, 1) }}</div>
                            @endif
                            @if($isVerified)
                                <span class="vendor-verified badge">Verified</span>
                            @endif
                        </div>
                        <div class="vendor-subtotal">₨{{ number_format($items->sum(fn($item) => $item->price * $item->quantity)) }}</div>
                    </div>

                    @foreach($items as $item)
                        <div class="cart-item">
                            <div class="item-image">
                                @if($item->product->mainImage())
                                    <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}" alt="{{ $item->product->name }}">
                                @else
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">📦</div>
                                @endif
                            </div>

                            <div class="item-details">
                                <div class="item-name">{{ $item->product->name }}</div>
                                @if($item->variant)
                                    <div class="item-variant">{{ $item->variant->variant_value }}</div>
                                @endif
                                <div class="item-price">₨{{ number_format($item->price) }}</div>
                            </div>

                            <div class="item-actions">
                                <div class="quantity-control">
                                    <button type="button" aria-label="Decrease quantity" onclick="updateQuantity({{ $item->id }}, -1)">−</button>
                                    <span id="qty-{{ $item->id }}">{{ $item->quantity }}</span>
                                    <button type="button" aria-label="Increase quantity" onclick="updateQuantity({{ $item->id }}, 1)">+</button>
                                </div>
                                <div class="item-total" id="total-{{ $item->id }}">₨{{ number_format($item->getTotal()) }}</div>
                                <button class="btn-remove" onclick="removeItem({{ $item->id }})">Remove</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <!-- Cart Summary Sidebar -->
        <div class="cart-summary">
            <div class="summary-header">
                <h3>Order Summary</h3>
            </div>

            <div class="summary-content">
                <div class="summary-row">
                    <span class="summary-label">Items ({{ $totalItems }})</span>
                    <span class="summary-value" id="summary-subtotal">₨{{ number_format($subtotal) }}</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Shipping</span>
                    <span class="summary-value" style="color: var(--success);">Free</span>
                </div>

                <div class="summary-row">
                    <span class="summary-label">Taxes</span>
                    <span class="summary-value">Calculated</span>
                </div>

                <div class="summary-divider"></div>

                <div class="summary-total">
                    <span class="summary-label">Total</span>
                    <span class="total-value" id="cart-total">₨{{ number_format($subtotal) }}</span>
                </div>

                <div class="summary-actions">
                    @if(auth()->check())
                        <a href="{{ route('checkout.index') }}" class="btn-checkout">Proceed to Checkout</a>
                    @else
                        <a href="{{ route('customer.login', ['redirect' => route('checkout.index')]) }}" class="btn-checkout">Login to Checkout</a>
                    @endif
                    <a href="{{ route('storefront.products.index') }}" class="btn-secondary">Continue Shopping</a>
                    <button class="btn-secondary" onclick="clearCart()" style="background: rgba(239, 68, 68, 0.1); color: var(--error); border-color: var(--error);">Clear Cart</button>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
<!-- Custom popup -->
<div class="vx-overlay" id="vxOverlay" role="dialog" aria-modal="true" aria-labelledby="vxTitle">
    <div class="vx-box" id="vxBox" onclick="event.stopPropagation()">
        <div class="vx-icon" id="vxIcon">✨</div>
        <h3 class="vx-title" id="vxTitle">Done!</h3>
        <p class="vx-msg" id="vxMsg"></p>
        <div class="vx-actions" id="vxActions"></div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        /* ===== Custom popup helpers (replace alert/confirm) ===== */
        let vxResolver = null;

        function vxOpen(icon, title, message, buttons) {
            const box = document.getElementById('vxBox');
            const actions = document.getElementById('vxActions');
            document.getElementById('vxIcon').textContent = icon;
            document.getElementById('vxTitle').textContent = title;
            document.getElementById('vxMsg').textContent = message;
            actions.innerHTML = '';
            buttons.forEach(b => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'vx-btn ' + b.style;
                btn.textContent = b.label;
                btn.onclick = () => vxClose(b.value);
                actions.appendChild(btn);
            });
            document.getElementById('vxOverlay').style.display = 'flex';
            requestAnimationFrame(() => {
                box.style.opacity = '1';
                box.style.transform = 'scale(1) translateY(0)';
            });
            const f = actions.querySelector('.primary, .danger');
            if (f) f.focus();
        }

        function vxClose(value) {
            const box = document.getElementById('vxBox');
            box.style.opacity = '0';
            box.style.transform = 'scale(0.94) translateY(10px)';
            setTimeout(() => { document.getElementById('vxOverlay').style.display = 'none'; }, 250);
            if (vxResolver) { const r = vxResolver; vxResolver = null; r(value); }
        }

        function showPopup(message, type = 'info') {
            const map = {
                success: ['✨', 'Done!'],
                error: ['⚠️', 'Oops'],
                info: ['ℹ️', 'Notice']
            };
            const t = map[type] || map.info;
            return new Promise(resolve => {
                vxResolver = resolve;
                vxOpen(t[0], t[1], message || 'Something went wrong.', [{ label: 'OK', style: 'primary', value: true }]);
            });
        }

        function confirmPopup(message, confirmLabel = 'Confirm') {
            return new Promise(resolve => {
                vxResolver = resolve;
                vxOpen('🗑️', 'Are you sure?', message, [
                    { label: 'Cancel', style: 'ghost', value: false },
                    { label: confirmLabel, style: 'danger', value: true }
                ]);
            });
        }

        document.getElementById('vxOverlay').addEventListener('click', () => vxClose(false));
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && document.getElementById('vxOverlay').style.display === 'flex') vxClose(false);
        });

        /* ===== Cart actions ===== */
        async function updateQuantity(itemId, change) {
            const quantityElement = document.getElementById('qty-' + itemId);
            const controls = quantityElement?.closest('.quantity-control');
            const currentQuantity = parseInt(quantityElement?.textContent || '1', 10);
            const newQuantity = currentQuantity + change;

            if (newQuantity < 1) {
                if (await confirmPopup('Remove this item from your cart?', 'Remove')) {
                    removeItem(itemId);
                }
                return;
            }

            if (controls) {
                controls.querySelectorAll('button').forEach(button => {
                    button.disabled = true;
                });
            }

            try {
                const response = await fetch(`{{ route('cart.update', ':id') }}`.replace(':id', itemId), {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: newQuantity })
                });
                const data = await response.json();

                if (!response.ok || !data.success) {
                    await showPopup(data.message || 'Unable to update this item.', 'error');
                    return;
                }

                quantityElement.textContent = data.quantity ?? newQuantity;
                document.getElementById('total-' + itemId).textContent = '₨' + parseFloat(data.item_total).toLocaleString('en-US', {maximumFractionDigits: 0});
                document.getElementById('cart-total').textContent = '₨' + parseFloat(data.cart_subtotal).toLocaleString('en-US', {maximumFractionDigits: 0});
                document.getElementById('summary-subtotal').textContent = '₨' + parseFloat(data.cart_subtotal).toLocaleString('en-US', {maximumFractionDigits: 0});
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            } catch (error) {
                console.error('Cart quantity update failed:', error);
                await showPopup('An error occurred. Please try again.', 'error');
            } finally {
                if (controls) {
                    controls.querySelectorAll('button').forEach(button => {
                        button.disabled = false;
                    });
                }
            }
        }

        function removeItem(itemId) {
            fetch(`{{ route('cart.remove', ':id') }}`.replace(':id', itemId), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showPopup(data.message, 'error');
                }
            })
            .catch(e => { console.error('Error:', e); showPopup('An error occurred. Please try again.', 'error'); });
        }

        async function clearCart() {
            if (!(await confirmPopup('This will remove every item from your cart.', 'Clear Cart'))) return;

            fetch('{{ route('cart.clear') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showPopup(data.message, 'error');
                }
            })
            .catch(e => { console.error('Error:', e); showPopup('An error occurred. Please try again.', 'error'); });
        }
    </script>
@endpush
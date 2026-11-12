@extends('layouts.storefront')

@section('title', 'Checkout - VandoraX')

@push('styles')
<style>
    #stripe-payment-element { min-height: 120px; padding: 12px; background: #fff; border-radius: 8px; }
    #stripe-payment-error { display: none; margin-top: 10px; color: var(--error); font-size: 13px; }
</style>
@endpush

@push('styles')
<style>
    .container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 40px 24px;
    }

    /* Centered page wrapper with equal side space and bottom padding */
    .checkout-page {
        box-sizing: border-box;
        width: 100%;
        max-width: 1400px;
        padding-top: 20px;
        padding-bottom: 80px;
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

    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 32px;
    }

    /* ===== FORM SECTIONS ===== */
    .form-section {
        background: var(--primary-light);
        border-radius: 16px;
        border: 1px solid var(--border-light);
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        margin-bottom: 24px;
    }

    .section-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-light);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .section-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 18px;
        flex-shrink: 0;
    }

    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }

    .section-content {
        padding: 24px;
    }

    /* ===== FORM INPUTS ===== */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .form-row.full {
        grid-template-columns: 1fr;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-primary);
    }

    .form-required::after {
        content: ' *';
        color: var(--accent);
    }

    .form-input,
    .form-textarea {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--border-medium);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
        font-family: 'Poppins', sans-serif;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-input::placeholder,
    .form-textarea::placeholder {
        color: var(--text-muted);
    }

    .form-input:focus,
    .form-textarea:focus {
        outline: none;
        border-color: var(--accent);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* ===== ADDRESS SELECTION ===== */
    .address-options {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 24px;
    }

    .address-option {
        padding: 16px;
        border: 2px solid var(--border-medium);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.03);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .address-option:hover {
        border-color: var(--accent);
        background: rgba(255, 165, 0, 0.08);
    }

    .address-option input[type="radio"] {
        margin-right: 8px;
        cursor: pointer;
        accent-color: var(--accent);
    }

    .address-option.selected {
        border-color: var(--accent);
        background: rgba(255, 165, 0, 0.12);
    }

    .address-label {
        font-weight: 700;
        margin-bottom: 8px;
        color: var(--text-primary);
    }

    .address-detail {
        font-size: 13px;
        color: var(--text-secondary);
        line-height: 1.5;
    }

    /* ===== BUTTONS ===== */
    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 20px;
    }

    .btn-primary {
        flex: 1;
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
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
    }

    .btn-secondary {
        flex: 1;
        padding: 14px 20px;
        background: transparent;
        color: var(--accent);
        border: 1px solid var(--border-medium);
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: rgba(255, 165, 0, 0.1);
        border-color: var(--accent);
    }

    .btn-toggle {
        color: var(--accent);
        background: transparent;
        border: none;
        cursor: pointer;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .btn-toggle:hover {
        opacity: 0.8;
    }

    /* ===== ORDER SUMMARY SIDEBAR ===== */
    .order-summary {
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
        margin-bottom: 12px;
        font-size: 14px;
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
    }

    .btn-checkout:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
    }

    .btn-checkout:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-back {
        padding: 14px 20px;
        background: transparent;
        color: var(--accent);
        border: 1px solid var(--border-medium);
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: rgba(255, 165, 0, 0.1);
        border-color: var(--accent);
    }

    /* ===== ORDER ITEMS ===== */
    .order-items {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 24px;
        max-height: 400px;
        overflow-y: auto;
    }

    .order-item {
        padding: 12px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 8px;
        border: 1px solid var(--border-light);
        display: flex;
        gap: 12px;
        font-size: 13px;
    }

    .order-item-image {
        width: 50px;
        height: 50px;
        border-radius: 6px;
        overflow: hidden;
        background: linear-gradient(135deg, var(--border-light) 0%, var(--border-medium) 100%);
        flex-shrink: 0;
    }

    .order-item-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .order-item-info {
        flex: 1;
        min-width: 0;
    }

    .order-item-name {
        font-weight: 700;
        color: var(--text-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .order-item-qty {
        color: var(--text-secondary);
    }

    .order-item-price {
        font-weight: 700;
        color: var(--accent);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 1024px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }

        .order-summary {
            position: static;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .address-options {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .container {
            padding: 24px 16px;
        }

        .page-title {
            font-size: 32px;
            margin-bottom: 24px;
        }

        .section-header {
            padding: 16px 12px;
        }

        .section-content {
            padding: 16px 12px;
        }
    }

    @media (max-width: 480px) {
        .container {
            padding: 20px 12px;
        }

        .page-title {
            font-size: 24px;
        }

        .section-number {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }

        .section-header {
            padding: 14px 12px;
        }

        .section-content {
            padding: 14px 12px;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        .address-options {
            grid-template-columns: 1fr;
        }

        .btn-group {
            flex-direction: column;
        }
    }

    /* Storefront checkout theme */
    .checkout-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 8px 0 64px;
        color: #0e2a22;
        width: 100%;
        min-width: 0;
        overflow-x: clip;
    }

    .checkout-page *,
    .checkout-page *::before,
    .checkout-page *::after {
        box-sizing: border-box;
        max-width: 100%;
    }

    .checkout-page .page-title {
        margin: 0 0 32px;
        color: #0e2a22;
        font-family: var(--font-display), sans-serif;
        font-size: clamp(2rem, 5vw, 3rem);
        letter-spacing: -0.04em;
        background: none;
        -webkit-text-fill-color: currentColor;
    }

    .checkout-page .checkout-grid {
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 24px;
        align-items: start;
        min-width: 0;
    }

    .checkout-page .checkout-grid > *,
    .checkout-page .form-section,
    .checkout-page .order-summary {
        min-width: 0;
    }

    .checkout-page .form-section,
    .checkout-page .order-summary {
        background: #fff;
        border: 1px solid #e4eae5;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.06);
    }

    .checkout-page .form-section { margin-bottom: 20px; }

    .checkout-page .section-header,
    .checkout-page .summary-header {
        padding: 16px 20px;
        background: #f5f7f4;
        border-bottom: 1px solid #e4eae5;
    }

    .checkout-page .section-number {
        width: 34px;
        height: 34px;
        background: #f2b705;
        color: #0e2a22;
        font-size: 0.9rem;
    }

    .checkout-page .section-title,
    .checkout-page .summary-header h3 {
        color: #0e2a22;
        font-family: var(--font-display), sans-serif;
        font-size: 1rem;
        font-weight: 800;
    }

    .checkout-page .section-content,
    .checkout-page .summary-content {
        padding: 20px;
    }

    .checkout-page .form-label,
    .checkout-page .address-label,
    .checkout-page .section-content h4 {
        color: #0e2a22 !important;
    }

    .checkout-page .form-input,
    .checkout-page .form-textarea {
        border-color: #c8d6ce;
        background: #fff;
        color: #0e2a22;
    }

    .checkout-page .form-input::placeholder,
    .checkout-page .form-textarea::placeholder,
    .checkout-page .address-detail {
        color: rgba(14, 42, 34, 0.62);
    }

    .checkout-page .form-input:focus,
    .checkout-page .form-textarea:focus {
        border-color: #1e6f5c;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(30, 111, 92, 0.12);
    }

    .checkout-page .address-option,
    .checkout-page .payment-option {
        border-color: #c8d6ce !important;
        background: #fff !important;
    }

    .checkout-page .address-option:hover,
    .checkout-page .payment-option:hover,
    .checkout-page .address-option.selected,
    .checkout-page .payment-option.selected {
        border-color: #f2b705 !important;
        background: rgba(242, 183, 5, 0.08) !important;
    }

    .checkout-page .payment-option div[style*="font-weight"],
    .checkout-page .payment-option div[style*="font-size"] {
        color: #0e2a22 !important;
    }

    .checkout-page .payment-option div[style*="font-size: 13px"] {
        color: rgba(14, 42, 34, 0.68) !important;
    }

    .checkout-page .btn-toggle {
        color: #1e6f5c;
    }

    .checkout-page .btn-checkout {
        background: #f2b705;
        color: #0e2a22;
        box-shadow: 0 8px 18px rgba(242, 183, 5, 0.2);
    }

    .checkout-page .btn-back {
        background: #fff;
        border-color: #c8d6ce;
        color: #0e2a22;
    }

    .checkout-page .btn-back:hover {
        background: #f5f7f4;
        border-color: #1e6f5c;
    }

    .checkout-page .order-summary {
        position: static;
    }

    .checkout-page .summary-label,
    .checkout-page .summary-value {
        color: #0e2a22;
    }

    .checkout-page .summary-label {
        color: rgba(14, 42, 34, 0.68);
    }

    .checkout-page .summary-divider {
        border-color: #e4eae5;
    }

    .checkout-page .summary-total {
        margin-top: 14px;
        padding: 14px;
        border-color: #c8d6ce;
        background: rgba(242, 183, 5, 0.08);
    }

    .checkout-page .summary-total .total-value {
        color: #0e2a22;
        background: none;
        -webkit-text-fill-color: currentColor;
    }

    .checkout-page .order-items {
        max-height: 360px;
        margin-top: 20px;
    }

    .checkout-page .order-item {
        border-color: #e4eae5;
        background: #f5f7f4;
    }

    .checkout-page .order-item-name,
    .checkout-page .order-item-price {
        color: #0e2a22;
    }

    .checkout-page .order-item-qty {
        color: rgba(14, 42, 34, 0.68);
    }

    #global-message {
        position: fixed;
        top: 76px;
        right: 24px;
        z-index: 9999;
        display: grid;
        gap: 10px;
        width: min(390px, calc(100vw - 32px));
    }

    #global-message .checkout-message {
        position: relative;
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 14px 42px 14px 16px;
        border: 1px solid #e4eae5;
        border-left: 4px solid #1e6f5c;
        border-radius: 12px;
        background: #fff;
        color: #0e2a22;
        box-shadow: 0 14px 34px rgba(14, 42, 34, 0.16);
        font-size: 0.86rem;
        font-weight: 600;
        line-height: 1.5;
        animation: checkout-message-in 0.25s ease both;
    }

    #global-message .checkout-message.success { border-left-color: #1e7a4b; }
    #global-message .checkout-message.error { border-left-color: #b23a2e; }
    #global-message .checkout-message.warning { border-left-color: #f2b705; }
    #global-message .checkout-message.info { border-left-color: #1e6f5c; }

    #global-message .checkout-message-close {
        position: absolute;
        top: 8px;
        right: 10px;
        width: 26px;
        height: 26px;
        border: 0;
        border-radius: 50%;
        background: #f5f7f4;
        color: #0e2a22;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
    }

    #global-message .checkout-message-close:hover { background: #e4eae5; }

    #loading-overlay {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(8, 24, 19, 0.62);
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
    }

    #loading-overlay .loading-card {
        width: min(390px, 100%);
        padding: 30px 26px 26px;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 20px;
        background: #fff;
        color: #0e2a22;
        text-align: center;
        box-shadow: 0 24px 70px rgba(8, 24, 19, 0.28);
    }

    #loading-overlay .loading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 58px;
        height: 58px;
        margin: 0 auto 18px;
        border: 4px solid #e4eae5;
        border-top-color: #f2b705;
        border-right-color: #1e6f5c;
        border-radius: 50%;
        animation: checkout-loading-spin 0.9s linear infinite;
    }

    #loading-overlay .loading-icon::after {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f2b705;
    }

    #loading-overlay .loading-title {
        margin: 0;
        color: #0e2a22;
        font-family: var(--font-display), sans-serif;
        font-size: 1.15rem;
        font-weight: 800;
    }

    #loading-overlay .loading-message {
        margin: 8px 0 0;
        color: rgba(14, 42, 34, 0.68);
        font-size: 0.86rem;
        line-height: 1.5;
    }

    @keyframes checkout-loading-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes checkout-message-in {
        from { opacity: 0; transform: translateY(-8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 1180px) {
        .checkout-page .checkout-grid { grid-template-columns: 1fr; }
        .checkout-page .order-summary { position: static; }
        .checkout-page .order-summary { order: initial; }
        .checkout-page .order-items { max-height: none; }
    }

    @media (max-width: 640px) {
        .checkout-page { padding: 0 0 48px; }
        .checkout-page .page-title { margin-bottom: 24px; }
        .checkout-page .section-header,
        .checkout-page .section-content,
        .checkout-page .summary-header,
        .checkout-page .summary-content {
            padding: 16px;
        }

        #global-message {
            top: 64px;
            right: 16px;
            width: calc(100vw - 32px);
        }

        .checkout-page .payment-option {
            min-width: 0;
        }

        .checkout-page #payment-form,
        .checkout-page #payment-details,
        .checkout-page #stripe-payment-element {
            min-width: 0;
            width: 100%;
        }

        .checkout-page .order-summary {
            margin-bottom: 4px;
        }

        .checkout-page .order-items {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .checkout-page .order-item {
            min-width: 0;
            padding: 10px;
        }

        .checkout-page .order-item-image {
            width: 42px;
            height: 42px;
        }

        .checkout-page .order-item-name {
            font-size: 0.75rem;
        }

        .checkout-page .btn-checkout,
        .checkout-page .btn-back {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="checkout-page" id="checkoutPage">
<h1 class="page-title">Checkout</h1>

<div class="checkout-grid">
    <!-- Checkout Form -->
    <div>
        <!-- Step 1: Delivery Address -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-number">1</div>
                <div class="section-title">Delivery Address</div>
            </div>

            <div class="section-content">
                @if($addresses->count() > 0)
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px;">Select Saved Address</h4>
                    <div class="address-options">
                        @foreach($addresses as $address)
                            <label class="address-option" id="addr-option-{{ $address->id }}" onclick="selectAddress({{ $address->id }})">
                                <input type="radio" name="address_id" value="{{ $address->id }}" onchange="selectAddress({{ $address->id }})">
                                <div class="address-label">
                                    @if($address->label){{ $address->label }}@else Home @endif
                                </div>
                                <div class="address-detail">
                                    {{ $address->full_name }}<br>
                                    {{ $address->phone }}<br>
                                    {{ $address->address_line_1 }}
                                    @if($address->address_line_2), {{ $address->address_line_2 }} @endif
                                    <br>
                                    {{ $address->city }}, {{ $address->state_province }} {{ $address->postal_code }}
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div style="border-top: 1px solid var(--border-light); padding-top: 16px; margin-top: 16px;">
                        <button type="button" onclick="toggleAddressForm()" class="btn-toggle">+ Add New Address</button>
                    </div>
                @endif

                <!-- New Address Form -->
                <div id="address-form" style="display: @if($addresses->count() > 0) none @endif; margin-top: 20px;">
                    <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px;">Enter New Address</h4>
                    <form id="new-address-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label form-required">Full Name</label>
                                <input type="text" name="full_name" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Phone</label>
                                <input type="tel" name="phone" required class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label form-required">Address Line 1</label>
                            <input type="text" name="address_line_1" required class="form-input">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line_2" class="form-input">
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label form-required">City</label>
                                <input type="text" name="city" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">State/Province</label>
                                <input type="text" name="state_province" required class="form-input">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label form-required">Postal Code</label>
                                <input type="text" name="postal_code" required class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Country</label>
                                <input type="text" name="country" required value="United States" class="form-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Delivery Instructions</label>
                            <textarea name="delivery_instructions" class="form-textarea"></textarea>
                        </div>

                        <div class="btn-group">
                            <button type="button" onclick="saveAddress()" class="btn-primary">Save Address</button>
                            @if($addresses->count() > 0)
                                <button type="button" onclick="toggleAddressForm()" class="btn-secondary">Cancel</button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Step 2: Payment Method -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-number">2</div>
                <div class="section-title">Payment Method</div>
            </div>

            <div class="section-content">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <!-- Card Option -->
                    <label class="payment-option" id="payment-card-label" onclick="selectPaymentMethod('card')" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid var(--border-medium); border-radius: 10px; cursor: pointer; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.03);">
                        <input type="radio" name="payment_method" value="card" onchange="selectPaymentMethod('card')" style="margin-top: 4px; cursor: pointer; accent-color: var(--accent);">
                        <div style="flex: 1;"><div style="font-weight: 700; font-size: 15px; color: var(--text-primary); margin-bottom: 4px;">💳 Credit / Debit Card</div><div style="font-size: 13px; color: var(--text-secondary);">Secure card payment powered by Stripe</div></div>
                    </label>

                    <!-- Bank Transfer Option -->
                    <label class="payment-option" id="payment-bank-label" onclick="selectPaymentMethod('bank_transfer')" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid var(--border-medium); border-radius: 10px; cursor: pointer; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.03);">
                        <input type="radio" name="payment_method" value="bank_transfer" onchange="selectPaymentMethod('bank_transfer')" style="margin-top: 4px; cursor: pointer; accent-color: var(--accent);">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 15px; color: var(--text-primary); margin-bottom: 4px;">🏦 Bank Transfer</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">Transfer to our bank account - Admin verification required</div>
                        </div>
                    </label>

                    <!-- Cash on Delivery Option -->
                    <label class="payment-option" id="payment-cod-label" onclick="selectPaymentMethod('cod')" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid var(--border-medium); border-radius: 10px; cursor: pointer; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.03);">
                        <input type="radio" name="payment_method" value="cod" onchange="selectPaymentMethod('cod')" style="margin-top: 4px; cursor: pointer; accent-color: var(--accent);">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 15px; color: var(--text-primary); margin-bottom: 4px;">💵 Cash on Delivery</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">Pay when your order is delivered</div>
                        </div>
                    </label>
                </div>

                <!-- Payment Method Details -->
                <form id="payment-form" enctype="multipart/form-data">
                <div id="payment-details" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border-light);">
                    <!-- Stripe Card Details -->
                    <div id="card-details" style="display: none;">
                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Secure Card Details</h4>
                        <div id="stripe-payment-element"></div>
                        <div id="stripe-fake-notice" style="display:none; background: rgba(16,185,129,0.12); border: 1px solid #10b981; padding: 12px 14px; border-radius: 8px; color: #065f46; font-size: 13px; line-height: 1.5;"></div>
                        <p id="stripe-payment-error"></p>
                        <p style="margin-top: 10px; color: var(--text-secondary); font-size: 12px;">Your card details are securely handled by Stripe and are never stored on our server.</p>
                    </div>

                    <!-- Bank Transfer Details -->
                    <div id="bank_transfer-details" style="display: none;">
                        <div style="background: rgba(255, 165, 0, 0.08); padding: 16px; border-radius: 10px; border-left: 3px solid var(--accent); margin-bottom: 16px;">
                            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Company Bank Account Information</h4>
                            @php
                                $bankDetails = config('bank-transfer.bank');
                            @endphp
                            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                                <div>
                                    <span style="color: var(--text-secondary);">Bank Name:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ $bankDetails['name'] ?? '' }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">Account Title:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ $bankDetails['account_title'] ?? '' }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">Account Number:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ $bankDetails['account_number'] ?? '' }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">IBAN:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ $bankDetails['iban'] ?? '' }}</span>
                                </div>
                            </div>
                        </div>

                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 16px; color: var(--text-primary);">Your Payment Details</h4>
                        <div class="form-group">
                            <label class="form-label form-required">Customer Name</label>
                            <input type="text" id="bank_payer_name" name="bank_payer_name" placeholder="Enter the name of the person making the payment" class="form-input">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label form-required">Payment App / Platform</label>
                                <input type="text" id="bank_payment_app" name="bank_payment_app" placeholder="e.g. HBL Mobile, Easypaisa, ATM" class="form-input">
                            </div>
                            <div class="form-group">
                                <label class="form-label form-required">Payment Source</label>
                                <input type="text" id="bank_payment_source" name="bank_payment_source" placeholder="e.g. Personal bank account" class="form-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label form-required">Customer Account Number</label>
                            <input type="text" id="bank_customer_account_number" name="bank_customer_account_number" placeholder="Account number used for the payment" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Payment Amount</label>
                            <input type="text" readonly value="₨{{ number_format($subtotal, 2) }}" class="form-input" style="background: rgba(255, 255, 255, 0.05); cursor: not-allowed;">
                        </div>
                        <div class="form-group">
                            <label class="form-label form-required">Payment Slip / Receipt Image</label>
                            <input type="file" id="payment_slip" name="payment_slip" accept="image/*" required class="form-input">
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Step 3: Order Review -->
        <div class="form-section">
            <div class="section-header">
                <div class="section-number">3</div>
                <div class="section-title">Order Summary</div>
            </div>

            <div class="section-content">
                @foreach($itemsByVendor as $vendorId => $items)
                    <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border-light);">
                        <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px;">{{ $items->first()->product->vendor->user->name }}</h4>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            @foreach($items as $item)
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                    <div>
                                        <div style="font-weight: 700; color: var(--text-primary);">{{ $item->product->name }}</div>
                                        @if($item->variant)
                                            <div style="color: var(--text-secondary); margin-top: 2px;">{{ $item->variant->variant_value }}</div>
                                        @endif
                                        <div style="color: var(--text-muted); margin-top: 2px;">Qty: {{ $item->quantity }}</div>
                                    </div>
                                    <div style="font-weight: 700; color: var(--accent);">₨{{ number_format($item->getTotal()) }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if(isset($vendorCommissions[$vendorId]))
                            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid var(--border-light); display: flex; flex-direction: column; gap: 6px; font-size: 13px;">
                                <div style="display: flex; justify-content: space-between; color: var(--text-secondary);">
                                    <span>Subtotal</span>
                                    <span>₨{{ number_format($vendorCommissions[$vendorId]['subtotal']) }}</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; color: var(--success); font-weight: 700;">
                                    <span>Shipping</span>
                                    <span>Free</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-weight: 700; background: rgba(255, 165, 0, 0.1); padding: 8px; border-radius: 6px;">
                                    <span>Vendor Total</span>
                                    <span style="background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">₨{{ number_format($vendorCommissions[$vendorId]['subtotal']) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Order Summary Sidebar -->
    <div class="order-summary">
        <div class="summary-header">
            <h3>Order Total</h3>
        </div>

        <div class="summary-content">
            <div class="summary-row">
                <span class="summary-label">Items ({{ $totalItems }})</span>
                <span class="summary-value">₨{{ number_format($subtotal) }}</span>
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
                <span class="total-value">₨{{ number_format($subtotal) }}</span>
            </div>

            <div class="summary-actions">
                <button onclick="processCheckout()" id="checkout-btn" class="btn-checkout">Place Order</button>
                <a href="{{ route('cart.index') }}" class="btn-back">Back to Cart</a>
            </div>

            <div class="order-items">
                @foreach($itemsByVendor as $vendorId => $items)
                    @foreach($items as $item)
                        <div class="order-item">
                            @if($item->product->mainImage())
                                <div class="order-item-image">
                                    <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}" alt="{{ $item->product->name }}">
                                </div>
                            @else
                                <div class="order-item-image" style="display: flex; align-items: center; justify-content: center;">📦</div>
                            @endif
                            <div class="order-item-info">
                                <div class="order-item-name">{{ $item->product->name }}</div>
                                <div class="order-item-qty">Qty: {{ $item->quantity }}</div>
                                <div class="order-item-price">₨{{ number_format($item->price) }}</div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        let selectedAddressId = @json($defaultAddress ? $defaultAddress->id : null);
        let selectedPaymentMethod = 'cod';
        let stripeInstance = null;
        let stripeElements = null;
        let pendingCardPayment = null;
        let currentOrderId = null;
        let retryCount = 0;
        const MAX_RETRIES = 3;

        function setButtonState(btn, state, text) {
            btn.disabled = state !== 'idle';
            btn.textContent = text;
            btn.style.opacity = state === 'idle' ? '1' : '0.7';
            btn.style.cursor = state === 'idle' ? 'pointer' : 'not-allowed';
        }

        function showGlobalMessage(type, message) {
            let container = document.getElementById('global-message');
            if (!container) {
                container = document.createElement('div');
                container.id = 'global-message';
                document.body.appendChild(container);
            }
            const msgEl = document.createElement('div');
            msgEl.className = 'checkout-message ' + (type || 'info');
            msgEl.innerHTML = '<span></span><button type="button" class="checkout-message-close" aria-label="Dismiss message">&times;</button>';
            msgEl.querySelector('span').textContent = message;
            msgEl.querySelector('button').addEventListener('click', () => msgEl.remove());
            container.appendChild(msgEl);
            setTimeout(() => {
                if (!msgEl.isConnected) return;
                msgEl.style.opacity = '0';
                msgEl.style.transition = 'opacity .25s';
                setTimeout(() => msgEl.remove(), 250);
            }, 5000);
        }

        function selectAddress(addressId) {
            selectedAddressId = addressId;
            document.querySelectorAll('[id^="addr-option-"]').forEach(el => el.classList.remove('selected'));
            const el = document.getElementById('addr-option-' + addressId);
            if (el) el.classList.add('selected');
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;
            document.querySelectorAll('.payment-option').forEach(el => {
                el.style.borderColor = 'var(--border-medium)';
                el.style.background = 'rgba(255, 255, 255, 0.03)';
            });
            const selectedLabel = document.getElementById('payment-' + method + '-label');
            if (selectedLabel) {
                selectedLabel.style.borderColor = 'var(--accent)';
                selectedLabel.style.background = 'rgba(255, 165, 0, 0.12)';
            }
            document.getElementById('card-details').style.display = 'none';
            document.getElementById('bank_transfer-details').style.display = 'none';
            if (method === 'card') document.getElementById('card-details').style.display = 'block';
            else if (method === 'bank_transfer') document.getElementById('bank_transfer-details').style.display = 'block';
            hideStripeError();
        }

        function toggleAddressForm() {
            const form = document.getElementById('address-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        function saveAddress() {
            const form = document.getElementById('new-address-form');
            const formData = new FormData(form);
            fetch('{{ route('checkout.address.store') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            }).then(r => r.json()).then(data => {
                if (data.success) { showGlobalMessage('success', 'Address saved successfully!'); setTimeout(() => location.reload(), 800); }
                else showGlobalMessage('error', data.message || 'Failed to save address');
            }).catch(() => showGlobalMessage('error', 'Network error. Please try again.'));
        }

        function showStripeError(message) {
            const error = document.getElementById('stripe-payment-error');
            if (error) { error.textContent = message; error.style.display = 'block'; }
        }

        function hideStripeError() {
            const error = document.getElementById('stripe-payment-error');
            if (error) { error.textContent = ''; error.style.display = 'none'; }
        }

        function showLoadingOverlay(show) {
            let overlay = document.getElementById('loading-overlay');
            if (show && !overlay) {
                overlay = document.createElement('div');
                overlay.id = 'loading-overlay';
                overlay.setAttribute('role', 'dialog');
                overlay.setAttribute('aria-modal', 'true');
                overlay.setAttribute('aria-label', 'Processing payment');
                overlay.innerHTML = '<div class="loading-card"><div class="loading-icon" aria-hidden="true"></div><p class="loading-title">Processing your payment</p><p class="loading-message">Please keep this page open while we securely confirm your order.</p></div>';
                document.body.appendChild(overlay);
            } else if (!show && overlay) {
                overlay.remove();
            }
        }

        async function processCheckout() {
            if (pendingCardPayment) { confirmCardPayment(); return; }
            if (!selectedAddressId) { showGlobalMessage('warning', 'Please select or add a delivery address.'); return; }
            if (!selectedPaymentMethod) { showGlobalMessage('warning', 'Please select a payment method.'); return; }

            const btn = document.getElementById('checkout-btn');
            const formData = new FormData();
            formData.append('address_id', selectedAddressId);
            formData.append('payment_method', selectedPaymentMethod);

            if (selectedPaymentMethod === 'bank_transfer') {
                const payerName = document.getElementById('bank_payer_name').value.trim();
                const paymentApp = document.getElementById('bank_payment_app').value.trim();
                const paymentSource = document.getElementById('bank_payment_source').value.trim();
                const customerAccountNumber = document.getElementById('bank_customer_account_number').value.trim();
                const slip = document.getElementById('payment_slip').files[0];
                if (!payerName || !paymentApp || !paymentSource || !customerAccountNumber || !slip) {
                    showGlobalMessage('warning', 'Please provide all required bank transfer details and upload the slip.');
                    return;
                }
                formData.append('bank_payer_name', payerName);
                formData.append('bank_payment_app', paymentApp);
                formData.append('bank_payment_source', paymentSource);
                formData.append('bank_customer_account_number', customerAccountNumber);
                formData.append('payment_slip', slip);
            }

            setButtonState(btn, 'loading', 'Processing...');
            showLoadingOverlay(true);

            try {
                const response = await fetch('{{ route('checkout.process') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: formData
                });
                const data = await response.json();

                if (data.success) {
                    currentOrderId = data.order_id;
                    if (selectedPaymentMethod === 'card') {
                        showLoadingOverlay(false);
                        prepareStripePayment(data, btn);
                        return;
                    }
                    showLoadingOverlay(false);
                    showGlobalMessage('success', data.message || 'Order placed successfully!');
                    if (data.redirect) setTimeout(() => window.location.href = data.redirect, 1000);
                } else {
                    showLoadingOverlay(false);
                    showGlobalMessage('error', data.message || 'An error occurred.');
                    if (data.errors) {
                        const errorMsg = Object.values(data.errors).flat().join(' ');
                        showGlobalMessage('error', errorMsg);
                    }
                    setButtonState(btn, 'idle', 'Place Order');
                }
            } catch (e) {
                showLoadingOverlay(false);
                showGlobalMessage('error', 'Network error. Please check your connection and try again.');
                setButtonState(btn, 'idle', 'Place Order');
            }
        }

        function prepareStripePayment(data, btn) {
            // ── FAKE / demo mode: no real Stripe keys. Backend already marked the order completed.
            // Skip mounting Stripe Element entirely; just show a success notice and redirect.
            if (data.stripe_is_fake || (data.stripe_client_secret && data.stripe_client_secret.includes('_secret_fake'))) {
                pendingCardPayment = data;
                document.getElementById('card-details').style.display = 'block';
                document.getElementById('stripe-payment-element').style.display = 'none';
                const fakeNote = document.getElementById('stripe-fake-notice');
                if (fakeNote) {
                    fakeNote.style.display = 'block';
                    fakeNote.innerHTML = '&#9992; <strong>Demo mode:</strong> No real card needed. Your payment has been <strong>auto-approved</strong> (fake Stripe). Redirecting to confirmation&hellip; <br><small style="opacity:.8;">Set real <code>STRIPE_KEY</code> / <code>STRIPE_SECRET</code> in <code>.env</code> for live payments.</small>';
                }
                hideStripeError();
                setButtonState(btn, 'loading', 'Payment approved — redirecting...');
                showGlobalMessage('success', 'Demo payment approved. Opening confirmation page...');
                setTimeout(() => { window.location.href = data.redirect; }, 1800);
                return;
            }

            if (!data.stripe_client_secret || !data.stripe_publishable_key || typeof Stripe === 'undefined') {
                showStripeError('Stripe is not configured. Please contact support.');
                setButtonState(btn, 'idle', 'Place Order');
                return;
            }
            hideStripeError();
            // Restore element visibility if coming from a previous fake run
            document.getElementById('stripe-payment-element').style.display = 'block';
            const fakeNote2 = document.getElementById('stripe-fake-notice');
            if (fakeNote2) fakeNote2.style.display = 'none';
            try {
                stripeInstance = Stripe(data.stripe_publishable_key);
                stripeElements = stripeInstance.elements({ clientSecret: data.stripe_client_secret });
                const paymentElement = stripeElements.create('payment', {
                    layout: { type: 'tabs', defaultCollapsed: false }
                });
                paymentElement.mount('#stripe-payment-element');
                paymentElement.on('change', (event) => {
                    if (event.error) showStripeError(event.error.message);
                    else hideStripeError();
                });
                pendingCardPayment = data;
                setButtonState(btn, 'idle', 'Pay Now');
                document.getElementById('card-details').style.display = 'block';
                document.getElementById('stripe-payment-element').scrollIntoView({ behavior: 'smooth', block: 'center' });
                showGlobalMessage('info', 'Enter your card details below to complete payment.');
            } catch (e) {
                showStripeError('Failed to initialize payment form. Please refresh and try again.');
                setButtonState(btn, 'idle', 'Place Order');
            }
        }

        async function confirmCardPayment() {
            const btn = document.getElementById('checkout-btn');
            setButtonState(btn, 'loading', 'Processing payment...');
            showLoadingOverlay(true);
            hideStripeError();

            try {
                const result = await stripeInstance.confirmPayment({
                    elements: stripeElements,
                    redirect: 'if_required',
                    confirmParams: { return_url: pendingCardPayment.redirect || window.location.href }
                });

                if (result.error) {
                    showLoadingOverlay(false);
                    if (result.error.type === 'card_error' || result.error.type === 'validation_error') {
                        showStripeError(result.error.message);
                    } else {
                        showGlobalMessage('error', result.error.message || 'Payment failed. Please try again.');
                    }
                    if (retryCount < MAX_RETRIES) {
                        retryCount++;
                        setButtonState(btn, 'idle', 'Retry Payment (' + retryCount + '/' + MAX_RETRIES + ')');
                        showGlobalMessage('warning', 'You can retry. Attempt ' + retryCount + ' of ' + MAX_RETRIES + '.');
                    } else {
                        setButtonState(btn, 'idle', 'Place Order');
                        showGlobalMessage('error', 'Maximum retries reached. Please try a different payment method or contact support.');
                        pendingCardPayment = null;
                        retryCount = 0;
                    }
                    return;
                }

                if (result.paymentIntent) {
                    const status = result.paymentIntent.status;
                    if (status === 'succeeded') {
                        showGlobalMessage('success', 'Payment successful! Redirecting...');
                        try {
                            await fetch('{{ url('payment/stripe/confirm') }}', {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                                body: JSON.stringify({ payment_id: pendingCardPayment.payment_id || null, payment_intent_id: result.paymentIntent.id })
                            });
                        } catch (e) {}
                        setTimeout(() => window.location.href = pendingCardPayment.redirect, 1500);
                        return;
                    }
                    if (status === 'processing') {
                        showGlobalMessage('info', 'Payment is being processed. You will receive a confirmation shortly.');
                        setTimeout(() => window.location.href = pendingCardPayment.redirect, 2000);
                        return;
                    }
                    if (status === 'requires_action') {
                        showLoadingOverlay(false);
                        showGlobalMessage('warning', 'Additional verification required. Please follow the prompts.');
                        setButtonState(btn, 'idle', 'Complete Verification');
                        return;
                    }
                }

                showLoadingOverlay(false);
                showGlobalMessage('error', 'Payment could not be completed. Please try again.');
                setButtonState(btn, 'idle', 'Retry Payment');
            } catch (e) {
                showLoadingOverlay(false);
                const msg = (e && e.message) ? e.message : String(e);
                console.error('[Stripe confirmPayment exception]', e, { paymentId: pendingCardPayment?.payment_id, orderId: currentOrderId });
                showGlobalMessage('error', msg || 'An unexpected error occurred. Please try again.');
                showStripeError(msg || 'Unexpected error. Check console for details.');
                setButtonState(btn, 'idle', 'Retry Payment');
            }
        }

        async function retryStripePayment() {
            if (!currentOrderId) { showGlobalMessage('error', 'No order to retry.'); return; }
            const btn = document.getElementById('checkout-btn');
            setButtonState(btn, 'loading', 'Retrying...');
            showLoadingOverlay(true);
            try {
                const response = await fetch('{{ url('payment/stripe/retry') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order_id: currentOrderId })
                });
                const data = await response.json();
                showLoadingOverlay(false);
                if (data.success) {
                    retryCount = 0;
                    prepareStripePayment({ stripe_client_secret: data.client_secret, stripe_publishable_key: data.publishable_key, redirect: pendingCardPayment?.redirect, payment_id: data.payment_id }, btn);
                    showGlobalMessage('info', 'New payment session created. Enter your card details.');
                } else {
                    showGlobalMessage('error', data.message || 'Retry failed.');
                    setButtonState(btn, 'idle', 'Place Order');
                }
            } catch (e) {
                showLoadingOverlay(false);
                showGlobalMessage('error', 'Network error during retry.');
                setButtonState(btn, 'idle', 'Place Order');
            }
        }

        window.addEventListener('load', function() {
            if (selectedAddressId) selectAddress(selectedAddressId);
            selectPaymentMethod('cod');
        });
    </script>
@endpush

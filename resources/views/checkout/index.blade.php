@extends('layouts.storefront')

@section('title', 'Checkout - VandoraX')

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
                    <!-- JazzCash Option -->
                    <label class="payment-option" id="payment-jazzcash-label" onclick="selectPaymentMethod('jazzcash')" style="display: flex; align-items: flex-start; gap: 12px; padding: 16px; border: 2px solid var(--border-medium); border-radius: 10px; cursor: pointer; transition: all 0.3s ease; background: rgba(255, 255, 255, 0.03);">
                        <input type="radio" name="payment_method" value="jazzcash" onchange="selectPaymentMethod('jazzcash')" style="margin-top: 4px; cursor: pointer; accent-color: var(--accent);">
                        <div style="flex: 1;">
                            <div style="font-weight: 700; font-size: 15px; color: var(--text-primary); margin-bottom: 4px;">📱 JazzCash Mobile Payment</div>
                            <div style="font-size: 13px; color: var(--text-secondary);">Pay directly from your JazzCash account - Instant confirmation</div>
                        </div>
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
                <div id="payment-details" style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border-light);">
                    <!-- JazzCash Details -->
                    <div id="jazzcash-details" style="display: none;">
                        <div class="form-group">
                            <label class="form-label form-required">JazzCash Account Number</label>
                            <input type="tel" id="jazzcash_number" name="jazzcash_number" placeholder="03001234567 or 92XXXXXXXXX" class="form-input" pattern="^(\+92|0|92)?3\d{9}$">
                            <div style="font-size: 12px; color: var(--text-secondary); margin-top: 6px;">Format: 03XXXXXXXXX or 92XXXXXXXXXX</div>
                        </div>
                    </div>

                    <!-- Bank Transfer Details -->
                    <div id="bank_transfer-details" style="display: none;">
                        <div style="background: rgba(255, 165, 0, 0.08); padding: 16px; border-radius: 10px; border-left: 3px solid var(--accent); margin-bottom: 16px;">
                            <h4 style="font-size: 14px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary);">Bank Account Details</h4>
                            <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">
                                <div>
                                    <span style="color: var(--text-secondary);">Bank Name:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ config('bank-transfer.bank_name') }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">Account Title:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ config('bank-transfer.account_name') }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">Account Number:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ config('bank-transfer.account_number') }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">IBAN:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ config('bank-transfer.iban') }}</span>
                                </div>
                                <div>
                                    <span style="color: var(--text-secondary);">Swift Code:</span>
                                    <span style="font-weight: 700; color: var(--text-primary); margin-left: 8px;">{{ config('bank-transfer.swift_code') }}</span>
                                </div>
                            </div>
                        </div>
                        <p style="font-size: 13px; color: var(--text-secondary); line-height: 1.6;">After placing your order, you'll receive an email with transfer instructions and a reference number. Please include the reference number with your transfer so we can match and confirm your payment.</p>
                    </div>
                </div>
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
<script>
    (function () {
        function centerCheckoutPage() {
            var el = document.getElementById('checkoutPage');
            if (!el) return;
            el.style.marginLeft = '0px';
            var docW = document.documentElement.clientWidth;
            var w = Math.min(1400, docW - 32);
            el.style.width = w + 'px';
            var left = el.getBoundingClientRect().left + window.pageXOffset;
            el.style.marginLeft = (((docW - w) / 2) - left) + 'px';
        }
        centerCheckoutPage();
        window.addEventListener('load', centerCheckoutPage);
        window.addEventListener('resize', centerCheckoutPage);
    })();
</script>
@endsection

@push('scripts')
    <script>
        let selectedAddressId = @json($defaultAddress ? $defaultAddress->id : null);
        let selectedPaymentMethod = 'cod'; // Default to Cash on Delivery

        function selectAddress(addressId) {
            selectedAddressId = addressId;
            document.querySelectorAll('[id^="addr-option-"]').forEach(el => {
                el.classList.remove('selected');
            });
            document.getElementById('addr-option-' + addressId).classList.add('selected');
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;

            // Update label styles
            document.querySelectorAll('.payment-option').forEach(el => {
                el.style.borderColor = 'var(--border-medium)';
                el.style.background = 'rgba(255, 255, 255, 0.03)';
            });

            const selectedLabel = document.getElementById('payment-' + method + '-label');
            if (selectedLabel) {
                selectedLabel.style.borderColor = 'var(--accent)';
                selectedLabel.style.background = 'rgba(255, 165, 0, 0.12)';
            }

            // Hide all payment details
            document.getElementById('jazzcash-details').style.display = 'none';
            document.getElementById('bank_transfer-details').style.display = 'none';

            // Show selected method details
            if (method === 'jazzcash') {
                document.getElementById('jazzcash-details').style.display = 'block';
            } else if (method === 'bank_transfer') {
                document.getElementById('bank_transfer-details').style.display = 'block';
            }
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
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Address saved successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(e => {
                console.error('Error:', e);
                alert('An error occurred. Please try again.');
            });
        }

        function processCheckout() {
            if (!selectedAddressId) {
                alert('Please select or add a delivery address.');
                return;
            }

            if (!selectedPaymentMethod) {
                alert('Please select a payment method.');
                return;
            }

            // Validate payment method specific fields
            if (selectedPaymentMethod === 'jazzcash') {
                const mobileNumber = document.getElementById('jazzcash_number').value;
                if (!mobileNumber) {
                    alert('Please enter your JazzCash account number.');
                    return;
                }
            }

            const btn = document.getElementById('checkout-btn');
            btn.disabled = true;
            btn.textContent = 'Processing...';

            const payloadData = {
                address_id: selectedAddressId,
                payment_method: selectedPaymentMethod
            };

            // Add payment method specific data
            if (selectedPaymentMethod === 'jazzcash') {
                payloadData.jazzcash_number = document.getElementById('jazzcash_number').value;
            }

            fetch('{{ route('checkout.process') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payloadData)
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (data.payment_method === 'jazzcash' && data.redirect) {
                        // Redirect to JazzCash payment portal
                        window.location.href = data.redirect;
                    } else {
                        alert(data.message);
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    }
                } else {
                    alert('Error: ' + data.message);
                    if (data.issues) {
                        console.log('Issues:', data.issues);
                    }
                    btn.disabled = false;
                    btn.textContent = 'Place Order';
                }
            })
            .catch(e => {
                console.error('Error:', e);
                alert('An error occurred. Please try again.');
                btn.disabled = false;
                btn.textContent = 'Place Order';
            });
        }

        window.addEventListener('load', function() {
            if (selectedAddressId) {
                selectAddress(selectedAddressId);
            }
            // Initialize payment method display
            selectPaymentMethod('cod');
        });
    </script>
@endpush
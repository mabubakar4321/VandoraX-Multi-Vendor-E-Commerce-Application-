<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - VandoraX</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #FFFFFF;
            color: #000000;
            line-height: 1.6;
        }

        /* HEADER */
        .header {
            background: #FFFFFF;
            border-bottom: 1px solid #E8E8E8;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #000000;
            text-decoration: none;
        }

        .nav-back {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #000000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: opacity 0.3s ease;
        }

        .nav-back:hover {
            opacity: 0.6;
        }

        /* PROGRESS STEPS */
        .checkout-progress {
            max-width: 1400px;
            margin: 48px auto 0;
            padding: 0 48px;
        }

        .steps {
            display: flex;
            justify-content: space-between;
            position: relative;
            margin-bottom: 48px;
        }

        .steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #E8E8E8;
            z-index: 0;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            flex: 1;
            position: relative;
            z-index: 1;
        }

        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #E8E8E8;
            color: #666666;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .step.active .step-number {
            background: #000000;
            color: #FFFFFF;
        }

        .step.completed .step-number {
            background: #000000;
            color: #FFFFFF;
        }

        .step.completed .step-number::after {
            content: '✓';
        }

        .step-label {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666666;
            text-align: center;
        }

        .step.active .step-label {
            color: #000000;
        }

        /* CONTAINER */
        .checkout-container {
            max-width: 1400px;
            margin: 0 auto 48px;
            padding: 0 48px;
        }

        .checkout-layout {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 48px;
        }

        /* STEP CONTENT */
        .step-content {
            display: none;
        }

        .step-content.active {
            display: block;
        }

        /* SECTIONS */
        .section {
            background: #FFFFFF;
            border: 1px solid #E8E8E8;
            margin-bottom: 32px;
        }

        .section-header {
            padding: 24px;
            border-bottom: 1px solid #E8E8E8;
            background: #F8F7F5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-body {
            padding: 24px;
        }

        /* ADDRESS CARDS */
        .address-list {
            display: grid;
            gap: 16px;
        }

        .address-card {
            padding: 20px;
            border: 2px solid #E8E8E8;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .address-card:hover {
            border-color: #000000;
        }

        .address-card.selected {
            border-color: #000000;
            background: #F8F7F5;
        }

        .address-card.selected::after {
            content: '✓';
            position: absolute;
            top: 16px;
            right: 16px;
            width: 24px;
            height: 24px;
            background: #000000;
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .address-label {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666666;
            margin-bottom: 8px;
        }

        .address-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .address-details {
            font-size: 14px;
            color: #666666;
            line-height: 1.8;
        }

        .address-phone {
            font-size: 14px;
            color: #000000;
            margin-top: 8px;
        }

        .btn-new-address {
            width: 100%;
            padding: 16px;
            background: transparent;
            border: 2px dashed #E8E8E8;
            color: #000000;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-new-address:hover {
            border-color: #000000;
        }

        /* PAYMENT METHODS */
        .payment-methods {
            display: grid;
            gap: 16px;
        }

        .payment-card {
            padding: 20px;
            border: 2px solid #E8E8E8;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .payment-card:hover {
            border-color: #000000;
        }

        .payment-card.selected {
            border-color: #000000;
            background: #F8F7F5;
        }

        .payment-card.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .payment-card.disabled:hover {
            border-color: #E8E8E8;
        }

        .payment-icon {
            font-size: 32px;
        }

        .payment-info {
            flex: 1;
        }

        .payment-name {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .payment-description {
            font-size: 13px;
            color: #666666;
        }

        .payment-card.selected::after {
            content: '✓';
            position: absolute;
            top: 50%;
            right: 20px;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background: #000000;
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        /* ORDER REVIEW */
        .review-section {
            margin-bottom: 24px;
            padding: 24px;
            border: 1px solid #E8E8E8;
            background: #F8F7F5;
        }

        .review-title {
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }

        .review-content {
            font-size: 14px;
            line-height: 1.8;
            color: #333333;
        }

        .review-change-link {
            color: #000000;
            text-decoration: underline;
            cursor: pointer;
            font-weight: 600;
        }

        /* VENDOR SECTION */
        .vendor-section {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #E8E8E8;
        }

        .vendor-section:last-child {
            border-bottom: none;
        }

        .vendor-name {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }

        .item-name {
            color: #666666;
        }

        .item-price {
            font-weight: 600;
        }

        .vendor-subtotal {
            display: flex;
            justify-content: space-between;
            padding-top: 12px;
            margin-top: 12px;
            border-top: 1px solid #E8E8E8;
            font-weight: 700;
        }

        /* SUMMARY */
        .summary-totals {
            padding: 24px;
            border-top: 2px solid #E8E8E8;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 16px;
            font-size: 15px;
        }

        .summary-row.total {
            font-size: 20px;
            font-weight: 700;
            padding-top: 16px;
            border-top: 1px solid #E8E8E8;
        }

        /* BUTTONS */
        .step-actions {
            display: flex;
            gap: 16px;
            margin-top: 32px;
        }

        .btn-primary {
            flex: 1;
            padding: 20px;
            background: #000000;
            color: #FFFFFF;
            border: none;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #333333;
        }

        .btn-primary:disabled {
            background: #CCCCCC;
            cursor: not-allowed;
        }

        .btn-secondary {
            flex: 1;
            padding: 20px;
            background: transparent;
            color: #000000;
            border: 1px solid #E8E8E8;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            border-color: #000000;
        }

        /* LOADING */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #FFFFFF;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .checkout-layout {
                grid-template-columns: 1fr;
            }

            .steps {
                flex-wrap: wrap;
                gap: 24px;
            }

            .step {
                flex: 1 1 45%;
                min-width: 150px;
            }
        }

        @media (max-width: 768px) {
            .nav-container,
            .checkout-progress,
            .checkout-container {
                padding: 16px 24px;
            }

            .step-label {
                font-size: 11px;
            }

            .step-number {
                width: 36px;
                height: 36px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">VANDORAX</a>
            <a href="{{ route('cart.index') }}" class="nav-back">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Cart
            </a>
        </div>
    </header>

    <!-- Progress Steps -->
    <div class="checkout-progress">
        <div class="steps">
            <div class="step active" data-step="1">
                <div class="step-number">1</div>
                <div class="step-label">Cart & Address</div>
            </div>
            <div class="step" data-step="2">
                <div class="step-number">2</div>
                <div class="step-label">Payment Method</div>
            </div>
            <div class="step" data-step="3">
                <div class="step-number">3</div>
                <div class="step-label">Review Order</div>
            </div>
            <div class="step" data-step="4">
                <div class="step-number">4</div>
                <div class="step-label">Process Payment</div>
            </div>
        </div>
    </div>

    <!-- Checkout Content -->
    <div class="checkout-container">
        <div class="checkout-layout">
            <!-- Left Column: Steps Content -->
            <div class="checkout-main">

                <!-- STEP 1: Cart & Address -->
                <div class="step-content active" id="step1">
                    <div class="section">
                        <div class="section-header">
                            <h2 class="section-title">Delivery Address</h2>
                        </div>
                        <div class="section-body">
                            @if($addresses->count() > 0)
                                <div class="address-list" id="addressList">
                                    @foreach($addresses as $address)
                                        <div class="address-card {{ $loop->first ? 'selected' : '' }}"
                                             data-address-id="{{ $address->id }}"
                                             onclick="selectAddress({{ $address->id }})">
                                            @if($address->label)
                                                <div class="address-label">{{ $address->label }}</div>
                                            @endif
                                            <div class="address-name">{{ $address->full_name }}</div>
                                            <div class="address-details">
                                                {{ $address->address_line_1 }}@if($address->address_line_2), {{ $address->address_line_2 }}@endif<br>
                                                {{ $address->city }}, {{ $address->state_province }} {{ $address->postal_code }}<br>
                                                {{ $address->country }}
                                            </div>
                                            <div class="address-phone">📞 {{ $address->phone }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color: #666; margin-bottom: 16px;">You don't have any saved addresses yet.</p>
                            @endif

                            <button class="btn-new-address" style="margin-top: 16px;" onclick="alert('Add address form will be implemented')">
                                + Add New Address
                            </button>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button class="btn-primary" onclick="goToStep(2)" id="step1NextBtn" {{ $addresses->count() == 0 ? 'disabled' : '' }}>
                            Continue to Payment
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Payment Method -->
                <div class="step-content" id="step2">
                    <div class="section">
                        <div class="section-header">
                            <h2 class="section-title">Select Payment Method</h2>
                        </div>
                        <div class="section-body">
                            <div class="payment-methods">
                                <div class="payment-card selected" data-payment="cod" onclick="selectPayment('cod')">
                                    <div class="payment-icon">💵</div>
                                    <div class="payment-info">
                                        <div class="payment-name">Cash on Delivery</div>
                                        <div class="payment-description">Pay with cash when your order is delivered</div>
                                    </div>
                                </div>

                                <div class="payment-card" data-payment="bank_transfer" onclick="selectPayment('bank_transfer')">
                                    <div class="payment-icon">🏦</div>
                                    <div class="payment-info">
                                        <div class="payment-name">Bank Transfer</div>
                                        <div class="payment-description">Direct bank transfer to our account</div>
                                    </div>
                                </div>

                                <div class="payment-card disabled" data-payment="card">
                                    <div class="payment-icon">💳</div>
                                    <div class="payment-info">
                                        <div class="payment-name">Credit/Debit Card</div>
                                        <div class="payment-description">Pay securely with your card (Coming Soon)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button class="btn-secondary" onclick="goToStep(1)">Back</button>
                        <button class="btn-primary" onclick="goToStep(3)">Continue to Review</button>
                    </div>
                </div>

                <!-- STEP 3: Review Order -->
                <div class="step-content" id="step3">
                    <div class="review-section">
                        <div class="review-title">📍 Delivery Address</div>
                        <div class="review-content" id="selectedAddressReview">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <a class="review-change-link" onclick="goToStep(1)">Change</a>
                    </div>

                    <div class="review-section">
                        <div class="review-title">💳 Payment Method</div>
                        <div class="review-content" id="selectedPaymentReview">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <a class="review-change-link" onclick="goToStep(2)">Change</a>
                    </div>

                    <div class="section">
                        <div class="section-header">
                            <h2 class="section-title">Order Items</h2>
                        </div>
                        <div class="section-body">
                            @foreach($itemsByVendor as $vendorId => $items)
                                @php
                                    $vendor = $items->first()->product->vendor;
                                    $vendorSubtotal = $items->sum(fn($item) => $item->price * $item->quantity);
                                @endphp

                                <div class="vendor-section">
                                    <div class="vendor-name">{{ $vendor->store_name ?? 'Store' }}</div>

                                    @foreach($items as $item)
                                        <div class="order-item">
                                            <span class="item-name">
                                                {{ $item->product->name }}
                                                @if($item->variant)
                                                    <br><small>({{ $item->variant->variant_value }})</small>
                                                @endif
                                                × {{ $item->quantity }}
                                            </span>
                                            <span class="item-price">Rs. {{ number_format($item->price * $item->quantity, 0) }}</span>
                                        </div>
                                    @endforeach

                                    <div class="vendor-subtotal">
                                        <span>{{ $vendor->store_name ?? 'Store' }} Subtotal</span>
                                        <span>Rs. {{ number_format($vendorSubtotal, 0) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="step-actions">
                        <button class="btn-secondary" onclick="goToStep(2)">Back</button>
                        <button class="btn-primary" onclick="goToStep(4)">Proceed to Payment</button>
                    </div>
                </div>

                <!-- STEP 4: Process Payment -->
                <div class="step-content" id="step4">
                    <div class="section">
                        <div class="section-header">
                            <h2 class="section-title">Processing Order</h2>
                        </div>
                        <div class="section-body">
                            <div style="text-align: center; padding: 48px 24px;">
                                <div style="font-size: 64px; margin-bottom: 24px;">⏳</div>
                                <h3 style="font-size: 24px; font-weight: 700; margin-bottom: 16px;">Processing Your Order</h3>
                                <p style="color: #666; margin-bottom: 32px;">Please wait while we process your order...</p>
                                <div id="paymentMethodInfo" style="padding: 24px; background: #F8F7F5; border: 1px solid #E8E8E8;">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-actions">
                        <button class="btn-secondary" onclick="goToStep(3)">Back</button>
                        <button class="btn-primary" onclick="placeOrder()">Confirm & Place Order</button>
                    </div>
                </div>

            </div>

            <!-- Right Column: Order Summary (Sticky) -->
            <div class="checkout-sidebar">
                <div class="section" style="position: sticky; top: 120px;">
                    <div class="section-header">
                        <h2 class="section-title">Order Summary</h2>
                    </div>
                    <div class="section-body">
                        @foreach($itemsByVendor as $vendorId => $items)
                            @php
                                $vendor = $items->first()->product->vendor;
                                $vendorSubtotal = $items->sum(fn($item) => $item->price * $item->quantity);
                            @endphp

                            <div class="vendor-section">
                                <div class="vendor-name">{{ $vendor->store_name ?? 'Store' }}</div>
                                <div class="order-item">
                                    <span class="item-name">{{ $items->count() }} {{ Str::plural('item', $items->count()) }}</span>
                                    <span class="item-price">Rs. {{ number_format($vendorSubtotal, 0) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal ({{ $totalItems }} items)</span>
                            <span>Rs. {{ number_format($subtotal, 0) }}</span>
                        </div>

                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Free</span>
                        </div>

                        <div class="summary-row total">
                            <span>Total</span>
                            <span>Rs. {{ number_format($subtotal, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let currentStep = 1;
        let selectedAddressId = {{ $defaultAddress ? $defaultAddress->id : ($addresses->first()->id ?? 'null') }};
        let selectedPaymentMethod = 'cod';

        // Store address data for review
        const addresses = {!! json_encode($addresses->map(function($addr) {
            return [
                'id' => $addr->id,
                'full_name' => $addr->full_name,
                'address_line_1' => $addr->address_line_1,
                'address_line_2' => $addr->address_line_2,
                'city' => $addr->city,
                'state_province' => $addr->state_province,
                'postal_code' => $addr->postal_code,
                'country' => $addr->country,
                'phone' => $addr->phone,
            ];
        })) !!};

        const paymentMethods = {
            'cod': {
                name: 'Cash on Delivery',
                description: 'Pay with cash when your order is delivered',
                icon: '💵'
            },
            'bank_transfer': {
                name: 'Bank Transfer',
                description: 'Direct bank transfer to our account',
                icon: '🏦'
            }
        };

        function showLoading() {
            document.getElementById('loading').classList.add('active');
        }

        function hideLoading() {
            document.getElementById('loading').classList.remove('active');
        }

        function goToStep(step) {
            // Hide all step contents
            document.querySelectorAll('.step-content').forEach(el => {
                el.classList.remove('active');
            });

            // Show selected step
            document.getElementById(`step${step}`).classList.add('active');

            // Update step indicators
            document.querySelectorAll('.step').forEach(el => {
                const stepNum = parseInt(el.dataset.step);
                el.classList.remove('active', 'completed');

                if (stepNum < step) {
                    el.classList.add('completed');
                } else if (stepNum === step) {
                    el.classList.add('active');
                }
            });

            currentStep = step;

            // Update review content when going to step 3
            if (step === 3) {
                updateReviewContent();
            }

            // Update payment info when going to step 4
            if (step === 4) {
                updatePaymentInfo();
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function selectAddress(addressId) {
            document.querySelectorAll('.address-card').forEach(card => {
                card.classList.remove('selected');
            });

            document.querySelector(`[data-address-id="${addressId}"]`).classList.add('selected');
            selectedAddressId = addressId;

            // Enable next button
            document.getElementById('step1NextBtn').disabled = false;
        }

        function selectPayment(method) {
            if (document.querySelector(`[data-payment="${method}"]`).classList.contains('disabled')) {
                return;
            }

            document.querySelectorAll('.payment-card').forEach(card => {
                card.classList.remove('selected');
            });

            document.querySelector(`[data-payment="${method}"]`).classList.add('selected');
            selectedPaymentMethod = method;
        }

        function updateReviewContent() {
            // Update address review
            const address = addresses.find(a => a.id === selectedAddressId);
            if (address) {
                const addressHtml = `
                    <strong>${address.full_name}</strong><br>
                    ${address.address_line_1}${address.address_line_2 ? ', ' + address.address_line_2 : ''}<br>
                    ${address.city}, ${address.state_province} ${address.postal_code}<br>
                    ${address.country}<br>
                    📞 ${address.phone}
                `;
                document.getElementById('selectedAddressReview').innerHTML = addressHtml;
            }

            // Update payment review
            const payment = paymentMethods[selectedPaymentMethod];
            if (payment) {
                const paymentHtml = `
                    ${payment.icon} <strong>${payment.name}</strong><br>
                    ${payment.description}
                `;
                document.getElementById('selectedPaymentReview').innerHTML = paymentHtml;
            }
        }

        function updatePaymentInfo() {
            const payment = paymentMethods[selectedPaymentMethod];
            let infoHtml = '';

            if (selectedPaymentMethod === 'cod') {
                infoHtml = `
                    <div style="text-align: left;">
                        <h4 style="font-weight: 700; margin-bottom: 12px;">Cash on Delivery</h4>
                        <p style="color: #666; margin-bottom: 16px;">Please keep the exact amount ready when the delivery arrives.</p>
                        <ul style="color: #666; line-height: 1.8; padding-left: 20px;">
                            <li>Our delivery partner will collect the payment</li>
                            <li>Please inspect your items before payment</li>
                            <li>Keep your order ID ready for reference</li>
                        </ul>
                    </div>
                `;
            } else if (selectedPaymentMethod === 'bank_transfer') {
                infoHtml = `
                    <div style="text-align: left;">
                        <h4 style="font-weight: 700; margin-bottom: 12px;">Bank Transfer Details</h4>
                        <p style="color: #666; margin-bottom: 16px;">Please transfer the order amount to the following account:</p>
                        <div style="background: #FFFFFF; padding: 16px; border: 1px solid #E8E8E8; margin-bottom: 16px;">
                            <div style="margin-bottom: 8px;"><strong>Bank:</strong> Example Bank</div>
                            <div style="margin-bottom: 8px;"><strong>Account Name:</strong> VandoraX Marketplace</div>
                            <div style="margin-bottom: 8px;"><strong>Account Number:</strong> 1234567890</div>
                            <div><strong>IBAN:</strong> PK12 EXMP 1234 5678 9012 3456</div>
                        </div>
                        <p style="color: #666; font-size: 13px;">After placing your order, you'll receive bank details via email. Please complete the transfer within 24 hours.</p>
                    </div>
                `;
            }

            document.getElementById('paymentMethodInfo').innerHTML = infoHtml;
        }

        async function placeOrder() {
            if (!selectedAddressId) {
                alert('Please select a delivery address.');
                return;
            }

            showLoading();

            try {
                const response = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        address_id: selectedAddressId,
                        payment_method: selectedPaymentMethod,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    alert('Order placed successfully!\n\nOrder processing will be fully implemented in the next phase.');
                    window.location.href = '{{ route("cart.index") }}';
                } else {
                    if (data.issues && data.issues.length > 0) {
                        let message = 'Unable to proceed. Stock issues:\n\n';
                        data.issues.forEach(issue => {
                            message += `• ${issue.product_name}\n  ${issue.message}\n\n`;
                        });
                        alert(message);
                    } else {
                        alert(data.message || 'Unable to process order');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                hideLoading();
            }
        }
    </script>
</body>
</html>

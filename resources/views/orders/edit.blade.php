@extends('layouts.app')

@section('title', 'Edit Order')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Order</h1>
                    <p class="text-gray-600 mt-1">{{ $order->order_number }}</p>
                </div>
                <a href="{{ route('order.index') }}" class="text-blue-600 hover:text-blue-800 transition">
                    ← Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <h3 class="text-red-800 font-semibold">Errors:</h3>
                <ul class="text-red-700 mt-2 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('order.update', $order) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Order Items -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">📦 Order Items</h2>
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex gap-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($item->product->mainImage())
                                        <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-20 h-20 object-cover rounded">
                                    @else
                                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">No image</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $item->product->name }}</h3>
                                    @if($item->variant)
                                        <p class="text-sm text-gray-600 mt-1">{{ $item->variant->variant_value }}</p>
                                    @endif
                                    <p class="text-sm text-gray-600 mt-2">
                                        Unit Price: <span class="font-semibold">₨{{ number_format($item->price_at_purchase) }}</span>
                                    </p>
                                </div>

                                <!-- Quantity Input -->
                                <div class="flex flex-col items-end gap-2">
                                    <label class="text-sm font-semibold text-gray-700">Quantity</label>
                                    <input type="number"
                                           name="quantity[{{ $item->id }}]"
                                           value="{{ $item->quantity }}"
                                           min="1"
                                           class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <p class="text-sm text-gray-600 mt-2">
                                        Total: <span class="font-semibold">₨{{ number_format($item->total_amount) }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">🏠 Delivery Address</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="address_full_name" value="{{ $order->address->full_name }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="address_phone" value="{{ $order->address->phone }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Address Line 1</label>
                        <input type="text" name="address_line_1" value="{{ $order->address->address_line_1 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Address Line 2 (Optional)</label>
                        <input type="text" name="address_line_2" value="{{ $order->address->address_line_2 }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                        <input type="text" name="address_city" value="{{ $order->address->city }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">State/Province</label>
                        <input type="text" name="address_state_province" value="{{ $order->address->state_province }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="address_postal_code" value="{{ $order->address->postal_code }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Country</label>
                        <input type="text" name="address_country" value="{{ $order->address->country }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Delivery Instructions (Optional)</label>
                        <textarea name="address_delivery_instructions" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $order->address->delivery_instructions }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Method (read-only) -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">💳 Payment Method</h2>
                <p class="text-sm text-gray-600">Payment method cannot be changed after the order is placed.</p>
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-50 p-4 font-semibold text-gray-900">
                    @if($order->payment)
                        {{ $order->payment->payment_method === 'card' ? 'Stripe Card' : ucfirst(str_replace('_', ' ', $order->payment->payment_method)) }}
                    @else
                        Cash on Delivery
                    @endif
                </div>
            </div>

            <div class="hidden">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">💳 Payment Method</h2>
                <div class="space-y-3">
                    @php
                        $payment = $order->payment;
                        $hasCOD = !$payment;
                        $storedPaymentMethod = $hasCOD ? 'cod' : ($payment->payment_method ?? 'cod');
                        $submittedPaymentMethod = old('payment_method', $storedPaymentMethod);
                        $paymentMethod = in_array($submittedPaymentMethod, ['cod', 'bank_transfer'], true)
                            ? $submittedPaymentMethod
                            : 'cod';

                        $bankPayerName = $payment ? ($payment->bank_payer_name ?? '') : '';
                        $bankPaymentApp = $payment ? ($payment->bank_payment_app ?? '') : '';
                        $bankPaymentSource = $payment ? ($payment->bank_payment_source ?? '') : '';
                        $bankCustomerAccountNumber = $payment ? ($payment->bank_customer_account_number ?? '') : '';

                        $bankDetails = config('bank-transfer.bank');
                    @endphp

                    @foreach(['cod' => ['label' => '💵 Cash on Delivery', 'desc' => 'Pay when your order is delivered'], 'bank_transfer' => ['label' => '🏦 Bank Transfer', 'desc' => 'Transfer directly to our bank account']] as $key => $option)
                        <label class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition {{ $paymentMethod === $key ? 'border-blue-500 bg-blue-50' : '' }}" onclick="this.querySelector('input').checked=true">
                            <input type="radio" name="payment_method" value="{{ $key }}" {{ $paymentMethod === $key ? 'checked' : '' }} class="w-4 h-4" onchange="togglePaymentDetails(this.value)">
                            <div class="flex-1">
                                <div class="font-semibold text-gray-900">{{ $option['label'] }}</div>
                                <div class="text-sm text-gray-600">{{ $option['desc'] }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Payment Details & Slip Upload (Dynamic) -->
            <div id="payment-details-section" class="hidden bg-white rounded-lg shadow p-6 mb-6" style="display: none;">

                <!-- Bank Transfer Fields -->
                <div id="bank_transfer-container" style="display: {{ $paymentMethod === 'bank_transfer' ? 'block' : 'none' }};">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">🏦 Bank Transfer Details</h2>
                    @if(!empty($bankDetails['name']))
                        <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                            <h4 class="font-semibold text-gray-900 mb-2">Company Bank Account Information</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-700">
                                <p><span class="text-gray-500">Bank Name:</span> <span class="font-semibold">{{ $bankDetails['name'] }}</span></p>
                                <p><span class="text-gray-500">Account Title:</span> <span class="font-semibold">{{ $bankDetails['account_title'] }}</span></p>
                                <p><span class="text-gray-500">Account Number:</span> <span class="font-semibold">{{ $bankDetails['account_number'] }}</span></p>
                                <p><span class="text-gray-500">IBAN:</span> <span class="font-semibold">{{ $bankDetails['iban'] }}</span></p>
                                <p><span class="text-gray-500">SWIFT Code:</span> <span class="font-semibold">{{ $bankDetails['swift_code'] }}</span></p>
                                @if(!empty($bankDetails['branch']))
                                    <p><span class="text-gray-500">Branch:</span> <span class="font-semibold">{{ $bankDetails['branch'] }}</span></p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Name <span class="text-red-500">*</span></label>
                                <input type="text"
                                        id="bank_payer_name"
                                        name="bank_payer_name"
                                        value="{{ old('bank_payer_name', $bankPayerName) }}"
                                        placeholder="Name of the person making the payment"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment App / Platform <span class="text-red-500">*</span></label>
                                <input type="text"
                                        id="bank_payment_app"
                                        name="bank_payment_app"
                                        value="{{ old('bank_payment_app', $bankPaymentApp) }}"
                                        placeholder="e.g. HBL Mobile, Easypaisa, ATM"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Source <span class="text-red-500">*</span></label>
                                <input type="text"
                                       id="bank_payment_source"
                                       name="bank_payment_source"
                                       value="{{ old('bank_payment_source', $bankPaymentSource) }}"
                                       placeholder="e.g. Personal bank account"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Customer Account Number <span class="text-red-500">*</span></label>
                                <input type="text"
                                       id="bank_customer_account_number"
                                       name="bank_customer_account_number"
                                       value="{{ old('bank_customer_account_number', $bankCustomerAccountNumber) }}"
                                       placeholder="Account number used for the payment"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Amount (₨) <span class="text-red-500">*</span></label>
                            <input type="text"
                                   readonly
                                   value="₨{{ number_format($order->total_amount, 2) }}"
                                   class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-700 font-semibold">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Bank Payment Slip / Receipt Image <span class="text-red-500">*</span></label>
                            <input type="file"
                                   id="payment_slip"
                                   name="payment_slip"
                                   accept="image/*"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @if($payment && $payment->payment_slip_path)
                                <div class="mt-2">
                                    <span class="text-xs text-green-600 font-semibold">✓ Current slip uploaded:</span>
                                    <a href="{{ asset('storage/' . $payment->payment_slip_path) }}" target="_blank" class="text-xs text-blue-600 underline ml-1">View Slip</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>₨{{ number_format($order->total_amount) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span class="text-green-600 font-semibold">Free</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span>Included</span>
                    </div>
                    <div class="border-t border-gray-200 pt-3 flex justify-between text-lg font-bold text-gray-900">
                        <span>Total</span>
                        <span>₨{{ number_format($order->total_amount) }}</span>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Status</h2>
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                        Status: {{ ucfirst($order->status) }}
                    </span>
                    <p class="text-sm text-gray-600">
                        Ordered on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex gap-4">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition">
                        ✅ Save All Changes
                    </button>
                    <a href="{{ route('order.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle payment details section visibility, clear fields, and manage required attributes
function togglePaymentDetails(method, clearFields = true) {
    const detailsSection = document.getElementById('payment-details-section');
    const bankContainer = document.getElementById('bank_transfer-container');

    // Bank Transfer Input fields
    const bankPayerName = document.getElementById('bank_payer_name');
    const bankPaymentApp = document.getElementById('bank_payment_app');
    const bankPaymentSource = document.getElementById('bank_payment_source');
    const bankCustomerAccountNumber = document.getElementById('bank_customer_account_number');
    const paymentSlip = document.getElementById('payment_slip');
    const existingPaymentMethod = @json($paymentMethod);
    const hasExistingSlip = @json((bool) ($payment && $payment->payment_slip_path));

    // Reset required attributes. Clear values only after the customer switches methods.
    [bankPayerName, bankPaymentApp, bankPaymentSource, bankCustomerAccountNumber, paymentSlip].forEach(el => {
        if (el) {
            el.required = false;
            if (clearFields && el.type !== 'file') {
                el.value = '';
            }
        }
    });

    if (method === 'cod') {
        // Hide entire payment details section for COD
        detailsSection.style.display = 'none';
        bankContainer.style.display = 'none';
    } else if (method === 'bank_transfer') {
        detailsSection.style.display = 'block';
        bankContainer.style.display = 'block';

        if (bankPayerName) bankPayerName.required = true;
        if (bankPaymentApp) bankPaymentApp.required = true;
        if (bankPaymentSource) bankPaymentSource.required = true;
        if (bankCustomerAccountNumber) bankCustomerAccountNumber.required = true;
        if (paymentSlip && (!hasExistingSlip || (clearFields && existingPaymentMethod !== 'bank_transfer'))) {
            paymentSlip.required = true;
        }
    }

    // Update border/active styling for radio options
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        const label = input.closest('label');
        if (input.checked) {
            label.classList.add('border-blue-500', 'bg-blue-50');
        } else {
            label.classList.remove('border-blue-500', 'bg-blue-50');
        }
    });
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (selectedMethod) {
        togglePaymentDetails(selectedMethod.value, false);
    }

    // Add change listeners to all payment method radio buttons
    document.querySelectorAll('input[name="payment_method"]').forEach(input => {
        input.addEventListener('change', function() {
            togglePaymentDetails(this.value);
        });
    });
});
</script>
@endsection

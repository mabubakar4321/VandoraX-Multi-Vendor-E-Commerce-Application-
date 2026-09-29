@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <!-- Step 1: Delivery Address -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full mr-3 text-sm font-bold">1</span>
                            Delivery Address
                        </h2>
                    </div>

                    <div class="px-6 py-6">
                        <!-- Saved Addresses -->
                        @if($addresses->count() > 0)
                            <div class="mb-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-4">Select Saved Address</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    @foreach($addresses as $address)
                                        <label class="border-2 rounded-lg p-4 cursor-pointer transition"
                                               id="addr-option-{{ $address->id }}"
                                               onclick="selectAddress({{ $address->id }})"
                                               style="border-color: #e5e7eb;">
                                            <input type="radio" name="address_id" value="{{ $address->id }}"
                                                   class="mr-3" onchange="selectAddress({{ $address->id }})">
                                            <div class="ml-6">
                                                @if($address->label)
                                                    <p class="font-semibold text-gray-900">{{ $address->label }}</p>
                                                @endif
                                                <p class="text-sm text-gray-700 mt-1">{{ $address->full_name }}</p>
                                                <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                                                <p class="text-sm text-gray-600">{{ $address->address_line_1 }}</p>
                                                @if($address->address_line_2)
                                                    <p class="text-sm text-gray-600">{{ $address->address_line_2 }}</p>
                                                @endif
                                                <p class="text-sm text-gray-600">{{ $address->city }}, {{ $address->state_province }} {{ $address->postal_code }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <button type="button" onclick="toggleAddressForm()"
                                        class="text-blue-600 font-semibold hover:text-blue-800 transition">
                                    + Add New Address
                                </button>
                            </div>
                        @endif

                        <!-- New Address Form -->
                        <div id="address-form" class="mt-6" style="display: @if($addresses->count() > 0) none @endif;">
                            <h3 class="text-sm font-semibold text-gray-900 mb-4">Enter New Address</h3>
                            <form id="new-address-form" class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Full Name *</label>
                                        <input type="text" name="full_name" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Phone *</label>
                                        <input type="tel" name="phone" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Address Line 1 *</label>
                                    <input type="text" name="address_line_1" required
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Address Line 2</label>
                                    <input type="text" name="address_line_2"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">City *</label>
                                        <input type="text" name="city" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">State/Province *</label>
                                        <input type="text" name="state_province" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-900 mb-1">Postal Code *</label>
                                        <input type="text" name="postal_code" required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Country *</label>
                                    <input type="text" name="country" required value="United States"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-900 mb-1">Delivery Instructions</label>
                                    <textarea name="delivery_instructions" rows="3"
                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                <div class="flex gap-3">
                                    <button type="button" onclick="saveAddress()"
                                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-blue-700 transition">
                                        Save Address
                                    </button>
                                    @if($addresses->count() > 0)
                                        <button type="button" onclick="toggleAddressForm()"
                                                class="flex-1 border border-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold hover:bg-gray-50 transition">
                                            Cancel
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Order Review -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full mr-3 text-sm font-bold">2</span>
                            Order Summary
                        </h2>
                    </div>

                    <div class="px-6 py-6">
                        @foreach($itemsByVendor as $vendorId => $items)
                            <div class="mb-6 pb-6 border-b border-gray-200 last:border-b-0 last:mb-0 last:pb-0">
                                <h3 class="font-semibold text-gray-900 mb-4">
                                    {{ $items->first()->product->vendor->user->name }}
                                </h3>
                                <div class="space-y-3">
                                    @foreach($items as $item)
                                        <div class="flex justify-between text-sm">
                                            <div>
                                                <p class="text-gray-900">{{ $item->product->name }}</p>
                                                @if($item->variant)
                                                    <p class="text-gray-600">{{ $item->variant->variant_value }}</p>
                                                @endif
                                                <p class="text-gray-600">Qty: {{ $item->quantity }}</p>
                                            </div>
                                            <p class="text-gray-900 font-semibold">${{ number_format($item->getTotal(), 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Vendor Breakdown -->
                                @if(isset($vendorCommissions[$vendorId]))
                                    <div class="mt-4 pt-4 border-t border-gray-200 space-y-2 text-sm">
                                        <div class="flex justify-between text-gray-600">
                                            <span>Subtotal</span>
                                            <span>${{ number_format($vendorCommissions[$vendorId]['subtotal'], 2) }}</span>
                                        </div>
                                        <div class="flex justify-between text-gray-600">
                                            <span>Shipping</span>
                                            <span class="text-green-600 font-semibold">Free</span>
                                        </div>
                                        <div class="flex justify-between font-semibold text-gray-900">
                                            <span>Vendor Total</span>
                                            <span>${{ number_format($vendorCommissions[$vendorId]['subtotal'], 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow sticky top-24">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Order Total</h3>
                    </div>

                    <div class="px-6 py-6 space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Items ({{ $totalItems }})</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600 font-semibold">Free</span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Taxes</span>
                            <span>Calculated at checkout</span>
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 space-y-3">
                        <button onclick="processCheckout()"
                                id="checkout-btn"
                                class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition disabled:bg-gray-400">
                            Place Order
                        </button>

                        <a href="{{ route('cart.index') }}"
                           class="block w-full text-center border border-gray-300 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Back to Cart
                        </a>
                    </div>
                </div>

                <!-- Items by Vendor -->
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Items</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($itemsByVendor as $vendorId => $items)
                            @foreach($items as $item)
                                <div class="px-6 py-4 flex gap-3">
                                    @if($item->product->mainImage())
                                        <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-16 h-16 object-cover rounded">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-400 text-xs">No image</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $item->product->name }}</p>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-1">${{ number_format($item->price, 2) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedAddressId = @json($defaultAddress ? $defaultAddress->id : null);

function selectAddress(addressId) {
    selectedAddressId = addressId;
    // Update UI
    document.querySelectorAll('[id^="addr-option-"]').forEach(el => {
        el.style.borderColor = '#e5e7eb';
    });
    document.getElementById('addr-option-' + addressId).style.borderColor = '#2563eb';
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

    const btn = document.getElementById('checkout-btn');
    btn.disabled = true;
    btn.textContent = 'Processing...';

    fetch('{{ route('checkout.process') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            address_id: selectedAddressId
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            if (data.redirect) {
                window.location.href = data.redirect;
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

// Set default address on load
window.addEventListener('load', function() {
    if (selectedAddressId) {
        selectAddress(selectedAddressId);
    }
});
</script>
@endsection

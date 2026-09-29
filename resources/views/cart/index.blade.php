@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($cart->getTotalItems() == 0)
            <!-- Empty Cart -->
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-gray-900">Your cart is empty</h2>
                <p class="mt-2 text-gray-600">Start shopping to add items to your cart.</p>
                <a href="{{ route('storefront.products.index') }}" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items by Vendor -->
                <div class="lg:col-span-2">
                    @foreach($itemsByVendor as $vendorId => $items)
                        <div class="bg-white rounded-lg shadow mb-6">
                            <!-- Vendor Header -->
                            <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $items->first()->product->vendor->user->name }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            @if($items->first()->product->vendor->rating)
                                                ⭐ {{ number_format($items->first()->product->vendor->rating, 1) }}
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-600">Subtotal</p>
                                        <p class="text-lg font-semibold text-gray-900">
                                            ${{ number_format($items->sum(fn($item) => $item->price * $item->quantity), 2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Items -->
                            <div class="divide-y divide-gray-200">
                                @foreach($items as $item)
                                    <div class="px-6 py-4 flex gap-4">
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
                                            <h4 class="text-sm font-semibold text-gray-900">
                                                <a href="{{ route('storefront.products.show', $item->product) }}" class="hover:text-blue-600">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h4>
                                            @if($item->variant)
                                                <p class="text-sm text-gray-600 mt-1">
                                                    {{ $item->variant->variant_value }}
                                                </p>
                                            @endif
                                            <p class="text-sm font-semibold text-gray-900 mt-2">
                                                ${{ number_format($item->price, 2) }}
                                            </p>
                                        </div>

                                        <!-- Quantity & Actions -->
                                        <div class="flex flex-col items-end gap-3">
                                            <!-- Quantity Controls -->
                                            <div class="flex items-center border border-gray-300 rounded-lg">
                                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition">−</button>
                                                <span class="px-4 py-1 text-gray-900 font-semibold min-w-[50px] text-center" id="qty-{{ $item->id }}">
                                                    {{ $item->quantity }}
                                                </span>
                                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
                                                        class="px-3 py-1 text-gray-600 hover:bg-gray-100 transition">+</button>
                                            </div>

                                            <!-- Item Total -->
                                            <p class="text-sm font-semibold text-gray-900" id="total-{{ $item->id }}">
                                                ${{ number_format($item->getTotal(), 2) }}
                                            </p>

                                            <!-- Remove Button -->
                                            <button onclick="removeItem({{ $item->id }})"
                                                    class="text-sm text-red-600 hover:text-red-800 transition">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow sticky top-24">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                        </div>

                        <div class="px-6 py-4 space-y-4">
                            <div class="flex justify-between text-gray-600">
                                <span>Items ({{ $totalItems }})</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>

                            <div class="flex justify-between text-gray-600">
                                <span>Shipping</span>
                                <span class="text-green-600 font-semibold">Free</span>
                            </div>

                            <div class="border-t border-gray-200 pt-4 flex justify-between text-lg font-semibold text-gray-900">
                                <span>Total</span>
                                <span id="cart-total">${{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>

                        <div class="px-6 py-4 border-t border-gray-200 space-y-3">
                            @if(auth()->check())
                                <a href="{{ route('checkout.index') }}"
                                   class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Proceed to Checkout
                                </a>
                            @else
                                <a href="{{ route('login', ['redirect' => route('checkout.index')]) }}"
                                   class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                                    Login to Checkout
                                </a>
                            @endif

                            <button onclick="clearCart()"
                                    class="w-full border border-gray-300 text-gray-700 px-4 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                                Clear Cart
                            </button>

                            <a href="{{ route('storefront.products.index') }}"
                               class="block w-full text-center text-blue-600 px-4 py-3 font-semibold hover:text-blue-800 transition">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function updateQuantity(itemId, newQuantity) {
    if (newQuantity < 1) {
        if (confirm('Remove this item from cart?')) {
            removeItem(itemId);
        }
        return;
    }

    fetch(`{{ route('cart.update', ':id') }}`.replace(':id', itemId), {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ quantity: newQuantity })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('qty-' + itemId).textContent = newQuantity;
            document.getElementById('total-' + itemId).textContent = '$' + parseFloat(data.item_total).toFixed(2);
            document.getElementById('cart-total').textContent = '$' + parseFloat(data.cart_subtotal).toFixed(2);
        } else {
            alert(data.message);
            location.reload();
        }
    })
    .catch(e => console.error('Error:', e));
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
            alert(data.message);
        }
    })
    .catch(e => console.error('Error:', e));
}

function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) return;

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
            alert(data.message);
        }
    })
    .catch(e => console.error('Error:', e));
}
</script>
@endsection

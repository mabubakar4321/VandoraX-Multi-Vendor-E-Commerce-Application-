@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                    <p class="text-gray-600 mt-1">{{ $order->order_number }}</p>
                </div>
                <a href="{{ route('order.index') }}" class="text-blue-600 hover:text-blue-800 transition">
                    ← Back to Orders
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Order Status -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Status</h2>
                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            Order: {{ ucfirst($order->status) }}
                        </span>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold
                            @if($order->payment_status === 'completed') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($order->payment_status === 'completed')
                                Payment: Completed ✓
                            @else
                                Payment: {{ ucfirst($order->payment_status) }}
                            @endif
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mt-4">
                        Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>

                <!-- Delivery Address -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Delivery Address</h2>
                    <div class="text-gray-700 space-y-1">
                        <p class="font-semibold">{{ $order->address->full_name }}</p>
                        <p>{{ $order->address->address_line_1 }}</p>
                        @if($order->address->address_line_2)
                            <p>{{ $order->address->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->address->city }}, {{ $order->address->state_province }} {{ $order->address->postal_code }}</p>
                        <p>{{ $order->address->country }}</p>
                        <p class="text-blue-600 font-semibold pt-2">{{ $order->address->phone }}</p>
                    </div>
                    @if($order->address->delivery_instructions)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm font-semibold text-gray-900">Delivery Instructions</p>
                            <p class="text-gray-700 mt-2">{{ $order->address->delivery_instructions }}</p>
                        </div>
                    @endif
                </div>

                <!-- Items by Vendor -->
                @foreach($itemsByVendor as $vendorId => $items)
                    <div class="bg-white rounded-lg shadow mb-6">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ $items->first()->product->vendor->user->name }}
                            </h3>
                            @if(isset($order->vendorOrders))
                                @php
                                    $vendorOrder = $order->vendorOrders()->where('vendor_id', $vendorId)->first();
                                @endphp
                                @if($vendorOrder)
                                    <p class="text-sm text-gray-600 mt-1">
                                        Status: <span class="font-semibold capitalize">{{ $vendorOrder->status }}</span>
                                    </p>
                                @endif
                            @endif
                        </div>

                        <div class="divide-y divide-gray-200">
                            @foreach($items as $item)
                                <div class="px-6 py-4 flex gap-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($item->product->mainImage())
                                            <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="w-24 h-24 object-cover rounded">
                                        @else
                                            <div class="w-24 h-24 bg-gray-200 rounded flex items-center justify-center">
                                                <span class="text-gray-400 text-xs">No image</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1">
                                        <h4 class="text-base font-semibold text-gray-900">
                                            <a href="{{ route('storefront.products.show', $item->product) }}" class="hover:text-blue-600 transition">
                                                {{ $item->product->name }}
                                            </a>
                                        </h4>
                                        @if($item->variant)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $item->variant->variant_value }}
                                            </p>
                                        @endif

                                        <div class="mt-3 space-y-1 text-sm">
                                            <p class="text-gray-600">
                                                <span>Unit Price:</span>
                                                <span class="font-semibold text-gray-900">${{ number_format($item->price_at_purchase, 2) }}</span>
                                            </p>
                                            <p class="text-gray-600">
                                                <span>Quantity:</span>
                                                <span class="font-semibold text-gray-900">{{ $item->quantity }}</span>
                                            </p>
                                            <p class="text-gray-600">
                                                <span>Status:</span>
                                                <span class="font-semibold text-gray-900 capitalize">{{ $item->status }}</span>
                                            </p>
                                        </div>

                                        @if($item->return_requested)
                                            <div class="mt-3 px-3 py-2 bg-orange-50 border border-orange-200 rounded text-sm">
                                                <p class="text-orange-800 font-semibold">Return Requested</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Item Total -->
                                    <div class="flex flex-col items-end">
                                        <p class="text-2xl font-bold text-gray-900">
                                            ${{ number_format($item->total_amount, 2) }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $item->quantity }} × ${{ number_format($item->price_at_purchase, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Vendor Subtotal -->
                            <div class="px-6 py-4 bg-gray-50">
                                <div class="flex justify-between font-semibold text-gray-900">
                                    <span>Vendor Subtotal</span>
                                    <span>${{ number_format($items->sum('total_amount'), 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow sticky top-24 mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                    </div>

                    <div class="px-6 py-6 space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($order->total_amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600 font-semibold">Free</span>
                        </div>

                        <div class="flex justify-between text-gray-600">
                            <span>Tax</span>
                            <span>Included</span>
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex justify-between text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>${{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Order Information -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Order Information</h3>
                    </div>

                    <div class="px-6 py-4 space-y-4 text-sm">
                        <div>
                            <p class="text-gray-600 font-semibold">Order Number</p>
                            <p class="text-gray-900 font-mono mt-1">{{ $order->order_number }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-semibold">Order Date</p>
                            <p class="text-gray-900 mt-1">{{ $order->created_at->format('F d, Y g:i A') }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-semibold">Total Items</p>
                            <p class="text-gray-900 mt-1">{{ $order->items()->count() }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-semibold">Vendors</p>
                            <p class="text-gray-900 mt-1">{{ $order->vendor_count }}</p>
                        </div>

                        <div>
                            <p class="text-gray-600 font-semibold">Payment Method</p>
                            <p class="text-gray-900 mt-1">
                                @if($order->payment_status === 'completed')
                                    Credit Card / Debit Card
                                @else
                                    Pending
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">Actions</h3>
                    </div>

                    <div class="px-6 py-4 space-y-3">
                        <button onclick="printOrder()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Print Order
                        </button>

                        <a href="{{ route('storefront.products.index') }}" class="block w-full px-4 py-2 bg-blue-600 text-white text-center rounded-lg font-semibold hover:bg-blue-700 transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printOrder() {
    window.print();
}
</script>
@endsection

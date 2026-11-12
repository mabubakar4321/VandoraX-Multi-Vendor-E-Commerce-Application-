@extends('layouts.app')

@section('title', 'Order Confirmation')

@section('content')
@php $displayStatus = $order->effective_status; @endphp
<div class="confirmation-page min-h-screen overflow-x-hidden bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <h1 class="text-3xl font-bold text-gray-900">Order Confirmed</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 sm:p-6 mb-8">
            <div class="flex items-start sm:items-center">
                <svg class="w-6 h-6 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h2 class="text-lg font-semibold text-green-900">Thank you for your order!</h2>
                    <p class="text-green-700 mt-1 break-words">We've received your order and will start processing it right away.</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Details -->
            <div class="lg:col-span-2">
                <!-- Order Number -->
                <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Number</h2>
                    <p class="text-2xl sm:text-3xl font-bold text-blue-600 break-all">{{ $order->order_number }}</p>
                    <p class="text-gray-600 text-sm mt-2">Placed on {{ $order->created_at->format('F d, Y \a\t g:i A') }}</p>
                </div>

                <!-- Delivery Address -->
                <div class="bg-white rounded-lg shadow p-4 sm:p-6 mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Delivery Address</h2>
                    <div class="text-gray-700">
                        <p class="font-semibold">{{ $order->address->full_name }}</p>
                        <p class="mt-2">{{ $order->address->address_line_1 }}</p>
                        @if($order->address->address_line_2)
                            <p>{{ $order->address->address_line_2 }}</p>
                        @endif
                        <p>{{ $order->address->city }}, {{ $order->address->state_province }} {{ $order->address->postal_code }}</p>
                        <p>{{ $order->address->country }}</p>
                        <p class="mt-2 text-blue-600">{{ $order->address->phone }}</p>
                    </div>
                    @if($order->address->delivery_instructions)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <p class="text-sm text-gray-600"><strong>Delivery Instructions:</strong></p>
                            <p class="text-gray-700 mt-1">{{ $order->address->delivery_instructions }}</p>
                        </div>
                    @endif
                </div>

                <!-- Items by Vendor -->
                @foreach($itemsByVendor as $vendorId => $items)
                    <div class="bg-white rounded-lg shadow mb-6 overflow-hidden">
                        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900 break-words">
                                {{ $items->first()->product->vendor->user->name }}
                            </h3>
                        </div>

                        <div class="divide-y divide-gray-200">
                            @foreach($items as $item)
                                <div class="px-4 sm:px-6 py-4 flex flex-col xs:flex-row sm:flex-row gap-4">
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
                                    <div class="min-w-0 flex-1">
                                        <h4 class="text-sm font-semibold text-gray-900 break-words">
                                            {{ $item->product->name }}
                                        </h4>
                                        @if($item->variant)
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $item->variant->variant_value }}
                                            </p>
                                        @endif
                                        <div class="mt-2 flex flex-wrap items-start justify-between gap-2">
                                            <div>
                                                <p class="text-sm text-gray-600">Quantity: <span class="font-semibold">{{ $item->quantity }}</span></p>
                                                <p class="text-sm text-gray-600">Unit Price: <span class="font-semibold">₨{{ number_format($item->price_at_purchase) }}</span></p>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                ₨{{ number_format($item->total_amount) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Vendor Total -->
                            <div class="px-4 sm:px-6 py-4 bg-gray-50">
                                <div class="flex flex-wrap justify-between gap-2 font-semibold text-gray-900">
                                    <span>Vendor Total</span>
                                    <span>₨{{ number_format($items->sum('total_amount')) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow lg:sticky lg:top-24">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                    </div>

                    <div class="px-4 sm:px-6 py-6 space-y-4">
                        <div class="flex flex-wrap justify-between gap-2 text-gray-600">
                            <span>Subtotal</span>
                            <span>₨{{ number_format($order->total_amount) }}</span>
                        </div>

                        <div class="flex flex-wrap justify-between gap-2 text-gray-600">
                            <span>Shipping</span>
                            <span class="text-green-600 font-semibold">Free</span>
                        </div>

                        <div class="flex flex-wrap justify-between gap-2 text-gray-600">
                            <span>Tax</span>
                            <span>Included in total</span>
                        </div>

                        <div class="border-t border-gray-200 pt-4 flex flex-wrap justify-between gap-2 text-lg font-bold text-gray-900">
                            <span>Total</span>
                            <span>₨{{ number_format($order->total_amount) }}</span>
                        </div>
                    </div>

                    <div class="px-4 sm:px-6 py-4 border-t border-gray-200 space-y-3">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm font-semibold text-blue-900">Payment Status</p>
                            <p class="text-sm text-blue-700 mt-1 capitalize">
                                @php
                                    $hasCOD = !$order->payment;
                                @endphp
                                @if($hasCOD)
                                    <span class="text-orange-600 font-semibold">⚠ Not Paid (Cash on Delivery)</span>
                                @elseif($order->payment_status === 'completed')
                                    <span class="text-green-600 font-semibold">✓ Paid — Funds held in escrow</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="text-yellow-600 font-semibold">⏳ Pending Payment</span>
                                @elseif($order->payment_status === 'failed')
                                    <span class="text-red-600 font-semibold">✗ Payment Failed</span>
                                @else
                                    <span class="capitalize">{{ $order->payment_status }}</span>
                                @endif
                            </p>
                            @if($order->payment)
                                <div class="mt-3 pt-3 border-t border-blue-200 text-xs space-y-1">
                                    <div class="flex justify-between"><span class="text-blue-700/70">Method</span><strong>{{ $order->payment->payment_method === 'card' ? 'Stripe Card' : ucfirst(str_replace('_',' ',$order->payment->payment_method)) }}</strong></div>
                                    <div class="flex justify-between"><span class="text-blue-700/70">Gateway</span><strong>{{ ucfirst($order->payment->payment_gateway) }}</strong></div>
                                    <div class="flex justify-between"><span class="text-blue-700/70">Transaction ID</span><strong class="break-all max-w-[160px]">{{ $order->payment->transaction_id }}</strong></div>
                                    @if($order->payment->escrow_status)
                                    <div class="flex justify-between"><span class="text-blue-700/70">Escrow</span><strong class="capitalize">{{ $order->payment->escrow_status }}</strong></div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('order.show', $order) }}"
                            class="block w-full bg-blue-600 text-white text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                            View Order Details
                        </a>

                        <a href="{{ route('invoice.view', $order) }}" target="_blank"
                            class="block w-full border border-blue-200 text-blue-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                            View Invoice
                        </a>

                        <a href="{{ route('invoice.download', $order) }}"
                            class="block w-full border border-blue-200 text-blue-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
                            Download Invoice PDF
                        </a>

                        <a href="{{ route('payment.history') }}"
                            class="block w-full border border-gray-300 text-gray-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Payment History
                        </a>

                        <a href="{{ route('storefront.products.index') }}"
                            class="block w-full border border-gray-300 text-gray-700 text-center px-4 py-3 rounded-lg font-semibold hover:bg-gray-50 transition">
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Order Timeline -->
                <div class="bg-white rounded-lg shadow mt-6">
                    <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-900">What's Next?</h3>
                    </div>
                    <div class="px-4 sm:px-6 py-6 space-y-4">
                        <div class="flex gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-green-100">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Order Confirmed</p>
                                <p class="text-xs text-gray-600 mt-1">{{ $order->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-gray-200">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 3a2 2 0 012-2h6a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Processing</p>
                                <p class="text-xs text-gray-600 mt-1">We're preparing your items</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-gray-200">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Shipped</p>
                                <p class="text-xs text-gray-600 mt-1">On the way to you</p>
                            </div>
                        </div>

                        <div class="flex gap-3">
                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-gray-200">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1V3a1 1 0 011-1h5a1 1 0 011 1v1h1V3a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V3a1 1 0 011-1zm0 5a1 1 0 000 2h10a1 1 0 100-2H5z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Delivered</p>
                                <p class="text-xs text-gray-600 mt-1">Arriving soon</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

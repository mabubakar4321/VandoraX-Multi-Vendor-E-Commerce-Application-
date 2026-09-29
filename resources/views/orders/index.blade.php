@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($orders->count() == 0)
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h2 class="mt-4 text-xl font-semibold text-gray-900">No orders yet</h2>
                <p class="mt-2 text-gray-600">Start shopping to place your first order.</p>
                <a href="{{ route('storefront.products.index') }}" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                    Start Shopping
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow hover:shadow-lg transition">
                        <div class="px-6 py-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $order->order_number }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Ordered on {{ $order->created_at->format('F d, Y') }}
                                    </p>
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                            @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                            @if($order->payment_status === 'completed') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($order->payment_status === 'completed')
                                                ✓ Paid
                                            @else
                                                {{ ucfirst($order->payment_status) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:text-right">
                                    <p class="text-2xl font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $order->items()->count() }} {{ Str::plural('item', $order->items()->count()) }}</p>
                                    <a href="{{ route('order.show', $order) }}" class="mt-3 text-blue-600 font-semibold hover:text-blue-800 transition">
                                        View Details →
                                    </a>
                                </div>
                            </div>

                            <!-- Order Items Preview -->
                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <p class="text-sm text-gray-600 mb-3">Items:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($order->items()->with('product')->take(3)->get() as $item)
                                        <div class="flex items-center gap-2">
                                            @if($item->product->mainImage())
                                                <img src="{{ asset('storage/' . $item->product->mainImage()->image_path) }}"
                                                     alt="{{ $item->product->name }}"
                                                     class="w-10 h-10 object-cover rounded">
                                            @else
                                                <div class="w-10 h-10 bg-gray-200 rounded"></div>
                                            @endif
                                            <div class="text-xs">
                                                <p class="font-semibold text-gray-900 truncate max-w-[150px]">{{ $item->product->name }}</p>
                                                <p class="text-gray-600">x{{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($order->items()->count() > 3)
                                        <div class="flex items-center px-2 py-1 text-xs text-gray-600">
                                            +{{ $order->items()->count() - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

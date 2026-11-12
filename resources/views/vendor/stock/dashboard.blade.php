@extends('layouts.vendor')

@section('title', 'Stock Management')

@section('content')
<div class="mb-8">
    <div class="flex items-baseline justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-forest-500">Inventory</p>
            <h1 class="mt-1 text-[32px] leading-tight">Stock Dashboard</h1>
            <p class="mt-2 text-[15px] text-graphite">Real-time inventory tracking & management</p>
        </div>
    </div>
</div>

<!-- ALERTS SECTION -->
@if ($lowStockProducts->count() > 0 || $outOfStockProducts->count() > 0)
<div class="mb-8 grid grid-cols-1 md:grid-cols-2 gap-4">
    @if ($outOfStockProducts->count() > 0)
    <div class="border-l-4 border-vermilion bg-vermilion/10 p-4 rounded">
        <div class="flex gap-3">
            <div class="h-6 w-6 shrink-0 text-vermilion mt-0.5">⚠️</div>
            <div>
                <h3 class="font-semibold text-vermilion">Out of Stock Alert</h3>
                <p class="text-sm text-graphite mt-1">{{ $outOfStockProducts->count() }} products need immediate attention</p>
            </div>
        </div>
    </div>
    @endif

    @if ($lowStockProducts->count() > 0)
    <div class="border-l-4 border-marigold bg-marigold/10 p-4 rounded">
        <div class="flex gap-3">
            <div class="h-6 w-6 shrink-0 text-marigold mt-0.5">⏱️</div>
            <div>
                <h3 class="font-semibold text-marigold">Low Stock Warning</h3>
                <p class="text-sm text-graphite mt-1">{{ $lowStockProducts->count() }} products running low</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

<!-- STATS CARDS -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-forest">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">Total Products</p>
                <p class="text-3xl font-bold text-forest mt-2">{{ $stats['total_products'] }}</p>
            </div>
            <div class="text-3xl">📦</div>
        </div>
    </div>

    <!-- In Stock -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">In Stock</p>
                <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['in_stock'] }}</p>
            </div>
            <div class="text-3xl">✅</div>
        </div>
    </div>

    <!-- Low Stock -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-marigold">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">Low Stock</p>
                <p class="text-3xl font-bold text-marigold mt-2">{{ $stats['low_stock'] }}</p>
            </div>
            <div class="text-3xl">⚠️</div>
        </div>
    </div>

    <!-- Out of Stock -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-vermilion">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">Out of Stock</p>
                <p class="text-3xl font-bold text-vermilion mt-2">{{ $stats['out_of_stock'] }}</p>
            </div>
            <div class="text-3xl">🔴</div>
        </div>
    </div>

    <!-- Total Items -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-forest-200">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">Total Items</p>
                <p class="text-3xl font-bold text-graphite mt-2">{{ $stats['total_items'] }}</p>
            </div>
            <div class="text-3xl">📊</div>
        </div>
    </div>

    <!-- Reserved Items -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">Reserved</p>
                <p class="text-3xl font-bold text-blue-600 mt-2">{{ $stats['reserved_items'] }}</p>
            </div>
            <div class="text-3xl">📌</div>
        </div>
    </div>

    <!-- Available Items -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-forest-300">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-graphite text-sm font-medium">Available</p>
                <p class="text-3xl font-bold text-forest mt-2">{{ $stats['total_items'] - $stats['reserved_items'] }}</p>
            </div>
            <div class="text-3xl">🎁</div>
        </div>
    </div>
</div>

<!-- OUT OF STOCK PRODUCTS -->
@if ($outOfStockProducts->count() > 0)
<div class="mb-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-mist px-6 py-4 bg-gradient-to-r from-vermilion/5 to-transparent">
            <h2 class="text-lg font-semibold text-vermilion flex items-center gap-2">
                🔴 Out of Stock Products
                <span class="text-sm font-normal text-graphite">({{ $outOfStockProducts->count() }})</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Product</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">SKU</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Current Stock</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($outOfStockProducts as $product)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-graphite">
                            <a href="{{ route('vendor.stock.show', $product) }}" class="text-forest hover:underline">
                                {{ Str::limit($product->name, 40) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-graphite/70">{{ $product->sku }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-vermilion/20 text-vermilion">
                                {{ $inventoryByProduct->get($product->id, collect())->sum('quantity') - $inventoryByProduct->get($product->id, collect())->sum('reserved_quantity') }} units
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-graphite">₨{{ number_format($product->base_price) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                OUT OF STOCK
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('vendor.stock.show', $product) }}" class="text-forest hover:text-forest-600 font-medium text-sm">
                                Restock →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- LOW STOCK PRODUCTS -->
@if ($lowStockProducts->count() > 0)
<div class="mb-8">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="border-b border-mist px-6 py-4 bg-gradient-to-r from-marigold/5 to-transparent">
            <h2 class="text-lg font-semibold text-marigold flex items-center gap-2">
                ⚠️ Low Stock Warning
                <span class="text-sm font-normal text-graphite">({{ $lowStockProducts->count() }})</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Product</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">SKU</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Stock</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Threshold</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Available</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lowStockProducts as $product)
                        @php
                            $inventory = $inventoryByProduct->get($product->id, collect());
                            $quantity = $inventory->sum('quantity');
                            $reserved = $inventory->sum('reserved_quantity');
                            $available = $quantity - $reserved;
                            $threshold = $inventory->sum('low_stock_threshold');
                        @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-graphite">
                            <a href="{{ route('vendor.stock.show', $product) }}" class="text-forest hover:underline">
                                {{ Str::limit($product->name, 40) }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-graphite/70">{{ $product->sku }}</td>
                        <td class="px-6 py-4 font-semibold text-graphite">
                            {{ $quantity }} units
                        </td>
                        <td class="px-6 py-4 text-sm text-graphite/70">
                            {{ $threshold }} units
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold bg-marigold/20 text-marigold">
                                {{ $available }} units
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                                LOW STOCK
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('vendor.stock.show', $product) }}" class="text-forest hover:text-forest-600 font-medium text-sm">
                                Adjust →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- RECENT INVENTORY ACTIVITY -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="border-b border-mist px-6 py-4 bg-gradient-to-r from-forest/5 to-transparent">
        <h2 class="text-lg font-semibold text-forest flex items-center gap-2">
            📋 Recent Inventory Activity
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Product</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Action</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Change</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Before → After</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentLogs as $log)
                @if ($log->product)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-graphite">
                        <a href="{{ route('vendor.stock.show', $log->product) }}" class="text-forest hover:underline text-sm">
                            {{ Str::limit($log->product->name, 35) }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold"
                              style="background-color: rgba({{ match($log->getActionColor()) {
                                  'blue' => '59, 130, 246',
                                  'red' => '239, 68, 68',
                                  'green' => '34, 197, 94',
                                  'orange' => '249, 115, 22',
                                  'purple' => '147, 51, 234',
                                  default => '107, 114, 128'
                              } }}, 0.1); color: {{ match($log->getActionColor()) {
                                  'blue' => '#0284c7',
                                  'red' => '#dc2626',
                                  'green' => '#22c55e',
                                  'orange' => '#ea580c',
                                  'purple' => '#a855f7',
                                  default => '#6b7280'
                              } }};">
                            {{ $log->getActionLabel() }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if ($log->isIncrease())
                            <span class="text-green-600 font-semibold">+{{ $log->quantity_change }}</span>
                        @else
                            <span class="text-red-600 font-semibold">{{ $log->quantity_change }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-graphite/70">
                        {{ $log->quantity_before }} → {{ $log->quantity_after }}
                    </td>
                    <td class="px-6 py-4 text-sm text-graphite/70">
                        {{ $log->created_at->format('M d, Y H:i') }}
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-graphite/70">
                        No inventory activity yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

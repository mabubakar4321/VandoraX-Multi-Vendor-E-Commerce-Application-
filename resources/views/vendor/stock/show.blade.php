@extends('layouts.vendor')

@section('title', 'Stock Management - ' . $product->name)

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <a href="{{ route('vendor.stock.dashboard') }}" class="text-sm text-forest-500 hover:text-forest font-medium">← Back to Stock Dashboard</a>
            <h1 class="mt-2 text-[32px] leading-tight">{{ $product->name }}</h1>
            <p class="mt-2 text-[15px] text-graphite">SKU: {{ $product->sku }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm text-graphite/70">Product ID: {{ $product->id }}</p>
            <p class="text-sm text-graphite/70">Category: {{ $product->category?->name }}</p>
        </div>
    </div>
</div>

<!-- CURRENT STOCK STATUS -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Stock Status Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-forest">
        <p class="text-sm text-graphite font-medium">Current Stock</p>
        <p class="text-4xl font-bold text-forest mt-2">{{ $inventory->quantity }}</p>
        <p class="text-xs text-graphite/70 mt-2">units in warehouse</p>
    </div>

    <!-- Available Stock Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <p class="text-sm text-graphite font-medium">Available for Sale</p>
        <p class="text-4xl font-bold text-green-600 mt-2">{{ $inventory->getAvailableStock() }}</p>
        <p class="text-xs text-graphite/70 mt-2">
            ({{ $inventory->quantity }} total - {{ $inventory->reserved_quantity }} reserved)
        </p>
    </div>

    <!-- Low Stock Threshold Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-marigold">
        <p class="text-sm text-graphite font-medium">Low Stock Threshold</p>
        <p class="text-4xl font-bold text-marigold mt-2">{{ $inventory->low_stock_threshold }}</p>
        <p class="text-xs text-graphite/70 mt-2">
            @if ($inventory->isLowStock())
                ⚠️ Currently below threshold
            @else
                ✅ Healthy level
            @endif
        </p>
    </div>
</div>

<!-- STATUS INDICATOR -->
<div class="mb-8">
    @php
        $status = match(true) {
            $inventory->quantity <= 0 => ['label' => 'OUT OF STOCK', 'color' => 'vermilion', 'icon' => '🔴'],
            $inventory->isLowStock() => ['label' => 'LOW STOCK', 'color' => 'marigold', 'icon' => '⚠️'],
            default => ['label' => 'IN STOCK', 'color' => 'green-600', 'icon' => '✅']
        }
    @endphp
    <div class="bg-{{ $status['color'] }}/10 border-l-4 border-{{ $status['color'] }} p-6 rounded">
        <div class="flex items-center gap-3">
            <span class="text-3xl">{{ $status['icon'] }}</span>
            <div>
                <h3 class="text-lg font-bold text-{{ $status['color'] }}">
                    {{ $status['label'] }}
                </h3>
                @if ($inventory->quantity <= 0)
                    <p class="text-sm text-graphite mt-1">This product is currently unavailable for customers. Restock immediately.</p>
                @elseif ($inventory->isLowStock())
                    <p class="text-sm text-graphite mt-1">Stock is below your threshold. Consider restocking soon to avoid running out.</p>
                @else
                    <p class="text-sm text-graphite mt-1">Stock level is healthy and available for sale.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- QUICK ACTIONS SECTION -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Manual Stock Adjustment -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-forest">
        <h3 class="text-lg font-bold text-forest mb-4">📝 Adjust Stock</h3>
        <form action="{{ route('vendor.stock.adjust', $product) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-graphite mb-2">Quantity Change</label>
                <input type="number" name="quantity_change" placeholder="e.g., +50 or -10" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-forest outline-none" required>
                <p class="text-xs text-graphite/70 mt-1">Use positive numbers to add, negative to subtract</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-graphite mb-2">Reason</label>
                <select name="reason" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-forest outline-none">
                    <option value="">Select reason...</option>
                    <option value="new_purchase">New Purchase</option>
                    <option value="stock_count">Stock Count/Audit</option>
                    <option value="damage">Damaged Items</option>
                    <option value="lost">Lost/Missing</option>
                    <option value="return">Customer Return</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-graphite mb-2">Notes</label>
                <textarea name="notes" placeholder="Add any additional notes..." rows="3" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-forest outline-none"></textarea>
            </div>

            <button type="submit" class="w-full bg-forest text-white px-4 py-2.5 rounded-lg font-medium hover:bg-forest-600 transition">
                Update Stock
            </button>
        </form>
    </div>

    <!-- Set Exact Quantity -->
    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
        <h3 class="text-lg font-bold text-green-600 mb-4">🎯 Set Exact Quantity</h3>
        <form action="{{ route('vendor.stock.set-quantity', $product) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-graphite mb-2">New Quantity</label>
                <input type="number" name="quantity" value="{{ $inventory->quantity }}" min="0" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-green-500 outline-none" required>
                <p class="text-xs text-graphite/70 mt-1">Set the exact number of units in stock</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-graphite mb-2">Reason</label>
                <select name="reason" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-green-500 outline-none">
                    <option value="">Select reason...</option>
                    <option value="physical_count">Physical Count</option>
                    <option value="system_correction">System Correction</option>
                    <option value="restock">Restock</option>
                    <option value="clearance">Clearance</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-graphite mb-2">Notes</label>
                <textarea name="notes" placeholder="Add any additional notes..." rows="3" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-green-500 outline-none"></textarea>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white px-4 py-2.5 rounded-lg font-medium hover:bg-green-700 transition">
                Set Quantity
            </button>
        </form>
    </div>
</div>

<!-- LOW STOCK THRESHOLD SETTINGS -->
<div class="bg-white rounded-lg shadow p-6 border-t-4 border-marigold mb-8">
    <h3 class="text-lg font-bold text-marigold mb-4">🚨 Low Stock Alert Threshold</h3>
    <p class="text-sm text-graphite mb-4">When stock falls below this level, you'll receive an alert</p>

    <form action="{{ route('vendor.stock.set-threshold', $product) }}" method="POST" class="flex gap-4 items-end">
        @csrf
        <div class="flex-1">
            <label class="block text-sm font-medium text-graphite mb-2">Threshold (units)</label>
            <input type="number" name="threshold" value="{{ $inventory->low_stock_threshold }}" min="0" max="1000" class="w-full px-4 py-2 border border-mist rounded-lg focus:ring-2 focus:ring-marigold outline-none" required>
        </div>
        <button type="submit" class="bg-marigold text-white px-6 py-2.5 rounded-lg font-medium hover:bg-marigold/90 transition">
            Update Threshold
        </button>
    </form>

    <div class="mt-4 p-4 bg-marigold/5 rounded">
        <p class="text-xs text-graphite">
            <strong>Example:</strong> If threshold is set to 5, you'll be alerted when stock reaches 5 units or less.
        </p>
    </div>
</div>

<!-- VARIANTS SECTION (if product has variants) -->
@if ($variants->count() > 0)
<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="border-b border-mist px-6 py-4 bg-gradient-to-r from-forest/5 to-transparent">
        <h3 class="text-lg font-bold text-forest">📦 Product Variants Stock</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Variant</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">SKU</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Stock</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Available</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($variants as $variant)
                    @php
                        $variantInventory = \App\Models\ProductInventory::where('product_id', $product->id)
                            ->where('variant_id', $variant->id)
                            ->first();
                        $variantStatus = match(true) {
                            !$variantInventory || $variantInventory->quantity <= 0 => 'out_of_stock',
                            $variantInventory->isLowStock() => 'low_stock',
                            default => 'in_stock'
                        };
                    @endphp
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 font-medium text-graphite">
                        {{ $variant->type }}: {{ $variant->value }}
                    </td>
                    <td class="px-6 py-4 text-sm text-graphite/70">{{ $variant->sku ?? 'N/A' }}</td>
                    <td class="px-6 py-4 font-semibold text-graphite">
                        {{ $variantInventory?->quantity ?? 0 }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-block px-3 py-1 rounded text-xs font-medium
                            @if ($variantStatus === 'out_of_stock')
                                bg-vermilion/20 text-vermilion
                            @elseif ($variantStatus === 'low_stock')
                                bg-marigold/20 text-marigold
                            @else
                                bg-green-100 text-green-800
                            @endif">
                            {{ $variantInventory?->getAvailableStock() ?? 0 }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if ($variantStatus === 'out_of_stock')
                            <span class="inline-block px-3 py-1 rounded text-xs font-bold bg-red-100 text-red-800">OUT OF STOCK</span>
                        @elseif ($variantStatus === 'low_stock')
                            <span class="inline-block px-3 py-1 rounded text-xs font-bold bg-yellow-100 text-yellow-800">LOW STOCK</span>
                        @else
                            <span class="inline-block px-3 py-1 rounded text-xs font-bold bg-green-100 text-green-800">IN STOCK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- INVENTORY HISTORY / LOGS -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="border-b border-mist px-6 py-4 bg-gradient-to-r from-forest/5 to-transparent">
        <h3 class="text-lg font-bold text-forest flex items-center gap-2">
            📊 Inventory History
            <span class="text-sm font-normal text-graphite">(Last 50 changes)</span>
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Date & Time</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Action</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Change</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Before → After</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-graphite">Reason / Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($logs as $log)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-graphite/70 whitespace-nowrap">
                        {{ $log->created_at->format('M d, Y') }}<br>
                        <span class="text-xs">{{ $log->created_at->format('H:i A') }}</span>
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
                    <td class="px-6 py-4 font-semibold">
                        @if ($log->isIncrease())
                            <span class="text-green-600">+{{ $log->quantity_change }}</span>
                        @else
                            <span class="text-red-600">{{ $log->quantity_change }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-graphite/70">
                        <span class="font-medium">{{ $log->quantity_before }}</span> → <span class="font-medium">{{ $log->quantity_after }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-graphite/70">
                        @if ($log->reason)
                            <div class="mb-1"><strong>{{ ucfirst(str_replace('_', ' ', $log->reason)) }}</strong></div>
                        @endif
                        @if ($log->notes)
                            <div class="text-xs">{{ $log->notes }}</div>
                        @endif
                        @if (!$log->reason && !$log->notes)
                            <span class="text-graphite/50">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-graphite/70">
                        No inventory history yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($logs->hasPages())
    <div class="border-t border-mist px-6 py-4">
        {{ $logs->links() }}
    </div>
    @endif
</div>

@endsection

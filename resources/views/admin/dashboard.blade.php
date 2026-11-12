@extends('layouts.admin')
@section('title', 'Admin — VandoraX')

@section('content')
@php
    $pendingCategoryRequests = \App\Models\VendorCategoryRequest::where('status', 'pending')->count();
    $currency = fn ($value) => 'Rs ' . number_format((float) $value);
@endphp

<div class="min-w-0 space-y-8">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Admin overview</p>
            <h1 class="mt-2 text-3xl leading-tight sm:text-4xl">Today on VandoraX</h1>
            <p class="mt-2 text-sm text-graphite/70">A live view of your marketplace, orders, sellers, and money.</p>
        </div>
        <p class="text-sm text-graphite/60">{{ now()->format('D, M d, Y') }}</p>
    </div>

    <section aria-label="Marketplace totals" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Products', $totalProducts, 'Catalog inventory', 'border-marigold'],
            ['Customers', $totalUsers, 'Registered buyers', 'border-forest-500'],
            ['Vendors', $totalVendors, $activeVendors . ' active sellers', 'border-indigo-500'],
            ['Total orders', $totalOrders, $pendingOrders . ' pending now', 'border-vermilion'],
        ] as [$label, $value, $caption, $border])
            <div class="border-t-2 {{ $border }} bg-white p-5 shadow-sm" style="border-radius: 2px;">
                <p class="text-sm font-medium text-graphite">{{ $label }}</p>
                <p class="tabular mt-3 font-display text-4xl leading-none text-forest">{{ number_format($value) }}</p>
                <p class="mt-3 text-xs text-graphite/65">{{ $caption }}</p>
            </div>
        @endforeach
    </section>

    <section aria-label="Financial and order metrics" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ([
            ['Revenue collected', $currency($totalRevenue), 'Completed payments', 'bg-forest'],
            ['Pending payments', $currency($pendingRevenue), $pendingPayments . ' awaiting review', 'bg-marigold'],
            ['Commission earned', $currency($totalCommission), 'Across vendor orders', 'bg-indigo-600'],
            ['Today', $todayOrders . ' orders', $currency($todayRevenue) . ' collected', 'bg-vermilion'],
        ] as [$label, $value, $caption, $tone])
            <div class="{{ $tone }} p-5 text-white shadow-sm" style="border-radius: 2px;">
                <p class="text-sm font-medium text-white/75">{{ $label }}</p>
                <p class="mt-3 break-words font-display text-2xl leading-tight">{{ $value }}</p>
                <p class="mt-3 text-xs text-white/70">{{ $caption }}</p>
            </div>
        @endforeach
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="min-w-0 border border-mist bg-white p-5 sm:p-6 xl:col-span-2" style="border-radius: 2px;">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl">Revenue and orders</h2>
                    <p class="mt-1 text-sm text-graphite/65">Performance over the last seven days.</p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wider text-graphite/50">7 days</span>
            </div>
            <div class="relative mt-6 h-72"><canvas id="revenueOrdersChart"></canvas></div>
        </div>

        <div class="min-w-0 border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <h2 class="text-xl">Order status</h2>
            <p class="mt-1 text-sm text-graphite/65">Where orders are in the journey.</p>
            <div class="relative mx-auto mt-4 h-64 max-w-xs"><canvas id="orderStatusChart"></canvas></div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="min-w-0 border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <h2 class="text-xl">Products by category</h2>
            <p class="mt-1 text-sm text-graphite/65">Catalog distribution across {{ $totalCategories }} categories.</p>
            <div class="relative mt-6 h-72"><canvas id="categoryChart"></canvas></div>
        </div>
        <div class="min-w-0 border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <h2 class="text-xl">Vendor health</h2>
            <p class="mt-1 text-sm text-graphite/65">Active, pending, blocked, and other seller accounts.</p>
            <div class="relative mx-auto mt-4 h-64 max-w-xs"><canvas id="vendorStatusChart"></canvas></div>
        </div>
    </section>

    <section class="grid gap-6 xl:grid-cols-3">
        <div class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <div class="flex items-center justify-between gap-3">
                <div><h2 class="text-xl">Action queue</h2><p class="mt-1 text-sm text-graphite/65">Items needing attention.</p></div>
            </div>
            <div class="mt-5 divide-y divide-mist">
                @foreach ([
                    ['Pending orders', $pendingOrders, 'Orders awaiting processing'],
                    ['Pending payments', $pendingPayments, 'Payments to verify'],
                    ['Vendor approvals', $pendingVendors, 'Seller applications'],
                    ['Category requests', $pendingCategoryRequests, 'New category proposals'],
                ] as [$label, $value, $caption])
                    <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                        <div><p class="font-medium text-forest">{{ $label }}</p><p class="mt-1 text-xs text-graphite/65">{{ $caption }}</p></div>
                        <span class="tabular text-2xl font-semibold {{ $value > 0 ? 'text-vermilion' : 'text-forest-500' }}">{{ number_format($value) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border border-mist bg-white p-5 sm:p-6 xl:col-span-2" style="border-radius: 2px;">
            <div class="flex items-start justify-between gap-4"><div><h2 class="text-xl">Recent orders</h2><p class="mt-1 text-sm text-graphite/65">The latest customer activity.</p></div><span class="text-xs font-semibold uppercase tracking-wider text-graphite/50">Latest 8</span></div>
            <div class="mt-5 overflow-x-auto">
                <table class="w-full min-w-[620px] text-left text-sm">
                    <thead class="border-b border-mist text-xs uppercase tracking-wider text-graphite/60"><tr><th class="pb-3 pr-4">Order</th><th class="pb-3 pr-4">Customer</th><th class="pb-3 pr-4">Status</th><th class="pb-3 text-right">Amount</th></tr></thead>
                    <tbody class="divide-y divide-mist">
                        @forelse($recentOrders as $order)
                            <tr><td class="py-3 pr-4 font-medium text-forest">{{ $order->order_number }}</td><td class="py-3 pr-4">{{ $order->customer->name ?? 'Guest' }}</td><td class="py-3 pr-4"><span class="inline-flex rounded-full bg-mist px-2.5 py-1 text-xs font-semibold capitalize text-forest">{{ $order->status }}</span></td><td class="py-3 text-right font-semibold">{{ $currency($order->total_amount) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="py-8 text-center text-graphite/60">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <h2 class="text-xl">Newest products</h2>
            <p class="mt-1 text-sm text-graphite/65">The latest additions to the catalog.</p>
            <div class="mt-5 space-y-3">
                @forelse($topProducts as $product)
                    <div class="flex items-center justify-between gap-4 border-b border-mist pb-3 last:border-0 last:pb-0"><span class="min-w-0 truncate font-medium text-forest">{{ $product->name }}</span><span class="text-xs text-graphite/55">New</span></div>
                @empty
                    <p class="text-sm text-graphite/60">No products yet.</p>
                @endforelse
            </div>
        </div>
        <div class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
            <h2 class="text-xl">Management tools</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ([
                    ['Manage products', 'admin.products.index'], ['Manage categories', 'admin.categories.index'],
                    ['Category tree', 'admin.categories.tree'], ['Category requests', 'admin.category-requests.index'],
                    ['Pending vendors', 'admin.vendors.pending'],
                ] as [$label, $route])
                    <a href="{{ route($route) }}" class="border border-mist px-4 py-3 text-sm font-semibold text-indigo-600 transition-colors hover:border-indigo-300 hover:bg-indigo-50">{{ $label }} <span aria-hidden="true">→</span></a>
                @endforeach
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    const chartColors = ['#0E2A22', '#F2B705', '#B23A2E', '#4f46e5', '#1E6F5C', '#94a3b8'];
    const chartBase = { responsive: true, maintainAspectRatio: false, plugins: { legend: { labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Instrument Sans' } } } } };

    new Chart(document.getElementById('revenueOrdersChart'), { type: 'line', data: { labels: @json($revenueLabels), datasets: [
        { label: 'Revenue', data: @json($revenueData), borderColor: '#0E2A22', backgroundColor: 'rgba(14,42,34,.08)', fill: true, tension: .35, yAxisID: 'revenue' },
        { label: 'Orders', data: @json($ordersData), borderColor: '#F2B705', backgroundColor: 'transparent', tension: .35, yAxisID: 'orders' }
    ] }, options: { ...chartBase, scales: { revenue: { beginAtZero: true, grid: { color: '#e4eae5' }, ticks: { callback: value => 'Rs ' + Number(value).toLocaleString() } }, orders: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } } } } });
    new Chart(document.getElementById('orderStatusChart'), { type: 'doughnut', data: { labels: @json($orderStatusData->keys()->values()), datasets: [{ data: @json($orderStatusData->values()), backgroundColor: chartColors, borderWidth: 0 }] }, options: { ...chartBase, cutout: '68%' } });
    new Chart(document.getElementById('categoryChart'), { type: 'bar', data: { labels: @json($categoryNames->values()), datasets: [{ label: 'Products', data: @json($productsByCategory->values()), backgroundColor: '#1E6F5C', borderRadius: 2 }] }, options: { ...chartBase, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } } } });
    new Chart(document.getElementById('vendorStatusChart'), { type: 'doughnut', data: { labels: @json($vendorStatusData->keys()->values()), datasets: [{ data: @json($vendorStatusData->values()), backgroundColor: chartColors, borderWidth: 0 }] }, options: { ...chartBase, cutout: '68%' } });
</script>
@endpush
@endsection
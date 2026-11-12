@extends('layouts.vendor')
@section('title', 'Seller Centre — VandoraX')

@section('content')
<div class="vendor-dashboard">
    <div class="vendor-dashboard-hero">
        <div>
            <p class="vendor-kicker">Seller centre</p>
            <h1>{{ $vendor->store_name }}</h1>
            <p>Welcome back. Here is a clear view of your store performance and fulfilment.</p>
        </div>
        <div class="vendor-dashboard-actions">
            <a href="{{ route('vendor.products.create') }}" class="vendor-dashboard-btn vendor-dashboard-btn-primary">Add product</a>
            <a href="{{ route('vendor.orders.index') }}" class="vendor-dashboard-btn vendor-dashboard-btn-secondary">View sales</a>
            <a href="{{ route('vendor.reviews.deletion-requests.index') }}" class="vendor-dashboard-btn vendor-dashboard-btn-secondary">Request review deletion</a>
        </div>
    </div>

    <div class="vendor-metric-grid">
        @foreach([
            ['Total sales', '₨'.number_format($stats['total_sales']), 'Before commission deduction', 'metric-sales'],
            ['Commission deducted', '₨'.number_format($stats['deducted_commission']), 'Platform commission', 'metric-commission'],
            ['Released earnings', '₨'.number_format($stats['net_earnings']), 'Available after admin release', 'metric-earnings'],
            ['Total orders', number_format($stats['total_orders']), 'All vendor orders', 'metric-orders'],
            ['Pending orders', number_format($stats['pending_orders']), 'Waiting for confirmation', 'metric-pending'],
            ['Shipping orders', number_format($stats['shipping_orders']), 'Processing or shipped', 'metric-shipping'],
            ['Delivered orders', number_format($stats['delivered_orders']), 'Successfully delivered', 'metric-delivered'],
            ['Customers', number_format($stats['customers']), 'Unique customers', 'metric-customers'],
        ] as [$label, $figure, $caption, $tone])
            <div class="vendor-metric-card {{ $tone }}">
                <span>{{ $label }}</span>
                <strong>{{ $figure }}</strong>
                <small>{{ $caption }}</small>
            </div>
        @endforeach
    </div>

    <div class="vendor-dashboard-columns">
        <section class="vendor-dashboard-panel vendor-chart-panel">
            <div class="vendor-panel-heading">
                <div>
                    <p class="vendor-kicker">Performance</p>
                    <h2>Sales overview</h2>
                </div>
                <span class="vendor-panel-note">Last 6 months</span>
            </div>
            @php $maxSales = max(1, $monthlySales->max('sales')); @endphp
            <div class="vendor-chart" aria-label="Sales overview chart">
                @foreach($monthlySales as $month)
                    <div class="vendor-chart-column">
                        <div class="vendor-chart-value">₨{{ number_format($month['sales']) }}</div>
                        <div class="vendor-chart-track">
                            <div class="vendor-chart-bar" style="height: {{ max(8, ($month['sales'] / $maxSales) * 100) }}%"></div>
                        </div>
                        <strong>{{ $month['label'] }}</strong>
                        <small>{{ $month['orders'] }} {{ Str::plural('order', $month['orders']) }}</small>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="vendor-dashboard-panel">
            <div class="vendor-panel-heading">
                <div>
                    <p class="vendor-kicker">Payouts</p>
                    <h2>Money movement</h2>
                </div>
                <a href="{{ route('vendor.payments.index') }}" class="vendor-panel-link">View payouts →</a>
            </div>
            <div class="vendor-money-list">
                <div><span>Completed payouts</span><strong>₨{{ number_format($payoutStats['completed']) }}</strong></div>
                <div><span>Pending payouts</span><strong>₨{{ number_format($payoutStats['pending']) }}</strong></div>
                <div><span>Held in escrow</span><strong>₨{{ number_format($payoutStats['held']) }}</strong></div>
                <div><span>Available balance</span><strong>₨{{ number_format($payoutStats['completed']) }}</strong></div>
                <div><span>Published products</span><strong>{{ number_format($stats['published_products']) }}</strong></div>
                <div><span>Total products</span><strong>{{ number_format($stats['products']) }}</strong></div>
            </div>
            <a href="{{ route('vendor.stock.dashboard') }}" class="vendor-dashboard-callout">
                <span><strong>Review your stock</strong><small>Keep popular items available for customers.</small></span>
                <span aria-hidden="true">→</span>
            </a>
        </section>
    </div>

    <div class="vendor-dashboard-columns">
        <section class="vendor-dashboard-panel">
            <div class="vendor-panel-heading">
                <div>
                    <p class="vendor-kicker">Customers</p>
                    <h2>Recent orders</h2>
                </div>
                <a href="{{ route('vendor.orders.index') }}" class="vendor-panel-link">All sales →</a>
            </div>
            @if($recentOrders->isEmpty())
                <p class="vendor-empty-state">Your recent orders will appear here once customers start buying.</p>
            @else
                <div class="vendor-recent-orders">
                    @foreach($recentOrders as $vendorOrder)
                        <a href="{{ route('vendor.orders.show', $vendorOrder) }}" class="vendor-recent-order">
                            <span>
                                <strong>{{ $vendorOrder->order->customer->name ?? 'Customer' }}</strong>
                                <small>{{ $vendorOrder->vendor_order_number }} · {{ $vendorOrder->created_at->format('M d, Y') }}</small>
                            </span>
                            <span class="vendor-recent-order-right">
                                <strong>₨{{ number_format($vendorOrder->subtotal) }}</strong>
                                <small>{{ ucfirst($vendorOrder->status) }}</small>
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="vendor-dashboard-panel">
            <div class="vendor-panel-heading">
                <div>
                    <p class="vendor-kicker">Catalogue</p>
                    <h2>Products by category</h2>
                </div>
                <a href="{{ route('vendor.categories.my') }}" class="vendor-panel-link">Manage →</a>
            </div>
            @if($categories->isEmpty())
                <p class="vendor-empty-state">Approved product categories will appear here.</p>
            @else
                <div class="vendor-category-list">
                    @foreach($categories as $category)
                        <div class="vendor-category-row">
                            <span><strong>{{ $category->name }}</strong><small>{{ $category->parent?->name ?? 'Main category' }}</small></span>
                            <b>{{ $category->products_count }}</b>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</div>

<style>
    .vendor-dashboard { max-width: 1280px; }
    .vendor-dashboard-hero { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; padding-bottom:30px; border-bottom:1px solid #E4EAE5; }
    .vendor-kicker { margin:0 0 7px; color:#1E6F5C; font-size:11px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
    .vendor-dashboard h1 { margin:0; color:#0E2A22; font-size:clamp(2rem,4vw,3rem); line-height:1; }
    .vendor-dashboard-hero > div:first-child > p:last-child { margin:13px 0 0; color:#6B7671; font-size:15px; }
    .vendor-dashboard-actions { display:flex; flex-wrap:wrap; gap:10px; }
    .vendor-dashboard-btn { display:inline-flex; align-items:center; justify-content:center; min-height:42px; padding:10px 15px; border-radius:3px; font-size:13px; font-weight:700; text-decoration:none; transition:.2s ease; }
    .vendor-dashboard-btn-primary { background:#F2B705; color:#0E2A22; }
    .vendor-dashboard-btn-primary:hover { background:#D9A404; }
    .vendor-dashboard-btn-secondary { border:1px solid #C8D6CE; color:#0E2A22; background:#fff; }
    .vendor-dashboard-btn-secondary:hover { border-color:#1E6F5C; background:#F5F7F4; }
    .vendor-metric-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-top:24px; }
    .vendor-metric-card { min-width:0; padding:18px; border:1px solid #E4EAE5; border-top:3px solid #1E6F5C; border-radius:4px; background:#fff; box-shadow:0 7px 20px rgba(14,42,34,.04); }
    .vendor-metric-card span,.vendor-metric-card small { display:block; color:#6B7671; font-size:12px; }
    .vendor-metric-card strong { display:block; margin:9px 0 6px; color:#0E2A22; font-family:var(--font-display),sans-serif; font-size:clamp(1.35rem,2.1vw,1.9rem); line-height:1; overflow-wrap:anywhere; }
    .vendor-metric-card small { font-size:11px; }
    .metric-commission { border-top-color:#B23A2E; } .metric-earnings { border-top-color:#F2B705; } .metric-pending { border-top-color:#D9A404; } .metric-shipping { border-top-color:#5A4A8A; } .metric-delivered { border-top-color:#1E6F5C; }
    .vendor-dashboard-columns { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(300px,1fr); gap:18px; margin-top:18px; }
    .vendor-dashboard-panel { min-width:0; border:1px solid #E4EAE5; border-radius:4px; background:#fff; box-shadow:0 7px 20px rgba(14,42,34,.04); padding:22px; }
    .vendor-panel-heading { display:flex; align-items:flex-start; justify-content:space-between; gap:15px; margin-bottom:22px; }
    .vendor-panel-heading h2 { margin:0; color:#0E2A22; font-size:20px; }
    .vendor-panel-note,.vendor-panel-link { color:#6B7671; font-size:12px; text-decoration:none; } .vendor-panel-link { color:#1E6F5C; font-weight:700; white-space:nowrap; }
    .vendor-chart { display:grid; grid-template-columns:repeat(6,minmax(0,1fr)); align-items:end; gap:10px; min-height:220px; padding-top:10px; }
    .vendor-chart-column { display:flex; flex-direction:column; align-items:center; min-width:0; height:220px; }
    .vendor-chart-value { width:100%; overflow:hidden; color:#6B7671; font-size:10px; text-align:center; text-overflow:ellipsis; white-space:nowrap; }
    .vendor-chart-track { display:flex; align-items:flex-end; justify-content:center; width:100%; height:145px; margin:8px 0 10px; border-bottom:1px solid #C8D6CE; background:linear-gradient(to top,rgba(228,234,229,.5) 1px,transparent 1px); background-size:100% 36px; }
    .vendor-chart-bar { width:min(30px,65%); min-height:8px; border-radius:3px 3px 0 0; background:linear-gradient(to top,#1E6F5C,#76B8A2); }
    .vendor-chart-column > strong { color:#0E2A22; font-size:12px; } .vendor-chart-column > small { color:#6B7671; font-size:10px; }
    .vendor-money-list { display:grid; gap:0; border-top:1px solid #E4EAE5; }
    .vendor-money-list > div,.vendor-category-row { display:flex; justify-content:space-between; gap:14px; padding:13px 0; border-bottom:1px solid #E4EAE5; }
    .vendor-money-list span,.vendor-category-row small { color:#6B7671; font-size:13px; } .vendor-money-list strong,.vendor-category-row b { color:#0E2A22; }
    .vendor-dashboard-callout { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:18px; padding:14px; border:1px solid #D6E3DD; border-radius:3px; background:#F5F7F4; color:#0E2A22; text-decoration:none; }
    .vendor-dashboard-callout strong,.vendor-dashboard-callout small { display:block; } .vendor-dashboard-callout small { margin-top:4px; color:#6B7671; font-size:11px; }
    .vendor-empty-state { margin:0; padding:20px 0; color:#6B7671; font-size:14px; }
    .vendor-recent-orders,.vendor-category-list { border-top:1px solid #E4EAE5; }
    .vendor-recent-order { display:flex; align-items:center; justify-content:space-between; gap:15px; padding:13px 0; border-bottom:1px solid #E4EAE5; color:#0E2A22; text-decoration:none; }
    .vendor-recent-order span,.vendor-recent-order small { display:block; } .vendor-recent-order strong { font-size:13px; } .vendor-recent-order small { margin-top:4px; color:#6B7671; font-size:11px; } .vendor-recent-order-right { text-align:right; }
    .vendor-category-row span { display:block; min-width:0; } .vendor-category-row strong { display:block; color:#0E2A22; font-size:13px; } .vendor-category-row small { display:block; margin-top:4px; font-size:11px; }
    @media (max-width:1100px) { .vendor-metric-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:760px) { .vendor-dashboard-hero { display:block; } .vendor-dashboard-actions { margin-top:20px; } .vendor-dashboard-columns { grid-template-columns:1fr; } }
    @media (max-width:480px) { .vendor-metric-grid { grid-template-columns:1fr; } .vendor-dashboard-panel { padding:17px; } .vendor-chart { gap:4px; } .vendor-chart-value { font-size:8px; } .vendor-panel-heading { display:block; } .vendor-panel-note,.vendor-panel-link { display:inline-block; margin-top:8px; } }
</style>
@endsection

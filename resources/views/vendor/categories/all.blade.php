@extends('layouts.vendor')

@section('title', 'My Categories — VandoraX')

@section('content')
<div class="vendor-categories-page">
    <header class="categories-hero">
        <div>
            <p class="categories-kicker">Seller centre</p>
            <h1>My categories</h1>
            <p>Manage the categories assigned to your store and track your category requests.</p>
        </div>
        <a href="{{ route('vendor.categories.request.create') }}" class="categories-primary-action">
            Request a category <span aria-hidden="true">+</span>
        </a>
    </header>

    <div class="categories-metrics">
        <div class="category-metric">
            <span>Store categories</span>
            <strong>{{ $vendorCategories->count() }}</strong>
            <small>Assigned to your vendor account</small>
        </div>
        <div class="category-metric">
            <span>Total products</span>
            <strong>{{ number_format($vendorCategories->sum('products_count')) }}</strong>
            <small>Products across your categories</small>
        </div>
        <div class="category-metric">
            <span>Open requests</span>
            <strong>{{ $vendorRequests->where('status', 'pending')->count() }}</strong>
            <small>Waiting for admin review</small>
        </div>
    </div>

    <section class="categories-panel">
        <div class="categories-panel-header">
            <div>
                <p class="categories-kicker">Your catalogue</p>
                <h2>Categories assigned to your store</h2>
                <p>Only your vendor categories are shown here. Global marketplace categories are not included.</p>
            </div>
            <span class="categories-count">{{ $vendorCategories->count() }} {{ Str::plural('category', $vendorCategories->count()) }}</span>
        </div>

        @if($vendorCategories->isEmpty())
            <div class="categories-empty">
                <div class="categories-empty-icon" aria-hidden="true">+</div>
                <h3>No vendor categories yet</h3>
                <p>Request a category from the admin team to start organizing your products.</p>
                <a href="{{ route('vendor.categories.request.create') }}" class="categories-secondary-action">Request your first category</a>
            </div>
        @else
            <div class="category-card-grid">
                @foreach($vendorCategories as $category)
                    <article class="category-card">
                        <div class="category-card-icon" aria-hidden="true">
                            @if($category->icon)
                                <img src="{{ asset('storage/' . $category->icon) }}" alt="">
                            @else
                                <span>{{ Str::upper(Str::substr($category->name, 0, 1)) }}</span>
                            @endif
                        </div>
                        <div class="category-card-copy">
                            <h3>{{ $category->name }}</h3>
                            <p>{{ $category->parent?->name ?? 'Primary store category' }}</p>
                        </div>
                        <div class="category-card-footer">
                            <span>{{ $category->products_count }} {{ Str::plural('product', $category->products_count) }}</span>
                            <a href="{{ route('vendor.categories.edit', $category) }}">Manage <span aria-hidden="true">→</span></a>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="categories-panel requests-panel">
        <div class="categories-panel-header">
            <div>
                <p class="categories-kicker">Category access</p>
                <h2>Your category requests</h2>
                <p>Follow the status of category access requests submitted by your store.</p>
            </div>
            <a href="{{ route('vendor.categories.requests') }}" class="categories-panel-link">View all →</a>
        </div>

        @if($vendorRequests->isEmpty())
            <div class="request-empty">You have not submitted any category requests yet.</div>
        @else
            <div class="requests-table-wrap">
                <table class="requests-table">
                    <thead>
                        <tr>
                            <th>Requested category</th>
                            <th>Status</th>
                            <th>Requested</th>
                            <th>Response</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vendorRequests as $request)
                            <tr>
                                <td>
                                    <strong>{{ $request->category_name }}</strong>
                                    @if($request->description)
                                        <small>{{ Str::limit($request->description, 70) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="request-status request-status-{{ $request->status }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td>{{ optional($request->requested_at)->format('M d, Y') }}</td>
                                <td>{{ optional($request->approved_at)->format('M d, Y') ?? '—' }}</td>
                                <td><a href="{{ route('vendor.categories.request.show', $request) }}">Details →</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>

<style>
    .vendor-categories-page { max-width:1280px; color:#4A5551; }
    .categories-hero { display:flex; align-items:flex-end; justify-content:space-between; gap:24px; padding-bottom:30px; border-bottom:1px solid #E4EAE5; }
    .categories-kicker { margin:0 0 7px; color:#1E6F5C; font-size:11px; font-weight:800; letter-spacing:.14em; text-transform:uppercase; }
    .categories-hero h1 { margin:0; color:#0E2A22; font-size:clamp(2rem,4vw,3rem); line-height:1; }
    .categories-hero p:last-child { margin:13px 0 0; color:#6B7671; font-size:15px; line-height:1.55; }
    .categories-primary-action,.categories-secondary-action { display:inline-flex; align-items:center; justify-content:center; gap:9px; border-radius:3px; font-size:13px; font-weight:700; text-decoration:none; transition:.2s ease; }
    .categories-primary-action { flex:none; padding:12px 16px; background:#F2B705; color:#0E2A22; }
    .categories-primary-action:hover { background:#D9A404; }
    .categories-metrics { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; margin:24px 0; }
    .category-metric { padding:18px; border:1px solid #E4EAE5; border-top:3px solid #1E6F5C; border-radius:4px; background:#fff; box-shadow:0 7px 20px rgba(14,42,34,.04); }
    .category-metric:nth-child(2) { border-top-color:#F2B705; } .category-metric:nth-child(3) { border-top-color:#5A4A8A; }
    .category-metric span,.category-metric small { display:block; color:#6B7671; font-size:12px; } .category-metric strong { display:block; margin:8px 0 5px; color:#0E2A22; font-family:var(--font-display),sans-serif; font-size:28px; }
    .category-metric small { font-size:11px; }
    .categories-panel { margin-top:18px; border:1px solid #E4EAE5; border-radius:4px; background:#fff; box-shadow:0 7px 20px rgba(14,42,34,.04); }
    .categories-panel-header { display:flex; align-items:flex-start; justify-content:space-between; gap:18px; padding:22px; border-bottom:1px solid #E4EAE5; }
    .categories-panel-header h2 { margin:0; color:#0E2A22; font-size:20px; } .categories-panel-header p:last-child { margin:7px 0 0; color:#6B7671; font-size:13px; }
    .categories-count { color:#6B7671; font-size:12px; white-space:nowrap; } .categories-panel-link { color:#1E6F5C; font-size:13px; font-weight:700; text-decoration:none; white-space:nowrap; }
    .category-card-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:12px; padding:22px; }
    .category-card { display:flex; flex-direction:column; min-width:0; padding:16px; border:1px solid #E4EAE5; border-radius:4px; background:#F8FBF9; }
    .category-card:hover { border-color:#9BBDAF; }
    .category-card-icon { display:grid; place-items:center; width:48px; height:48px; margin-bottom:15px; overflow:hidden; border-radius:4px; background:#DCEEE7; color:#1E6F5C; font-size:19px; font-weight:800; }
    .category-card-icon img { width:100%; height:100%; object-fit:cover; }
    .category-card-copy { min-height:66px; } .category-card-copy h3 { margin:0; color:#0E2A22; font-size:16px; } .category-card-copy p { margin:6px 0 0; color:#6B7671; font-size:12px; }
    .category-card-footer { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:15px; padding-top:13px; border-top:1px solid #E4EAE5; color:#6B7671; font-size:11px; }
    .category-card-footer a { color:#1E6F5C; font-size:12px; font-weight:700; text-decoration:none; }
    .categories-empty { padding:50px 20px; text-align:center; } .categories-empty-icon { display:grid; place-items:center; width:52px; height:52px; margin:0 auto 15px; border-radius:50%; background:#DCEEE7; color:#1E6F5C; font-size:26px; }
    .categories-empty h3 { margin:0; color:#0E2A22; font-size:20px; } .categories-empty p { margin:8px auto 20px; color:#6B7671; font-size:14px; }
    .categories-secondary-action { padding:10px 14px; border:1px solid #C8D6CE; color:#0E2A22; background:#fff; } .categories-secondary-action:hover { border-color:#1E6F5C; background:#F5F7F4; }
    .request-empty { padding:30px 22px; color:#6B7671; font-size:14px; }
    .requests-table-wrap { overflow-x:auto; } .requests-table { width:100%; min-width:700px; border-collapse:collapse; text-align:left; }
    .requests-table th { padding:13px 18px; color:#6B7671; background:#F8FBF9; font-size:11px; text-transform:uppercase; letter-spacing:.05em; }
    .requests-table td { padding:15px 18px; border-top:1px solid #E4EAE5; color:#4A5551; font-size:13px; } .requests-table td strong,.requests-table td small { display:block; } .requests-table td strong { color:#0E2A22; } .requests-table td small { margin-top:4px; color:#6B7671; font-size:11px; } .requests-table td a { color:#1E6F5C; font-weight:700; text-decoration:none; white-space:nowrap; }
    .request-status { display:inline-flex; padding:5px 9px; border-radius:999px; font-size:11px; font-weight:700; } .request-status-approved { color:#176147; background:#DDF2E8; } .request-status-rejected { color:#9B342A; background:#F7E4E1; } .request-status-pending { color:#866300; background:#FDF3D4; }
    @media (max-width:850px) { .category-card-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:640px) { .categories-hero { display:block; } .categories-primary-action { margin-top:20px; width:100%; } .categories-metrics { grid-template-columns:1fr; } .categories-panel-header { display:block; padding:18px; } .categories-count,.categories-panel-link { display:inline-block; margin-top:9px; } .category-card-grid { grid-template-columns:1fr; padding:16px; } }
</style>
@endsection

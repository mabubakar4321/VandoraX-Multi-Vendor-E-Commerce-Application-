<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop All Products - VandoraX</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-dark: #0a0e27;
            --primary: #16213e;
            --primary-light: #1a2d5c;
            --accent: #ffa500;
            --accent-light: #ffb84d;
            --accent-dark: #e68a00;
            --bg-dark: #0f1729;
            --bg-darker: #0a0e27;
            --border-light: #1a2d5c;
            --border-medium: #2a3d6b;
            --text-primary: #ffffff;
            --text-secondary: #d4d4d8;
            --text-muted: #888888;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
        }

        @media (prefers-color-scheme: light) {
            :root:not([data-theme="dark"]) {
                --primary-dark: #ffffff;
                --primary: #f5f7fa;
                --primary-light: #f0f3f8;
                --bg-dark: #f8f9fa;
                --bg-darker: #f5f7fa;
                --border-light: #e8eef5;
                --border-medium: #dce4f0;
                --text-primary: #1a1a2e;
                --text-secondary: #4a4a6a;
                --text-muted: #888888;
            }
        }

        :root[data-theme="light"] {
            --primary-dark: #ffffff;
            --primary: #f5f7fa;
            --primary-light: #f0f3f8;
            --bg-dark: #f8f9fa;
            --bg-darker: #f5f7fa;
            --border-light: #e8eef5;
            --border-medium: #dce4f0;
            --text-primary: #1a1a2e;
            --text-secondary: #4a4a6a;
            --text-muted: #888888;
            color-scheme: light;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-darker);
            color: var(--text-primary);
            line-height: 1.6;
            letter-spacing: 0.3px;
        }

        /* ===== SHOP HERO ===== */
        .shop-hero {
            max-width: 1400px;
            margin: 40px auto 20px;
            padding: 0 24px;
            text-align: center;
        }

        .shop-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 6vw, 56px);
            font-weight: 900;
            margin-bottom: 16px;
            color: var(--text-primary);
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .shop-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
            max-width: 600px;
            margin: 0 auto;
            font-weight: 300;
        }

        /* ===== FILTER SECTION ===== */
        .filter-section {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 24px;
        }

        .filter-form {
            background: var(--primary);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            padding: 24px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr auto;
            gap: 16px;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        @media (max-width: 968px) {
            .filter-form {
                grid-template-columns: 1fr;
            }
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-input,
        .filter-select {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-medium);
            border-radius: 10px;
            color: var(--text-primary);
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            outline: none;
            transition: all 0.3s ease;
        }

        .filter-input:focus,
        .filter-select:focus {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.15);
        }

        .filter-select option {
            background: var(--primary-dark);
            color: var(--text-primary);
        }

        .filter-actions {
            display: flex;
            gap: 12px;
            align-self: flex-end;
        }

        .btn-filter {
            padding: 12px 28px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 47px;
        }

        .btn-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        .btn-reset {
            padding: 12px 20px;
            background: transparent;
            border: 1px solid var(--border-medium);
            color: var(--text-secondary);
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 47px;
        }

        .btn-reset:hover {
            border-color: var(--text-secondary);
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        /* ===== PRODUCTS CONTAINER ===== */
        .products-container {
            max-width: 1400px;
            margin: 0 auto 80px;
            padding: 0 24px;
        }

        .products-header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 32px;
        }

        .product-card {
            background: var(--primary-light);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid var(--border-light);
            position: relative;
            display: flex;
            flex-direction: column;
            text-decoration: none;
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--accent) 0%, var(--accent-dark) 100%);
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10;
        }

        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 20px 50px rgba(255, 165, 0, 0.2);
            border-color: var(--border-medium);
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-image {
            width: 100%;
            height: 260px;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .product-card:hover .product-image img {
            transform: scale(1.12);
        }

        .no-image {
            color: var(--text-muted);
            font-size: 13px;
            text-align: center;
            padding: 20px;
        }

        .product-content {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-category-badge {
            display: inline-block;
            background: rgba(255, 165, 0, 0.15);
            color: var(--accent);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: fit-content;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 165, 0, 0.3);
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-vendor {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 16px;
            font-weight: 300;
        }

        .product-footer {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid var(--border-light);
        }

        .product-price {
            font-size: 20px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-view-product {
            padding: 8px 16px;
            background: rgba(255, 165, 0, 0.1);
            color: var(--accent);
            border: 1px solid rgba(255, 165, 0, 0.3);
            border-radius: 8px;
            font-weight: 600;
            font-size: 12px;
            transition: all 0.3s;
            text-decoration: none;
        }

        .product-card:hover .btn-view-product {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border-color: transparent;
            box-shadow: 0 4px 12px rgba(255, 165, 0, 0.3);
        }

        /* ===== PAGINATION ===== */
        .pagination-wrapper {
            margin-top: 60px;
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 8px;
            list-style: none;
            align-items: center;
        }

        .pagination li a,
        .pagination li span {
            padding: 10px 16px;
            background: var(--primary);
            border: 1px solid var(--border-medium);
            color: var(--text-primary);
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination li a:hover {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border-color: var(--accent);
        }

        .pagination li.active span {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border-color: var(--accent);
        }

        .pagination li.disabled span {
            opacity: 0.5;
            cursor: not-allowed;
            background: var(--primary);
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 80px 24px;
            background: var(--primary);
            border: 1px solid var(--border-light);
            border-radius: 16px;
            margin-top: 40px;
        }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 12px;
            color: var(--text-primary);
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 15px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>

<x-storefront-header />

<main>
    <!-- SHOP HERO SECTION -->
    <div class="shop-hero">
        <h1 class="shop-title">Curated Marketplace</h1>
        <p class="shop-subtitle">Explore verified premium products from our trusted vendors, reviewed and approved for uncompromising quality.</p>
    </div>

    <!-- FILTER & SEARCH SECTION -->
    <div class="filter-section">
        <form action="{{ route('storefront.products.index') }}" method="GET" class="filter-form">
            <div class="filter-group">
                <label for="search">Search Products</label>
                <input type="text" id="search" name="search" class="filter-input" placeholder="Search by name, SKU, or keyword..." value="{{ request('search') }}">
            </div>

            <div class="filter-group">
                <label for="category">Category</label>
                <select id="category" name="category" class="filter-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label for="sort">Sort By</label>
                <select id="sort" name="sort" class="filter-select">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Arrivals</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A to Z</option>
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn-filter">Filter</button>
                @if(request()->anyFilled(['search', 'category', 'sort']))
                    <a href="{{ route('storefront.products.index') }}" class="btn-reset" title="Reset Filters">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- PRODUCTS LIST -->
    <div class="products-container">
        <div class="products-header-bar">
            <span>Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() }} results</span>
        </div>

        @if($products->isEmpty())
            <div class="empty-state">
                <h3>No Products Found</h3>
                <p>We couldn't find any products matching your search criteria. Try adjusting your filters or search terms.</p>
                <a href="{{ route('storefront.products.index') }}" class="btn-filter" style="display: inline-flex; width: auto;">View All Products</a>
            </div>
        @else
            <div class="products-grid">
                @foreach($products as $product)
                    <a href="{{ route('storefront.products.show', $product) }}" class="product-card">
                        <div class="product-image">
                            @php
                                $img = $product->mainImage() ?? $product->images->first();
                            @endphp
                            @if($img)
                                <img src="{{ $img->getThumbnailUrl() }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image">No Image Available</div>
                            @endif
                        </div>
                        <div class="product-content">
                            @if($product->category)
                                <span class="product-category-badge">{{ $product->category->name }}</span>
                            @endif
                            <h2 class="product-name">{{ $product->name }}</h2>
                            <div class="product-vendor">By {{ $product->vendor->store_name ?? 'Verified Seller' }}</div>
                            <div class="product-footer">
                                <span class="product-price">PKR {{ number_format($product->base_price, 2) }}</span>
                                <span class="btn-view-product">View Product</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- PAGINATION -->
            @if($products->hasPages())
                <div class="pagination-wrapper">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
</main>

<x-storefront-footer />

</body>
</html>

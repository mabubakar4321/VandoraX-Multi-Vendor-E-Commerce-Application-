<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - VandoraX</title>

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

        /* ===== HEADER ===== */
        header {
            background: linear-gradient(180deg, var(--primary) 0%, rgba(22, 33, 62, 0.95) 100%);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 8px 32px rgba(255, 165, 0, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .header-wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-decoration: none;
            flex-shrink: 0;
            letter-spacing: -1px;
        }

        .search-bar {
            flex: 1;
            max-width: 450px;
            display: flex;
            gap: 0;
            min-width: 200px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 18px;
            border: 1px solid var(--border-light);
            border-radius: 8px 0 0 8px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            outline: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .search-bar input::placeholder {
            color: var(--text-muted);
        }

        .search-bar input:focus {
            border-color: var(--accent);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 0 0 3px rgba(255, 165, 0, 0.1);
        }

        .search-bar button {
            padding: 12px 28px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border: none;
            border-radius: 0 8px 8px 0;
            color: var(--primary);
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
        }

        .search-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 28px;
            flex-shrink: 0;
        }

        .header-icon {
            position: relative;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 22px;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 10px;
            text-decoration: none;
        }

        .header-icon:hover {
            background: rgba(255, 165, 0, 0.1);
            color: var(--accent);
            transform: scale(1.15);
        }

        .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 12px;
            min-width: 22px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(255, 165, 0, 0.4);
        }

        /* ===== COLLECTION HEADER ===== */
        .collection-header {
            max-width: 1400px;
            margin: 60px auto 40px;
            padding: 0 24px;
            text-align: center;
        }

        .collection-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 6vw, 56px);
            font-weight: 900;
            margin-bottom: 16px;
            color: var(--text-primary);
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .collection-description {
            font-size: 16px;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
            max-width: 600px;
            margin: 0 auto 20px;
            font-weight: 300;
        }

        .collection-count {
            font-size: 14px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== PRODUCTS SECTION ===== */
        .products-section {
            max-width: 1400px;
            margin: 0 auto 80px;
            padding: 0 24px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 32px;
            margin-bottom: 60px;
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
            cursor: pointer;
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
            transform: translateY(-16px);
            box-shadow: 0 20px 50px rgba(255, 165, 0, 0.2);
            border-color: var(--border-medium);
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-image {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, var(--border-light) 0%, var(--border-medium) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 56px;
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

        .product-content {
            padding: 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: fit-content;
            margin-bottom: 12px;
        }

        .product-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .product-price {
            font-size: 24px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 16px;
        }

        .btn-view {
            padding: 12px 16px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: auto;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        /* ===== PAGINATION ===== */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 12px;
            align-items: center;
            margin-top: 60px;
        }

        .pagination a,
        .pagination span {
            padding: 12px 20px;
            border: 1px solid var(--border-medium);
            color: var(--text-primary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.03);
        }

        .pagination a:hover {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .pagination .active {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary);
            border-color: var(--accent);
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 100px 24px;
        }

        .empty-state h3 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .empty-state p {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
            font-weight: 300;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 42px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border: none;
            color: var(--primary);
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.5px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.35);
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(255, 165, 0, 0.45);
        }

        /* ===== FOOTER ===== */
        footer {
            background: var(--primary-dark);
            color: var(--text-secondary);
            padding: 80px 24px 40px;
            border-top: 1px solid var(--border-light);
            margin-top: 80px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 50px;
            margin-bottom: 60px;
        }

        .footer-section h3 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 24px;
            color: var(--accent);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section li {
            margin-bottom: 12px;
        }

        .footer-section a {
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            letter-spacing: 0.3px;
        }

        .footer-section a:hover {
            color: var(--accent);
            padding-left: 6px;
        }

        .footer-bottom {
            border-top: 1px solid var(--border-light);
            padding-top: 40px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
            letter-spacing: 0.5px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .header-wrapper {
                padding: 12px 16px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 24px;
            }

            .collection-title {
                font-size: 42px;
            }
        }

        @media (max-width: 768px) {
            .header-wrapper {
                flex-direction: column;
                gap: 12px;
                padding: 12px 16px;
            }

            .logo {
                font-size: 24px;
                width: 100%;
                text-align: center;
            }

            .search-bar {
                max-width: 100%;
                width: 100%;
                order: 3;
                flex-basis: 100%;
            }

            .header-right {
                order: 2;
                width: 100%;
                justify-content: space-between;
                gap: 16px;
            }

            .collection-header {
                margin: 40px auto 24px;
                padding: 0 16px;
            }

            .collection-title {
                font-size: 32px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
            }

            .products-section {
                padding: 0 16px;
            }

            .product-image {
                height: 240px;
            }

            footer {
                padding: 60px 16px 30px;
            }

            .footer-content {
                gap: 30px;
            }
        }

        @media (max-width: 480px) {
            .header-wrapper {
                padding: 12px 12px;
                gap: 8px;
            }

            .logo {
                font-size: 20px;
            }

            .search-bar {
                gap: 4px;
            }

            .search-bar input,
            .search-bar button {
                font-size: 12px;
                padding: 10px 12px;
            }

            .collection-title {
                font-size: 24px;
            }

            .collection-header {
                margin: 30px auto 20px;
                padding: 0 12px;
            }

            .products-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .product-card {
                border-radius: 12px;
            }

            .product-image {
                height: 200px;
            }

            .pagination {
                gap: 8px;
            }

            .pagination a,
            .pagination span {
                padding: 10px 14px;
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- ===== HEADER ===== -->
    <header>
        <div class="header-wrapper">
            <a href="{{ route('home') }}" class="logo">VandoraX</a>

            <form action="{{ route('storefront.products.index') }}" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Search premium products..." value="{{ request('search', '') }}">
                <button type="submit">Search</button>
            </form>

            <div class="header-right">
                @if(auth()->check())
                    <a href="{{ route('wishlist.index') }}" class="header-icon" title="Wishlist">
                        ♡
                        <span class="badge" id="wishlist-count" style="display: none;">0</span>
                    </a>
                @else
                    <a href="{{ route('customer.login') }}" class="header-icon" title="Login to use wishlist" style="opacity: 0.5;">
                        ♡
                    </a>
                @endif

                <a href="{{ route('cart.index') }}" class="header-icon" title="Shopping Cart">
                    🛒
                    <span class="badge" id="cart-count" style="display: none;">0</span>
                </a>

                @if(auth()->check())
                    <div class="header-user" style="display: flex; align-items: center; gap: 16px; padding-left: 28px; border-left: 1px solid var(--border-light);">
                        <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-view" style="padding: 8px 14px; font-size: 12px; margin: 0;">Logout</button>
                        </form>
                    </div>
                @else
                    <div class="auth-buttons" style="display: flex; gap: 12px; padding-left: 28px; border-left: 1px solid var(--border-light);">
                        <a href="{{ route('customer.login') }}" class="btn-view" style="padding: 8px 14px; font-size: 12px; margin: 0; background: transparent; border: 1px solid var(--border-medium); color: var(--accent); box-shadow: none;">Login</a>
                        <a href="{{ route('customer.register') }}" class="btn-view" style="padding: 8px 14px; font-size: 12px; margin: 0;">Register</a>
                    </div>
                @endif
            </div>
        </div>
    </header>

    <!-- ===== COLLECTION HEADER ===== -->
    <section class="collection-header">
        <h1 class="collection-title">{{ $title }}</h1>
        <p class="collection-description">{{ $description }}</p>
        <p class="collection-count">{{ $products->total() }} {{ Str::plural('item', $products->total()) }}</p>
    </section>

    <!-- ===== PRODUCTS SECTION ===== -->
    <section class="products-section">
        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    <div class="product-card">
                        <div class="product-image">
                            @if($product->images->first())
                                <img src="{{ $product->images->first()->getImageUrl() }}" alt="{{ $product->name }}">
                            @else
                                <div>📦</div>
                            @endif
                        </div>
                        <div class="product-content">
                            <div class="product-badge">{{ $product->category?->name ?? 'Premium' }}</div>
                            <h3 class="product-name">{{ Str::limit($product->name, 45) }}</h3>
                            <div class="product-price">₨{{ number_format($product->base_price) }}</div>

                            <div class="product-actions" style="margin-top: auto;">
                                <a href="{{ route('storefront.products.show', $product) }}" class="btn-view">View Details</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="pagination">
                    @if($products->onFirstPage())
                        <span style="opacity: 0.4; cursor: not-allowed;">Previous</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}">Previous</a>
                    @endif

                    <span>Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</span>

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}">Next</a>
                    @else
                        <span style="opacity: 0.4; cursor: not-allowed;">Next</span>
                    @endif
                </div>
            @endif
        @else
            <div class="empty-state">
                <h3>No Products Yet</h3>
                <p>This collection is being curated. Check back soon for luxury pieces.</p>
                <a href="{{ route('home') }}" class="btn-primary">Back to Home</a>
            </div>
        @endif
    </section>

    <!-- ===== FOOTER ===== -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>For Customers</h3>
                <ul>
                    <li><a href="{{ route('storefront.products.index') }}">Browse Products</a></li>
                    <li><a href="{{ route('customer.register') }}">Create Account</a></li>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Return Policy</a></li>
                    <li><a href="#">Track Orders</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>For Sellers</h3>
                <ul>
                    <li><a href="{{ route('vendor.register') }}">Start Selling</a></li>
                    <li><a href="{{ route('vendor.login') }}">Seller Login</a></li>
                    <li><a href="#">Seller Guide</a></li>
                    <li><a href="#">Commission Info</a></li>
                    <li><a href="#">Support</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Company</h3>
                <ul>
                    <li><a href="#">About VandoraX</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Connect</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">LinkedIn</a></li>
                    <li><a href="#">YouTube</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 VandoraX Premium Marketplace. All rights reserved. ✨</p>
        </div>
    </footer>

    <script>
        // Update cart count
        function updateCartCount() {
            fetch('{{ route('cart.count') }}')
                .then(r => r.json())
                .then(data => {
                    const count = data.count || 0;
                    const el = document.getElementById('cart-count');
                    if (el) {
                        if (count > 0) {
                            el.textContent = count;
                            el.style.display = 'block';
                        } else {
                            el.style.display = 'none';
                        }
                    }
                })
                .catch(e => console.error('Cart error:', e));
        }

        // Update wishlist count
        function updateWishlistCount() {
            @if(auth()->check())
            fetch('{{ route('wishlist.count') }}')
                .then(r => r.json())
                .then(data => {
                    const count = data.count || 0;
                    const el = document.getElementById('wishlist-count');
                    if (el) {
                        if (count > 0) {
                            el.textContent = count;
                            el.style.display = 'block';
                        } else {
                            el.style.display = 'none';
                        }
                    }
                })
                .catch(e => console.error('Wishlist error:', e));
            @endif
        }

        // Load counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
        });
    </script>
</body>
</html>

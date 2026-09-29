<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - VandoraX</title>
    <style>

        /* BREADCRUMBS */
        .breadcrumbs {
            max-width: 1400px;
            margin: 0 auto;
            padding: 12px 24px;
            display: flex;
            gap: 8px;
            font-size: 12px;
            color: #999;
            border-bottom: 1px solid #F0F0F0;
        }

        .breadcrumbs a {
            color: #999;
            text-decoration: none;
        }

        .breadcrumbs a:hover {
            color: #000;
        }

        .breadcrumbs span {
            color: #000;
            font-weight: 500;
        }

        /* MAIN CONTAINER */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 24px;
        }

        /* PRODUCT GRID */
        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            margin-bottom: 80px;
        }

        /* GALLERY SECTION */
        .gallery-section {
            position: sticky;
            top: 100px;
            height: fit-content;
        }

        .main-image-container {
            width: 100%;
            aspect-ratio: 1/1;
            background: #f5f5f5;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .main-image {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .main-image:hover {
            transform: scale(1.02);
        }

        .thumbnail-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
        }

        .thumbnail {
            aspect-ratio: 1/1;
            background: #f5f5f5;
            border: 2px solid transparent;
            border-radius: 6px;
            cursor: pointer;
            overflow: hidden;
            transition: all 0.2s;
        }

        .thumbnail:hover,
        .thumbnail.active {
            border-color: #000;
        }

        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* PRODUCT INFO */
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .badge {
            display: inline-block;
            background: #000;
            color: white;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            width: fit-content;
        }

        .product-title {
            font-size: 36px;
            font-weight: 700;
            line-height: 1.2;
            color: #000;
        }

        .vendor-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 0;
            border-top: 1px solid #E8E8E8;
            border-bottom: 1px solid #E8E8E8;
        }

        .vendor-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #000;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
        }

        .vendor-details h3 {
            font-size: 14px;
            font-weight: 600;
            color: #000;
        }

        .vendor-details p {
            font-size: 12px;
            color: #999;
            margin-top: 2px;
        }

        .price {
            font-size: 42px;
            font-weight: 700;
            color: #000;
            margin: 16px 0;
        }

        .product-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            padding: 24px 0;
            border-bottom: 1px solid #E8E8E8;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .meta-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 0.5px;
        }

        .meta-value {
            font-size: 14px;
            font-weight: 500;
            color: #000;
        }

        .meta-value.in-stock {
            color: #22c55e;
        }

        .meta-value.out-stock {
            color: #ef4444;
        }

        /* VARIANTS */
        .variants-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 24px 0;
            border-bottom: 1px solid #E8E8E8;
        }

        .section-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            letter-spacing: 0.5px;
        }

        .variants-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
        }

        .variant-card {
            padding: 12px;
            border: 1px solid #E8E8E8;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
        }

        .variant-card:hover {
            border-color: #000;
            background: #f9f9f9;
        }

        .variant-card.selected {
            border-color: #000;
            background: #f0f0f0;
        }

        .variant-value {
            font-size: 14px;
            font-weight: 600;
            color: #000;
            margin-bottom: 4px;
        }

        .variant-price {
            font-size: 12px;
            color: #666;
        }

        .variant-stock {
            font-size: 11px;
            color: #999;
            margin-top: 4px;
        }

        /* QUANTITY SELECTOR */
        .quantity-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 24px 0;
            border-bottom: 1px solid #E8E8E8;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 1px solid #E8E8E8;
            border-radius: 6px;
            width: fit-content;
        }

        .quantity-btn {
            width: 44px;
            height: 44px;
            border: none;
            background: white;
            color: #000;
            font-size: 18px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .quantity-btn:hover {
            background: #f5f5f5;
        }

        .quantity-input {
            width: 60px;
            height: 44px;
            border: none;
            border-left: 1px solid #E8E8E8;
            border-right: 1px solid #E8E8E8;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            background: white;
        }

        .quantity-input:focus {
            outline: none;
        }

        /* BUTTONS */
        .buttons-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 24px 0;
        }

        .btn {
            padding: 14px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: #000;
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background: #1a1a1a;
        }

        .btn-primary:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: white;
            color: #000;
            border: 1px solid #E8E8E8;
        }

        .btn-secondary:hover {
            background: #f5f5f5;
            border-color: #000;
        }

        .btn-wishlist {
            background: white;
            color: #000;
            border: 1px solid #E8E8E8;
            padding: 12px 24px;
        }

        .btn-wishlist:hover {
            background: #f5f5f5;
        }

        /* FEATURES */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            padding: 40px 0;
            margin-top: 40px;
            border-top: 1px solid #E8E8E8;
        }

        .feature-item {
            text-align: center;
        }

        .feature-icon {
            font-size: 32px;
            margin-bottom: 12px;
        }

        .feature-title {
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            color: #000;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .feature-desc {
            font-size: 12px;
            color: #999;
        }

        /* TABS */
        .tabs-container {
            margin-top: 80px;
            border-top: 1px solid #E8E8E8;
        }

        .tabs {
            display: flex;
            gap: 40px;
            border-bottom: 1px solid #E8E8E8;
            margin-bottom: 40px;
        }

        .tab {
            padding: 16px 0;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            color: #999;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.2s;
            letter-spacing: 0.5px;
        }

        .tab:hover {
            color: #000;
        }

        .tab.active {
            color: #000;
            border-bottom-color: #000;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .description-content {
            font-size: 14px;
            line-height: 1.8;
            color: #666;
            max-width: 800px;
        }

        .description-content p {
            margin-bottom: 16px;
        }

        /* SPECS TABLE */
        .specs-table {
            display: grid;
            gap: 0;
            max-width: 800px;
        }

        .spec-row {
            display: grid;
            grid-template-columns: 200px 1fr;
            padding: 12px 0;
            border-bottom: 1px solid #E8E8E8;
            gap: 24px;
        }

        .spec-label {
            font-weight: 600;
            color: #000;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .spec-value {
            color: #666;
            font-size: 13px;
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .product-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .gallery-section {
                position: static;
                top: auto;
            }

            .container {
                padding: 24px 16px;
            }

            .nav-container {
                padding: 12px 16px;
            }

            .breadcrumbs {
                padding: 8px 16px;
            }
        }

        @media (max-width: 640px) {
            .container {
                padding: 16px 12px;
            }

            .product-title {
                font-size: 28px;
            }

            .price {
                font-size: 32px;
            }

            .product-grid {
                gap: 24px;
            }

            .product-meta {
                grid-template-columns: 1fr;
            }

            .variants-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .features-grid {
                grid-template-columns: 1fr;
            }

            .tabs {
                gap: 20px;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .tab {
                white-space: nowrap;
                font-size: 12px;
                padding: 12px 0;
            }

            .nav-container {
                padding: 12px 12px;
            }
        }

        /* CART SIDEBAR */
        #cart-sidebar {
            position: fixed;
            right: 0;
            top: 0;
            width: 384px;
            height: 100vh;
            background: white;
            box-shadow: -4px 0 20px rgba(0,0,0,0.1);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        #cart-sidebar.open {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #E8E8E8;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sidebar-header h2 {
            font-size: 18px;
            font-weight: 700;
        }

        .sidebar-close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
        }

        .sidebar-close-btn:hover {
            color: #000;
        }

        .sidebar-items {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }

        .cart-empty {
            text-align: center;
            color: #999;
            padding: 40px 20px;
        }

        .sidebar-summary {
            border-top: 1px solid #E8E8E8;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
        }

        .summary-row.total {
            font-weight: 700;
            font-size: 16px;
            border-top: 1px solid #E8E8E8;
            padding-top: 12px;
            margin-top: 12px;
        }

        .sidebar-actions {
            padding: 20px;
            border-top: 1px solid #E8E8E8;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar-btn {
            padding: 12px 16px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
        }

        .sidebar-btn-primary {
            background: #000;
            color: white;
        }

        .sidebar-btn-primary:hover {
            background: #1a1a1a;
        }

        .sidebar-btn-secondary {
            background: #f5f5f5;
            color: #000;
            border: 1px solid #E8E8E8;
        }

        .sidebar-btn-secondary:hover {
            background: #f0f0f0;
        }

        #cart-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease-in-out;
        }

        #cart-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        @media (max-width: 640px) {
            #cart-sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- PROFESSIONAL HEADER - UNIFIED DESIGN -->
    <header style="background: white; position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); border-bottom: 1px solid rgba(0, 0, 0, 0.05);">
        <div style="max-width: 1400px; margin: 0 auto; padding: 0 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; height: 70px;">
                <!-- Logo -->
                <a href="{{ route('home') }}" style="text-decoration: none; font-size: 26px; font-weight: 700; color: #1a1a2e; letter-spacing: -0.5px; flex-shrink: 0;">Vandora<span style="color: #ffa500;">X</span></a>

                <!-- Search Bar -->
                <div style="flex: 1; max-width: 450px; margin: 0 40px;">
                    <form action="{{ route('storefront.products.index') }}" method="GET" style="display: flex; gap: 6px;">
                        <input type="text" name="search" placeholder="Search products..." style="flex: 1; padding: 10px 16px; border: 1px solid #E8E8E8; border-radius: 6px 0 0 6px; font-size: 14px; background: white; transition: border-color 0.2s; outline: none;" value="{{ request('search', '') }}" onfocus="this.style.borderColor='#1a1a2e'" onblur="this.style.borderColor='#E8E8E8'">
                        <button type="submit" style="padding: 10px 24px; background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%); border: none; border-radius: 0 6px 6px 0; color: white; font-weight: 600; cursor: pointer; font-size: 14px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(26, 26, 46, 0.2);" onmouseover="this.style.boxShadow='0 6px 16px rgba(26, 26, 46, 0.3)'" onmouseout="this.style.boxShadow='0 4px 12px rgba(26, 26, 46, 0.2)'">Search</button>
                    </form>
                </div>

                <!-- Right Menu -->
                <div style="display: flex; align-items: center; gap: 28px; flex-shrink: 0;">
                    <!-- Wishlist Icon -->
                    @if(auth()->check())
                    <a href="{{ route('wishlist.index') }}" title="Wishlist" style="text-decoration: none; color: #1a1a2e; font-size: 22px; position: relative; display: flex; align-items: center; justify-content: center; transition: transform 0.2s, color 0.2s;" onmouseover="this.style.color='#ffa500'; this.style.transform='scale(1.1)'" onmouseout="this.style.color='#1a1a2e'; this.style.transform='scale(1)'">
                        <span>♡</span>
                        <span id="wishlist-count" style="position: absolute; top: -8px; right: -12px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #1a1a2e; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 12px; min-width: 20px; text-align: center; display: none; box-shadow: 0 2px 8px rgba(255, 165, 0, 0.3);">0</span>
                    </a>
                    @else
                    <a href="{{ route('customer.login') }}" title="Login to use wishlist" style="text-decoration: none; color: #999; font-size: 22px; position: relative; display: flex; align-items: center; justify-content: center; transition: all 0.2s; opacity: 0.6;" onmouseover="this.style.color='#ffa500'; this.style.opacity='1'; this.style.transform='scale(1.1)'" onmouseout="this.style.color='#999'; this.style.opacity='0.6'; this.style.transform='scale(1)'">
                        <span>♡</span>
                    </a>
                    @endif

                    <!-- Cart Icon -->
                    <a href="{{ route('cart.index') }}" title="Shopping Cart" style="text-decoration: none; color: #1a1a2e; font-size: 22px; position: relative; display: flex; align-items: center; justify-content: center; transition: transform 0.2s, color 0.2s;" onmouseover="this.style.color='#ffa500'; this.style.transform='scale(1.1)'" onmouseout="this.style.color='#1a1a2e'; this.style.transform='scale(1)'">
                        <span>🛒</span>
                        <span id="cart-count" style="position: absolute; top: -8px; right: -12px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #1a1a2e; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 12px; min-width: 20px; text-align: center; display: none; box-shadow: 0 2px 8px rgba(255, 165, 0, 0.3);">0</span>
                    </a>

                    <!-- User Menu / Auth -->
                    @if(auth()->check())
                        <div style="display: flex; align-items: center; gap: 16px; padding-left: 28px; border-left: 1px solid #E8E8E8;">
                            <div style="text-align: right;">
                                <div style="color: #1a1a2e; font-size: 13px; font-weight: 600;">{{ auth()->user()->name }}</div>
                                <div style="color: #999; font-size: 11px; margin-top: 2px;">Customer</div>
                            </div>
                            <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0;">
                                @csrf
                                <button type="submit" style="background: rgba(255, 165, 0, 0.1); border: none; color: #ffa500; cursor: pointer; font-weight: 600; font-size: 12px; padding: 6px 12px; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background='rgba(255, 165, 0, 0.2)'" onmouseout="this.style.background='rgba(255, 165, 0, 0.1)'">Logout</button>
                            </form>
                        </div>
                    @else
                        <div style="display: flex; gap: 12px; padding-left: 28px; border-left: 1px solid #E8E8E8;">
                            <a href="{{ route('customer.login') }}" style="color: #1a1a2e; text-decoration: none; font-weight: 600; font-size: 13px; padding: 6px 12px; border-radius: 4px; transition: all 0.2s;" onmouseover="this.style.background='#f5f5f5'" onmouseout="this.style.background='transparent'">Login</a>
                            <a href="{{ route('customer.register') }}" style="color: white; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); text-decoration: none; font-weight: 600; font-size: 13px; padding: 6px 12px; border-radius: 4px; display: inline-block; transition: all 0.2s; box-shadow: 0 2px 8px rgba(255, 165, 0, 0.3);" onmouseover="this.style.boxShadow='0 4px 12px rgba(255, 165, 0, 0.4)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(255, 165, 0, 0.3)'">Register</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <script>
        // Update cart count
        function updateCartCount() {
            fetch('{{ route('cart.count') }}')
                .then(r => r.json())
                .then(data => {
                    const count = data.count || 0;
                    const cartCountEl = document.getElementById('cart-count');
                    if (cartCountEl) {
                        if (count > 0) {
                            cartCountEl.textContent = count;
                            cartCountEl.style.display = 'block';
                        } else {
                            cartCountEl.style.display = 'none';
                        }
                    }
                })
                .catch(e => console.error('Cart count error:', e));
        }

        // Update wishlist count
        function updateWishlistCount() {
            @if(auth()->check())
            fetch('{{ route('wishlist.count') }}')
                .then(r => r.json())
                .then(data => {
                    const count = data.count || 0;
                    const wishlistCountEl = document.getElementById('wishlist-count');
                    if (wishlistCountEl) {
                        if (count > 0) {
                            wishlistCountEl.textContent = count;
                            wishlistCountEl.style.display = 'block';
                        } else {
                            wishlistCountEl.style.display = 'none';
                        }
                    }
                })
                .catch(e => console.error('Wishlist count error:', e));
            @endif
        }

        // Load counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
        });
    </script>

    <!-- BREADCRUMBS -->
    <div class="breadcrumbs">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('storefront.products.index') }}">Shop</a>
        @if($product->category)
            <span>/</span>
            <a href="{{ route('storefront.products.index', ['category' => $product->category->id]) }}">{{ $product->category->name }}</a>
        @endif
        <span>/</span>
        <span>{{ Str::limit($product->name, 50) }}</span>
    </div>

    <!-- MAIN CONTAINER -->
    <div class="container">
        <!-- PRODUCT GRID -->
        <div class="product-grid">
            <!-- GALLERY -->
            <div class="gallery-section">
                @php
                    $placeholders = [
                        'https://images.unsplash.com/photo-1591290619270-6c1b6c3dc45e?w=800&h=800&fit=crop&q=85',
                    ];
                    $mainImage = $product->images->first();
                    $fallbackImage = $placeholders[0];
                    $mainImageUrl = $mainImage ? $mainImage->getImageUrl() : $fallbackImage;
                @endphp

                <div class="main-image-container">
                    <img id="mainImage" src="{{ $mainImageUrl }}" alt="{{ $product->name }}" class="main-image">
                </div>

                @if($product->images->count() > 1)
                    <div class="thumbnail-grid">
                        @foreach($product->images as $index => $image)
                            <div class="thumbnail {{ $index === 0 ? 'active' : '' }}" onclick="changeImage('{{ $image->getImageUrl() }}', this)">
                                <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- PRODUCT INFO -->
            <div class="product-info">
                <span class="badge">{{ $product->moderation_status === 'approved' ? '✓ Verified' : 'Available' }}</span>

                <h1 class="product-title">{{ $product->name }}</h1>

                <div class="vendor-info">
                    <div class="vendor-avatar">{{ substr($product->vendor?->store_name ?? 'V', 0, 1) }}</div>
                    <div class="vendor-details">
                        <h3>{{ $product->vendor?->store_name ?? 'Premium Store' }}</h3>
                        <p>{{ $product->vendor?->user?->name ?? 'Verified Seller' }}</p>
                    </div>
                </div>

                <div class="price">PKR {{ number_format($product->base_price, 0) }}</div>

                <div class="product-meta">
                    <div class="meta-item">
                        <span class="meta-label">Category</span>
                        <span class="meta-value">{{ $product->category?->name ?? 'General' }}</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Stock</span>
                        <span class="meta-value {{ $product->getTotalStock() > 0 ? 'in-stock' : 'out-stock' }}">
                            {{ $product->getTotalStock() > 0 ? $product->getTotalStock() . ' Available' : 'Out of Stock' }}
                        </span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Delivery</span>
                        <span class="meta-value">{{ $product->delivery_time_days ?? 3 }} - {{ ($product->delivery_time_days ?? 3) + 2 }} Days</span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">SKU</span>
                        <span class="meta-value">{{ $product->sku ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- VARIANTS -->
                @if($product->variants->isNotEmpty())
                <div class="variants-section">
                    <div class="section-title">Select Variant</div>
                    <div class="variants-grid">
                        @foreach($product->variants as $variant)
                            <div class="variant-card" data-variant-id="{{ $variant->id }}" onclick="selectVariant(this, {{ $variant->price }}, {{ $variant->stock }})">
                                <div class="variant-value">{{ $variant->variant_value }}</div>
                                <div class="variant-price">PKR {{ number_format($variant->price, 0) }}</div>
                                <div class="variant-stock">{{ $variant->stock > 0 ? 'In stock' : 'Out' }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- QUANTITY -->
                <div class="quantity-section">
                    <div class="section-title">Quantity</div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="decreaseQty()">−</button>
                        <input type="number" id="quantity" class="quantity-input" value="1" min="1" max="{{ $product->getTotalStock() }}" readonly>
                        <button class="quantity-btn" onclick="increaseQty()">+</button>
                    </div>
                </div>

                <!-- BUTTONS -->
                <div class="buttons-section">
                    <button class="btn btn-primary" {{ $product->getTotalStock() > 0 ? '' : 'disabled' }} onclick="addToCart()">
                        🛒 Add to Cart
                    </button>
                    <a href="{{ route('cart.index') }}" class="btn btn-secondary">
                        📋 View Cart
                    </a>
                    @if(auth()->check())
                    <button class="btn btn-wishlist" onclick="addToWishlist()">
                        ♡ Save for Later
                    </button>
                    @else
                    <a href="{{ route('customer.login') }}" class="btn btn-wishlist">
                        Login to Save
                    </a>
                    @endif
                </div>

                <!-- FEATURES -->
                <div class="features-grid">
                    <div class="feature-item">
                        <div class="feature-icon">🚚</div>
                        <div class="feature-title">Fast Delivery</div>
                        <div class="feature-desc">{{ $product->delivery_time_days ?? 3 }}-{{ ($product->delivery_time_days ?? 3) + 2 }} days</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">✓</div>
                        <div class="feature-title">Secure</div>
                        <div class="feature-desc">100% Protected</div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">↻</div>
                        <div class="feature-title">Returns</div>
                        <div class="feature-desc">7 days return</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TABS -->
        <div class="tabs-container">
            <div class="tabs">
                <div class="tab active" onclick="switchTab(0)">Description</div>
                <div class="tab" onclick="switchTab(1)">Specifications</div>
                <div class="tab" onclick="switchTab(2)">Shipping & Returns</div>
            </div>

            <div class="tab-content active" id="tab-0">
                <div class="description-content">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>

            <div class="tab-content" id="tab-1">
                <div class="specs-table">
                    @if($product->specifications->count() > 0)
                        @foreach($product->specifications as $spec)
                            <div class="spec-row">
                                <div class="spec-label">{{ $spec->spec_key }}</div>
                                <div class="spec-value">{{ $spec->spec_value }}</div>
                            </div>
                        @endforeach
                    @else
                        <div class="spec-row">
                            <div class="spec-label">Product</div>
                            <div class="spec-value">{{ $product->name }}</div>
                        </div>
                    @endif
                    <div class="spec-row">
                        <div class="spec-label">SKU</div>
                        <div class="spec-value">{{ $product->sku ?? 'Not assigned' }}</div>
                    </div>
                    <div class="spec-row">
                        <div class="spec-label">Category</div>
                        <div class="spec-value">{{ $product->category?->name ?? 'General' }}</div>
                    </div>
                </div>
            </div>

            <div class="tab-content" id="tab-2">
                <div class="description-content">
                    <p><strong>Shipping:</strong> Delivery in {{ $product->delivery_time_days ?? 3 }}-{{ ($product->delivery_time_days ?? 3) + 2 }} business days. Free shipping on all orders.</p>
                    <p><strong>Returns:</strong> 7-day return policy for unused items in original packaging. Refund processed within 5-7 business days.</p>
                    <p><strong>Support:</strong> Available 24/7 through VandoraX platform. Direct chat with seller available.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CART SIDEBAR (Hidden by default) -->
    <div id="cart-overlay" onclick="closeCartSidebar()"></div>

    <div id="cart-sidebar">
        <div class="sidebar-header">
            <h2>Shopping Cart</h2>
            <button class="sidebar-close-btn" onclick="closeCartSidebar()">✕</button>
        </div>

        <div class="sidebar-items" id="cart-items-container">
            <div class="cart-empty">Your cart is empty</div>
        </div>

        <div class="sidebar-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="sidebar-subtotal">$0.00</span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span style="color: #22c55e;">Free</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="sidebar-total">$0.00</span>
            </div>
        </div>

        <div class="sidebar-actions">
            @if(auth()->check())
                <a href="{{ route('checkout.index') }}" class="sidebar-btn sidebar-btn-primary">Proceed to Checkout</a>
            @else
                <a href="{{ route('customer.login') }}" class="sidebar-btn sidebar-btn-primary">Login to Checkout</a>
            @endif
            <a href="{{ route('cart.index') }}" class="sidebar-btn sidebar-btn-secondary">View Full Cart</a>
            <button class="sidebar-btn sidebar-btn-secondary" onclick="closeCartSidebar()">Continue Shopping</button>
        </div>
    </div>

    <script>
        let selectedVariantId = null;

        function changeImage(imageUrl, thumbnail) {
            document.getElementById('mainImage').src = imageUrl;
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            thumbnail.classList.add('active');
        }

        function selectVariant(card, price, stock) {
            document.querySelectorAll('.variant-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');
            selectedVariantId = card.dataset.variantId;

            document.querySelectorAll('.product-grid')[0].style.opacity = '1';
        }

        function increaseQty() {
            const input = document.getElementById('quantity');
            const max = parseInt(input.max) || 99;
            if (parseInt(input.value) < max) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function decreaseQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function switchTab(index) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            document.querySelectorAll('.tab')[index].classList.add('active');
            document.getElementById('tab-' + index).classList.add('active');
        }

        function openCartSidebar() {
            document.getElementById('cart-sidebar').classList.add('open');
            document.getElementById('cart-overlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeCartSidebar() {
            document.getElementById('cart-sidebar').classList.remove('open');
            document.getElementById('cart-overlay').classList.remove('open');
            document.body.style.overflow = 'auto';
        }

        function updateCartSidebar() {
            fetch('/cart-sidebar/items')
                .then(r => r.json())
                .then(data => {
                    if (!data.success || data.total_items === 0) {
                        document.getElementById('cart-items-container').innerHTML = '<div class="cart-empty">Your cart is empty</div>';
                        document.getElementById('sidebar-subtotal').textContent = '$0.00';
                        document.getElementById('sidebar-total').textContent = '$0.00';
                        return;
                    }

                    let html = '';
                    data.items_by_vendor.forEach((vendorItems, idx) => {
                        html += '<div style="margin-bottom: 20px;">';
                        if (vendorItems[0]?.vendor_name) {
                            html += '<div style="font-size: 12px; font-weight: 600; color: #999; text-transform: uppercase; margin-bottom: 12px;">' + vendorItems[0].vendor_name + '</div>';
                        }

                        vendorItems.forEach(item => {
                            html += `
                                <div style="display: flex; gap: 12px; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #E8E8E8;">
                                    ${item.product_image ? `<img src="${item.product_image}" alt="${item.product_name}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">` : ''}
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #000; font-size: 13px; margin-bottom: 4px;">${item.product_name}</div>
                                        ${item.variant_name ? `<div style="font-size: 12px; color: #999;">${item.variant_name}</div>` : ''}
                                        <div style="font-size: 12px; color: #999;">Qty: ${item.quantity}</div>
                                        <div style="font-weight: 600; color: #000; margin-top: 4px;">Rs. ${parseFloat(item.total).toLocaleString()}</div>
                                        <button onclick="removeFromCart(${item.id})" style="background: none; border: none; color: #ef4444; font-size: 12px; cursor: pointer; margin-top: 4px;">[Remove]</button>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    });

                    document.getElementById('cart-items-container').innerHTML = html;
                    document.getElementById('sidebar-subtotal').textContent = 'PKR ' + parseFloat(data.subtotal).toLocaleString();
                    document.getElementById('sidebar-total').textContent = 'PKR ' + parseFloat(data.subtotal).toLocaleString();
                })
                .catch(e => console.error('Error:', e));
        }

        function removeFromCart(cartItemId) {
            if (!confirm('Remove this item?')) return;

            fetch(`/cart/items/${cartItemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateCartSidebar();
                }
            });
        }

        function addToCart() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const btn = event.target;
            const variantId = selectedVariantId || null;

            btn.disabled = true;
            btn.textContent = 'Adding...';

            fetch('{{ route('cart.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }},
                    variant_id: variantId,
                    quantity: quantity
                })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.textContent = '✓ Added!';
                    document.getElementById('quantity').value = 1;
                    updateCartSidebar();
                    openCartSidebar();
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.textContent = '🛒 Add to Cart';
                    }, 2000);
                } else {
                    alert(data.message || 'Error adding to cart');
                    btn.disabled = false;
                    btn.textContent = '🛒 Add to Cart';
                }
            })
            .catch(e => {
                console.error('Error:', e);
                btn.disabled = false;
                btn.textContent = '🛒 Add to Cart';
            });
        }

        function addToWishlist() {
            const btn = event.target;
            btn.disabled = true;
            btn.textContent = 'Adding...';

            fetch('{{ route('wishlist.add') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    product_id: {{ $product->id }}
                })
            })
            .then(r => {
                console.log('Status:', r.status);
                return r.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('JSON parse error:', e, 'Text:', text);
                        throw new Error('Invalid response from server');
                    }
                });
            })
            .then(data => {
                console.log('Parsed data:', data);
                if (data.success) {
                    showSuccessPopup('✓ Added to Wishlist!', 'Item saved for later');
                    btn.textContent = '♡ Saved!';
                    setTimeout(() => {
                        btn.disabled = false;
                        btn.textContent = '♡ Save for Later';
                    }, 2000);
                } else {
                    showErrorPopup('Error', data.message || 'Could not save item');
                    btn.disabled = false;
                    btn.textContent = '♡ Save for Later';
                }
            })
            .catch(e => {
                console.error('Wishlist error:', e);
                showErrorPopup('Error', e.message || 'Could not save item. Please try again.');
                btn.disabled = false;
                btn.textContent = '♡ Save for Later';
            });
        }

        function showSuccessPopup(title, message) {
            const popup = document.createElement('div');
            popup.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                padding: 32px;
                max-width: 400px;
                text-align: center;
                z-index: 2000;
                animation: slideIn 0.3s ease-out;
            `;

            popup.innerHTML = `
                <div style="font-size: 48px; margin-bottom: 16px;">✓</div>
                <h3 style="margin: 0 0 8px 0; color: #000; font-size: 20px; font-weight: 600;">${title}</h3>
                <p style="margin: 0; color: #666; font-size: 14px;">${message}</p>
            `;

            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.3);
                z-index: 1999;
            `;

            document.body.appendChild(overlay);
            document.body.appendChild(popup);

            setTimeout(() => {
                popup.style.opacity = '0';
                overlay.style.opacity = '0';
                popup.style.transition = 'opacity 0.3s ease-out';
                overlay.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => {
                    popup.remove();
                    overlay.remove();
                }, 300);
            }, 2000);
        }

        function showErrorPopup(title, message) {
            const popup = document.createElement('div');
            popup.style.cssText = `
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                padding: 32px;
                max-width: 400px;
                text-align: center;
                z-index: 2000;
                animation: slideIn 0.3s ease-out;
            `;

            popup.innerHTML = `
                <div style="font-size: 48px; margin-bottom: 16px;">⚠</div>
                <h3 style="margin: 0 0 8px 0; color: #ef4444; font-size: 20px; font-weight: 600;">${title}</h3>
                <p style="margin: 0 0 20px 0; color: #666; font-size: 14px;">${message}</p>
                <button onclick="this.closest('div').parentElement.remove(); document.querySelector('div[style*=\"rgba(0,0,0,0.3)\"]')?.remove();" style="
                    background: #000;
                    color: white;
                    border: none;
                    padding: 10px 24px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 600;
                    font-size: 14px;
                ">Close</button>
            `;

            const overlay = document.createElement('div');
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.3);
                z-index: 1999;
            `;

            document.body.appendChild(overlay);
            document.body.appendChild(popup);
        }

        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translate(-50%, -45%);
                }
                to {
                    opacity: 1;
                    transform: translate(-50%, -50%);
                }
            }
        `;
        document.head.appendChild(style);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeCartSidebar();
        });
    </script>
</body>
</html>

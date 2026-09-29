<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Wishlist - VandoraX</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #FFFFFF;
            color: #000000;
            line-height: 1.6;
        }

        /* HEADER */
        .header {
            background: #FFFFFF;
            border-bottom: 1px solid #E8E8E8;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 24px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: #000000;
            text-decoration: none;
        }

        .nav-back {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #000000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: opacity 0.3s ease;
        }

        .nav-back:hover {
            opacity: 0.6;
        }

        /* CONTAINER */
        .wishlist-container {
            max-width: 1400px;
            margin: 48px auto;
            padding: 0 48px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 48px;
        }

        .page-title {
            font-size: 42px;
            font-weight: 700;
        }

        .page-subtitle {
            font-size: 16px;
            color: #666666;
            margin-top: 8px;
        }

        .btn-clear-all {
            padding: 12px 32px;
            background: transparent;
            color: #000000;
            border: 1px solid #E8E8E8;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-clear-all:hover {
            border-color: #000000;
            background: #000000;
            color: #FFFFFF;
        }

        /* PRODUCT GRID */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            margin-bottom: 64px;
        }

        .product-card {
            position: relative;
            background: #FFFFFF;
            border: 1px solid #E8E8E8;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            border-color: #000000;
        }

        .product-image {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: #F8F7F5;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .remove-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid #E8E8E8;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .remove-btn:hover {
            background: #000000;
            border-color: #000000;
            color: #FFFFFF;
        }

        .product-info {
            padding: 20px;
        }

        .product-category {
            font-size: 11px;
            color: #666666;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .product-vendor {
            font-size: 13px;
            color: #666666;
            margin-bottom: 16px;
        }

        .product-price {
            font-size: 18px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 16px;
        }

        .btn-add-to-cart {
            width: 100%;
            padding: 14px;
            background: #000000;
            color: #FFFFFF;
            border: none;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn-add-to-cart:hover {
            background: #333333;
        }

        /* EMPTY STATE */
        .empty-wishlist {
            text-align: center;
            padding: 120px 24px;
        }

        .empty-icon {
            font-size: 80px;
            margin-bottom: 24px;
            opacity: 0.3;
        }

        .empty-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .empty-text {
            font-size: 16px;
            color: #666666;
            margin-bottom: 32px;
        }

        .btn-primary {
            display: inline-block;
            padding: 16px 48px;
            background: #000000;
            color: #FFFFFF;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #333333;
        }

        /* LOADING */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading.active {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #FFFFFF;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .nav-container,
            .wishlist-container {
                padding: 16px 24px;
            }

            .page-title {
                font-size: 32px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 16px;
            }
        }

        @media (max-width: 480px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading" id="loading">
        <div class="spinner"></div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">VANDORAX</a>
            <a href="{{ route('storefront.products.index') }}" class="nav-back">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Continue Shopping
            </a>
        </div>
    </header>

    <!-- Wishlist Content -->
    <div class="wishlist-container">
        @if($wishlists->count() > 0)
            <div class="page-header">
                <div>
                    <h1 class="page-title">My Wishlist</h1>
                    <p class="page-subtitle">{{ $wishlists->count() }} {{ Str::plural('item', $wishlists->count()) }} saved</p>
                </div>
                <button class="btn-clear-all" onclick="clearWishlist()">Clear All</button>
            </div>

            <div class="product-grid">
                @foreach($wishlists as $wishlist)
                    @php
                        $product = $wishlist->product;
                        $image = $product->images->first();
                        $placeholders = [
                            'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=400&h=400&fit=crop&q=85',
                            'https://images.unsplash.com/photo-1467043237213-65f2da53396f?w=400&h=400&fit=crop&q=85',
                        ];
                        $fallbackImage = $placeholders[($product->id % 2)];
                        $imageUrl = $image ? $image->getImageUrl() : $fallbackImage;
                    @endphp

                    <div class="product-card" data-wishlist-id="{{ $wishlist->id }}">
                        <div class="product-image">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
                            <button class="remove-btn" onclick="removeFromWishlist({{ $wishlist->id }})">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="product-info">
                            <div class="product-category">{{ $product->category->name ?? 'Product' }}</div>
                            <h3 class="product-name">{{ $product->name }}</h3>
                            <div class="product-vendor">{{ $product->vendor->store_name ?? 'Store' }}</div>
                            <div class="product-price">Rs. {{ number_format($product->price, 0) }}</div>
                            <a href="{{ route('storefront.products.show', $product->id) }}" class="btn-add-to-cart">
                                View Product
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-wishlist">
                <div class="empty-icon">❤️</div>
                <h2 class="empty-title">Your Wishlist is Empty</h2>
                <p class="empty-text">Save your favorite items for later!</p>
                <a href="{{ route('storefront.products.index') }}" class="btn-primary">Start Shopping</a>
            </div>
        @endif
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showLoading() {
            document.getElementById('loading').classList.add('active');
        }

        function hideLoading() {
            document.getElementById('loading').classList.remove('active');
        }

        async function removeFromWishlist(wishlistId) {
            if (!confirm('Remove this item from your wishlist?')) {
                return;
            }

            showLoading();

            try {
                const response = await fetch(`/wishlist/${wishlistId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Remove card from DOM
                    const card = document.querySelector(`[data-wishlist-id="${wishlistId}"]`);
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();

                        // Check if wishlist is now empty
                        const remainingCards = document.querySelectorAll('.product-card').length;
                        if (remainingCards === 0) {
                            location.reload();
                        } else {
                            updatePageSubtitle();
                        }
                    }, 300);
                } else {
                    alert(data.message || 'Failed to remove item');
                    hideLoading();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                hideLoading();
            }
        }

        async function clearWishlist() {
            if (!confirm('Are you sure you want to clear your entire wishlist?')) {
                return;
            }

            showLoading();

            try {
                const response = await fetch('/wishlist/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            } finally {
                hideLoading();
            }
        }

        function updatePageSubtitle() {
            const count = document.querySelectorAll('.product-card').length;
            const subtitle = document.querySelector('.page-subtitle');
            if (subtitle) {
                subtitle.textContent = `${count} ${count === 1 ? 'item' : 'items'} saved`;
            }
        }
    </script>
</body>
</html>

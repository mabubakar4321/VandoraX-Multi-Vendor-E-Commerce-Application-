<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - VandoraX</title>

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

        /* Header styles (simplified version) */
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

        /* Collection header */
        .collection-header {
            max-width: 1400px;
            margin: 80px auto 64px;
            padding: 0 48px;
            text-align: center;
        }

        .collection-title {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .collection-description {
            font-size: 16px;
            color: #666666;
            letter-spacing: 0.5px;
            max-width: 600px;
            margin: 0 auto;
        }

        .collection-count {
            font-size: 14px;
            color: #999999;
            margin-top: 24px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Products grid */
        .products-section {
            max-width: 1400px;
            margin: 0 auto 120px;
            padding: 0 48px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            margin-bottom: 64px;
        }

        .product-card {
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .product-card:hover {
            opacity: 0.8;
        }

        .product-image {
            width: 100%;
            aspect-ratio: 3/4;
            background: #F8F7F5;
            margin-bottom: 16px;
            overflow: hidden;
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

        .product-info {
            text-align: left;
        }

        .product-name {
            font-size: 14px;
            font-weight: 600;
            color: #000000;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }

        .product-fabric {
            font-size: 13px;
            color: #666666;
            margin-bottom: 8px;
        }

        .product-price {
            font-size: 16px;
            font-weight: 700;
            color: #000000;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 16px;
            align-items: center;
        }

        .pagination a,
        .pagination span {
            padding: 12px 20px;
            border: 1px solid #E8E8E8;
            color: #000000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #000000;
            color: #FFFFFF;
            border-color: #000000;
        }

        .pagination .active {
            background: #000000;
            color: #FFFFFF;
            border-color: #000000;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 120px 24px;
        }

        .empty-state h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: 1px;
        }

        .empty-state p {
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
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #333333;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }

            .collection-title {
                font-size: 36px;
            }
        }

        @media (max-width: 768px) {
            .nav-container,
            .collection-header,
            .products-section {
                padding: 16px 24px;
            }

            .collection-header {
                margin: 48px auto 32px;
            }

            .collection-title {
                font-size: 28px;
                letter-spacing: 1px;
            }

            .products-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .products-section {
                margin-bottom: 64px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">VANDORAX</a>
            <a href="{{ route('home') }}" class="nav-back">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Back to Home
            </a>
        </div>
    </header>

    <!-- Collection Header -->
    <section class="collection-header">
        <h1 class="collection-title">{{ $title }}</h1>
        <p class="collection-description">{{ $description }}</p>
        <p class="collection-count">{{ $products->total() }} {{ Str::plural('item', $products->total()) }}</p>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        @if($products->count() > 0)
            <div class="products-grid">
                @foreach($products as $product)
                    @php
                        $placeholders = [
                            'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=600&h=800&fit=crop&q=80',
                            'https://images.unsplash.com/photo-1467043237213-65f2da53396f?w=600&h=800&fit=crop&q=80',
                            'https://images.unsplash.com/photo-1496747611176-843222e1e57c?w=600&h=800&fit=crop&q=80',
                            'https://images.unsplash.com/photo-1525507119028-ed4c629a60a3?w=600&h=800&fit=crop&q=80',
                        ];
                        $fallbackImage = $placeholders[($product->id % 4)];

                        // Get product image with fallback
                        $productImage = $product->images->first();
                        $imageUrl = $productImage ? $productImage->getImageUrl() : $fallbackImage;
                    @endphp
                    <div class="product-card" onclick="window.location='{{ route('storefront.products.show', $product) }}'">
                        <div class="product-image">
                            <img src="{{ $imageUrl }}"
                                 alt="{{ $product->name }}"
                                 loading="lazy"
                                 onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
                        </div>
                        <div class="product-info">
                            <div class="product-name">{{ Str::limit($product->name, 50) }}</div>
                            <div class="product-fabric">{{ $product->category?->name ?? 'Premium Collection' }}</div>
                            <div class="product-price">Rs. {{ number_format($product->base_price) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($products->hasPages())
                <div class="pagination">
                    @if($products->onFirstPage())
                        <span style="opacity: 0.3;">Previous</span>
                    @else
                        <a href="{{ $products->previousPageUrl() }}">Previous</a>
                    @endif

                    <span class="active">{{ $products->currentPage() }}</span>
                    <span>of</span>
                    <span>{{ $products->lastPage() }}</span>

                    @if($products->hasMorePages())
                        <a href="{{ $products->nextPageUrl() }}">Next</a>
                    @else
                        <span style="opacity: 0.3;">Next</span>
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
</body>
</html>

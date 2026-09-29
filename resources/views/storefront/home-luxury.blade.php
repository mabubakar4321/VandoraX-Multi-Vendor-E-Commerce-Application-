<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VandoraX - Luxury Fashion</title>

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
            overflow-x: hidden;
        }

        /* HEADER */
        .header {
            position: sticky;
            top: 0;
            background: #FFFFFF;
            border-bottom: 1px solid #E8E8E8;
            z-index: 1000;
            transition: box-shadow 0.3s ease;
        }

        .header.scrolled {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .announcement-bar {
            background: #000000;
            color: #FFFFFF;
            text-align: center;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
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

        .nav-links {
            display: flex;
            gap: 48px;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: #000000;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: opacity 0.3s ease;
        }

        .nav-links a:hover {
            opacity: 0.6;
        }

        .nav-icons {
            display: flex;
            gap: 24px;
            align-items: center;
        }

        .nav-icon {
            width: 22px;
            height: 22px;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }

        .nav-icon:hover {
            opacity: 0.6;
        }

        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
        }

        .hamburger span {
            width: 24px;
            height: 2px;
            background: #000000;
            transition: all 0.3s ease;
        }

        /* HERO SECTION */
        .hero {
            width: 100%;
            height: 85vh;
            min-height: 600px;
            max-height: 900px;
            position: relative;
            overflow: hidden;
            background: #F8F7F5;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .hero-content {
            position: absolute;
            bottom: 80px;
            left: 48px;
            color: #FFFFFF;
            z-index: 10;
        }

        .hero-title {
            font-size: 48px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 16px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .hero-cta {
            display: inline-block;
            margin-top: 24px;
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

        .hero-cta:hover {
            background: #FFFFFF;
            color: #000000;
        }

        /* COLLECTION TABS */
        .collections-section {
            max-width: 1400px;
            margin: 80px auto;
            padding: 0 48px;
        }

        .section-title {
            text-align: center;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 48px;
            color: #000000;
        }

        .tabs {
            display: flex;
            justify-content: center;
            gap: 48px;
            margin-bottom: 64px;
            border-bottom: 1px solid #E8E8E8;
        }

        .tab {
            padding: 16px 8px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #666666;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab:hover {
            color: #000000;
        }

        .tab.active {
            color: #000000;
            border-bottom-color: #000000;
        }

        /* PRODUCT GRID */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 32px;
            margin-bottom: 80px;
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

        /* CATEGORIES SHOWCASE */
        .categories-section {
            max-width: 1400px;
            margin: 120px auto;
            padding: 0 48px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .category-card {
            position: relative;
            height: 400px;
            overflow: hidden;
            cursor: pointer;
        }

        .category-card.large {
            grid-column: span 2;
            grid-row: span 2;
            height: auto;
        }

        .category-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }

        .category-card:hover .category-image {
            transform: scale(1.08);
        }

        .category-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .category-card:hover .category-overlay {
            background: rgba(0, 0, 0, 0.35);
        }

        .category-name {
            color: #FFFFFF;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        /* NEWSLETTER */
        .newsletter-section {
            max-width: 800px;
            margin: 120px auto;
            padding: 80px 48px;
            text-align: center;
            background: #F8F7F5;
        }

        .newsletter-title {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }

        .newsletter-subtitle {
            font-size: 14px;
            color: #666666;
            margin-bottom: 32px;
            letter-spacing: 0.5px;
        }

        .newsletter-form {
            display: flex;
            max-width: 500px;
            margin: 0 auto;
            gap: 0;
        }

        .newsletter-input {
            flex: 1;
            padding: 16px 24px;
            border: 1px solid #000000;
            background: #FFFFFF;
            font-size: 14px;
            outline: none;
        }

        .newsletter-button {
            padding: 16px 32px;
            background: #000000;
            color: #FFFFFF;
            border: 1px solid #000000;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .newsletter-button:hover {
            background: #FFFFFF;
            color: #000000;
        }

        /* FOOTER */
        .footer {
            background: #000000;
            color: #FFFFFF;
            padding: 80px 48px 32px;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 48px;
            margin-bottom: 64px;
        }

        .footer-column h3 {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-bottom: 24px;
            opacity: 0.6;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 12px;
        }

        .footer-links a {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 14px;
            letter-spacing: 0.5px;
            transition: opacity 0.3s ease;
        }

        .footer-links a:hover {
            opacity: 0.6;
        }

        .footer-social {
            display: flex;
            gap: 16px;
        }

        .social-icon {
            width: 24px;
            height: 24px;
            opacity: 0.8;
            transition: opacity 0.3s ease;
            cursor: pointer;
        }

        .social-icon:hover {
            opacity: 1;
        }

        .footer-bottom {
            padding-top: 32px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            font-size: 12px;
            opacity: 0.6;
            letter-spacing: 0.5px;
        }

        /* WHATSAPP CTA */
        .whatsapp-float {
            position: fixed;
            bottom: 32px;
            right: 32px;
            width: 60px;
            height: 60px;
            background: #25D366;
            color: #FFFFFF;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            cursor: pointer;
            box-shadow: 0 4px 16px rgba(37, 211, 102, 0.4);
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .whatsapp-float:hover {
            transform: scale(1.1);
        }

        /* RESPONSIVE */
        @media (max-width: 1024px) {
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }

            .categories-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .category-card.large {
                grid-column: span 1;
                grid-row: span 1;
            }

            .footer-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 32px;
            }
        }

        @media (max-width: 768px) {
            .nav-container {
                padding: 16px 24px;
            }

            .nav-links {
                display: none;
            }

            .hamburger {
                display: flex;
            }

            .hero {
                height: 70vh;
                min-height: 500px;
            }

            .hero-content {
                left: 24px;
                bottom: 40px;
            }

            .hero-title {
                font-size: 32px;
                letter-spacing: 1px;
            }

            .section-title {
                font-size: 28px;
                letter-spacing: 1px;
            }

            .collections-section,
            .categories-section {
                padding: 0 24px;
                margin: 48px auto;
            }

            .tabs {
                gap: 24px;
                overflow-x: auto;
                justify-content: flex-start;
                padding-bottom: 8px;
            }

            .tab {
                white-space: nowrap;
                font-size: 12px;
            }

            .products-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .categories-grid {
                grid-template-columns: 1fr;
            }

            .category-card {
                height: 350px;
            }

            .newsletter-section {
                padding: 48px 24px;
                margin: 64px 24px;
            }

            .newsletter-form {
                flex-direction: column;
            }

            .newsletter-button {
                width: 100%;
            }

            .footer {
                padding: 48px 24px 24px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 32px;
            }

            .whatsapp-float {
                width: 56px;
                height: 56px;
                bottom: 24px;
                right: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        Free Shipping on Orders Over Rs. 5,000
    </div>

    <!-- Header -->
    <header class="header" id="header">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">VANDORAX</a>

            <nav>
                <ul class="nav-links">
                    <li><a href="{{ route('collections.new-arrivals') }}">New Arrivals</a></li>
                    <li><a href="#collections">Collections</a></li>
                    <li><a href="#categories">Categories</a></li>
                    <li><a href="{{ route('storefront.products.index') }}">Shop</a></li>
                </ul>
            </nav>

            <div class="nav-icons">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
                @auth
                    <a href="{{ auth()->user()->hasRole('customer') ? route('customer.dashboard') : route('vendor.dashboard') }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('customer.login') }}">
                        <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </a>
                @endauth
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <img src="https://images.unsplash.com/photo-1539533018447-63fcce2678e3?w=1600&h=1200&fit=crop&q=80" alt="Luxury Fashion Collection" class="hero-image">
        <div class="hero-content">
            <h1 class="hero-title">Spring Summer 2026</h1>
            <a href="#collections" class="hero-cta">Explore Collection</a>
        </div>
    </section>

    <!-- Collections Section with Tabs -->
    <section class="collections-section" id="collections">
        <h2 class="section-title">Featured Collections</h2>

        <div class="tabs">
            <div class="tab active" data-tab="new-arrivals" onclick="window.location='{{ route('collections.new-arrivals') }}'">New Arrivals</div>
            <div class="tab" data-tab="bestsellers" onclick="window.location='{{ route('collections.bestsellers') }}'">Bestsellers</div>
            <div class="tab" data-tab="summer" onclick="window.location='{{ route('collections.summer') }}'">Summer Edition</div>
        </div>

        <div class="products-grid" id="products-container">
            @forelse($products->take(8) as $product)
                @php
                    // High-quality fashion placeholder images
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
                <div class="product-card" data-category="{{ $product->category?->name ?? 'all' }}" onclick="window.location='{{ route('storefront.products.show', $product) }}'">
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
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 80px 24px;">
                    <p style="font-size: 18px; color: #666666; letter-spacing: 0.5px;">New collections launching soon</p>
                </div>
            @endforelse
        </div>

        @if($products->count() >= 8)
            <div style="text-align: center; margin-top: 48px;">
                <a href="{{ route('storefront.products.index') }}" class="hero-cta" style="position: static; color: #FFFFFF;">
                    View All Products
                </a>
            </div>
        @endif
    </section>

    <!-- Categories Showcase -->
    <section class="categories-section" id="categories">
        <h2 class="section-title">Shop by Category</h2>

        <div class="categories-grid">
            @php
                $categoryImages = [
                    'Fashion' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=800&h=1000&fit=crop&q=80',
                    'Jewelry' => 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=800&h=1000&fit=crop&q=80',
                    'Beauty' => 'https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=1000&fit=crop&q=80',
                    'Electronics' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=800&h=1000&fit=crop&q=80',
                    'Home & Garden' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800&h=1000&fit=crop&q=80',
                    'Sports' => 'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?w=800&h=1000&fit=crop&q=80',
                    'Books' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=800&h=1000&fit=crop&q=80',
                    'Furniture' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&h=1000&fit=crop&q=80',
                ];
            @endphp

            @foreach($categories->take(6) as $index => $category)
                <div class="category-card {{ $index === 0 ? 'large' : '' }}" onclick="window.location='{{ route('collections.category', $category->id) }}'">
                    <img src="{{ $categoryImages[$category->name] ?? 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=800&h=1000&fit=crop&q=80' }}"
                         alt="{{ $category->name }}"
                         class="category-image"
                         loading="lazy">
                    <div class="category-overlay">
                        <h3 class="category-name">{{ $category->name }}</h3>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter-section">
        <h2 class="newsletter-title">Join Our Newsletter</h2>
        <p class="newsletter-subtitle">Be the first to know about new collections and exclusive offers</p>
        <form class="newsletter-form">
            <input type="email" placeholder="Enter your email address" class="newsletter-input" required>
            <button type="submit" class="newsletter-button">Subscribe</button>
        </form>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul class="footer-links">
                        <li><a href="tel:+923001234567">+92 300 1234567</a></li>
                        <li><a href="mailto:info@vandorax.com">info@vandorax.com</a></li>
                        <li>Karachi, Pakistan</li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>About</h3>
                    <ul class="footer-links">
                        <li><a href="#">Our Story</a></li>
                        <li><a href="#">Craftsmanship</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Press</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Customer Care</h3>
                    <ul class="footer-links">
                        <li><a href="#">Shipping & Returns</a></li>
                        <li><a href="#">Size Guide</a></li>
                        <li><a href="#">Care Instructions</a></li>
                        <li><a href="#">FAQs</a></li>
                    </ul>
                </div>

                <div class="footer-column">
                    <h3>Follow Us</h3>
                    <div class="footer-social">
                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" fill="none" stroke="#000" stroke-width="2"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="#000" stroke-width="2"></line>
                        </svg>
                        <svg class="social-icon" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2026 VandoraX. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/923001234567" target="_blank" class="whatsapp-float" title="Chat on WhatsApp">
        <svg viewBox="0 0 24 24" fill="currentColor" width="32" height="32">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>

    <script>
        // Sticky header with shadow on scroll
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // Tab switching functionality with filtering
        const tabs = document.querySelectorAll('.tab');
        const productsContainer = document.getElementById('products-container');
        const allProducts = Array.from(productsContainer.children);

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const selectedTab = tab.dataset.tab;

                // Filter products based on tab
                allProducts.forEach((product, index) => {
                    if (product.style.gridColumn) return; // Skip "coming soon" message

                    let shouldShow = false;

                    if (selectedTab === 'new-arrivals') {
                        // Show first 8 products (newest)
                        shouldShow = index < 8;
                    } else if (selectedTab === 'bestsellers') {
                        // Simulate bestsellers - show products with certain categories
                        const category = product.dataset.category;
                        shouldShow = ['Fashion', 'Jewelry', 'Beauty'].includes(category) || Math.random() > 0.5;
                    } else if (selectedTab === 'summer') {
                        // Summer collection - lighter categories
                        const category = product.dataset.category;
                        shouldShow = ['Fashion', 'Beauty', 'Sports'].includes(category) || Math.random() > 0.6;
                    }

                    // Animate visibility
                    if (shouldShow) {
                        product.style.display = 'block';
                        product.style.animation = 'fadeIn 0.5s ease-in-out';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        });

        // Add fade-in animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

        // Newsletter form submission
        document.querySelector('.newsletter-form').addEventListener('submit', (e) => {
            e.preventDefault();
            const email = e.target.querySelector('input').value;
            alert('Thank you for subscribing!');
            e.target.reset();
        });

        // Mobile hamburger menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');

        hamburger?.addEventListener('click', () => {
            navLinks.style.display = navLinks.style.display === 'flex' ? 'none' : 'flex';
        });
    </script>
</body>
</html>

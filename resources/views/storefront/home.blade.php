@extends('layouts.storefront')

@section('content')
    <section class="hero-luxury">
        <div class="row">
            {{-- Content within hero-luxury starts around line 283 --}}
            {{-- Need to leave hero-luxury styles in push('styles') and keep hero structure in content --}}

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: 'Poppins', sans-serif;
            background: #ffffff;
            color: #1a1a1a;
            scroll-behavior: smooth;
        }

        /* ===== PREMIUM GRADIENTS ===== */
        .gradient-gold {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        }

        .gradient-royal {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }

        .gradient-emerald {
            background: linear-gradient(135deg, #0d6e3e 0%, #1fb584 100%);
        }

        .gradient-platinum {
            background: linear-gradient(135deg, #e8e8e8 0%, #f5f5f5 100%);
        }

        .gradient-sunset {
            background: linear-gradient(135deg, #ff6b6b 0%, #ff8e53 50%, #ffa94d 100%);
        }

        /* ===== TYPOGRAPHY ===== */
        .title-luxury {
            font-family: 'Playfair Display', serif;
            font-size: 72px;
            font-weight: 900;
            letter-spacing: -2px;
            line-height: 1.1;
            background: linear-gradient(135deg, #1a1a1a 0%, #4a4a4a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle-luxury {
            font-size: 28px;
            font-weight: 300;
            letter-spacing: 1px;
            color: #666;
            margin-top: 16px;
        }

        .heading-section {
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 24px;
            position: relative;
            display: inline-block;
        }

        .heading-section::after {
            content: '';
            position: absolute;
            bottom: -12px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
            border-radius: 2px;
        }

        /* ===== PREMIUM BUTTONS ===== */
        .btn-luxury-primary {
            padding: 18px 48px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            color: white;
            text-decoration: none;
            display: inline-block;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(26, 26, 46, 0.3);
            letter-spacing: 1px;
        }

        .btn-luxury-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-luxury-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(26, 26, 46, 0.5);
        }

        .btn-luxury-primary:hover::before {
            left: 100%;
        }

        .btn-luxury-secondary {
            padding: 18px 48px;
            font-size: 16px;
            font-weight: 700;
            border: 3px solid #1a1a2e;
            border-radius: 12px;
            cursor: pointer;
            background: transparent;
            color: #1a1a2e;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
            display: inline-block;
            position: relative;
            letter-spacing: 1px;
        }

        .btn-luxury-secondary::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: #1a1a2e;
            z-index: -1;
            transition: left 0.4s;
        }

        .btn-luxury-secondary:hover {
            color: white;
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(26, 26, 46, 0.3);
        }

        .btn-luxury-secondary:hover::after {
            left: 0;
        }

        .btn-luxury-gold {
            padding: 18px 48px;
            font-size: 16px;
            font-weight: 700;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1a1a2e;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 10px 30px rgba(255, 167, 0, 0.3);
            letter-spacing: 1px;
        }

        .btn-luxury-gold:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(255, 167, 0, 0.5);
        }

        /* ===== PREMIUM CARDS ===== */
        .card-luxury {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .card-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 50%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .card-luxury:hover {
            transform: translateY(-12px);
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.15);
        }

        .card-luxury:hover::before {
            opacity: 1;
        }

        /* ===== HEADER / NAVIGATION ===== */
        .nav-luxury {
            background: white;
            padding: 24px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .logo-luxury {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
            font-family: 'Playfair Display', serif;
        }

        .nav-links-luxury {
            display: flex;
            gap: 48px;
            align-items: center;
            list-style: none;
        }

        .nav-link-luxury {
            color: #1a1a1a;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            position: relative;
            transition: color 0.3s;
            letter-spacing: 0.5px;
        }

        .nav-link-luxury::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
            transition: width 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .nav-link-luxury:hover {
            color: #FFD700;
        }

        .nav-link-luxury:hover::after {
            width: 100%;
        }

        /* ===== HERO SECTION ===== */
        .hero-luxury {
            min-height: 100vh;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 50%, #f0f4f8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-luxury::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .hero-luxury::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(15, 52, 96, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 25s ease-in-out infinite reverse;
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .hero-text h1 {
            font-family: 'Playfair Display', serif;
            font-size: 80px;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 24px;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -3px;
        }

        .hero-text p {
            font-size: 20px;
            color: #666;
            line-height: 1.8;
            margin-bottom: 32px;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .hero-buttons {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
        }

        .hero-visual {
            position: relative;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-visual::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            border-radius: 20px;
            opacity: 0.1;
            animation: rotate 20s linear infinite;
            transform: rotate(45deg);
        }

        .hero-visual::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            border-radius: 20px;
            opacity: 0.1;
            animation: rotate 20s linear infinite reverse;
            transform: rotate(-45deg);
        }

        .hero-text-overlay {
            position: relative;
            z-index: 3;
            font-family: 'Playfair Display', serif;
            font-size: 56px;
            font-weight: 800;
            color: #1a1a2e;
            text-align: center;
            line-height: 1.2;
        }

        /* ===== STATS SECTION ===== */
        .stats-luxury {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            color: white;
            padding: 80px 40px;
            margin-bottom: 120px;
        }

        .stats-grid {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
        }

        .stat-item {
            text-align: center;
            position: relative;
        }

        .stat-item::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
            border-radius: 2px;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 64px;
            font-weight: 900;
            margin-bottom: 8px;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
            letter-spacing: 1px;
        }

        /* ===== CATEGORIES SECTION ===== */
        .section-categories {
            max-width: 1400px;
            margin: 0 auto;
            padding: 120px 40px;
            position: relative;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-header .heading-section {
            display: block;
            margin: 0 auto 24px;
        }

        .section-subtitle {
            font-size: 20px;
            color: #666;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 32px;
        }

        .category-card-luxury {
            position: relative;
            height: 280px;
            border-radius: 20px;
            overflow: hidden;
            cursor: pointer;
            group: 'hover';
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            font-weight: 700;
            font-size: 24px;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .category-card-luxury::before {
            content: '';
            position: absolute;
            inset: 0;
            background-size: 120%;
            background-position: center;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: -1;
        }

        .category-card-luxury:nth-child(1)::before {
            background: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
        }

        .category-card-luxury:nth-child(2)::before {
            background: linear-gradient(135deg, #4158D0 0%, #C850C0 100%);
        }

        .category-card-luxury:nth-child(3)::before {
            background: linear-gradient(135deg, #0d6e3e 0%, #1fb584 100%);
        }

        .category-card-luxury:nth-child(4)::before {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        }

        .category-card-luxury:nth-child(5)::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .category-card-luxury:nth-child(6)::before {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .category-card-luxury:nth-child(7)::before {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .category-card-luxury:nth-child(8)::before {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .category-card-luxury:hover {
            transform: scale(1.08) translateY(-8px);
            box-shadow: 0 40px 80px rgba(0, 0, 0, 0.2);
        }

        .category-card-luxury::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            transition: all 0.5s;
        }

        .category-card-luxury:hover::after {
            background: rgba(0, 0, 0, 0.1);
        }

        .category-name {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* ===== PRODUCTS SECTION ===== */
        .section-products {
            max-width: 1400px;
            margin: 0 auto;
            padding: 120px 40px;
            background: linear-gradient(180deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 40px;
            margin-left: 40px;
            margin-right: 40px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 40px;
            margin-top: 60px;
        }

        .product-card-luxury {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .product-card-luxury::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 50%, transparent 100%);
            opacity: 0;
            transition: opacity 0.4s;
            z-index: 10;
        }

        .product-card-luxury:hover {
            transform: translateY(-20px);
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.15);
        }

        .product-card-luxury:hover::before {
            opacity: 1;
        }

        .product-image-luxury {
            width: 100%;
            height: 340px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 64px;
            overflow: hidden;
            position: relative;
        }

        .product-image-luxury img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .product-card-luxury:hover .product-image-luxury img {
            transform: scale(1.1);
        }

        .product-content {
            padding: 32px 24px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .product-category-badge {
            display: inline-block;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #1a1a2e;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: fit-content;
            margin-bottom: 16px;
        }

        .product-name-luxury {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .product-price-luxury {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }

        .vendor-card-luxury {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f4f8 100%);
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .vendor-avatar-luxury {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a1a2e;
            font-weight: 800;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(255, 167, 0, 0.3);
        }

        .vendor-info-luxury {
            flex-grow: 1;
        }

        .vendor-name-luxury {
            font-weight: 700;
            color: #1a1a1a;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .vendor-store-luxury {
            font-size: 12px;
            color: #999;
            font-weight: 500;
        }

        .product-actions {
            display: flex;
            gap: 12px;
            margin-top: auto;
        }

        .btn-view-product {
            flex: 1;
            padding: 14px 20px;
            background: linear-gradient(135deg, #1a1a2e 0%, #0f3460 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s;
            font-size: 14px;
            letter-spacing: 0.5px;
            box-shadow: 0 8px 20px rgba(26, 26, 46, 0.2);
        }

        .btn-view-product:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(26, 26, 46, 0.3);
        }

        /* ===== CTA SECTION ===== */
        .cta-luxury {
            max-width: 1200px;
            margin: 120px auto;
            padding: 100px 60px;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 40px;
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 30px 100px rgba(26, 26, 46, 0.3);
        }

        .cta-luxury::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .cta-luxury::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 215, 0, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 25s ease-in-out infinite reverse;
        }

        .cta-content {
            position: relative;
            z-index: 2;
        }

        .cta-title {
            font-family: 'Playfair Display', serif;
            font-size: 64px;
            font-weight: 900;
            margin-bottom: 24px;
            line-height: 1.2;
            letter-spacing: -2px;
        }

        .cta-subtitle {
            font-size: 20px;
            margin-bottom: 48px;
            opacity: 0.95;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .cta-buttons {
            display: flex;
            gap: 24px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ===== FOOTER ===== */
        .footer-luxury {
            background: linear-gradient(135deg, #0a0a14 0%, #1a1a2e 100%);
            color: white;
            padding: 100px 40px 40px;
            margin-top: 120px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-section h3 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 24px;
            font-family: 'Playfair Display', serif;
            background: linear-gradient(90deg, #FFD700 0%, #FFA500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 1px;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section li {
            margin-bottom: 16px;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }

        .footer-section a:hover {
            color: #FFD700;
            padding-left: 8px;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 40px;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        /* ===== ANIMATIONS ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }

        @keyframes rotate {
            0% { transform: rotate(45deg); }
            100% { transform: rotate(405deg); }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: slideInUp 0.8s ease-out;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .hero-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .title-luxury {
                font-size: 56px;
            }

            .hero-text h1 {
                font-size: 56px;
            }

            .heading-section {
                font-size: 42px;
            }

            .cta-title {
                font-size: 48px;
            }
        }

        @media (max-width: 768px) {
            .nav-luxury {
                padding: 16px 20px;
            }

            .logo-luxury {
                font-size: 24px;
            }

            .nav-links-luxury {
                gap: 24px;
                font-size: 13px;
            }

            .title-luxury {
                font-size: 42px;
            }

            .hero-text h1 {
                font-size: 40px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .btn-luxury-primary, .btn-luxury-secondary, .btn-luxury-gold {
                width: 100%;
                text-align: center;
            }

            .hero-luxury::before,
            .hero-luxury::after {
                display: none;
            }

            .hero-visual {
                display: none;
            }

            .heading-section {
                font-size: 32px;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 20px;
            }

            .category-card-luxury {
                height: 220px;
            }

            .category-name {
                font-size: 20px;
            }

            .products-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .section-products {
                padding: 60px 20px;
                margin-left: 20px;
                margin-right: 20px;
            }

            .section-categories,
            .cta-luxury {
                padding: 60px 20px;
                margin-left: 0;
                margin-right: 0;
            }

            .cta-title {
                font-size: 36px;
            }

            .stats-luxury {
                padding: 60px 20px;
            }

            .stats-grid {
                gap: 30px;
            }

            .stat-number {
                font-size: 42px;
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

        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            updateWishlistCount();
        });
    </script>

    <!-- Hero Section -->
    <section class="hero-luxury">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Premium Marketplace Experience</h1>
                <p>Discover curated collections from verified sellers. Shop with confidence, every purchase guaranteed premium quality.</p>
                <div class="hero-buttons">
                    @guest
                        <button class="btn-luxury-primary" onclick="window.location='{{ route('customer.register') }}'">
                            🛍️ Start Shopping
                        </button>
                        <button class="btn-luxury-secondary" onclick="window.location='{{ route('vendor.register') }}'">
                            🏪 Become a Seller
                        </button>
                    @else
                        <button class="btn-luxury-primary" onclick="window.location='{{ route('storefront.products.index') }}'">
                            Explore Products
                        </button>
                    @endauth
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-text-overlay">
                    Experience<br>Luxury<br>Shopping
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-luxury">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number">{{ $categories->count() }}+</div>
                <div class="stat-label">Premium Categories</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $products->total() }}+</div>
                <div class="stat-label">Curated Products</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">100%</div>
                <div class="stat-label">Verified Sellers</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Premium Support</div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="section-categories" id="categories">
        <div class="section-header">
            <h2 class="heading-section">Browse Categories</h2>
            <p class="section-subtitle">Explore our curated selection of premium product categories</p>
        </div>
        <div class="categories-grid">
            @foreach($categories->take(8) as $category)
                <div class="category-card-luxury" style="cursor: pointer;" onclick="document.location='#products'">
                    <div class="category-name">
                        @switch($category->name)
                            @case('Electronics') 📱 @break
                            @case('Fashion') 👔 @break
                            @case('Home & Garden') 🏠 @break
                            @case('Sports') ⚽ @break
                            @case('Books') 📚 @break
                            @case('Jewelry') 💎 @break
                            @case('Beauty') 💄 @break
                            @case('Furniture') 🛋️ @break
                            @default 🎁
                        @endswitch
                        {{ $category->name }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Products Section -->
    <section class="section-products" id="products">
        <div class="section-header">
            <h2 class="heading-section">Featured Collection</h2>
            <p class="section-subtitle">Hand-selected premium products from our top-rated sellers</p>
        </div>

        <div class="products-grid">
            @forelse($products as $product)
                <div class="product-card-luxury fade-in-up">
                    <div class="product-image-luxury">
                        @if($product->images->first())
                            <img src="{{ $product->images->first()->getImageUrl() }}" alt="{{ $product->name }}">
                        @else
                            <div style="font-size: 80px;">📦</div>
                        @endif
                    </div>
                    <div class="product-content">
                        <div class="product-category-badge">
                            {{ $product->category?->name ?? 'Premium' }}
                        </div>
                        <h3 class="product-name-luxury">{{ Str::limit($product->name, 45) }}</h3>
                        <div class="product-price-luxury">₨{{ number_format($product->base_price) }}</div>

                        <div class="vendor-card-luxury">
                            <div class="vendor-avatar-luxury">
                                {{ substr($product->vendor?->user?->name ?? 'V', 0, 1) }}
                            </div>
                            <div class="vendor-info-luxury">
                                <div class="vendor-name-luxury">{{ $product->vendor?->store_name ?? 'Premium Store' }}</div>
                                <div class="vendor-store-luxury">{{ $product->vendor?->user?->name ?? 'Verified Seller' }}</div>
                            </div>
                        </div>

                        <div class="product-actions">
                            <button class="btn-view-product" onclick="window.location='{{ route('storefront.products.show', $product) }}'">
                                View Details →
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px;">
                    <p style="font-size: 24px; color: #999; font-family: 'Playfair Display', serif;">Premium Collection Coming Soon</p>
                </div>
            @endforelse
        </div>

        @if($products->hasPages())
            <div style="text-align: center; margin-top: 60px;">
                {{ $products->links() }}
            </div>
        @endif
    </section>

    <!-- CTA Section -->
    <section class="cta-luxury">
        <div class="cta-content">
            <h2 class="cta-title">Join the Premium Community</h2>
            <p class="cta-subtitle">Experience the future of luxury shopping with VandoraX</p>
            @guest
            <div class="cta-buttons">
                <button class="btn-luxury-gold" onclick="window.location='{{ route('customer.register') }}'">
                    Start Shopping Now
                </button>
                <button class="btn-luxury-secondary" style="border-color: white; color: white;" onclick="window.location='{{ route('vendor.register') }}'">
                    Open Your Luxury Shop
                </button>
            </div>
            @else
            <div class="cta-buttons">
                <button class="btn-luxury-gold" onclick="window.location='{{ route('storefront.products.index') }}'">
                    Continue Shopping
                </button>
            </div>
            @endguest
        </div>
    </section>


    <script>
        // Smooth scroll behavior for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Animate elements on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        document.querySelectorAll('.product-card-luxury, .category-card-luxury, .stat-item').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>

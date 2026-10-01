<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VandoraX - Premium Marketplace</title>

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
            --overlay: rgba(0, 0, 0, 0.3);
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

        .header-user {
            display: flex;
            align-items: center;
            gap: 16px;
            padding-left: 28px;
            border-left: 1px solid var(--border-light);
        }

        .user-info {
            text-align: right;
        }

        .user-name {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
        }

        .user-role {
            font-size: 11px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .logout-btn {
            padding: 8px 14px;
            background: rgba(255, 165, 0, 0.15);
            border: none;
            color: var(--accent);
            font-weight: 700;
            font-size: 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: rgba(255, 165, 0, 0.25);
        }

        .auth-buttons {
            display: flex;
            gap: 12px;
            padding-left: 28px;
            border-left: 1px solid var(--border-light);
        }

        .btn-login {
            padding: 8px 16px;
            background: transparent;
            border: 1px solid var(--border-medium);
            color: var(--text-secondary);
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .btn-register {
            padding: 8px 16px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border: none;
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 165, 0, 0.3);
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 165, 0, 0.4);
        }

        /* ===== HERO SECTION ===== */
        .hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 50%, rgba(255, 165, 0, 0.05) 100%);
            padding: 100px 24px 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 20s ease-in-out infinite;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.08) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 25s ease-in-out infinite reverse;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 900px;
            margin: 0 auto;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(36px, 8vw, 72px);
            font-weight: 900;
            margin-bottom: 20px;
            line-height: 1.1;
            letter-spacing: -1.5px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(16px, 2vw, 20px);
            font-weight: 300;
            margin-bottom: 40px;
            color: var(--text-secondary);
            letter-spacing: 0.5px;
        }

        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            padding: 14px 42px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border: none;
            color: var(--primary);
            font-weight: 700;
            font-size: 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 8px 25px rgba(255, 165, 0, 0.35);
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 35px rgba(255, 165, 0, 0.45);
        }

        .btn-secondary {
            padding: 14px 42px;
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
            font-weight: 700;
            font-size: 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: rgba(255, 165, 0, 0.1);
            transform: translateY(-4px);
        }

        /* ===== STATS SECTION ===== */
        .stats {
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
            padding: 80px 24px;
            border-top: 1px solid var(--border-light);
            border-bottom: 1px solid var(--border-light);
        }

        .stats-grid {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 40px;
        }

        .stat-item {
            text-align: center;
            position: relative;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 48px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 12px;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
        }

        /* ===== CATEGORIES SECTION ===== */
        .categories-section {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 24px;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 6vw, 56px);
            font-weight: 900;
            text-align: center;
            margin-bottom: 16px;
            color: var(--text-primary);
            letter-spacing: -1px;
        }

        .section-subtitle {
            font-size: 16px;
            color: var(--text-secondary);
            text-align: center;
            margin-bottom: 60px;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 28px;
        }

        .category-card {
            position: relative;
            height: 260px;
            border-radius: 16px;
            overflow: hidden;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: clamp(18px, 3vw, 22px);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-decoration: none;
        }

        .category-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 0;
        }

        .category-card:hover .category-image {
            transform: scale(1.1);
        }

        .category-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%);
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 1;
        }

        .category-card::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.25);
            transition: all 0.5s;
            z-index: 0;
        }

        .category-card:hover {
            transform: scale(1.08) translateY(-10px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.4);
        }

        .category-card:hover::after {
            background: rgba(0, 0, 0, 0.15);
        }

        .category-name {
            position: relative;
            z-index: 2;
            font-family: 'Playfair Display', serif;
            font-size: clamp(18px, 3vw, 24px);
            font-weight: 800;
            letter-spacing: 0.5px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        /* ===== PRODUCTS SECTION ===== */
        .products-section {
            max-width: 1400px;
            margin: 0 auto;
            padding: 100px 24px;
            background: linear-gradient(180deg, var(--primary) 0%, rgba(22, 33, 62, 0.5) 100%);
            border-radius: 24px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 32px;
            margin-top: 50px;
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
            height: 240px;
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

        .vendor-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: rgba(255, 165, 0, 0.05);
            border-radius: 10px;
            margin-bottom: 16px;
            border: 1px solid var(--border-light);
        }

        .vendor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 700;
            font-size: 16px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(255, 165, 0, 0.3);
        }

        .vendor-info {
            flex-grow: 1;
        }

        .vendor-name {
            font-weight: 700;
            font-size: 13px;
            color: var(--text-primary);
        }

        .vendor-store {
            font-size: 11px;
            color: var(--text-muted);
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: auto;
        }

        .btn-view {
            flex: 1;
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
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        /* ===== NEWSLETTER SECTION ===== */
        .newsletter {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: var(--text-primary);
            padding: 80px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin: 80px 24px;
            border-radius: 24px;
            border: 1px solid var(--border-light);
        }

        .newsletter::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        .newsletter::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(255, 165, 0, 0.05) 0%, transparent 70%);
            border-radius: 50%;
        }

        .newsletter-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
            margin: 0 auto;
        }

        .newsletter-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 6vw, 48px);
            font-weight: 800;
            margin-bottom: 16px;
            letter-spacing: -1px;
        }

        .newsletter-subtitle {
            font-size: 16px;
            margin-bottom: 40px;
            color: var(--text-secondary);
            font-weight: 300;
        }

        .newsletter-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .newsletter-form input {
            flex: 1;
            min-width: 250px;
            padding: 14px 18px;
            border: none;
            border-radius: 10px 0 0 10px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            outline: none;
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.1);
        }

        .newsletter-form button {
            padding: 14px 36px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            border: none;
            border-radius: 0 10px 10px 0;
            color: var(--primary);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
        }

        .newsletter-form button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 165, 0, 0.4);
        }

        /* ===== FOOTER ===== */
        footer {
            background: var(--primary-dark);
            color: var(--text-secondary);
            padding: 80px 24px 40px;
            border-top: 1px solid var(--border-light);
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

        /* ===== ANIMATIONS ===== */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(30px); }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .header-wrapper {
                height: auto;
                padding: 12px 16px;
            }

            .hero {
                padding: 80px 20px 60px;
            }

            .categories-section,
            .products-section {
                padding: 80px 20px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }

            .stats-grid {
                gap: 30px;
            }
        }

        @media (max-width: 768px) {
            .header-wrapper {
                flex-direction: column;
                gap: 12px;
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

            .auth-buttons, .header-user {
                border-left: none;
                padding-left: 0;
            }

            .header-user {
                flex: 1;
            }

            .auth-buttons {
                flex: 1;
                gap: 8px;
            }

            .btn-login, .btn-register {
                flex: 1;
                padding: 8px 12px;
                font-size: 12px;
            }

            .hero {
                padding: 60px 16px 50px;
            }

            .hero-title {
                font-size: 36px;
            }

            .hero-subtitle {
                font-size: 16px;
            }

            .hero-buttons {
                gap: 12px;
            }

            .btn-primary, .btn-secondary {
                padding: 12px 28px;
                font-size: 14px;
            }

            .categories-section,
            .products-section {
                padding: 60px 16px;
            }

            .section-title {
                font-size: 32px;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }

            .categories-grid {
                gap: 16px;
            }

            .category-card {
                height: 200px;
            }

            .newsletter {
                margin: 60px 16px;
                padding: 60px 20px;
            }

            .newsletter-form {
                flex-direction: column;
            }

            .newsletter-form input,
            .newsletter-form button {
                border-radius: 8px;
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

            .hero-title {
                font-size: 28px;
            }

            .section-title {
                font-size: 24px;
            }

            .categories-grid,
            .products-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .category-card {
                height: 180px;
            }

            .stat-number {
                font-size: 36px;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
                text-align: center;
                padding: 12px 20px;
            }

            .hero-buttons {
                flex-direction: column;
                width: 100%;
            }

            .product-card {
                border-radius: 12px;
            }

            .product-image {
                height: 200px;
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
                <div class="header-user">
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->name }}</div>
                        <div class="user-role">Customer</div>
                    </div>
                    <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" class="logout-btn">Logout</button>
                    </form>
                </div>
            @else
                <div class="auth-buttons">
                    <a href="{{ route('customer.login') }}" class="btn-login">Login</a>
                    <a href="{{ route('customer.register') }}" class="btn-register">Register</a>
                </div>
            @endif
        </div>
    </div>
</header>

<!-- ===== HERO SECTION ===== -->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Premium Marketplace Experience</h1>
        <p class="hero-subtitle">Discover luxury products from verified sellers worldwide. Premium quality guaranteed.</p>
        <div class="hero-buttons">
            @guest
                <button class="btn-primary" onclick="window.location='{{ route('customer.register') }}'">🛍️ Start Shopping</button>
                <button class="btn-secondary" onclick="window.location='{{ route('vendor.register') }}'">Become a Seller</button>
            @else
                <button class="btn-primary" onclick="window.location='{{ route('storefront.products.index') }}'">Explore Products</button>
                <a href="{{ route('cart.index') }}" class="btn-secondary">View Cart</a>
            @endauth
        </div>
    </div>
</section>

<!-- ===== STATS SECTION ===== -->
<section class="stats">
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-number">{{ $categories->count() ?? 0 }}+</div>
            <div class="stat-label">Categories</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">{{ isset($products) && method_exists($products, 'total') ? $products->total() : ($products->count() ?? 0) }}+</div>
            <div class="stat-label">Products</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">100%</div>
            <div class="stat-label">Verified</div>
        </div>
        <div class="stat-item">
            <div class="stat-number">24/7</div>
            <div class="stat-label">Support</div>
        </div>
    </div>
</section>

<!-- ===== CATEGORIES SECTION ===== -->
<section class="categories-section" id="categories">
    <h2 class="section-title">Browse Categories</h2>
    <p class="section-subtitle">Explore our premium collection across all marketplaces</p>
    <div class="categories-grid">
        @foreach($categories->take(8) as $category)
            <a href="{{ route('storefront.products.index', ['category' => $category->id]) }}" class="category-card">
                @if($category->icon)
                    <img src="{{ asset('storage/' . $category->icon) }}" alt="{{ $category->name }}" class="category-image">
                @else
                    <div class="category-image" style="background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%); display: flex; align-items: center; justify-content: center; font-size: 48px;">
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
                    </div>
                @endif
                <span class="category-name">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
</section>

<!-- ===== PRODUCTS SECTION ===== -->
<section class="products-section" id="products">
    <h2 class="section-title">Featured Collection</h2>
    <p class="section-subtitle">Curated premium products from our top-rated sellers</p>

    <div class="products-grid">
        @forelse($products as $product)
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

                    <div class="vendor-card">
                        <div class="vendor-avatar">{{ substr($product->vendor?->user?->name ?? 'V', 0, 1) }}</div>
                        <div class="vendor-info">
                            <div class="vendor-name">{{ $product->vendor?->store_name ?? 'Premium Store' }}</div>
                            <div class="vendor-store">{{ $product->vendor?->user?->name ?? 'Verified Seller' }}</div>
                        </div>
                    </div>

                    <div class="product-actions">
                        <a href="{{ route('storefront.products.show', $product) }}" class="btn-view">View Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 80px 20px;">
                <p style="font-size: 24px; color: var(--text-muted); font-family: 'Playfair Display', serif;">Premium Collection Coming Soon</p>
            </div>
        @endforelse
    </div>

    @if(isset($products) && method_exists($products, 'hasPages') && $products->hasPages())
        <div style="text-align: center; margin-top: 60px;">
            {{ $products->links() }}
        </div>
    @endif
</section>

<!-- ===== NEWSLETTER SECTION ===== -->
<section class="newsletter">
    <div class="newsletter-content">
        <h2 class="newsletter-title">Join the Exclusive Circle</h2>
        <p class="newsletter-subtitle">Get early access to new arrivals and exclusive premium deals</p>
        <form class="newsletter-form" onsubmit="handleNewsletterSignup(event)">
            <input type="email" placeholder="Enter your email address" required>
            <button type="submit">Subscribe</button>
        </form>
    </div>
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

    // Newsletter signup
    function handleNewsletterSignup(event) {
        event.preventDefault();
        const email = event.target.querySelector('input[type="email"]').value;

        const btn = event.target.querySelector('button');
        const originalText = btn.textContent;
        btn.textContent = '✓ Subscribed!';
        btn.disabled = true;

        setTimeout(() => {
            event.target.reset();
            btn.textContent = originalText;
            btn.disabled = false;
        }, 3000);
    }

    // Load counts on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        updateWishlistCount();
    });
function showPopup(msg,type='success'){const o=document.getElementById('popupOverlay'),b=document.getElementById('popupBox');document.getElementById('popupMsg').textContent=msg;document.getElementById('popupTitle').textContent=type==='success'?'Done!':type==='error'?'Oops':'Notice';document.getElementById('popupIcon').innerHTML=type==='success'?'✨':type==='error'?'⚠️':'ℹ️';o.style.display='flex';setTimeout(()=>{b.style.opacity='1';b.style.transform='scale(1) translateY(0)';},10);}function hidePopup(){const o=document.getElementById('popupOverlay'),b=document.getElementById('popupBox');b.style.opacity='0';b.style.transform='scale(0.95) translateY(10px)';setTimeout(()=>{o.style.display='none';},250);}
</script>

<div id="popupOverlay" onclick="hidePopup()" style="display:none;position:fixed;inset:0;background:rgba(10,14,39,0.85);z-index:99999;align-items:center;justify-content:center;backdrop-filter:blur(12px);"><div id="popupBox" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 100%);border:1px solid var(--border-light);border-radius:16px;padding:40px 36px;max-width:420px;text-align:center;box-shadow:0 20px 60px rgba(255,165,0,0.3);animation:popupIn 0.4s cubic-bezier(0.34,1.56,0.64,1);"><div id="popupIcon" style="font-size:60px;margin-bottom:16px;">✨</div><h3 id="popupTitle" style="font-family:'Playfair Display',serif;font-size:24px;color:var(--text-primary);margin-bottom:8px;">Done!</h3><p id="popupMsg" style="font-size:15px;color:var(--text-secondary);line-height:1.6;margin-bottom:24px;">Success</p><button onclick="hidePopup()" style="padding:12px 28px;background:linear-gradient(135deg,var(--accent) 0%,var(--accent-dark) 100%);color:var(--primary);border:none;border-radius:10px;font-weight:700;cursor:pointer;">OK</button></div></div><style>@keyframes popupIn{from{opacity:0;transform:scale(0.9) translateY(10px);}to{opacity:1;transform:scale(1) translateY(0);}}</style>
</body>
</html>

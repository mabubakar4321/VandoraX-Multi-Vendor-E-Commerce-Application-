<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Wishlist - VandoraX</title>

    <style>
        :root {
            --primary-dark: #0a0e27;
            --primary: #16213e;
            --primary-light: #1a2d5c;
            --accent: #ffa500;
            --accent-light: #ffb84d;
            --accent-dark: #e68a00;
            --bg-darker: #0a0e27;
            --surface: #111a33;
            --surface-2: #16213e;
            --border-light: #1f2f5a;
            --border-medium: #2a3d6b;
            --text-primary: #ffffff;
            --text-secondary: #b8c0d4;
            --text-muted: #7f8aa6;
            --error: #ef4444;
            --radius: 14px;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background:
                radial-gradient(900px 400px at 85% -100px, rgba(255, 165, 0, 0.10), transparent 60%),
                radial-gradient(700px 400px at -10% 0, rgba(42, 61, 107, 0.45), transparent 60%),
                var(--bg-darker);
            background-attachment: fixed;
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        a { color: inherit; }

        :focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 3px;
        }

        /* ---------- HEADER ---------- */
        .header {
            background: rgba(10, 14, 39, 0.8);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-light);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 18px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .logo {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
            text-decoration: none;
            background: linear-gradient(135deg, #fff 0%, var(--accent-light) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .nav-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border: 1px solid var(--border-medium);
            border-radius: 999px;
            color: var(--text-primary);
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            transition: all 0.25s ease;
        }

        .nav-back svg { transition: transform 0.25s ease; }

        .nav-back:hover {
            border-color: var(--accent);
            color: var(--accent-light);
            background: rgba(255, 165, 0, 0.08);
        }

        .nav-back:hover svg { transform: translateX(-3px); }

        /* ---------- LAYOUT ---------- */
        .wishlist-container {
            max-width: 1400px;
            margin: 48px auto 80px;
            padding: 0 48px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 40px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--border-light);
        }

        .page-title {
            font-size: 40px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.15;
        }

        .page-subtitle {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: var(--text-secondary);
            margin-top: 10px;
        }

        .page-subtitle::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }

        .btn-clear-all {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-medium);
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .btn-clear-all:hover {
            color: #fff;
            border-color: var(--error);
            background: rgba(239, 68, 68, 0.15);
        }

        /* ---------- PRODUCT GRID ---------- */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }

        .product-card {
            position: relative;
            display: flex;
            flex-direction: column;
            background: linear-gradient(180deg, var(--surface) 0%, var(--bg-darker) 100%);
            border: 1px solid var(--border-light);
            border-radius: var(--radius);
            overflow: hidden;
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease, opacity 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-6px);
            border-color: rgba(255, 165, 0, 0.55);
            box-shadow: var(--shadow), 0 0 0 1px rgba(255, 165, 0, 0.15);
        }

        .product-card.removing {
            opacity: 0;
            transform: scale(0.94);
            pointer-events: none;
        }

        .product-image {
            width: 100%;
            aspect-ratio: 1 / 1;
            background: linear-gradient(135deg, var(--border-light) 0%, var(--border-medium) 100%);
            overflow: hidden;
            position: relative;
        }

        .product-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 60%, rgba(10, 14, 39, 0.55) 100%);
            pointer-events: none;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.6s ease;
        }

        .product-card:hover .product-image img { transform: scale(1.06); }

        .remove-btn {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 38px;
            height: 38px;
            background: rgba(10, 14, 39, 0.65);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 50%;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.25s ease;
            z-index: 10;
        }

        .remove-btn:hover {
            background: var(--error);
            border-color: var(--error);
            transform: rotate(90deg);
        }

        .product-info {
            padding: 18px 20px 20px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .product-category {
            align-self: flex-start;
            font-size: 10px;
            font-weight: 600;
            color: var(--accent-light);
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.25);
            border-radius: 999px;
            padding: 3px 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .product-name {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 6px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.8em;
        }

        .product-vendor {
            font-size: 13px;
            color: var(--text-muted);
            margin-bottom: 14px;
        }

        .product-price {
            font-size: 20px;
            font-weight: 800;
            color: var(--accent-light);
            margin-top: auto;
            margin-bottom: 16px;
        }

        .btn-add-to-cart {
            display: block;
            width: 100%;
            padding: 13px;
            text-align: center;
            text-decoration: none;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary-dark);
            border: none;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.25s ease, filter 0.25s ease;
        }

        .btn-add-to-cart:hover {
            filter: brightness(1.08);
            box-shadow: 0 8px 20px rgba(255, 165, 0, 0.35);
            transform: translateY(-1px);
        }

        .btn-add-to-cart:active { transform: translateY(0); }

        /* ---------- EMPTY STATE ---------- */
        .empty-wishlist {
            max-width: 560px;
            margin: 40px auto 0;
            text-align: center;
            padding: 72px 32px;
            background: linear-gradient(180deg, var(--surface) 0%, transparent 100%);
            border: 1px dashed var(--border-medium);
            border-radius: 20px;
        }

        .empty-icon {
            width: 96px;
            height: 96px;
            margin: 0 auto 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 44px;
            background: rgba(255, 165, 0, 0.1);
            border: 1px solid rgba(255, 165, 0, 0.3);
            box-shadow: 0 0 40px rgba(255, 165, 0, 0.15);
        }

        .empty-title { font-size: 28px; font-weight: 800; margin-bottom: 12px; }

        .empty-text {
            font-size: 16px;
            color: var(--text-secondary);
            margin-bottom: 32px;
        }

        .btn-primary {
            display: inline-block;
            padding: 15px 44px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary-dark);
            text-decoration: none;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            transition: all 0.25s ease;
        }

        .btn-primary:hover {
            filter: brightness(1.08);
            box-shadow: 0 10px 26px rgba(255, 165, 0, 0.35);
            transform: translateY(-2px);
        }

        /* ---------- LOADING ---------- */
        .loading {
            position: fixed;
            inset: 0;
            background: rgba(10, 14, 39, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading.active { display: flex; }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid var(--border-medium);
            border-top-color: var(--accent);
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
        }

        @keyframes spin { to { transform: rotate(360deg); } }

        /* ---------- POPUP / CONFIRM ---------- */
        .popup-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 14, 39, 0.8);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .popup-box {
            width: 100%;
            max-width: 400px;
            text-align: center;
            background: linear-gradient(160deg, var(--primary) 0%, var(--primary-light) 100%);
            border: 1px solid var(--border-medium);
            border-radius: 18px;
            padding: 36px 32px 28px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.5), 0 0 40px rgba(255, 165, 0, 0.12);
            opacity: 0;
            transform: scale(0.94) translateY(10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .popup-icon { font-size: 52px; margin-bottom: 12px; line-height: 1; }
        .popup-title { font-size: 22px; font-weight: 700; margin-bottom: 8px; }
        .popup-msg { font-size: 15px; color: var(--text-secondary); margin-bottom: 24px; }

        .popup-actions { display: flex; gap: 10px; justify-content: center; }

        .popup-btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .popup-btn.primary {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-dark) 100%);
            color: var(--primary-dark);
            border: none;
        }

        .popup-btn.primary:hover { filter: brightness(1.08); }

        .popup-btn.danger {
            background: var(--error);
            color: #fff;
            border: none;
        }

        .popup-btn.danger:hover { filter: brightness(1.1); }

        .popup-btn.ghost {
            background: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-medium);
        }

        .popup-btn.ghost:hover { color: #fff; border-color: var(--text-secondary); }

        /* ---------- RESPONSIVE ---------- */
        @media (max-width: 1200px) {
            .product-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 900px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 20px; }
        }

        @media (max-width: 768px) {
            .nav-container { padding: 14px 20px; }
            .wishlist-container { padding: 0 20px; margin-top: 28px; }
            .page-title { font-size: 30px; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .nav-back { padding: 8px 14px; }
            .product-info { padding: 14px; }
            .product-name { font-size: 14px; }
            .product-price { font-size: 17px; }
        }

        @media (max-width: 480px) {
            .product-grid { grid-template-columns: 1fr; }
            .logo { font-size: 22px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading" id="loading" aria-hidden="true">
        <div class="spinner"></div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo">VANDORAX</a>
            <a href="{{ route('storefront.products.index') }}" class="nav-back">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Continue Shopping
            </a>
        </div>
    </header>

    <!-- Wishlist Content -->
    <main class="wishlist-container">
        @if($wishlists->count() > 0)
            <div class="page-header">
                <div>
                    <h1 class="page-title">My Wishlist</h1>
                    <p class="page-subtitle">{{ $wishlists->count() }} {{ Str::plural('item', $wishlists->count()) }} saved</p>
                </div>
                <button type="button" class="btn-clear-all" onclick="clearWishlist()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>
                    </svg>
                    Clear All
                </button>
            </div>

            <div class="product-grid">
                @foreach($wishlists as $wishlist)
                    @php
                        $product = $wishlist->product;
                    @endphp

                    @continue(!$product)

                    @php
                        $image = $product->images->first();
                        $placeholders = [
                            'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=400&h=400&fit=crop&q=85',
                            'https://images.unsplash.com/photo-1467043237213-65f2da53396f?w=400&h=400&fit=crop&q=85',
                        ];
                        $fallbackImage = $placeholders[($product->id % 2)];
                        $imageUrl = $image ? $image->getImageUrl() : $fallbackImage;
                    @endphp

                    <article class="product-card" data-wishlist-id="{{ $wishlist->id }}">
                        <div class="product-image">
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null; this.src='{{ $fallbackImage }}';">
                            <button type="button" class="remove-btn" onclick="removeFromWishlist({{ $wishlist->id }})" aria-label="Remove {{ $product->name }} from wishlist" title="Remove">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                    <path d="M18 6L6 18M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <div class="product-info">
                            <div class="product-category">{{ $product->category->name ?? 'Product' }}</div>
                            <h3 class="product-name">{{ $product->name }}</h3>
                            <div class="product-vendor">by {{ $product->vendor->store_name ?? 'Store' }}</div>
                            <div class="product-price">Rs. {{ number_format($product->price, 0) }}</div>
                            <a href="{{ route('storefront.products.show', $product->id) }}" class="btn-add-to-cart">
                                View Product
                            </a>
                        </div>
                    </article>
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
    </main>

    <!-- Popup / Confirm Dialog -->
    <div class="popup-overlay" id="popupOverlay" role="dialog" aria-modal="true" aria-labelledby="popupTitle">
        <div class="popup-box" id="popupBox" onclick="event.stopPropagation()">
            <div class="popup-icon" id="popupIcon">✨</div>
            <h3 class="popup-title" id="popupTitle">Done!</h3>
            <p class="popup-msg" id="popupMsg">Success</p>
            <div class="popup-actions" id="popupActions"></div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function showLoading() { document.getElementById('loading').classList.add('active'); }
        function hideLoading() { document.getElementById('loading').classList.remove('active'); }

        /* ----- Popup helpers ----- */
        let popupResolver = null;

        function openPopup({ icon, title, message, buttons }) {
            const overlay = document.getElementById('popupOverlay');
            const box = document.getElementById('popupBox');
            const actions = document.getElementById('popupActions');

            document.getElementById('popupIcon').textContent = icon;
            document.getElementById('popupTitle').textContent = title;
            document.getElementById('popupMsg').textContent = message;
            actions.innerHTML = '';

            buttons.forEach(b => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'popup-btn ' + b.style;
                btn.textContent = b.label;
                btn.onclick = () => closePopup(b.value);
                actions.appendChild(btn);
            });

            overlay.style.display = 'flex';
            requestAnimationFrame(() => {
                box.style.opacity = '1';
                box.style.transform = 'scale(1) translateY(0)';
            });
            const first = actions.querySelector('.primary, .danger') || actions.firstChild;
            if (first) first.focus();
        }

        function closePopup(value) {
            const overlay = document.getElementById('popupOverlay');
            const box = document.getElementById('popupBox');
            box.style.opacity = '0';
            box.style.transform = 'scale(0.94) translateY(10px)';
            setTimeout(() => { overlay.style.display = 'none'; }, 250);
            if (popupResolver) {
                const resolve = popupResolver;
                popupResolver = null;
                resolve(value);
            }
        }

        function showPopup(msg, type = 'success') {
            const map = {
                success: { icon: '✨', title: 'Done!' },
                error:   { icon: '⚠️', title: 'Oops' },
                info:    { icon: 'ℹ️', title: 'Notice' },
            };
            const t = map[type] || map.info;
            popupResolver = null;
            openPopup({
                icon: t.icon, title: t.title, message: msg,
                buttons: [{ label: 'OK', style: 'primary', value: true }],
            });
        }

        function hidePopup() { closePopup(false); }

        function confirmPopup(message, confirmLabel = 'Confirm') {
            return new Promise(resolve => {
                popupResolver = resolve;
                openPopup({
                    icon: '🗑️', title: 'Are you sure?', message,
                    buttons: [
                        { label: 'Cancel', style: 'ghost', value: false },
                        { label: confirmLabel, style: 'danger', value: true },
                    ],
                });
            });
        }

        document.getElementById('popupOverlay').addEventListener('click', hidePopup);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape' && document.getElementById('popupOverlay').style.display === 'flex') {
                hidePopup();
            }
        });

        /* ----- Wishlist actions ----- */
        async function removeFromWishlist(wishlistId) {
            const ok = await confirmPopup('Remove this item from your wishlist?', 'Remove');
            if (!ok) return;

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
                hideLoading();

                if (data.success) {
                    const card = document.querySelector(`[data-wishlist-id="${wishlistId}"]`);
                    if (!card) { location.reload(); return; }

                    card.classList.add('removing');
                    setTimeout(() => {
                        card.remove();
                        const remaining = document.querySelectorAll('.product-card').length;
                        if (remaining === 0) {
                            location.reload();
                        } else {
                            updatePageSubtitle();
                        }
                    }, 300);
                } else {
                    showPopup(data.message || 'Failed to remove item', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                hideLoading();
                showPopup('An error occurred. Please try again.', 'error');
            }
        }

        async function clearWishlist() {
            const ok = await confirmPopup('This will remove every item from your wishlist.', 'Clear All');
            if (!ok) return;

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
                } else {
                    hideLoading();
                    showPopup(data.message || 'Failed to clear wishlist', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                hideLoading();
                showPopup('An error occurred. Please try again.', 'error');
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
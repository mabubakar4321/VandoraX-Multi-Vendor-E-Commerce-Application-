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

<style>
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

    .btn-login, .btn-register {
        padding: 8px 16px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-login {
        color: var(--text-primary);
        background: transparent;
        border: 1px solid var(--border-medium);
    }

    .btn-login:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .btn-register {
        color: var(--primary);
        background: var(--accent);
        border: 1px solid var(--accent);
    }

    .btn-register:hover {
        background: var(--accent-light);
    }

</style>
<script>
    function updateCartCount() {
        fetch('{{ route('cart.count') }}')
            .then(r => r.json())
            .then(data => {
                const els = document.querySelectorAll('#cart-count');
                els.forEach(el => {
                    const count = data.count || 0;
                    if (count > 0) {
                        el.textContent = count;
                        el.style.display = 'flex';
                    } else {
                        el.style.display = 'none';
                    }
                });
            })
            .catch(e => console.error('Cart error:', e));
    }

    function updateWishlistCount() {
        @if(auth()->check())
        fetch('{{ route('wishlist.count') }}')
            .then(r => r.json())
            .then(data => {
                const els = document.querySelectorAll('#wishlist-count');
                els.forEach(el => {
                    const count = data.count || 0;
                    if (count > 0) {
                        el.textContent = count;
                        el.style.display = 'flex';
                    } else {
                        el.style.display = 'none';
                    }
                });
            })
            .catch(e => console.error('Wishlist error:', e));
        @endif
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
        updateWishlistCount();
    });

    document.addEventListener('cartUpdated', updateCartCount);
    document.addEventListener('wishlistUpdated', updateWishlistCount);
</script>

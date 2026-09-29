<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VandoraX')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper">

<header style="background: linear-gradient(135deg, #1a472a 0%, #2d6a47 100%); position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-bottom: 2px solid #ffa500;">
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; height: 70px;">
            <a href="{{ route('home') }}" style="text-decoration: none; font-size: 26px; font-weight: 700; color: white;">Vandora<span style="color: #ffa500;">X</span></a>

            <div style="flex: 1; max-width: 450px; margin: 0 40px;">
                <form action="{{ route('storefront.products.index') }}" method="GET" style="display: flex; gap: 6px;">
                    <input type="text" name="search" placeholder="Search products..." style="flex: 1; padding: 10px 16px; border: 1px solid #ddd; border-radius: 6px 0 0 6px; font-size: 14px;" value="{{ request('search', '') }}">
                    <button type="submit" style="padding: 10px 24px; background: #ffa500; border: none; border-radius: 0 6px 6px 0; color: white; font-weight: 600; cursor: pointer;">Search</button>
                </form>
            </div>

            <div style="display: flex; align-items: center; gap: 32px;">
                @if(auth()->check())
                <a href="{{ route('wishlist.index') }}" title="Wishlist" style="text-decoration: none; color: white; font-size: 22px; position: relative;">
                    <span>♡</span>
                    <span id="wishlist-count" style="position: absolute; top: -8px; right: -12px; background: #ffa500; color: white; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 12px; min-width: 20px; text-align: center; display: none;">0</span>
                </a>
                @endif

                <a href="{{ route('cart.index') }}" title="Shopping Cart" style="text-decoration: none; color: white; font-size: 22px; position: relative;">
                    <span>🛒</span>
                    <span id="cart-count" style="position: absolute; top: -8px; right: -12px; background: #ffa500; color: white; font-size: 11px; font-weight: 700; padding: 2px 6px; border-radius: 12px; min-width: 20px; text-align: center; display: none;">0</span>
                </a>

                @if(auth()->check())
                    <div style="display: flex; align-items: center; gap: 12px; padding-left: 24px; border-left: 1px solid rgba(255,255,255,0.2);">
                        <span style="color: white; font-size: 13px;">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #ffa500; cursor: pointer; font-weight: 600; font-size: 13px;">Logout</button>
                        </form>
                    </div>
                @else
                    <div style="display: flex; gap: 12px; padding-left: 24px; border-left: 1px solid rgba(255,255,255,0.2);">
                        <a href="{{ route('customer.login') }}" style="color: white; text-decoration: none; font-weight: 600; font-size: 13px;">Login</a>
                        <a href="{{ route('customer.register') }}" style="color: #ffa500; text-decoration: none; font-weight: 600; font-size: 13px;">Register</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</header>

<script>
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
    <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 lg:px-8">

        <div class="flex items-baseline gap-3">
            <span class="text-lg font-display font-bold text-white">
                Vandora<span class="text-marigold">X</span>
            </span>
            @hasSection('area')
                <span class="text-[13px] text-forest-100/60">@yield('area')</span>
            @endif
        </div>

        <div class="flex items-center gap-5">
            @if (request()->is('admin/*'))
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-forest-100/80 transition-colors hover:text-marigold">
                    Back to dashboard
                </a>
            @endif
            <span class="hidden text-sm text-forest-100/80 sm:inline">{{ auth()->user()->name }}</span>
            @php
                $logoutRoute = 'customer.logout';
                if (auth()->user()?->hasRole('admin')) {
                    $logoutRoute = 'admin.logout';
                } elseif (auth()->user()?->hasRole('vendor')) {
                    $logoutRoute = 'vendor.logout';
                }
            @endphp
            <form method="POST" action="{{ route($logoutRoute) }}">
                @csrf
                <button type="submit" class="text-sm text-forest-100/80 transition-colors hover:text-marigold">
                    Log out
                </button>
            </form>
        </div>
    </div>
</header>

<main class="mx-auto max-w-6xl px-5 py-10 lg:px-8 lg:py-14">
    @include('partials.alerts')
    @yield('content')
</main>

</body>
</html>
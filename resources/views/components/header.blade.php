<header style="background: #1a472a; position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="max-width: 1400px; margin: 0 auto; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center;">

        <!-- Logo -->
        <a href="{{ route('home') }}" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <span style="font-size: 24px; font-weight: 700; color: white;">Vandora<span style="color: #ffa500;">X</span></span>
        </a>

        <!-- Search (Center) -->
        <div style="flex: 1; max-width: 400px; margin: 0 40px;">
            <form action="{{ route('storefront.products.index') }}" method="GET" style="display: flex; gap: 8px;">
                <input type="text" name="search" placeholder="Search products..."
                    style="flex: 1; padding: 10px 16px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;"
                    value="{{ request('search') }}">
                <button type="submit" style="padding: 10px 16px; background: #ffa500; border: none; border-radius: 6px; color: white; font-weight: 600; cursor: pointer;">
                    Search
                </button>
            </form>
        </div>

        <!-- Right Menu -->
        <div style="display: flex; align-items: center; gap: 24px;">

            <!-- Wishlist Icon -->
            @if(auth()->check())
            <a href="{{ route('wishlist.index') }}" style="position: relative; text-decoration: none; display: flex; align-items: center; gap: 4px; color: white; font-size: 14px; transition: color 0.2s;">
                <span style="font-size: 20px;">♡</span>
                <span id="wishlist-count" style="background: #ffa500; color: white; font-size: 11px; padding: 2px 6px; border-radius: 12px; min-width: 20px; text-align: center;">0</span>
            </a>
            @endif

            <!-- Cart Icon -->
            <a href="{{ route('cart.index') }}" style="position: relative; text-decoration: none; display: flex; align-items: center; gap: 4px; color: white; font-size: 14px; transition: color 0.2s;">
                <span style="font-size: 20px;">🛒</span>
                <span id="cart-count" style="background: #ffa500; color: white; font-size: 11px; padding: 2px 6px; border-radius: 12px; min-width: 20px; text-align: center;">0</span>
            </a>

            <!-- User Menu -->
            @if(auth()->check())
                <div style="display: flex; align-items: center; gap: 12px; padding-left: 24px; border-left: 1px solid rgba(255,255,255,0.2);">
                    <span style="color: white; font-size: 13px;">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('customer.logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #ffa500; cursor: pointer; font-weight: 600; font-size: 13px;">
                            Logout
                        </button>
                    </form>
                </div>
            @else
                <div style="display: flex; gap: 12px; padding-left: 24px; border-left: 1px solid rgba(255,255,255,0.2);">
                    <a href="{{ route('customer.login') }}" style="color: white; text-decoration: none; font-weight: 600; font-size: 13px; transition: color 0.2s;">
                        Login
                    </a>
                    <a href="{{ route('customer.register') }}" style="color: #ffa500; text-decoration: none; font-weight: 600; font-size: 13px; transition: color 0.2s;">
                        Register
                    </a>
                </div>
            @endif
        </div>
    </div>
</header>

<script>
// Update cart count
function updateCartCount() {
    fetch('{{ route('cart.count') }}')
        .then(r => r.json())
        .then(data => {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.count || 0;
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
            const wishlistCount = document.getElementById('wishlist-count');
            if (wishlistCount) {
                wishlistCount.textContent = data.count || 0;
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

// Listen for cart/wishlist updates
document.addEventListener('cartUpdated', updateCartCount);
document.addEventListener('wishlistUpdated', updateWishlistCount);
</script>

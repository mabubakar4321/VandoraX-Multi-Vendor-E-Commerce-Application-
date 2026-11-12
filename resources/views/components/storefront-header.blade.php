<header class="sticky top-0 z-50 border-b border-forest-700 bg-forest shadow-sm">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center gap-3 py-3 sm:gap-5">
            <a href="{{ route('home') }}" class="shrink-0 text-xl font-display font-bold text-white sm:text-2xl">
                Vandora<span class="text-marigold">X</span>
            </a>

            <form action="{{ route('storefront.products.index') }}" method="GET" class="order-3 flex w-full min-w-0 sm:order-none sm:ml-auto sm:max-w-md sm:flex-1">
                <label for="storefront-search" class="sr-only">Search products</label>
                <input id="storefront-search" type="search" name="search" value="{{ request('search', '') }}"
                       placeholder="Search products..."
                       class="min-w-0 flex-1 rounded-l-md border border-white/20 bg-white px-3 py-2 text-sm text-forest outline-none placeholder:text-graphite/50 focus:border-marigold focus:ring-2 focus:ring-marigold/30">
                <button type="submit" class="rounded-r-md bg-marigold px-3 py-2 text-sm font-semibold text-forest transition-colors hover:bg-marigold-600 sm:px-5">
                    Search
                </button>
            </form>

            <nav class="ml-auto flex items-center gap-2 sm:gap-3" aria-label="Shopping">
                @if(auth()->check())
                    <a href="{{ route('wishlist.index') }}" class="relative rounded-md p-2 text-forest-100 transition-colors hover:bg-forest-700 hover:text-marigold" aria-label="Wishlist">
                        <span aria-hidden="true" class="text-xl">♡</span>
                        <span id="wishlist-count" class="absolute -right-1 -top-1 hidden min-w-5 rounded-full bg-marigold px-1.5 py-0.5 text-center text-[10px] font-bold text-forest">0</span>
                    </a>
                @endif
                <a href="{{ route('cart.index') }}" class="relative rounded-md p-2 text-forest-100 transition-colors hover:bg-forest-700 hover:text-marigold" aria-label="Shopping cart">
                    <span aria-hidden="true" class="text-lg">🛒</span>
                    <span id="cart-count" class="absolute -right-1 -top-1 hidden min-w-5 rounded-full bg-marigold px-1.5 py-0.5 text-center text-[10px] font-bold text-forest">0</span>
                </a>
                @if(auth()->check())
                    <a href="{{ route('order.index') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-forest-100 transition-colors hover:bg-forest-700 hover:text-marigold md:inline">
                        My orders
                    </a>
                    <form method="POST" action="{{ route('customer.logout') }}">
                        @csrf
                        <button type="submit" class="rounded-md px-2 py-2 text-sm font-medium text-forest-100 transition-colors hover:bg-forest-700 hover:text-marigold">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('customer.login') }}" class="hidden rounded-md px-2 py-2 text-sm font-medium text-forest-100 transition-colors hover:bg-forest-700 hover:text-marigold sm:inline">Log in</a>
                    <a href="{{ route('customer.register') }}" class="rounded-md bg-marigold px-3 py-2 text-sm font-semibold text-forest transition-colors hover:bg-marigold-600">Join</a>
                @endif
            </nav>
        </div>
    </div>
</header>

@once
    @push('scripts')
        <script>
            function updateCount(route, selector) {
                fetch(route)
                    .then(response => response.json())
                    .then(data => {
                        document.querySelectorAll(selector).forEach(element => {
                            const count = Number(data.count || 0);
                            element.textContent = count;
                            element.classList.toggle('hidden', count === 0);
                        });
                    })
                    .catch(error => console.error('Unable to update shopping count:', error));
            }

            document.addEventListener('DOMContentLoaded', function () {
                updateCount('{{ route('cart.count') }}', '#cart-count');
                @if(auth()->check())
                    updateCount('{{ route('wishlist.count') }}', '#wishlist-count');
                @endif
            });
        </script>
    @endpush
@endonce

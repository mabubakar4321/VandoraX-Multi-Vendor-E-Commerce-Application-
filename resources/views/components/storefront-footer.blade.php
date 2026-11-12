<footer class="border-t border-mist bg-white">
    <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:grid-cols-2 sm:px-6 lg:grid-cols-4 lg:px-8">
        <div class="sm:col-span-2 lg:col-span-1">
            <a href="{{ route('home') }}" class="text-xl font-display font-bold text-forest">Vandora<span class="text-marigold">X</span></a>
            <p class="mt-3 max-w-xs text-sm leading-6 text-graphite/75">A trusted marketplace for discovering products from verified local sellers.</p>
        </div>
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-forest">Shop</h2>
            <div class="mt-3 space-y-2 text-sm">
                <a href="{{ route('storefront.products.index') }}" class="block text-graphite transition-colors hover:text-forest">Browse products</a>
                <a href="{{ route('collections.new-arrivals') }}" class="block text-graphite transition-colors hover:text-forest">New arrivals</a>
                <a href="{{ route('collections.bestsellers') }}" class="block text-graphite transition-colors hover:text-forest">Bestsellers</a>
            </div>
        </div>
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-forest">Account</h2>
            <div class="mt-3 space-y-2 text-sm">
                <a href="{{ route('customer.login') }}" class="block text-graphite transition-colors hover:text-forest">Log in</a>
                <a href="{{ route('customer.register') }}" class="block text-graphite transition-colors hover:text-forest">Create account</a>
                @if(!auth()->check() || !auth()->user()->hasRole('customer'))
                    <a href="{{ route('vendor.register') }}" class="block text-graphite transition-colors hover:text-forest">Start selling</a>
                @endif
            </div>
        </div>
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-forest">Need help?</h2>
            <p class="mt-3 text-sm leading-6 text-graphite/75">Shop confidently with secure checkout and order tracking.</p>
        </div>
    </div>
    <div class="border-t border-mist">
        <p class="mx-auto max-w-7xl px-4 py-5 text-xs text-graphite/60 sm:px-6 lg:px-8">&copy; {{ date('Y') }} VandoraX. All rights reserved.</p>
    </div>
</footer>

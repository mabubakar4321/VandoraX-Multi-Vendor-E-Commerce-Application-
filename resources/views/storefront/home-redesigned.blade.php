@extends('layouts.storefront')

@section('title', 'VandoraX — Shop from trusted sellers')

@push('styles')
<style>
    .home-page {
        --home-forest: #0e2a22;
        --home-forest-light: #1e6f5c;
        --home-gold: #f2b705;
        --home-gold-dark: #d9a404;
        --home-mist: #e4eae5;
        color: var(--home-forest);
    }

    .home-page .home-hero {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background:
            radial-gradient(circle at 85% 20%, rgba(242, 183, 5, 0.18), transparent 28%),
            linear-gradient(135deg, #0e2a22 0%, #14392e 100%);
    }

    .home-page .home-hero::after {
        content: "";
        position: absolute;
        right: -80px;
        bottom: -120px;
        width: 340px;
        height: 340px;
        border: 1px solid rgba(242, 183, 5, 0.25);
        border-radius: 50%;
        box-shadow: 0 0 0 32px rgba(242, 183, 5, 0.04), 0 0 0 64px rgba(242, 183, 5, 0.03);
    }

    .home-page .hero-copy,
    .home-page .hero-panel {
        position: relative;
        z-index: 1;
    }

    .home-page .hero-panel {
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(8px);
    }

    .home-page .section-kicker {
        color: var(--home-forest-light);
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .home-page .section-heading {
        margin-top: 5px;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 800;
        letter-spacing: -0.035em;
    }

    .home-page .soft-card {
        border: 1px solid var(--home-mist);
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(14, 42, 34, 0.05);
    }

    .home-page .category-card,
    .home-page .product-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .home-page .category-card:hover,
    .home-page .product-card:hover {
        border-color: rgba(242, 183, 5, 0.7);
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(14, 42, 34, 0.1);
    }

    .home-page .category-image {
        height: 108px;
        overflow: hidden;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(135deg, #d6e3dd, #f5f7f4);
    }

    .home-page .category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .home-page .category-fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: var(--home-forest-light);
        font-size: 2rem;
    }

    .home-page .product-image {
        aspect-ratio: 1 / 1;
        overflow: hidden;
        background: #f5f7f4;
    }

    .home-page .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .home-page .product-card:hover .product-image img { transform: scale(1.04); }

    .home-page .vendor-strip {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 18px 18px 0 0;
        background: linear-gradient(135deg, #0e2a22, #14392e);
        color: #fff;
    }

    .home-page .vendor-avatar {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        flex: 0 0 auto;
        border-radius: 50%;
        background: var(--home-gold);
        color: var(--home-forest);
        font-weight: 800;
    }

    .home-page .action-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        min-height: 190px;
        border-radius: 20px;
        padding: 28px;
    }

    .home-page .action-card.seller {
        background: linear-gradient(135deg, #0e2a22, #1e6f5c);
        color: #fff;
    }

    .home-page .action-card.customer {
        border: 1px solid var(--home-mist);
        background: #fff;
    }

    .home-page .action-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 62px;
        height: 62px;
        flex: 0 0 auto;
        border-radius: 18px;
        background: rgba(242, 183, 5, 0.16);
        color: var(--home-gold);
        font-size: 1.8rem;
    }

    .home-page .seller .action-icon { background: rgba(242, 183, 5, 0.2); }

    .home-page .home-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 42px;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        transition: transform 0.2s ease, background 0.2s ease;
    }

    .home-page .home-button:hover { transform: translateY(-1px); }
    .home-page .home-button.gold { background: var(--home-gold); color: var(--home-forest); }
    .home-page .home-button.gold:hover { background: var(--home-gold-dark); }
    .home-page .home-button.outline { border: 1px solid rgba(255, 255, 255, 0.35); color: #fff; }
    .home-page .home-button.outline:hover { background: rgba(255, 255, 255, 0.1); }
    .home-page .home-button.forest { background: var(--home-forest); color: #fff; }

    @media (max-width: 640px) {
        .home-page .home-hero { border-radius: 18px; }
        .home-page .action-card { align-items: flex-start; flex-direction: column; min-height: 0; padding: 22px; }
        .home-page .action-card .home-button { width: 100%; }
        .home-page .vendor-strip { align-items: flex-start; flex-direction: column; }
    }
</style>
@endpush

@php
    $categoryImage = function ($category) {
        if (!$category->icon) {
            return null;
        }

        return filter_var($category->icon, FILTER_VALIDATE_URL)
            ? $category->icon
            : asset('storage/' . ltrim($category->icon, '/'));
    };
@endphp

@section('content')
<div class="home-page">
    <section class="home-hero grid items-center gap-8 px-6 py-10 sm:px-10 lg:grid-cols-[1.1fr_.9fr] lg:px-14 lg:py-14">
        <div class="hero-copy">
            <p class="text-sm font-bold uppercase tracking-[0.18em] text-marigold">The trusted local marketplace</p>
            <h1 class="mt-4 max-w-2xl text-4xl font-bold leading-tight text-white sm:text-5xl lg:text-6xl">Discover products from stores you can trust.</h1>
            <p class="mt-5 max-w-xl text-base leading-7 text-forest-100/80 sm:text-lg">Shop beautiful finds from local vendors, compare prices, and enjoy simple checkout with dependable order tracking.</p>
            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('storefront.products.index') }}" class="home-button gold">Shop all products</a>
                <a href="{{ route('collections.new-arrivals') }}" class="home-button outline">Explore new arrivals</a>
            </div>
        </div>
        <div class="hero-panel p-6 sm:p-8">
            <p class="text-sm font-semibold text-forest-100/70">A marketplace built for discovery</p>
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div>
                    <p class="text-3xl font-bold text-marigold">{{ $products->total() }}+</p>
                    <p class="mt-1 text-xs text-forest-100/70">approved products</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-marigold">{{ $vendors->count() }}+</p>
                    <p class="mt-1 text-xs text-forest-100/70">active stores</p>
                </div>
            </div>
            <div class="mt-7 border-t border-white/10 pt-5 text-sm leading-6 text-forest-100/75">
                Secure shopping, local sellers, and quality products in one place.
            </div>
        </div>
    </section>

    <section class="mt-8 grid gap-5 {{ auth()->check() && auth()->user()->hasRole('customer') ? 'lg:grid-cols-1' : 'lg:grid-cols-2' }}">
        @if(!auth()->check() || !auth()->user()->hasRole('customer'))
            <div class="action-card seller">
                <div class="flex items-start gap-4">
                    <div class="action-icon">✦</div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.14em] text-marigold">Grow with VandoraX</p>
                        <h2 class="mt-2 text-2xl font-bold">Become a seller</h2>
                        <p class="mt-2 max-w-md text-sm leading-6 text-forest-100/80">Open your store, reach new customers, and start selling your products with our vendor tools.</p>
                    </div>
                </div>
                <a href="{{ route('vendor.register') }}" class="home-button gold shrink-0">Start selling</a>
            </div>
        @endif
        <div class="action-card customer">
            <div class="flex items-start gap-4">
                <div class="action-icon">🛍</div>
                <div>
                    <p class="section-kicker">Ready to shop?</p>
                    <h2 class="mt-2 text-2xl font-bold">Join as a customer</h2>
                    <p class="mt-2 max-w-md text-sm leading-6 text-graphite/70">Save favorites, follow orders, and enjoy a faster checkout experience.</p>
                </div>
            </div>
            @if(!auth()->check())
                <a href="{{ route('customer.login') }}" class="home-button forest shrink-0">Login as customer</a>
            @elseif(auth()->user()->hasRole('customer'))
                <a href="{{ route('order.index') }}" class="home-button forest shrink-0">View my orders</a>
            @endif
        </div>
    </section>

    @if($categories->isNotEmpty())
        <section class="mt-14">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="section-kicker">Explore the marketplace</p>
                    <h2 class="section-heading">Shop by category</h2>
                </div>
                <a href="{{ route('storefront.products.index') }}" class="text-sm font-bold text-forest-500 hover:text-forest">View all products &rarr;</a>
            </div>
            <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($categories as $category)
                    @php($image = $categoryImage($category))
                    <a href="{{ route('collections.category', $category) }}" class="category-card soft-card overflow-hidden">
                        <div class="category-image">
                            @if($image)
                                <img src="{{ $image }}" alt="{{ $category->name }}">
                            @else
                                <div class="category-fallback">✦</div>
                            @endif
                        </div>
                        <div class="p-3 text-center">
                            <p class="truncate text-sm font-bold text-forest">{{ $category->name }}</p>
                            <p class="mt-1 text-xs text-graphite/60">Explore collection</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-14">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="section-kicker">Freshly curated</p>
                <h2 class="section-heading">Latest products</h2>
            </div>
            <a href="{{ route('storefront.products.index') }}" class="text-sm font-bold text-forest hover:text-forest-500">Browse everything &rarr;</a>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @forelse($products->take(8) as $product)
                @php($image = $product->images->first())
                <a href="{{ route('storefront.products.show', $product) }}" class="product-card soft-card group overflow-hidden">
                    <div class="product-image">
                        @if($image)
                            <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}">
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-graphite/50">No image</div>
                        @endif
                    </div>
                    <div class="p-3 sm:p-4">
                        <p class="truncate text-sm font-bold text-forest">{{ $product->name }}</p>
                        <p class="mt-1 text-sm font-bold text-forest-500">PKR {{ number_format($product->base_price) }}</p>
                        <p class="mt-2 truncate text-xs text-graphite/60">{{ $product->vendor?->store_name ?? 'Verified seller' }}</p>
                    </div>
                </a>
            @empty
                <div class="col-span-full soft-card p-10 text-center text-graphite">No products are available yet.</div>
            @endforelse
        </div>
    </section>

    @if($vendors->isNotEmpty())
        <section class="mt-14">
            <div>
                <p class="section-kicker">Meet the marketplace</p>
                <h2 class="section-heading">Shop from our stores</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-graphite/70">Discover two handpicked products from each active vendor and find a new favorite store.</p>
            </div>
            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                @foreach($vendors as $vendor)
                    <article class="soft-card overflow-hidden">
                        <div class="vendor-strip">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="vendor-avatar">{{ strtoupper(substr($vendor->store_name ?: 'V', 0, 1)) }}</div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-lg font-bold">{{ $vendor->store_name }}</h3>
                                    <p class="mt-1 truncate text-xs text-forest-100/70">{{ $vendor->category?->name ?? 'Independent local store' }}</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-forest-100">{{ $vendor->products->count() }} products shown</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 p-4">
                            @foreach($vendor->products->take(2) as $product)
                                @php($image = $product->images->first())
                                <div class="overflow-hidden rounded-xl border border-mist bg-paper">
                                    <a href="{{ route('storefront.products.show', $product) }}" class="product-image block">
                                        @if($image)
                                            <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}">
                                        @else
                                            <div class="flex h-full items-center justify-center text-xs text-graphite/50">No image</div>
                                        @endif
                                    </a>
                                    <div class="p-3">
                                        <p class="truncate text-sm font-bold text-forest">{{ $product->name }}</p>
                                        <p class="mt-1 text-sm font-bold text-forest-500">PKR {{ number_format($product->base_price) }}</p>
                                        <p class="mt-1 truncate text-xs text-graphite/60">{{ $vendor->store_name }}</p>
                                        <a href="{{ route('storefront.products.show', $product) }}" class="mt-3 inline-flex text-xs font-bold text-forest hover:text-forest-500">View product &rarr;</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-14 grid gap-4 sm:grid-cols-3">
        <div class="soft-card p-5">
            <p class="text-2xl">✓</p>
            <h3 class="mt-3 font-bold text-forest">Verified local sellers</h3>
            <p class="mt-2 text-sm leading-6 text-graphite/70">Shop with confidence from stores reviewed and approved by VandoraX.</p>
        </div>
        <div class="soft-card p-5">
            <p class="text-2xl">⚡</p>
            <h3 class="mt-3 font-bold text-forest">Simple checkout</h3>
            <p class="mt-2 text-sm leading-6 text-graphite/70">A clear shopping experience from product discovery to order tracking.</p>
        </div>
        <div class="soft-card p-5">
            <p class="text-2xl">↻</p>
            <h3 class="mt-3 font-bold text-forest">Helpful support</h3>
            <p class="mt-2 text-sm leading-6 text-graphite/70">We help customers and sellers keep every order moving smoothly.</p>
        </div>
    </section>
</div>
@endsection

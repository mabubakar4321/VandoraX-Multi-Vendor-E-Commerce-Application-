<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop approved products - VandoraX</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper text-forest">
<header class="bg-forest text-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 lg:px-8">
        <a href="{{ route('home') }}" class="font-display text-xl font-bold">Vandora<span class="text-marigold">X</span></a>
        <div class="flex items-center gap-5 text-sm text-forest-100/80">
            @auth
                <a href="{{ auth()->user()->dashboardRoute() ? route(auth()->user()->dashboardRoute()) : route('home') }}" class="hover:text-marigold">My account</a>
            @else
                <a href="{{ route('customer.login') }}" class="hover:text-marigold">Log in</a>
            @endauth
        </div>
    </div>
</header>

<main class="mx-auto max-w-7xl px-5 py-10 lg:px-8 lg:py-14">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-marigold">VandoraX marketplace</p>
            <h1 class="mt-2 font-display text-4xl font-bold tracking-tight md:text-5xl">Shop with confidence.</h1>
            <p class="mt-3 max-w-xl text-base text-graphite">Products reviewed and approved by our admin team, from sellers you can trust.</p>
        </div>
        <p class="text-sm text-graphite">{{ $products->total() }} approved products</p>
    </div>

    <form method="GET" class="mt-10 grid gap-3 border border-mist bg-white p-4 md:grid-cols-[1fr_240px_auto]" style="border-radius: 2px;">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="Search products, brands, or descriptions" class="min-w-0 border border-mist px-4 py-3 outline-none focus:border-forest">
        <select name="category" class="border border-mist px-4 py-3 outline-none focus:border-forest">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <button class="bg-marigold px-6 py-3 font-semibold text-forest hover:bg-yellow-400">Search</button>
    </form>

    @if($products->isEmpty())
        <div class="mt-10 border border-mist bg-white px-6 py-16 text-center">
            <h2 class="font-display text-2xl font-bold">No approved products found</h2>
            <p class="mt-2 text-graphite">Try another search or category.</p>
        </div>
    @else
        <div class="mt-10 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($products as $product)
                <a href="{{ route('storefront.products.show', $product) }}" class="group overflow-hidden border border-mist bg-white transition hover:-translate-y-1 hover:border-forest hover:shadow-lg" style="border-radius: 2px;">
                    <div class="aspect-[4/3] overflow-hidden bg-mist">
                        @if($product->images->first())
                            <img src="{{ $product->images->first()->getThumbnailUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                        @else
                            <div class="flex h-full items-center justify-center text-sm text-graphite/60">No image available</div>
                        @endif
                    </div>
                    <div class="p-5">
                        <p class="text-xs font-medium uppercase tracking-wide text-graphite/70">{{ $product->category?->name ?? 'Marketplace' }}</p>
                        <h2 class="mt-2 line-clamp-2 font-display text-xl font-bold text-forest">{{ $product->name }}</h2>
                        <p class="mt-3 text-lg font-semibold text-forest">PKR {{ number_format($product->base_price, 2) }}</p>
                        <p class="mt-2 text-sm text-graphite">{{ $product->vendor?->store_name ?? 'Verified seller' }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-10">{{ $products->links() }}</div>
    @endif
</main>
</body>
</html>

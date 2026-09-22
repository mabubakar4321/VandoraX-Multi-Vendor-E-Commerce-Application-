<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - VandoraX</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper text-forest">
<header class="bg-forest text-white">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 lg:px-8">
        <a href="{{ route('storefront.products.index') }}" class="font-display text-xl font-bold">Vandora<span class="text-marigold">X</span></a>
        <a href="{{ route('storefront.products.index') }}" class="text-sm text-forest-100/80 hover:text-marigold">Continue shopping</a>
    </div>
</header>

<main class="mx-auto max-w-7xl px-5 py-8 lg:px-8 lg:py-12">
    <a href="{{ route('storefront.products.index') }}" class="text-sm font-medium text-forest hover:text-marigold">&larr; Back to products</a>

    <div class="mt-8 grid gap-10 lg:grid-cols-[1.05fr_0.95fr]">
        <div>
            @if($product->images->first())
                <div class="aspect-square overflow-hidden border border-mist bg-white" style="border-radius: 2px;">
                    <img id="main-product-image" src="{{ $product->images->first()->getImageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-contain">
                </div>
                @if($product->images->count() > 1)
                    <div class="mt-4 grid grid-cols-5 gap-3">
                        @foreach($product->images as $image)
                            <button type="button" class="product-thumb aspect-square overflow-hidden border border-mist bg-white p-1" data-image="{{ $image->getImageUrl() }}">
                                <img src="{{ $image->getThumbnailUrl() }}" alt="{{ $product->name }} thumbnail" class="h-full w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="flex aspect-square items-center justify-center border border-mist bg-white text-graphite">No image available</div>
            @endif
        </div>

        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-marigold">Admin approved product</p>
            <h1 class="mt-3 font-display text-4xl font-bold leading-tight md:text-5xl">{{ $product->name }}</h1>
            <p class="mt-4 text-sm text-graphite">Sold by <span class="font-semibold text-forest">{{ $product->vendor?->store_name ?? 'Verified seller' }}</span></p>
            <p class="mt-8 font-display text-3xl font-bold text-forest">PKR {{ number_format($product->base_price, 2) }}</p>
            <div class="mt-6 border-y border-mist py-5 text-sm text-graphite">
                <p><span class="font-semibold text-forest">Category:</span> {{ $product->category?->name ?? 'Marketplace' }}{{ $product->subcategory ? ' / ' . $product->subcategory->name : '' }}</p>
                <p class="mt-2"><span class="font-semibold text-forest">Delivery:</span> {{ $product->delivery_time_days }} days</p>
                <p class="mt-2"><span class="font-semibold text-forest">Availability:</span> {{ $product->getTotalStock() > 0 ? 'In stock' : 'Currently unavailable' }}</p>
            </div>
            <button type="button" class="mt-8 w-full bg-marigold px-6 py-4 text-center font-semibold text-forest hover:bg-yellow-400">Add to cart</button>
            <p class="mt-3 text-center text-xs text-graphite/70">Secure checkout and seller support through VandoraX.</p>
        </div>
    </div>

    <div class="mt-14 grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
        <section class="border-t-2 border-forest pt-5">
            <h2 class="font-display text-2xl font-bold">Description</h2>
            <p class="mt-4 whitespace-pre-line leading-7 text-graphite">{{ $product->description }}</p>
        </section>
        <section class="border-t-2 border-marigold pt-5">
            <h2 class="font-display text-2xl font-bold">Product details</h2>
            <dl class="mt-4 divide-y divide-mist">
                @foreach($product->specifications as $specification)
                    <div class="flex justify-between gap-5 py-3 text-sm"><dt class="text-graphite">{{ $specification->spec_key }}</dt><dd class="text-right font-medium text-forest">{{ $specification->spec_value }}</dd></div>
                @endforeach
                <div class="flex justify-between gap-5 py-3 text-sm"><dt class="text-graphite">Warranty</dt><dd class="text-right font-medium text-forest">{{ $product->warranty ?: 'Not provided' }}</dd></div>
                <div class="flex justify-between gap-5 py-3 text-sm"><dt class="text-graphite">SKU</dt><dd class="text-right font-medium text-forest">{{ $product->sku ?: 'Not assigned' }}</dd></div>
            </dl>
        </section>
    </div>

    @if($product->variants->isNotEmpty())
        <section class="mt-14 border-t-2 border-forest pt-5">
            <h2 class="font-display text-2xl font-bold">Choose an option</h2>
            <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($product->variants as $variant)
                    <div class="border border-mist bg-white p-4" style="border-radius: 2px;">
                        <p class="text-xs uppercase tracking-wide text-graphite/70">{{ $variant->variant_name }}</p>
                        <p class="mt-1 font-semibold text-forest">{{ $variant->variant_value }}</p>
                        <p class="mt-3 font-medium">PKR {{ number_format($variant->price, 2) }}</p>
                        <p class="mt-1 text-xs text-graphite">{{ $variant->stock > 0 ? $variant->stock . ' available' : 'Out of stock' }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
</main>

<script>
    document.querySelectorAll('.product-thumb').forEach((thumbnail) => {
        thumbnail.addEventListener('click', () => {
            document.getElementById('main-product-image').src = thumbnail.dataset.image;
        });
    });
</script>
</body>
</html>

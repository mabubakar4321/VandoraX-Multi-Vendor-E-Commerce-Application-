@extends('layouts.storefront')

@section('title', 'Wishlist — VandoraX')

@section('content')
    <div class="flex flex-col gap-3 border-b border-mist pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-forest-500">Saved for later</p>
            <h1 class="mt-1 text-3xl font-bold sm:text-4xl">Your wishlist</h1>
            <p class="mt-2 text-sm text-graphite/75">{{ $wishlists->count() }} {{ Str::plural('item', $wishlists->count()) }} saved</p>
        </div>
        @if($wishlists->isNotEmpty())
            <form method="POST" action="{{ route('wishlist.clear') }}" onsubmit="return confirm('Remove every item from your wishlist?')">
                @csrf
                <button type="submit" class="rounded-md border border-mist px-4 py-2 text-sm font-semibold text-graphite transition-colors hover:border-vermilion hover:text-vermilion">Clear wishlist</button>
            </form>
        @endif
    </div>

    @if($wishlists->isNotEmpty())
        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($wishlists as $wishlist)
                @php($product = $wishlist->product)
                @php($image = $product->images->first())
                <article class="overflow-hidden rounded-xl border border-mist bg-white shadow-sm">
                    <a href="{{ route('storefront.products.show', $product) }}" class="group block">
                        <div class="aspect-square bg-mist">
                            @if($image)
                                <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                            @else
                                <div class="flex h-full items-center justify-center text-sm text-graphite/50">No image</div>
                            @endif
                        </div>
                        <div class="p-3 sm:p-4">
                            <p class="truncate text-sm font-semibold text-forest">{{ $product->name }}</p>
                            <p class="mt-1 text-sm font-bold text-forest-500">₨{{ number_format($product->base_price) }}</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-2 border-t border-mist p-3">
                        <a href="{{ route('storefront.products.show', $product) }}" class="flex-1 rounded-md bg-marigold px-3 py-2 text-center text-xs font-semibold text-forest transition-colors hover:bg-marigold-600">View product</a>
                        <form method="POST" action="{{ route('wishlist.remove', $wishlist) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-md border border-mist px-3 py-2 text-xs font-semibold text-graphite transition-colors hover:border-vermilion hover:text-vermilion" aria-label="Remove {{ $product->name }}">Remove</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="mt-8 rounded-xl border border-mist bg-white px-6 py-16 text-center">
            <h2 class="text-2xl font-bold">Your wishlist is empty</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-graphite/75">Save products you love and come back to them whenever you are ready.</p>
            <a href="{{ route('storefront.products.index') }}" class="btn-primary mx-auto mt-6 w-auto px-6">Start shopping</a>
        </div>
    @endif
@endsection

@extends('layouts.storefront')

@section('title', $title . ' — VandoraX')

@section('content')
    <div class="flex flex-col gap-3 border-b border-mist pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-forest-500">Collection</p>
            <h1 class="mt-1 text-3xl font-bold sm:text-4xl">{{ $title }}</h1>
            <p class="mt-2 max-w-2xl text-sm text-graphite/75">{{ $description }}</p>
        </div>
        <p class="text-sm text-graphite/70">{{ $products->total() }} products</p>
    </div>

    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @forelse($products as $product)
            @php($image = $product->images->first())
            <a href="{{ route('storefront.products.show', $product) }}" class="group overflow-hidden rounded-xl border border-mist bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
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
                    @if($product->vendor)
                        <p class="mt-1 truncate text-xs text-graphite/60">{{ $product->vendor->store_name }}</p>
                    @endif
                </div>
            </a>
        @empty
            <div class="col-span-full rounded-xl border border-mist bg-white p-12 text-center text-graphite">No products found in this collection.</div>
        @endforelse
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
@endsection

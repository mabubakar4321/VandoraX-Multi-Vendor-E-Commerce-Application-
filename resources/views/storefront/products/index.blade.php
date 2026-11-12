@extends('layouts.storefront')

@section('title', 'Shop all products — VandoraX')

@push('styles')
<style>
    .products-pagination {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: wrap;
    }

    .products-pagination a,
    .products-pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border: 1px solid var(--color-mist);
        border-radius: 8px;
        background: #fff;
        color: var(--color-forest);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .products-pagination a:hover {
        border-color: var(--color-forest-500);
        background: var(--color-forest-100);
    }

    .products-pagination .active {
        border-color: var(--color-forest);
        background: var(--color-forest);
        color: #fff;
    }

    .products-pagination .disabled {
        cursor: not-allowed;
        opacity: 0.45;
    }

    .products-pagination .ellipsis {
        min-width: 24px;
        padding: 0 4px;
        border-color: transparent;
        background: transparent;
    }

    @media (max-width: 640px) {
        .products-pagination {
            justify-content: center;
        }

        .products-pagination a,
        .products-pagination span {
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            font-size: 0.72rem;
        }
    }
</style>
@endpush

@section('content')
    <div class="flex flex-col gap-3 border-b border-mist pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wide text-forest-500">Marketplace</p>
            <h1 class="mt-1 text-3xl font-bold sm:text-4xl">Shop all products</h1>
            <p class="mt-2 text-sm text-graphite/75">Quality products from verified sellers.</p>
        </div>
        <p class="text-sm text-graphite/70">{{ $products->total() }} results</p>
    </div>

    <form method="GET" class="mt-6 grid gap-3 rounded-xl border border-mist bg-white p-4 sm:grid-cols-[1fr_auto_auto_auto]">
        <label class="sr-only" for="product-search">Search products</label>
        <input id="product-search" name="search" value="{{ request('search') }}" placeholder="Search products..." class="field">
        <select name="category" class="field sm:w-52">
            <option value="">All categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        <select name="sort" class="field sm:w-44">
            <option value="">Newest first</option>
            <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: low to high</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: high to low</option>
            <option value="name_asc" @selected(request('sort') === 'name_asc')>Name: A to Z</option>
            <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
        </select>
        <button type="submit" class="btn-primary sm:w-auto">Filter</button>
    </form>

    <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
        @forelse($products as $product)
            @php($image = $product->images->first())
            <article class="group flex flex-col overflow-hidden rounded-xl border border-mist bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="aspect-square bg-mist">
                    @if($image)
                        <img src="{{ $image->getImageUrl() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-graphite/50">No image</div>
                    @endif
                </div>
                <div class="flex flex-1 flex-col p-3 sm:p-4">
                    <p class="truncate text-sm font-semibold text-forest">{{ $product->name }}</p>
                    <p class="mt-1 text-sm font-bold text-forest-500">₨{{ number_format($product->base_price) }}</p>
                    <p class="mt-2 truncate text-xs text-graphite/65">
                        {{ $product->vendor?->store_name ?? 'Verified seller' }}
                    </p>
                    <a href="{{ route('storefront.products.show', $product) }}" class="mt-4 inline-flex min-h-9 items-center justify-center rounded-md bg-forest px-3 py-2 text-xs font-semibold text-white transition-colors hover:bg-forest-500">
                        View product
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full rounded-xl border border-mist bg-white p-12 text-center text-graphite">No products matched your search.</div>
        @endforelse
    </div>

    @if($products->hasPages())
        <nav class="mt-10 flex flex-col gap-4 border-t border-mist pt-6 sm:flex-row sm:items-center sm:justify-between" aria-label="Product pagination">
            <p class="text-sm text-graphite/70">
                Showing <span class="font-semibold text-forest">{{ $products->firstItem() }}</span>
                to <span class="font-semibold text-forest">{{ $products->lastItem() }}</span>
                of <span class="font-semibold text-forest">{{ $products->total() }}</span> products
            </p>
            <div class="products-pagination" aria-label="Product pages">
                @if($products->onFirstPage())
                    <span class="disabled" aria-disabled="true">Previous</span>
                @else
                    <a href="{{ $products->withQueryString()->previousPageUrl() }}" rel="prev">Previous</a>
                @endif

                @foreach($products->getUrlRange(max(1, $products->currentPage() - 2), min($products->lastPage(), $products->currentPage() + 2)) as $page => $url)
                    @if($page == $products->currentPage())
                        <span class="active" aria-current="page">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($products->hasMorePages())
                    <a href="{{ $products->withQueryString()->nextPageUrl() }}" rel="next">Next</a>
                @else
                    <span class="disabled" aria-disabled="true">Next</span>
                @endif
            </div>
        </nav>
    @elseif($products->total() > 0)
        <p class="mt-8 border-t border-mist pt-6 text-center text-sm text-graphite/70">
            Showing all {{ $products->total() }} products
        </p>
    @endif
@endsection

@extends('layouts.admin')

@section('title', 'Category Tree')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <a href="{{ route('admin.categories.index') }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-900">← Back to categories</a>
            <p class="mt-5 text-sm font-medium uppercase tracking-[0.18em] text-graphite/60">Catalog management</p>
            <h1 class="mt-2 text-3xl leading-tight">Category hierarchy</h1>
            <p class="mt-2 text-sm text-graphite/70">Explore parent categories, subcategories, and product counts.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn-primary w-full px-5 py-2.5 sm:w-auto">Add category</a>
    </div>

    <section class="border border-mist bg-white p-5 sm:p-6" style="border-radius: 2px;">
        @foreach($tree as $category)
            <div class="border-b border-mist py-5 first:pt-0 last:border-0 last:pb-0">
                <div class="border-l-2 border-forest-500 pl-4 sm:pl-5">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Main category</p>
                            <h2 class="mt-1 break-words text-xl font-semibold">{{ $category['name'] }}</h2>
                            <p class="mt-2 text-sm text-graphite/65">{{ $category['commission_rate'] }}% commission <span class="px-1">·</span> {{ number_format($category['product_count']) }} products</p>
                        </div>
                        <a href="{{ route('admin.categories.edit', $category['id']) }}" class="inline-flex items-center justify-center border border-mist px-4 py-2 text-sm font-semibold text-forest transition-colors hover:bg-paper">
                            Edit category
                        </a>
                    </div>

                    @if(count($category['subcategories']) > 0)
                        <div class="mt-4 space-y-3">
                            @foreach($category['subcategories'] as $subcategory)
                                <div class="ml-2 border border-mist bg-paper/60 p-4 sm:ml-6">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-wider text-graphite/55">Subcategory</p>
                                            <p class="mt-1 break-words font-semibold text-forest">{{ $subcategory['name'] }}</p>
                                            <p class="mt-2 text-xs text-graphite/65">{{ $subcategory['commission_rate'] }}% commission <span class="px-1">·</span> {{ number_format($subcategory['product_count']) }} products</p>
                                        </div>
                                        <a href="{{ route('admin.categories.edit', $subcategory['id']) }}" class="font-semibold text-indigo-600 hover:text-indigo-900">
                                            Edit
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
        @if(empty($tree))
            <div class="py-12 text-center">
                <h2 class="text-lg font-semibold">No categories yet</h2>
                <p class="mt-2 text-sm text-graphite/65">Create a category to start building your product hierarchy.</p>
                <a href="{{ route('admin.categories.create') }}" class="btn-primary mt-5 w-auto px-5 py-2.5">Create category</a>
            </div>
        @endif
    </section>
</div>
@endsection

@extends('layouts.vendor')
@section('title', 'Seller Centre — VandoraX')

@section('content')
@php $vendor = auth()->user()->vendor; @endphp

    <div class="flex flex-wrap items-center justify-between gap-4">
        <h1 class="text-[32px] leading-tight">{{ $vendor->store_name }}</h1>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('vendor.products.index') }}" class="btn-primary w-auto px-5 py-2.5">
                All products
            </a>
            <a href="{{ route('vendor.stock.dashboard') }}" class="btn-primary w-auto px-5 py-2.5 bg-marigold text-white hover:bg-marigold/90">
                📊 Stock Management
            </a>
            <a href="{{ route('vendor.categories.requests') }}" class="btn-primary w-auto px-5 py-2.5">
                Manage categories
            </a>
            <span class="bg-forest-100 px-2.5 py-1 text-[13px] font-medium text-forest-500">
                Open for business
            </span>
        </div>
    </div>

    <p class="mt-2 text-[15px] text-graphite">
        Selling since {{ $vendor->created_at->format('F Y') }}
    </p>

    <div class="mt-10 divide-y divide-mist border border-mist bg-white" style="border-radius: 2px;">
        @foreach ([
            ['Live listings', '0', 'Products customers can buy right now'],
            ['Orders to pack', '0', 'Paid and waiting to be shipped'],
            ['Next payout', 'Rs 0', 'Sent every Friday'],
        ] as [$label, $figure, $caption])
            <div class="flex items-baseline justify-between gap-6 px-6 py-5">
                <div>
                    <p class="text-[15px] font-medium text-forest">{{ $label }}</p>
                    <p class="mt-0.5 text-[13px] text-graphite/70">{{ $caption }}</p>
                </div>
                <p class="tabular font-display text-[28px] leading-none text-forest">{{ $figure }}</p>
            </div>
        @endforeach
    </div>

    <!-- Quick Stock Summary Card -->
    <div class="mt-8 bg-gradient-to-r from-marigold/10 to-forest/10 border border-mist rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-[18px] font-semibold text-forest">📊 Stock Overview</h2>
                <p class="text-[13px] text-graphite mt-1">Monitor your inventory levels in real-time</p>
            </div>
            <a href="{{ route('vendor.stock.dashboard') }}" class="text-forest font-medium hover:text-forest-600">
                View Full Dashboard →
            </a>
        </div>
    </div>

    <div class="mt-8 border border-mist bg-white px-6 py-8" style="border-radius: 2px;">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-[20px] leading-tight">Approved categories</h2>
                <p class="mt-2 text-[15px] text-graphite">Choose from the categories available for your products.</p>
            </div>
            <span class="text-[13px] text-graphite/70">{{ $categories->count() }} categories</span>
        </div>

        @if ($categories->isEmpty())
            <p class="mt-6 text-[15px] text-graphite/70">No approved categories are available yet.</p>
        @else
            <div class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($categories as $category)
                    <div class="border border-mist px-4 py-4" style="border-radius: 2px;">
                        <p class="font-medium text-forest">{{ $category->name }}</p>
                        @if ($category->parent)
                            <p class="mt-1 text-[13px] text-graphite/70">{{ $category->parent->name }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
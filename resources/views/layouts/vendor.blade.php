<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Seller Centre — VandoraX')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper">

{{-- ── Top header bar ── --}}
<header class="bg-forest">
    <div class="flex items-center justify-between px-5 py-4 lg:px-8">

        <div class="flex items-baseline gap-3">
            <a href="{{ route('vendor.dashboard') }}" class="text-lg font-display font-bold text-white">
                Vandora<span class="text-marigold">X</span>
            </a>
            <span class="text-[13px] text-forest-100/60">Seller Centre</span>
        </div>

        {{-- Mobile sidebar toggle --}}
        <button type="button" id="vendor-sidebar-toggle"
                class="lg:hidden text-forest-100/80 hover:text-marigold transition-colors"
                aria-label="Toggle sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="hidden lg:flex items-center gap-5">
            @php $vendor = auth()->user()->vendor; @endphp
            @if($vendor)
                <span class="text-sm text-marigold font-medium">{{ $vendor->store_name }}</span>
                <span class="text-forest-100/40">·</span>
            @endif
            <span class="text-sm text-forest-100/80">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('vendor.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-forest-100/80 transition-colors hover:text-marigold">
                    Log out
                </button>
            </form>
        </div>
    </div>
</header>

<div class="lg:flex">

    {{-- ── Sidebar ── --}}
    <aside id="vendor-sidebar"
           class="hidden lg:block w-64 shrink-0 border-r border-mist bg-white min-h-[calc(100vh-60px)] overflow-y-auto">
        <nav class="px-4 py-6 space-y-1">

            {{-- Dashboard --}}
            <a href="{{ route('vendor.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                </svg>
                Dashboard
            </a>

            {{-- ── Products Section ── --}}
            <p class="sidebar-heading">Products</p>

            <a href="{{ route('vendor.products.index') }}"
               class="sidebar-link {{ request()->routeIs('vendor.products.index') || request()->routeIs('vendor.products.show') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                My Products
            </a>

            <a href="{{ route('vendor.products.create') }}"
               class="sidebar-link {{ request()->routeIs('vendor.products.create') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Add Product
            </a>

            <a href="{{ route('vendor.products.bulk-import') }}"
               class="sidebar-link {{ request()->routeIs('vendor.products.bulk-import*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                Bulk Import
            </a>

            {{-- ── Categories Section ── --}}
            <p class="sidebar-heading">Categories</p>

            <a href="{{ route('vendor.categories.all') }}"
               class="sidebar-link {{ request()->routeIs('vendor.categories.all') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a2 2 0 012-2z"/>
                </svg>
                All Categories
            </a>

            <a href="{{ route('vendor.categories.requests') }}"
               class="sidebar-link {{ request()->routeIs('vendor.categories.requests') || request()->routeIs('vendor.categories.request.show') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                My Requests
            </a>

            <a href="{{ route('vendor.categories.request.create') }}"
               class="sidebar-link {{ request()->routeIs('vendor.categories.request.create') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Request Category
            </a>

            {{-- ── Stock Management Section ── --}}
            <p class="sidebar-heading">Inventory</p>

            <a href="{{ route('vendor.stock.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('vendor.stock.dashboard') || request()->routeIs('vendor.stock.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Stock Dashboard
            </a>

        </nav>

        {{-- Mobile-only: user info + logout at bottom --}}
        <div class="lg:hidden border-t border-mist px-4 py-4 mt-4">
            @php $vendor = auth()->user()->vendor; @endphp
            @if($vendor)
                <p class="text-xs text-marigold font-medium mb-1">{{ $vendor->store_name }}</p>
            @endif
            <p class="text-sm font-medium text-forest mb-2">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('vendor.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-vermilion hover:text-vermilion/80 transition-colors font-medium">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main content area ── --}}
    <main class="flex-1 min-w-0 px-5 py-10 lg:px-10 lg:py-14">
        @include('partials.alerts')
        @yield('content')
    </main>
</div>

<script>
(() => {
    const toggle = document.getElementById('vendor-sidebar-toggle');
    const sidebar = document.getElementById('vendor-sidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('lg:block');
            // On mobile, make sidebar overlay
            if (!sidebar.classList.contains('hidden')) {
                sidebar.classList.add('fixed', 'inset-y-0', 'left-0', 'z-40', 'shadow-2xl', 'mt-[60px]');
            } else {
                sidebar.classList.remove('fixed', 'inset-y-0', 'left-0', 'z-40', 'shadow-2xl', 'mt-[60px]');
            }
        });
    }
})();
</script>

</body>
</html>

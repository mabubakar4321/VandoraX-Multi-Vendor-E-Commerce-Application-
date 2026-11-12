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
<body class="vendor-shell-page min-h-screen bg-paper">

{{-- ── Top header bar ── --}}
<header id="vendor-header" class="fixed inset-x-0 top-0 z-50 h-16 bg-forest shadow-[0_8px_24px_rgba(8,24,19,0.16)]">
    <div class="flex h-full items-center justify-between px-5 lg:px-8">

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

<div id="vendor-shell" class="pt-16 lg:flex">

    {{-- ── Sidebar ── --}}
    <aside id="vendor-sidebar"
            class="hidden lg:sticky lg:top-16 lg:block lg:h-[calc(100vh-4rem)] w-64 shrink-0 overflow-y-auto overscroll-contain border-r border-mist bg-white">
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
            <a href="{{ route('vendor.stock.dashboard') }}"
                class="sidebar-link {{ request()->routeIs('vendor.stock.dashboard') || request()->routeIs('vendor.stock.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Stock Dashboard
            </a>

            {{-- ── Review System Section ── --}}
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

            {{-- ── Orders Section ── --}}
            <a href="{{ route('vendor.categories.my') }}"
               class="sidebar-link {{ request()->routeIs('vendor.categories.my') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H4a2 2 0 012-2h12a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6a2 2 0 012-2h1m10 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0h.01"/>
                </svg>
                My Categories
            </a>

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
            <a href="{{ route('vendor.orders.index') }}"
               class="sidebar-link {{ request()->routeIs('vendor.orders.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4 0a2 2 0 104 0m-4 0a2 2 0 114 0m-5 7h6m-6 4h6"/>
                </svg>
                Orders
            </a>

            <a href="{{ route('vendor.returns.index') }}"
               class="sidebar-link {{ request()->routeIs('vendor.returns.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                Returns
            </a>

            <a href="{{ route('vendor.refund-appeals.create') }}"
               class="sidebar-link {{ request()->routeIs('vendor.refund-appeals.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0-5v2m0 14v2M5.64 5.64l1.42 1.42m9.9 9.9l1.42 1.42M3 12h2m14 0h2M5.64 18.36l1.42-1.42m9.9-9.9l1.42-1.42"/></svg>
                Appeal for Refund
            </a>

            <a href="{{ route('vendor.complaints.create') }}"
               class="sidebar-link {{ request()->routeIs('vendor.complaints.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5a8.38 8.38 0 01-9 8.3 8.5 8.5 0 01-3.9-.9L3 20l1.2-4.2A8.3 8.3 0 013 11.5a8.5 8.5 0 1118 0z"/></svg>
                Complaints
            </a>

            <a href="{{ route('vendor.payments.index') }}"
               class="sidebar-link {{ request()->routeIs('vendor.payments.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3zM12 8v8m-4-4h8"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8a4 4 0 014 4H8a4 4 0 014-4z"/>
                </svg>
                Payments
            </a>

            {{-- ── Categories Section ── --}}
            <p class="sidebar-heading">Review System</p>

            <a href="{{ route('vendor.reviews.index') }}"
                class="sidebar-link {{ request()->routeIs('vendor.reviews.index') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.576.66.102.785l-4.414 1.563a.563.563 0 00-.329.498l1.09 5.388a.56.56 0 01-.82.588l-4.726-2.888a.563.563 0 00-.58 0L6.717 19.6a.562.562 0 01-.82-.588l1.09-5.388a.562.562 0 00-.328-.497l-4.415-1.564c-.474-.125-.396-.744.102-.785l5.518-.44a.562.562 0 00.474-.346l2.126-5.11z"/>
                </svg>
                My Reviews
            </a>

            <a href="{{ route('vendor.reviews.deletion-requests.index') }}"
                class="sidebar-link {{ request()->routeIs('vendor.reviews.deletion-requests.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h4m5 5H6a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                </svg>
                Request Review Deletion
            </a>

            <a href="{{ route('vendor.unblock.index') }}"
                class="sidebar-link {{ request()->routeIs('vendor.unblock.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.04 12.04 0 003 12c0 5.591 3.824 10.29 9 11.622C17.176 22.29 21 17.591 21 12a12.04 12.04 0 00-.382-3.04l.53.53z"/>
                </svg>
                Unblock Requests
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

    <div id="vendor-sidebar-backdrop"
         class="fixed inset-0 z-30 hidden bg-forest/45 backdrop-blur-[2px] lg:hidden"
         aria-hidden="true"></div>

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
    const backdrop = document.getElementById('vendor-sidebar-backdrop');

    if (!toggle || !sidebar || !backdrop) return;

    const closeSidebar = () => {
        sidebar.classList.add('hidden');
        sidebar.classList.remove('fixed', 'top-16', 'bottom-0', 'left-0', 'z-40', 'shadow-2xl');
        backdrop.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');
    };

    const openSidebar = () => {
        sidebar.classList.remove('hidden');
        sidebar.classList.add('fixed', 'top-16', 'bottom-0', 'left-0', 'z-40', 'shadow-2xl');
        backdrop.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');
    };

    toggle.setAttribute('aria-expanded', 'false');
    toggle.addEventListener('click', () => sidebar.classList.contains('hidden') ? openSidebar() : closeSidebar());
    backdrop.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', closeSidebar));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') closeSidebar();
    });
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) closeSidebar();
    });
})();
</script>

<style>
    .vendor-shell-page a.btn-primary,
    .vendor-shell-page button.btn-primary,
    .vendor-shell-page .btn.btn-primary,
    .vendor-shell-page a[class~="bg-blue-600"],
    .vendor-shell-page button[class~="bg-blue-600"],
    .vendor-shell-page a[class~="bg-indigo-600"],
    .vendor-shell-page button[class~="bg-indigo-600"],
    .vendor-shell-page a[class~="bg-green-600"],
    .vendor-shell-page button[class~="bg-green-600"],
    .vendor-shell-page a[class~="bg-emerald-600"],
    .vendor-shell-page button[class~="bg-emerald-600"] {
        background-color: #F2B705 !important;
        border-color: #F2B705 !important;
        color: #0E2A22 !important;
    }

    .vendor-shell-page a.btn-primary:hover,
    .vendor-shell-page button.btn-primary:hover,
    .vendor-shell-page .btn.btn-primary:hover,
    .vendor-shell-page a[class~="bg-blue-600"]:hover,
    .vendor-shell-page button[class~="bg-blue-600"]:hover,
    .vendor-shell-page a[class~="bg-indigo-600"]:hover,
    .vendor-shell-page button[class~="bg-indigo-600"]:hover,
    .vendor-shell-page a[class~="bg-green-600"]:hover,
    .vendor-shell-page button[class~="bg-green-600"]:hover,
    .vendor-shell-page a[class~="bg-emerald-600"]:hover,
    .vendor-shell-page button[class~="bg-emerald-600"]:hover {
        background-color: #D9A404 !important;
        border-color: #D9A404 !important;
        color: #0E2A22 !important;
    }

    .vendor-shell-page a[class~="text-indigo-600"],
    .vendor-shell-page a[class~="text-blue-600"],
    .vendor-shell-page a[class~="text-emerald-600"],
    .vendor-shell-page a[class~="text-green-600"] {
        color: #1E6F5C !important;
    }

    .vendor-shell-page a[class~="text-indigo-600"]:hover,
    .vendor-shell-page a[class~="text-blue-600"]:hover,
    .vendor-shell-page a[class~="text-emerald-600"]:hover,
    .vendor-shell-page a[class~="text-green-600"]:hover {
        color: #0E2A22 !important;
    }

    .vendor-shell-page a[class~="bg-gray-300"],
    .vendor-shell-page button[class~="bg-gray-300"],
    .vendor-shell-page a[class~="bg-gray-200"],
    .vendor-shell-page button[class~="bg-gray-200"] {
        background-color: #FFFFFF !important;
        border: 1px solid #C8D6CE !important;
        color: #0E2A22 !important;
    }

    .vendor-shell-page a[class~="bg-red-600"],
    .vendor-shell-page button[class~="bg-red-600"],
    .vendor-shell-page a[class~="bg-red-500"],
    .vendor-shell-page button[class~="bg-red-500"] {
        background-color: #B23A2E !important;
        border-color: #B23A2E !important;
        color: #FFFFFF !important;
    }

    .vendor-shell-page a[class~="bg-red-600"]:hover,
    .vendor-shell-page button[class~="bg-red-600"]:hover,
    .vendor-shell-page a[class~="bg-red-500"]:hover,
    .vendor-shell-page button[class~="bg-red-500"]:hover {
        background-color: #922F26 !important;
        border-color: #922F26 !important;
    }
</style>

</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin — VandoraX')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
</head>
<body class="min-h-screen overflow-x-hidden bg-paper">

{{-- ── Top header bar ── --}}
<header class="sticky top-0 z-30 bg-forest shadow-sm">
    <div class="flex min-h-16 items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">

        <div class="flex min-w-0 items-baseline gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-display font-bold text-white">
                Vandora<span class="text-marigold">X</span>
            </a>
            <span class="hidden text-[13px] text-forest-100/60 sm:inline">Admin workspace</span>
        </div>

        {{-- Mobile sidebar toggle --}}
        <div class="flex items-center gap-3">
        <span class="hidden max-w-40 truncate text-sm text-forest-100/80 sm:inline lg:hidden">{{ auth()->user()->name }}</span>
        <button type="button" id="sidebar-toggle" aria-controls="admin-sidebar" aria-expanded="false"
                class="inline-flex h-10 w-10 items-center justify-center border border-white/15 text-forest-100/80 transition-colors hover:border-marigold hover:text-marigold lg:hidden"
                aria-label="Open navigation menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="hidden items-center gap-5 lg:flex">
            <span class="max-w-56 truncate text-sm text-forest-100/80">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-forest-100/80 transition-colors hover:text-marigold">
                    Log out
                </button>
            </form>
        </div>
        </div>
    </div>
</header>

<div class="lg:flex">
    <button id="sidebar-backdrop" type="button" aria-label="Close navigation menu" class="fixed inset-0 z-40 hidden bg-forest/50 backdrop-blur-[1px] lg:hidden"></button>

    {{-- ── Sidebar ── --}}
    <aside id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-50 hidden w-[min(19rem,calc(100vw-2.5rem))] flex-col border-r border-mist bg-white shadow-2xl transition-transform duration-200 lg:sticky lg:top-16 lg:z-20 lg:flex lg:h-[calc(100vh-4rem)] lg:w-64 lg:translate-x-0 lg:shadow-none">
        <div class="flex items-center justify-between border-b border-mist px-5 py-4 lg:hidden">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-graphite/55">Navigation</p>
                <p class="mt-1 truncate text-sm font-semibold text-forest">{{ auth()->user()->name }}</p>
            </div>
            <button id="sidebar-close" type="button" class="inline-flex h-9 w-9 items-center justify-center text-graphite hover:bg-paper hover:text-forest" aria-label="Close navigation menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto px-4 py-5">

            {{-- Dashboard --}}
            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/>
                </svg>
                Dashboard
            </a>

            {{-- ── Vendors Section ── --}}
            <p class="sidebar-heading">Vendors</p>

                <a href="{{ route('admin.vendors.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Vendors
            </a>

            {{-- ── Orders Section ── --}}
            <p class="sidebar-heading">Sales</p>

            <a href="{{ route('admin.orders.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2m-4 0a2 2 0 104 0m-4 0a2 2 0 114 0m-5 7h6m-6 4h6"/>
                </svg>
                Orders
            </a>

            <a href="{{ route('admin.returns.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.returns.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/></svg>
                Returns
            </a>

            <a href="{{ route('admin.refund-requests.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.refund-requests.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zm0-5v2m0 14v2M5.64 5.64l1.42 1.42m9.9 9.9l1.42 1.42M3 12h2m14 0h2M5.64 18.36l1.42-1.42m9.9-9.9l1.42-1.42"/></svg>
                Request for Refund
            </a>

            <a href="{{ route('admin.vendor-complaints.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.vendor-complaints.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5a8.38 8.38 0 01-9 8.3 8.5 8.5 0 01-3.9-.9L3 20l1.2-4.2A8.3 8.3 0 013 11.5a8.5 8.5 0 1118 0z"/></svg>
                Vendor Complaints
            </a>

            <a href="{{ route('admin.customer-complaints.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.customer-complaints.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5a8.38 8.38 0 01-9 8.3 8.5 8.5 0 01-3.9-.9L3 20l1.2-4.2A8.3 8.3 0 013 11.5a8.5 8.5 0 1118 0z"/></svg>
                Customer Complaints
            </a>

            <a href="{{ route('admin.payments.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3zM12 8v8m-4-4h8"/>
                </svg>
                Payments
            </a>

            {{-- Finance: Total Admin Collected Amount --}}
            <a href="{{ route('admin.admin-collections.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.admin-collections.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4-1.79-4-4-4zM12 4v2M12 14v2M4 12h2M16 12h2M6.34 6.34l1.42 1.42M17.66 17.66l1.42 1.42M6.34 17.66l1.42-1.42M17.66 6.34l1.42-1.42"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v18H3z" opacity="0"/>
                </svg>
                Total Admin Collected Amount
            </a>

            <a href="{{ route('admin.customers.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5 5 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Customers
            </a>

            {{-- ── Categories Section ── --}}
            <p class="sidebar-heading">Categories</p>

            <a href="{{ route('admin.categories.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a2 2 0 012-2z"/>
                </svg>
                Manage Categories
            </a>

            <a href="{{ route('admin.categories.create') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories.create') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4v16m8-8H4"/>
                </svg>
                Add Category
            </a>

            <a href="{{ route('admin.categories.tree') }}"
               class="sidebar-link {{ request()->routeIs('admin.categories.tree') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
                Category Tree
            </a>

            <a href="{{ route('admin.category-requests.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.category-requests.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Category Requests
            </a>

            {{-- ── Products Section ── --}}
            <p class="sidebar-heading">Products</p>

            <a href="{{ route('admin.products.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                All Products
            </a>

            {{-- ── Review System Section ── --}}
            <p class="sidebar-heading">Review System</p>

            <a href="{{ route('admin.reviews-dashboard') }}"
                class="sidebar-link {{ request()->routeIs('admin.reviews-dashboard') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14z"/>
                </svg>
                Review Dashboard
            </a>

            <a href="{{ route('admin.reviews.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                Manage Reviews
            </a>

            <a href="{{ route('admin.review-deletion-requests.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.review-deletion-requests.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h4m5 5H6a2 2 0 01-2-2V5a2 2 0 012-2h7l5 5v11a2 2 0 01-2 2z"/>
                </svg>
                Review Delete Requests
            </a>

            <a href="{{ route('admin.unblock-requests.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.unblock-requests.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Unblock Requests
            </a>

        </nav>

        {{-- Mobile-only: user info + logout at bottom --}}
        <div class="border-t border-mist px-4 py-4 lg:hidden">
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full border border-mist px-4 py-2.5 text-left text-sm font-semibold text-vermilion transition-colors hover:bg-vermilion-100">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    {{-- ── Main content area ── --}}
    <main class="admin-content min-h-[calc(100vh-4rem)] min-w-0 flex-1 px-4 py-6 sm:px-6 sm:py-8 lg:px-10 lg:py-10">
        @include('partials.alerts')
        @yield('content')
    </main>
</div>

<script>
(() => {
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    const close = document.getElementById('sidebar-close');

    if (!toggle || !sidebar || !backdrop) return;

    const openMenu = () => {
        sidebar.classList.remove('hidden');
        backdrop.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        toggle.setAttribute('aria-label', 'Close navigation menu');
        document.body.classList.add('overflow-hidden');
        close?.focus();
    };

    const closeMenu = () => {
        sidebar.classList.add('hidden');
        backdrop.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Open navigation menu');
        document.body.classList.remove('overflow-hidden');
    };

    toggle.addEventListener('click', () => toggle.getAttribute('aria-expanded') === 'true' ? closeMenu() : openMenu());
    close?.addEventListener('click', closeMenu);
    backdrop.addEventListener('click', closeMenu);
    sidebar.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
        if (window.matchMedia('(max-width: 1023px)').matches) closeMenu();
    }));
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            closeMenu();
            toggle.focus();
        }
    });
    window.matchMedia('(min-width: 1024px)').addEventListener('change', event => {
        if (event.matches) {
            backdrop.classList.add('hidden');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        } else {
            closeMenu();
        }
    });
})();
</script>

@stack('scripts')

</body>
</html>

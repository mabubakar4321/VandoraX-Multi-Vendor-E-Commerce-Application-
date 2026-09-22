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
</head>
<body class="min-h-screen bg-paper">

{{-- ── Top header bar ── --}}
<header class="bg-forest">
    <div class="flex items-center justify-between px-5 py-4 lg:px-8">

        <div class="flex items-baseline gap-3">
            <a href="{{ route('admin.dashboard') }}" class="text-lg font-display font-bold text-white">
                Vandora<span class="text-marigold">X</span>
            </a>
            <span class="text-[13px] text-forest-100/60">Admin</span>
        </div>

        {{-- Mobile sidebar toggle --}}
        <button type="button" id="sidebar-toggle"
                class="lg:hidden text-forest-100/80 hover:text-marigold transition-colors"
                aria-label="Toggle sidebar">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>

        <div class="hidden lg:flex items-center gap-5">
            <span class="text-sm text-forest-100/80">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('admin.logout') }}">
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
    <aside id="admin-sidebar"
           class="hidden lg:block w-64 shrink-0 border-r border-mist bg-white min-h-[calc(100vh-60px)] overflow-y-auto">
        <nav class="px-4 py-6 space-y-1">

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

            <a href="{{ route('admin.vendors.pending') }}"
               class="sidebar-link {{ request()->routeIs('admin.vendors.*') ? 'active' : '' }}">
                <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pending Vendors
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

        </nav>

        {{-- Mobile-only: user info + logout at bottom --}}
        <div class="lg:hidden border-t border-mist px-4 py-4 mt-4">
            <p class="text-sm font-medium text-forest mb-2">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('admin.logout') }}">
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
    const toggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('admin-sidebar');

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

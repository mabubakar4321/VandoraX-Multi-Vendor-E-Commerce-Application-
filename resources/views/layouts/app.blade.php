<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VandoraX')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
    @stack('styles')
</head>
<body class="min-h-screen bg-paper text-graphite">
    <header class="border-b border-forest-700 bg-forest shadow-sm">
        <div class="mx-auto flex min-h-16 max-w-6xl items-center justify-between gap-4 px-5 py-3 lg:px-8">
            <a href="{{ route('home') }}" class="shrink-0 text-lg font-display font-bold text-white">
                Vandora<span class="text-marigold">X</span>
            </a>

            @hasSection('area')
                <span class="hidden text-[13px] text-forest-100/70 sm:inline">@yield('area')</span>
            @endif

            <div class="ml-auto flex items-center gap-4">
                @if(auth()->user()?->hasRole('customer'))
                    <a href="{{ route('order.index') }}" class="hidden text-sm font-medium text-forest-100/80 transition-colors hover:text-marigold sm:inline">My orders</a>
                    <a href="{{ route('returns.index') }}" class="hidden text-sm font-medium text-forest-100/80 transition-colors hover:text-marigold sm:inline">My returns</a>
                    <a href="{{ route('customer.complaints.create') }}" class="hidden text-sm font-medium text-forest-100/80 transition-colors hover:text-marigold sm:inline">Complaints</a>
                @endif
                @if (request()->is('admin/*'))
                    <a href="{{ route('admin.dashboard') }}" class="hidden text-sm text-forest-100/80 transition-colors hover:text-marigold sm:inline">
                        Back to dashboard
                    </a>
                @endif
                <span class="hidden text-sm text-forest-100/80 md:inline">{{ auth()->user()->name }}</span>
                @php
                    $logoutRoute = 'customer.logout';
                    if (auth()->user()?->hasRole('admin')) {
                        $logoutRoute = 'admin.logout';
                    } elseif (auth()->user()?->hasRole('vendor')) {
                        $logoutRoute = 'vendor.logout';
                    }
                @endphp
                <form method="POST" action="{{ route($logoutRoute) }}">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-forest-100/80 transition-colors hover:text-marigold">
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="mx-auto min-h-[calc(100vh-4rem)] max-w-6xl px-5 py-8 lg:px-8 lg:py-12">
        @include('partials.alerts')
        @yield('content')
    </main>
</body>
</html>

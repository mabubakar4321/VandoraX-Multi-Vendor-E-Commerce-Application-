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
</head>
<body class="min-h-screen bg-paper">

<header class="bg-forest">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-5 py-4 lg:px-8">

        <div class="flex items-baseline gap-3">
            <span class="text-lg font-display font-bold text-white">
                Vandora<span class="text-marigold">X</span>
            </span>
            @hasSection('area')
                <span class="text-[13px] text-forest-100/60">@yield('area')</span>
            @endif
        </div>

        <div class="flex items-center gap-5">
            @if (request()->is('admin/*'))
                <a href="{{ route('admin.dashboard') }}" class="text-sm text-forest-100/80 transition-colors hover:text-marigold">
                    Back to dashboard
                </a>
            @endif
            <span class="hidden text-sm text-forest-100/80 sm:inline">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ $logoutRoute }}">
                @csrf
                <button type="submit" class="text-sm text-forest-100/80 transition-colors hover:text-marigold">
                    Log out
                </button>
            </form>
        </div>
    </div>
</header>

<main class="mx-auto max-w-6xl px-5 py-10 lg:px-8 lg:py-14">
    @include('partials.alerts')
    @yield('content')
</main>

</body>
</html>
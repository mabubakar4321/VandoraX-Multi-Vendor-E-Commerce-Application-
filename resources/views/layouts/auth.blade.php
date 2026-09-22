<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VandoraX')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-paper">

<div class="flex min-h-screen flex-col lg:flex-row">

    {{-- Left: the forest panel. Collapses to a slim bar on mobile. --}}
    <aside class="flex flex-col justify-between bg-forest px-6 py-6 text-forest-100 lg:w-2/5 lg:max-w-lg lg:px-12 lg:py-14">

        <a href="{{ route('home') }}" class="inline-flex items-baseline gap-px text-xl font-display font-bold text-white">
            Vandora<span class="text-marigold">X</span>
        </a>

        {{-- Only the desktop panel carries context; on mobile it would push the form off-screen --}}
        <div class="hidden lg:block">
            @yield('panel')
        </div>

        <p class="hidden text-[13px] text-forest-100/60 lg:block">
            &copy; {{ date('Y') }} VandoraX
        </p>
    </aside>

    {{-- Right: the document panel --}}
    <main class="flex flex-1 items-start justify-center px-5 py-10 lg:items-center lg:px-12 lg:py-14">
        <div class="w-full @yield('width', 'max-w-md')">
            @include('partials.alerts')
            @yield('content')
        </div>
    </main>

</div>

</body>
</html>
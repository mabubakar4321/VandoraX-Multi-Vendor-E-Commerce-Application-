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
    <x-storefront-header />

    <main class="mx-auto min-h-[calc(100vh-20rem)] max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        @include('partials.alerts')
        @yield('content')
    </main>

    <x-storefront-footer />
    @stack('scripts')
</body>
</html>

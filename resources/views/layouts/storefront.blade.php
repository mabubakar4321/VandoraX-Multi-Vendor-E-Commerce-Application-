<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'VandoraX')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        :root {
            --primary-dark: #0a0e27;
            --primary: #16213e;
            --primary-light: #1a2d5c;
            --accent: #ffa500;
            --accent-light: #ffb84d;
            --accent-dark: #e68a00;
            --bg-dark: #0f1729;
            --bg-darker: #0a0e27;
            --border-light: #1a2d5c;
            --border-medium: #2a3d6b;
            --text-primary: #ffffff;
            --text-secondary: #d4d4d8;
            --text-muted: #888888;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --overlay: rgba(0, 0, 0, 0.3);
        }

        @media (prefers-color-scheme: light) {
            :root:not([data-theme="dark"]) {
                --primary-dark: #ffffff;
                --primary: #f5f7fa;
                --primary-light: #f0f3f8;
                --bg-dark: #f8f9fa;
                --bg-darker: #f5f7fa;
                --border-light: #e8eef5;
                --border-medium: #dce4f0;
                --text-primary: #1a1a2e;
                --text-secondary: #4a4a6a;
                --text-muted: #888888;
            }
        }

        :root[data-theme="light"] {
            --primary-dark: #ffffff;
            --primary: #f5f7fa;
            --primary-light: #f0f3f8;
            --bg-dark: #f8f9fa;
            --bg-darker: #f5f7fa;
            --border-light: #e8eef5;
            --border-medium: #dce4f0;
            --text-primary: #1a1a2e;
            --text-secondary: #4a4a6a;
            --text-muted: #888888;
            color-scheme: light;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--bg-darker);
            color: var(--text-primary);
            line-height: 1.6;
            letter-spacing: 0.3px;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen bg-bg-darker text-text-primary">

<x-storefront-header />

<main class="mx-auto max-w-6xl px-5 py-10 lg:px-8 lg:py-14">
    @include('partials.alerts')
    @yield('content')
</main>

<x-storefront-footer />
@stack('scripts')
</body>
</html>

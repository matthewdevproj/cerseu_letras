<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    use Illuminate\Support\Facades\Cache;
    $siteSettings = Cache::remember('site_settings', 3600, function () {
        return \App\Models\SiteSetting::first();
    });
@endphp

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', $siteSettings?->site_name ?? config('app.name', 'Laravel'))</title>

    {{-- Favicon dinámico --}}
    @if($siteSettings?->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon_path) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . $siteSettings->favicon_path) }}">
    @endif

    <!-- Fonts: Inter (optimizado - solo pesos necesarios y subset latin) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Merriweather:wght@700&display=swap&subset=latin"
        rel="stylesheet" media="print" onload="this.media='all'" />
    <noscript>
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Merriweather:wght@700&display=swap&subset=latin">
    </noscript>

    <!-- Font Awesome (lazy load no crítico) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css"
        media="print" onload="this.media='all'">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css"
        media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/brands.min.css">
    </noscript>

    <!-- Preload logo crítico -->
    <link rel="preload" as="image" href="https://letras.unmsm.edu.pe/wp-content/uploads/2020/11/LOGO_LETRAS_AI.png"
        fetchpriority="high">

    <!-- Alpine.js with Collapse Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js x-cloak style -->
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 overflow-x-hidden">
    <header class="fixed top-0 w-full z-50 flex flex-col">
        @include('layouts.partials.topbar')
        @include('layouts.partials.navbar')
    </header>

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <!-- WhatsApp Float Button -->
    <a href="{{ config('contacts.whatsapp', 'https://wa.me/51982085037') }}" target="_blank"
        class="fixed bottom-6 left-6 z-50 flex items-center p-4 bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 group">
        <i class="fab fa-whatsapp text-3xl"></i>
        <span
            class="max-w-0 overflow-hidden opacity-0 group-hover:max-w-xs group-hover:opacity-100 group-hover:ml-2 transition-all duration-500 ease-in-out whitespace-nowrap text-sm font-bold">
            ¡Contáctanos!
        </span>
    </a>

    @stack('scripts')
</body>

</html>
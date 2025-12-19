<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
@php
    $siteSettings = \App\Models\SiteSetting::get();
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

    <!-- Fonts: Inter (moderna) + Merriweather (serif institucional) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Merriweather:wght@400;700&display=swap"
        rel="stylesheet" />

    <!-- Font Awesome (íconos del topbar) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        referrerpolicy="no-referrer" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50">
    <!-- HEADER FIJO COMO EN EL EJEMPLO -->
    <header class="fixed top-0 w-full z-50 flex flex-col">
        <!-- Topbar -->
        @include('layouts.partials.topbar')

        <!-- Navbar -->
        @include('layouts.partials.navbar')
    </header>

    <!-- Contenido principal (sin padding-top para que el hero quede debajo del header fijo) -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('layouts.partials.footer')

    @stack('scripts')
</body>

</html>
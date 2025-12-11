<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel'))</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

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
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="no-js">
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
    {{-- Transiciones de vista nativas del navegador (sin JS): fade discreto
         entre páginas en navegadores compatibles; en el resto, no hace nada
         y la navegación se comporta exactamente igual que siempre. --}}
    <meta name="view-transition" content="same-origin">
    <script>document.documentElement.classList.replace('no-js', 'js');</script>

    @php
        $seoTitle = trim($__env->yieldContent('title', $siteSettings?->site_name ?? config('app.name', 'Posgrado Letras UNMSM')));
        $seoDescription = trim($__env->yieldContent('meta_description', $siteSettings?->site_description ?? 'Posgrado de la Facultad de Letras y Ciencias Humanas UNMSM: maestrías, doctorados y diplomados.'));
        $seoImage = trim($__env->yieldContent('og_image')) ?: ($siteSettings?->logo_path
            ? asset('storage/' . $siteSettings->logo_path)
            : asset('images/logo-letras.png'));
        $seoSiteName = $siteSettings?->site_name ?? 'Posgrado Letras UNMSM';
        $seoStructuredData = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $seoSiteName,
            'description' => $seoDescription,
            'url' => url('/'),
            'logo' => $seoImage,
            'sameAs' => [
                'https://www.facebook.com/posgradoletrasUNMSM/',
                'https://www.instagram.com/posgradoletrasunmsm/',
                'https://www.linkedin.com/in/posgrado-de-la-facultad-de-letras-unmsm-1a95862ab/',
                'https://x.com/PGLetras_UNMSM',
                'https://www.tiktok.com/@posgradoletrasunmsm',
            ],
            'parentOrganization' => [
                '@type' => 'CollegeOrUniversity',
                'name' => 'Universidad Nacional Mayor de San Marcos',
                'url' => 'https://www.unmsm.edu.pe',
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $seoSiteName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ $seoImage }}">
    <meta property="og:locale" content="es_PE">

    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $seoImage }}">
    @hasSection('twitter_site')
        <meta name="twitter:site" content="@yield('twitter_site')">
    @endif

    {{-- Datos estructurados (Google) --}}
    <script type="application/ld+json">
        {!! $seoStructuredData !!}
    </script>

    {{-- Favicon dinámico --}}
    @if($siteSettings?->favicon_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $siteSettings->favicon_path) }}">
        <link rel="shortcut icon" href="{{ asset('storage/' . $siteSettings->favicon_path) }}">
    @endif

    {{-- Tipografías (Inter + Merriweather) auto-alojadas vía @fontsource,
         empaquetadas en app.css por Vite. Ya no se carga Google Fonts. --}}

    {{-- Iconos: Font Awesome migrado a SVG inline (owenvoke/blade-fontawesome).
         Sin CSS/webfont ni requests a CDN; sin flash de iconos. --}}

    {{-- Preload del logo LCP (auto-alojado; sin dependencia externa).
         Coincide con el src que usa el navbar para no precargar en balde. --}}
    <link rel="preload" as="image" fetchpriority="high"
        href="{{ $siteSettings?->logo_path ? asset('storage/' . $siteSettings->logo_path) : asset('images/logo-letras.png') }}">

    <!-- Scripts (Alpine.js + plugin Collapse ya empaquetados en app.js; [x-cloak] ya está en app.css) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="font-sans antialiased bg-gray-50 overflow-x-hidden">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-white focus:text-unmsm-guinda focus:font-bold focus:px-4 focus:py-2 focus:rounded focus:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-guinda">
        Saltar al contenido principal
    </a>

    <header class="fixed top-0 w-full z-50 flex flex-col">
        @include('layouts.partials.topbar')
        @include('layouts.partials.navbar')
    </header>

    <main id="main-content">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    <!-- WhatsApp Float Button -->
    <a href="{{ config('contacts.whatsapp', 'https://wa.me/51982085037') }}" target="_blank" rel="noopener noreferrer"
        class="fixed bottom-6 left-6 z-50 flex items-center p-4 bg-green-500 text-white rounded-full shadow-lg hover:bg-green-600 transition-all duration-300 group">
        <x-fab-whatsapp class="text-3xl" />
        <span
            class="max-w-0 overflow-hidden opacity-0 group-hover:max-w-xs group-hover:opacity-100 group-hover:ml-2 transition-all duration-500 ease-in-out whitespace-nowrap text-sm font-bold">
            ¡Contáctanos!
        </span>
    </a>

    @include('layouts.partials.toast-container')

    @stack('scripts')
</body>

</html>
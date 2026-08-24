@php
    // Singleton cacheado compartido con el resto del sitio (misma clave
    // 'site_settings'): evita una segunda query/entrada de cache redundante.
    $siteSettings = \App\Models\SiteSetting::get();
    $footerEmail = $siteSettings?->email ?? \App\Models\SiteSetting::contacto('general');

    // Redes definidas: se recorren en un solo bucle para no repetir markup.
    $socials = array_filter([
        'facebook'  => ['url' => $siteSettings?->facebook,  'label' => 'Facebook',    'path' => 'M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z'],
        'instagram' => ['url' => $siteSettings?->instagram, 'label' => 'Instagram',   'path' => 'M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z'],
        'twitter'   => ['url' => $siteSettings?->twitter,   'label' => 'X (Twitter)',  'path' => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z'],
        'linkedin'  => ['url' => $siteSettings?->linkedin,  'label' => 'LinkedIn',     'path' => 'M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z'],
        'youtube'   => ['url' => $siteSettings?->youtube,   'label' => 'YouTube',      'path' => 'M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z'],
        'tiktok'    => ['url' => $siteSettings?->tiktok,    'label' => 'TikTok',       'path' => 'M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z'],
    ], fn ($s) => !empty($s['url']));
@endphp

{{-- Degradado de negro a rojo, enlazando con la sección oscura que lo precede
     (`to-[#0C1217]`) y descendiendo hasta el guinda de marca.

     Los tres tonos comparten el mismo matiz rojo (~358°) y sólo varían en
     luminosidad: así no aparecen los reflejos metálicos que salían al pasar
     por el guinda claro (#1C5287), más saturado y de matiz distinto. --}}
<footer class="relative bg-gradient-to-b from-[#0C1217] via-[#0A1F33] to-unmsm-azul text-white">
    {{-- Acento dorado degradado en el borde superior (más fino y elegante que el border-t-4 plano) --}}
    <div class="h-1 w-full bg-gradient-to-r from-transparent via-unmsm-dorado to-transparent" aria-hidden="true"></div>

    {{-- Textura radial muy sutil generada con CSS puro (sin imagen, sin request) --}}
    <div class="pointer-events-none absolute inset-0 opacity-[0.06]"
        style="background-image: radial-gradient(circle at 15% 20%, #fff 0, transparent 45%), radial-gradient(circle at 85% 0%, #B6A350 0, transparent 40%);"
        aria-hidden="true"></div>

    <div class="container relative mx-auto px-4 py-14">
        <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
            {{-- Marca --}}
            <div>
                {{-- El pie va sobre fondo guinda, así que el logo se fuerza a
                     blanco con el mismo filtro que usa el navbar sobre el hero.
                     Antes se servía el archivo tal cual: si había un logo subido
                     desde el panel (caso de producción), salía oscuro sobre
                     oscuro y era casi ilegible. El filtro funciona con cualquier
                     logo que se cargue, sin exigir una variante blanca aparte. --}}
                <img src="{{ $siteSettings?->logo_path ? asset('storage/' . $siteSettings->logo_path) : asset('images/logo-cerseu.webp') }}"
                    alt="{{ $siteSettings?->site_name ?? 'Logo CERSEU Letras' }}"
                    class="mb-4 h-16 w-auto brightness-0 invert" loading="lazy" decoding="async" width="1234" height="310">
                <p class="mb-5 max-w-xs text-xs leading-relaxed text-white/70">
                    {{ $siteSettings?->site_description ?? 'El CERSEU forma profesionales humanistas especializados en investigación, con alta rigurosidad, ética y calidad académica.' }}
                </p>
                @if(count($socials))
                    <div class="flex flex-wrap items-center gap-2.5">
                        @foreach($socials as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer"
                                class="footer-social" title="{{ $social['label'] }}"
                                aria-label="{{ $social['label'] }}">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="{{ $social['path'] }}" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Contacto --}}
            <div>
                <h4 class="footer-heading">Contacto</h4>
                <ul class="space-y-3.5">
                    @if($siteSettings?->direccion)
                        <li class="flex items-start gap-2.5 text-xs text-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-unmsm-dorado"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $siteSettings->direccion }}</span>
                        </li>
                    @endif
                    <li>
                        <a href="mailto:{{ $footerEmail }}"
                            class="group flex items-center gap-2.5 text-xs text-white/70 transition-colors hover:text-unmsm-dorado focus-visible:text-unmsm-dorado focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-unmsm-dorado rounded-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 text-unmsm-dorado"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="break-all">{{ $footerEmail }}</span>
                        </a>
                    </li>
                    @if($siteSettings?->telefono)
                        <li>
                            <a href="https://wa.me/51{{ preg_replace('/\D/', '', $siteSettings->telefono) }}"
                                target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-2.5 text-xs text-white/70 transition-colors hover:text-unmsm-dorado focus-visible:text-unmsm-dorado focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-unmsm-dorado rounded-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 flex-shrink-0 text-unmsm-dorado"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $siteSettings->telefono }}@if($siteSettings->anexo) · Anexo {{ $siteSettings->anexo }}@endif</span>
                            </a>
                        </li>
                    @endif
                    @if($siteSettings?->horario_atencion)
                        <li class="flex items-start gap-2.5 text-xs text-white/70">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-4 w-4 flex-shrink-0 text-unmsm-dorado"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>{{ $siteSettings->horario_atencion }}</span>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Enlaces Rápidos --}}
            <div>
                <h4 class="footer-heading">Enlaces Rápidos</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('home') }}" class="footer-link">Inicio</a></li>
                    <li><a href="{{ route('cursos.index') }}" class="footer-link">Cursos</a></li>
                    <li><a href="{{ route('admision') }}" class="footer-link">Admisión</a></li>
                    <li><a href="{{ route('tramites') }}" class="footer-link">Trámites</a></li>
                    <li><a href="{{ route('nosotros') }}" class="footer-link">Nosotros</a></li>
                </ul>
            </div>

            {{-- Institucional --}}
            <div>
                <h4 class="footer-heading">Institucional</h4>
                <ul class="space-y-2.5">
                    <li><a href="https://unmsm.edu.pe" target="_blank" rel="noopener noreferrer" class="footer-link">UNMSM</a></li>
                    <li><a href="{{ $siteSettings?->web_facultad ?: 'https://letras.unmsm.edu.pe' }}" target="_blank" rel="noopener noreferrer" class="footer-link">Facultad de Letras</a></li>
                    <li><a href="https://sanmarket.unmsm.edu.pe" target="_blank" rel="noopener noreferrer" class="footer-link">SanMarket</a></li>
                    <li><a href="https://sum.unmsm.edu.pe" target="_blank" rel="noopener noreferrer" class="footer-link">SUM</a></li>
                    @if($siteSettings?->directorio_facultad)
                        <li><a href="{{ $siteSettings->directorio_facultad }}" target="_blank" rel="noopener noreferrer" class="footer-link">Directorio Facultad</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center gap-1 border-t border-white/10 pt-6 text-center text-xs text-white/50">
            <p>&copy; {{ date('Y') }}
                {{ $siteSettings?->site_name ?? 'CERSEU - Facultad de Letras y Ciencias Humanas' }} —
                Universidad Nacional Mayor de San Marcos
            </p>
            <p class="font-serif italic text-unmsm-dorado/80">{{ $siteSettings?->footer_text ?? 'Decana de América' }}</p>
        </div>
    </div>
</footer>

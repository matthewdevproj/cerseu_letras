@extends('layouts.public')

@section('title', 'Inicio - CERSEU Letras UNMSM')

@push('styles')
    {{-- El CSS y el JS de Swiper ahora se empaquetan de forma modular vía Vite
         (resources/js/carousels.js), cargados solo en páginas con carrusel. --}}
    <style>
        /* Utilidad para titulos */
        .section-title::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 24px;
            background-color: #C9AA36;
            margin-right: 12px;
            border-radius: 2px;
            vertical-align: middle;
        }

        /* Nombre propio (heroFadeIn) para NO pisar el @keyframes fadeIn global
           de app.css, del que dependen las .program-card y otras vistas. */
        @keyframes heroFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-hero-in {
            animation: heroFadeIn 0.8s ease-out forwards;
        }

        /* Hero Swiper Styles.

           El posicionamiento se declara aquí y no solo con la clase `absolute`
           de Tailwind porque el CSS propio de Swiper trae `.swiper{position:
           relative}` y, al cargarse después en la cascada, gana: el carrusel
           dejaba de ser un overlay, entraba como ítem del flex y colapsaba a
           altura 0 (hero en blanco).

           El alto sale de `inset: 0`, no de `height: 100%`, porque el hero usa
           `min-height` y un porcentaje no resuelve contra ella. Con el alto ya
           resuelto aquí, el 100% de wrapper y slides sí funciona.

           El selector va compuesto (`.hero-swiper.swiper`) a propósito: el CSS
           de Swiper se carga como chunk diferido y se inyecta en el <head>
           DESPUÉS de este bloque, así que con la misma especificidad ganaría
           él. Con dos clases (0,2,0) este bloque manda. */
        .hero-swiper.swiper {
            position: absolute;
            inset: 0;
            width: 100%;
        }

        .hero-swiper.swiper .swiper-wrapper,
        .hero-swiper.swiper .swiper-slide {
            height: 100%;
        }

        .hero-swiper .swiper-slide {
            position: relative;
            overflow: hidden;
        }

        .hero-swiper .slide-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            transition: transform 8s ease-out;
        }

        .hero-swiper .swiper-slide-active .slide-bg {
            transform: scale(1.1);
        }

        .hero-swiper .swiper-pagination-bullet {
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 1;
        }

        .hero-swiper .swiper-pagination-bullet-active {
            background: #C9AA36;
        }

        /* Stats grid: 5 columnas en desktop, 2+2+1 en mobile */
        @media (min-width: 640px) {
            .stats-grid {
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 0;
            }
            .stats-grid > * {
                border-radius: 0 !important;
                border-right: 1px solid rgba(107, 114, 128, 0.3);
                grid-column: span 1 / span 1 !important;
            }
            .stats-grid > *:last-child {
                border-right: none;
            }
        }

        /* Indicadores que además son accesos directos (Obs. N.º 1): necesitan una
           señal visual clara de que se puede hacer clic —tinte dorado, subrayado
           de la etiqueta y una flecha que aparece— sin ensuciar la portada. */
        .stats-link {
            transition: background-color .25s ease, transform .25s ease;
        }

        .stats-link:hover,
        .stats-link:focus-visible {
            background-color: rgba(201, 170, 54, .14);
        }

        .stats-link .stats-label {
            text-decoration: underline;
            text-decoration-color: transparent;
            text-underline-offset: 3px;
            transition: color .25s ease, text-decoration-color .25s ease;
        }

        .stats-link:hover .stats-label,
        .stats-link:focus-visible .stats-label {
            color: #C9AA36;
            text-decoration-color: currentColor;
        }

        .stats-link .stats-arrow {
            opacity: 0;
            transform: translateX(-4px);
            transition: opacity .25s ease, transform .25s ease;
        }

        .stats-link:hover .stats-arrow,
        .stats-link:focus-visible .stats-arrow {
            opacity: 1;
            transform: translateX(0);
        }

        @media (prefers-reduced-motion: reduce) {
            .stats-link,
            .stats-link .stats-arrow {
                transition: none;
            }
        }
    </style>
@endpush

@section('content')
    {{-- HERO PRINCIPAL: pantalla completa, carrusel de campus + stats integrados al pie --}}
    {{-- `min-h-screen` en vez de `h-screen`: si el contenido necesita más alto
         (móviles estrechos, tipografía ampliada), el hero crece en lugar de
         recortarlo. --}}
    <header class="relative w-full min-h-screen flex flex-col overflow-hidden">
        {{-- Carrusel de fondo (overlay absoluto: llena todo el header) --}}
        {{-- Solo la primera diapositiva trae su fondo en el marcado. Las otras
             dos llevan la ruta en `data-bg-diferido` y las carga `carousels.js`
             cuando el navegador está ocioso: antes las tres se descargaban de
             golpe (≈690 KB) para enseñar una sola. --}}
        <div class="swiper hero-swiper absolute inset-0 z-0">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="slide-bg" style="background-image: url('{{ asset('images/campus-aerea.jpg') }}'); background-image: image-set(url('{{ asset('images/campus-aerea.webp') }}') type('image/webp'), url('{{ asset('images/campus-aerea.jpg') }}') type('image/jpeg'));"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-unmsm-azul/80 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-bg" data-bg-diferido="{{ asset('images/campus-aerea-2.webp') }}" data-bg-respaldo="{{ asset('images/campus-aerea-2.jpg') }}"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-unmsm-azul/80 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                </div>
                <div class="swiper-slide">
                    <div class="slide-bg" data-bg-diferido="{{ asset('images/campus-fachada.webp') }}" data-bg-respaldo="{{ asset('images/campus-fachada.jpg') }}"></div>
                    <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-unmsm-azul/80 to-transparent"></div>
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                </div>
            </div>
        </div>

        {{-- Contenido principal. Va en flujo normal (no superpuesto): así nunca
             puede quedar por debajo de la cabecera fija ni por detrás de la banda
             de indicadores, pase lo que pase con el alto del texto o del viewport.
             `pt-*` reserva el alto de la cabecera fija (topbar + navbar). --}}
        <div class="relative z-20 flex-1 flex items-center text-white pt-32 sm:pt-36 lg:pt-32 pb-8">
            <div class="container mx-auto px-6">
            @php
                // Textos editables desde el panel (Configuración → Portada). Los
                // valores de respaldo son los que llevaba escritos la plantilla:
                // mientras no se toque nada, la portada se ve igual que antes.
                $ajustesHero = \App\Models\SiteSetting::get();
                $heroKicker = $ajustesHero?->home_hero_kicker
                    ?: 'Universidad Nacional Mayor de San Marcos · Decana de América';
                $heroTitulo = $ajustesHero?->home_hero_titulo
                    ?: 'CERSEU de la Facultad de Letras y Ciencias Humanas';
                $heroTexto = $ajustesHero?->home_hero_texto
                    ?: 'Formamos profesionales comprometidos con el desarrollo cultural y social del país, mediante cursos y talleres de extensión universitaria.';
                $heroCta1Texto = $ajustesHero?->home_hero_cta1_texto ?: 'Ver cursos';
                $heroCta1Url = $ajustesHero?->home_hero_cta1_url ?: route('cursos.index');
                $heroCta2Texto = $ajustesHero?->home_hero_cta2_texto ?: 'Cómo inscribirte';
                $heroCta2Url = $ajustesHero?->home_hero_cta2_url ?: route('admision');
            @endphp
            <div class="max-w-4xl animate-hero-in">
                <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-[11px] sm:text-xs md:text-sm mb-3 md:mb-4 drop-shadow">
                    {{ $heroKicker }}
                </p>
                <h1 class="text-3xl sm:text-4xl md:text-6xl lg:text-7xl font-serif font-bold leading-[1.1] md:leading-[1.05] mb-4 md:mb-6 drop-shadow-lg text-balance">
                    {{ $heroTitulo }}
                </h1>
                <p class="text-sm sm:text-base md:text-xl text-gray-200 max-w-2xl mb-6 md:mb-8 font-normal leading-relaxed">
                    {{ $heroTexto }}
                </p>
                {{-- Accesos principales orientados a la oferta de diplomados (Obs. N.º 1).
                     Dos acciones claramente diferenciadas: explorar la oferta
                     (botón sólido) y conocer el proceso de postulación (contorno). --}}
                <div class="flex flex-wrap gap-4">
                    @if ($heroCta1Texto)
                        <a href="{{ $heroCta1Url }}"
                            class="group inline-flex items-center gap-2 px-7 py-3 bg-unmsm-dorado text-unmsm-azul font-bold rounded-lg hover:bg-yellow-400 transition shadow-lg motion-safe:hover:-translate-y-1 duration-200 text-sm md:text-base focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2">
                            {{ $heroCta1Texto }}
                            <x-fas-arrow-right class="text-xs motion-safe:group-hover:translate-x-1 transition-transform" aria-hidden="true" />
                        </a>
                    @endif
                    @if ($heroCta2Texto)
                        <a href="{{ $heroCta2Url }}"
                            class="inline-flex items-center gap-2 px-7 py-3 border border-white/80 text-white font-bold rounded-lg hover:bg-white/10 hover:border-white transition text-sm md:text-base focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2">
                            <x-fas-user-plus class="text-xs" aria-hidden="true" />
                            {{ $heroCta2Texto }}
                        </a>
                    @endif
                </div>
            </div>
            </div>
        </div>

        {{-- Indicadores al pie del hero. También en flujo normal (`mt-auto`), de
             modo que su alto real —mayor en móvil, donde ocupan varias filas—
             siempre se respeta en lugar de taparse con el contenido. --}}
        <div class="relative z-20 mt-auto border-t border-unmsm-dorado/40 bg-black/40 backdrop-blur-md">
            <div class="container mx-auto px-6 py-4 sm:py-5">
                {{-- Los tres primeros indicadores son además accesos directos a su
                     sección (Obs. N.º 1, sugerencia complementaria). Los dos últimos
                     son datos institucionales y se mantienen como texto. --}}
                <div data-reveal class="grid grid-cols-2 stats-grid gap-2 sm:gap-3 text-center">
                    @foreach (\App\Models\TipoOferta::cases() as $t)
                        <a href="{{ route($t->slug() . '.index') }}"
                            class="stats-item stats-link group relative flex flex-col items-center p-2 sm:p-3 rounded-xl focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-unmsm-dorado">
                            <x-fas-graduation-cap class="w-6 h-6 sm:w-7 sm:h-7 mb-1 text-unmsm-dorado" aria-hidden="true" />
                            <div class="text-xl sm:text-2xl md:text-3xl font-bold text-white"
                                data-count-to="{{ count($ofertaPorTipo[$t->value] ?? []) }}">{{ count($ofertaPorTipo[$t->value] ?? []) }}</div>
                            <div class="stats-label inline-flex items-center gap-1 text-[10px] md:text-xs text-gray-300 uppercase tracking-wider">
                                {{ $t->plural() }}
                                <x-fas-arrow-right class="stats-arrow text-[0.65em]" aria-hidden="true" />
                            </div>
                            <span class="sr-only">Ver la sección de {{ mb_strtolower($t->plural()) }}</span>
                        </a>
                    @endforeach
                    <div class="flex flex-col items-center p-2 sm:p-3 rounded-xl stats-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-unmsm-dorado mb-0.5 sm:mb-1" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                        @php
                            // Editable en Configuración → Portada. No se puede
                            // calcular: no guardamos la condición RENACYT de los
                            // docentes.
                            $docentesRenacyt = \App\Models\SiteSetting::get()?->home_stat_docentes ?: 20;
                        @endphp
                        <div class="text-xl sm:text-2xl md:text-3xl font-bold text-white" data-count-to="{{ $docentesRenacyt }}" data-count-suffix="+">{{ $docentesRenacyt }}+</div>
                        <div class="text-[10px] md:text-xs text-gray-300 uppercase tracking-wider">Docentes Renacyt</div>
                    </div>
                    <div class="col-span-2 flex flex-col items-center p-2 sm:p-3 rounded-xl stats-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8 text-unmsm-dorado mb-0.5 sm:mb-1" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                        </svg>
                        @php
                            // Se calcula, no se escribe: estaba fijado en 473, que era
                            // lo correcto en 2024 y envejecía solo cada 12 de mayo.
                            // La UNMSM se fundó el 12 de mayo de 1551.
                            // `diffInYears` devuelve float en Carbon 3: hay que truncarlo
                            // o sale «475.22759525922» en pantalla.
                            $aniosUnmsm = (int) \Carbon\Carbon::create(1551, 5, 12)->diffInYears(now());
                        @endphp
                        <div class="text-xl sm:text-2xl md:text-3xl font-bold text-white" data-count-to="{{ $aniosUnmsm }}">{{ $aniosUnmsm }}</div>
                        <div class="text-[10px] md:text-xs text-gray-300 uppercase tracking-wider">Años de Historia</div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
{{-- CRONOGRAMA DE ADMISIÓN (Obs. N.º 2): todo el contenido —encabezado, etapas
     y botón— se administra desde /admin/cronograma-admision. La sección entera
     desaparece cuando no hay convocatoria activa o no quedan etapas visibles. --}}
@php
    $cronoAdmision = \App\Models\CronogramaAdmision::get();
    $pasosAdmision = $cronoAdmision?->is_visible
        ? $cronoAdmision->pasos->where('is_visible', true)
        : collect();
@endphp

@if ($pasosAdmision->isNotEmpty())
<section id="admision" class="relative py-16 md:py-20 bg-gradient-to-b from-gray-900 to-[#0C1217] text-white overflow-hidden"
    aria-labelledby="cronograma-admision-titulo">
    {{-- textura de puntos + resplandor guinda (consistente con la banda institucional) --}}
    <div class="absolute inset-0 opacity-[0.05]"
        style="background-image: radial-gradient(circle at 1px 1px, #fff 1.5px, transparent 0); background-size: 34px 34px;">
    </div>
    <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[46rem] h-[46rem] rounded-full bg-unmsm-azul/25 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-12">
            @if ($cronoAdmision->eyebrow)
                <span class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-2 block">{{ $cronoAdmision->eyebrow }}</span>
            @endif
            <h2 id="cronograma-admision-titulo" class="text-3xl md:text-4xl font-bold mb-2 font-serif">
                {{ $cronoAdmision->titulo ?: 'Cronograma de Admisión' }}
            </h2>
            <div class="w-16 h-1 bg-unmsm-dorado mx-auto mt-3 rounded-full"></div>
        </div>

        <!-- Versión Desktop: timeline horizontal -->
        <div class="hidden lg:block">
            <div class="relative" style="margin-bottom: 3rem;">
                <div class="absolute" style="top: -3px; left: 10%; right: 10%; height: 2px; background: linear-gradient(90deg, rgba(201,170,54,.15), #C9AA36 50%, rgba(201,170,54,.15)); z-index: 0;"></div>

                <ol class="flex justify-between items-start gap-4 list-none" style="position: relative; z-index: 1;">
                    @foreach ($pasosAdmision as $paso)
                        <li class="flex-1 relative group" style="min-width: 0;">
                            <div class="{{ $paso->destacado ? 'bg-white border-unmsm-azul' : 'bg-white/[0.06] ring-1 ring-white/10 border-unmsm-dorado hover:bg-white/[0.1]' }} rounded-xl p-4 border-b-4 shadow-lg transition-all duration-300 motion-safe:hover:-translate-y-1 hover:shadow-xl h-full">
                                <div class="text-center">
                                    <div class="w-14 h-14 {{ $paso->destacado ? 'bg-unmsm-azul text-white' : 'bg-unmsm-dorado/20 text-unmsm-dorado ring-1 ring-unmsm-dorado/30' }} rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg motion-safe:group-hover:scale-110 transition-transform duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $paso->icono_path }}" />
                                        </svg>
                                    </div>
                                    <h3 class="text-sm font-bold {{ $paso->destacado ? 'text-gray-800' : 'text-white' }} mb-1 leading-tight">{{ $paso->titulo }}</h3>
                                    @if ($paso->fecha_display)
                                        <p class="{{ $paso->destacado ? 'text-unmsm-azul' : 'text-unmsm-dorado' }} font-bold text-xs mb-1">{{ $paso->fecha_display }}</p>
                                    @endif
                                    @if ($paso->detalle)
                                        <p class="{{ $paso->destacado ? 'text-gray-500' : 'text-gray-300' }} text-[10px] font-medium">{{ $paso->detalle }}</p>
                                    @endif
                                    @if ($paso->publico)
                                        <p class="{{ $paso->destacado ? 'text-gray-500' : 'text-gray-300' }} text-[10px] font-medium">{{ $paso->publico }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="absolute" style="top: -8px; left: 50%; transform: translateX(-50%); width: 12px; height: 12px; background-color: #C9AA36; border-radius: 50%; border: 3px solid #0C1217; box-shadow: 0 0 0 1px rgba(201,170,54,.4), 0 4px 6px -1px rgba(0, 0, 0, 0.3); z-index: 10;"></div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>

        <!-- Versión Mobile: timeline vertical -->
        <ol class="lg:hidden relative list-none">
            <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-white/15" aria-hidden="true"></div>

            @foreach ($pasosAdmision as $paso)
                <li class="relative mb-8 last:mb-0">
                    <div class="flex items-center">
                        <div class="relative z-10 w-12 h-12 {{ $paso->destacado ? 'bg-unmsm-azul text-white' : 'bg-unmsm-dorado/20 text-unmsm-dorado ring-1 ring-unmsm-dorado/30' }} rounded-full flex items-center justify-center shadow-lg flex-shrink-0 border-2 border-[#0C1217]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $paso->icono_path }}" />
                            </svg>
                        </div>
                        <div class="ml-4 flex-1">
                            <div class="{{ $paso->destacado ? 'bg-white border-unmsm-azul text-gray-800' : 'bg-white/[0.06] ring-1 ring-white/10 border-unmsm-dorado' }} rounded-lg p-4 border-l-4 shadow-lg">
                                <h3 class="text-base font-bold {{ $paso->destacado ? 'text-gray-900' : 'text-white' }}">
                                    {{ $paso->titulo }}@if ($paso->publico) <span class="font-medium">· {{ $paso->publico }}</span>@endif
                                </h3>
                                @if ($paso->fecha_display)
                                    <p class="{{ $paso->destacado ? 'text-unmsm-azul' : 'text-unmsm-dorado' }} font-bold text-xs mt-0.5">{{ $paso->fecha_display }}</p>
                                @endif
                                @if ($paso->detalle)
                                    <p class="{{ $paso->destacado ? 'text-gray-500' : 'text-gray-300' }} text-xs mt-0.5">{{ $paso->detalle }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ol>

        <!-- Botón Principal -->
        @if ($cronoAdmision->boton_texto && $cronoAdmision->boton_url)
            <div class="flex flex-col items-center mt-8">
                <a href="{{ $cronoAdmision->boton_url }}"
                    class="bg-gradient-to-r from-unmsm-azul to-unmsm-azul-dark hover:from-unmsm-azul-dark hover:to-unmsm-azul text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 motion-safe:transform motion-safe:hover:scale-105 shadow-2xl flex items-center gap-3 border border-unmsm-azul-dark/50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-dorado focus-visible:outline-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    </svg>
                    {{ $cronoAdmision->boton_texto }}
                </a>
            </div>
        @endif
    </div>
</section>
@endif
    
    {{-- FRANJA INSTITUCIONAL (compacta): identidad San Marcos + CTA a Nosotros.
         Misión/Visión completas viven en /nosotros; aquí solo el sello. --}}
    <section class="relative py-12 md:py-14 bg-unmsm-azul text-white overflow-hidden">
        {{-- textura de puntos sutil --}}
        <div class="absolute inset-0 opacity-[0.06]"
            style="background-image: radial-gradient(circle at 1px 1px, #fff 1.5px, transparent 0); background-size: 34px 34px;">
        </div>
        {{-- resplandor dorado --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-unmsm-dorado/20 blur-3xl"></div>

        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6" data-reveal>
                <div class="max-w-2xl">
                    <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-xs md:text-sm mb-2">
                        Universidad Nacional Mayor de San Marcos · Decana de América
                    </p>
                    <h2 class="text-2xl md:text-3xl font-serif font-bold mb-2 leading-tight">
                        Tradición humanística, vocación de futuro
                    </h2>
                    <p class="text-white/85 leading-relaxed">
                        En la universidad más antigua de América, el CERSEU forma investigadores y líderes que
                        piensan el país desde las letras y las ciencias humanas.
                    </p>
                </div>

                <a href="{{ route('nosotros') }}"
                    class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-unmsm-dorado text-unmsm-azul font-bold hover:bg-white transition-colors shadow-lg motion-safe:hover:-translate-y-0.5 duration-200">
                    Conócenos <x-fas-arrow-right aria-hidden="true" />
                </a>
            </div>
        </div>
    </section>

    <section id="programas" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-unmsm-azul mb-6 font-serif">Nuestros Cursos</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Excelencia académica para investigadores y líderes en Humanidades.
                </p>
            </div>

            {{-- Filtros: "Talleres" queda activo al cargar la página y "Todos"
                 al final, para poder ver la oferta completa del CERSEU. --}}
            @php
                $filtroBase = 'filter-btn flex items-center gap-2 px-6 py-2.5 rounded-full font-semibold transition-all duration-300 focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-azul focus-visible:outline-offset-2';
                $filtroActivo = $filtroBase . ' bg-unmsm-azul text-white shadow-lg scale-105';
                $filtroInactivo = $filtroBase . ' bg-white text-gray-600 hover:bg-gray-100 shadow-sm';
            @endphp
            <div class="flex flex-wrap justify-center gap-3 sm:gap-4 mb-12" role="group"
                aria-label="Filtrar cursos por tipo">
                @foreach (\App\Models\TipoOferta::cases() as $t)
                    <button type="button" data-filter="{{ $t->value }}" id="filter-{{ $t->value }}" aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                        class="{{ $loop->first ? $filtroActivo : $filtroInactivo }}">
                        <x-fas-graduation-cap aria-hidden="true" /> {{ $t->plural() }}
                    </button>
                @endforeach
                <button type="button" data-filter="todos" id="filter-todos" aria-pressed="false"
                    class="{{ $filtroInactivo }}">
                    <x-fas-globe aria-hidden="true" /> Todos
                </button>
            </div>

            <!-- Grid de Programas -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6" id="programas-grid">
                {{-- Cards vía componente reutilizable <x-program-card> (el mismo de
                     /cursos, /talleres y /especializaciones): DRY, accesible y
                     consistente. El bucle recorre los tipos, así que añadir uno
                     no obliga a tocar esta vista. El primero se pinta visible
                     porque es el filtro activo al cargar; el resto arranca
                     oculto y lo destapa el filtro. --}}
                @foreach ($ofertaPorTipo as $tipoOferta => $items)
                    @php $t = \App\Models\TipoOferta::from($tipoOferta); @endphp
                    @foreach ($items as $programa)
                        <x-program-card :programa="$programa" :tipo="$t->value"
                            :badge-label="$t->singular()"
                            :badge-color="$loop->parent->first ? 'bg-unmsm-azul-light' : 'bg-unmsm-azul'"
                            :class="$loop->parent->first ? '' : 'hidden'"
                            primary-cta-label="Más información" :show-brochure="true" />
                    @endforeach
                @endforeach
            </div>

            {{-- Mensaje para el caso en que un filtro no tenga programas vigentes. --}}
            <p id="programas-empty" role="status"
                class="hidden text-center text-gray-500 py-12">
                Por el momento no hay cursos disponibles en esta categoría.
            </p>

        </div>
    </section>


    <!-- TESTIMONIOS -->
    @include('home.partials.testimonios-section')

    <!-- PLANA DOCENTE -->
    {{-- @if (count($docentes) > 0)
        <section class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-center section-title font-serif text-3xl font-bold mb-12">Coordinadores de Programa</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                    @foreach ($docentes as $docente)
                        <div class="text-center group cursor-pointer">
                            <div
                                class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-4 border-gray-100 group-hover:border-unmsm-azul transition-colors relative">
                                <img src="@if ($docente->foto){{ asset('storage/' . $docente->foto) }}@else{{ 'https://ui-avatars.com/api/?name=' . urlencode($docente->nombres . '+' . $docente->apellidos) . '&background=random' }}@endif"
                                    alt="{{ $docente->nombre_completo }}"
                                    class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition duration-500"
                                    loading="lazy" decoding="async" width="96" height="96">
                            </div>
                            <h4 class="font-bold text-sm text-gray-800 group-hover:text-unmsm-azul transition">
                                {{ $docente->nombre_completo }}
                            </h4>
                            <p class="text-xs text-gray-500">Coordinador(a) de
                                {{ $docente->programas->first()?->nombre ?? 'Curso' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif --}}

    @include('home.partials.eventos-section')

    @include('home.partials.informativos-section')

    {{-- Sección de contacto: llamada a la acción, no ficha de datos.

         Antes repetía email, teléfono, dirección, horario y las seis redes
         justo encima del pie, que muestra exactamente lo mismo: ~950px
         seguidos con la misma información. Ahora el pie es la referencia
         canónica (y acompaña a todas las páginas) y aquí solo quedan los dos
         caminos por los que la gente escribe de verdad, con el mapa como
         protagonista. --}}
    @php
        $contactoWhatsapp = \App\Models\SiteSetting::contacto('whatsapp');
        $contactoAdmision = \App\Models\SiteSetting::contacto('admision');
    @endphp
    <section id="contacto" class="bg-gradient-to-b from-gray-900 to-[#0C1217] text-gray-300 py-14 border-t border-white/10">
        <div class="container mx-auto px-6">
            {{-- Encabezado y botones comparten fila en escritorio: con solo dos
                 CTA, apilarlos dejaba la mitad de la banda vacía. --}}
            <div class="mb-8 flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-xl">
                    <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-xs mb-2">Estamos para ayudarte</p>
                    <h3 class="text-white text-2xl font-serif font-bold mb-3 section-title">Contáctanos</h3>
                    <p class="font-normal">¿Dudas sobre admisión, cursos o trámites? Escríbenos y te
                        respondemos en horario de oficina. Los datos completos de la Unidad están
                        al pie de esta página.</p>
                </div>

                <div class="flex flex-col sm:flex-row flex-wrap gap-4 lg:flex-shrink-0">
                @if ($contactoWhatsapp)
                    <a href="{{ $contactoWhatsapp }}" target="_blank" rel="noopener noreferrer"
                        class="group inline-flex items-center gap-3 rounded-xl bg-green-600 px-6 py-4 font-semibold text-white shadow-lg transition-all hover:bg-green-500 motion-safe:hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-400">
                        <x-fab-whatsapp class="text-xl" aria-hidden="true" />
                        <span>
                            <span class="block leading-tight">Escríbenos por WhatsApp</span>
                            <span class="block text-[11px] font-normal text-white/80 leading-tight">Consultas de admisión</span>
                        </span>
                    </a>
                @endif
                @if ($contactoAdmision)
                    <a href="mailto:{{ $contactoAdmision }}"
                        class="group inline-flex items-center gap-3 rounded-xl bg-white/5 px-6 py-4 font-semibold text-white ring-1 ring-white/15 transition-all hover:bg-white/10 hover:ring-unmsm-dorado/50 motion-safe:hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-unmsm-dorado">
                        <x-fas-envelope class="text-xl text-unmsm-dorado" aria-hidden="true" />
                        <span>
                            <span class="block leading-tight">Escribir un correo</span>
                            <span class="block text-[11px] font-normal text-white/60 leading-tight">{{ $contactoAdmision }}</span>
                        </span>
                    </a>
                @endif
                </div>
            </div>

                {{-- Mapa con carga bajo demanda.

                     El `iframe` de Google Maps arrastra cientos de KB y varias
                     peticiones a terceros en la página más visitada del sitio,
                     incluso para quien nunca mira el mapa. Aquí se muestra una
                     previsualización auto-alojada (16 KB) y el mapa real solo se
                     inserta al pulsar. Ventaja añadida: no se contacta a Google
                     hasta que el visitante lo pide.

                     Sin JS, el bloque degrada a un enlace a Google Maps. --}}
                <div x-data="{ cargado: false }"
                    class="relative rounded-xl overflow-hidden bg-gray-800 h-[300px] md:h-[340px] shadow-lg border border-gray-700">

                    <template x-if="cargado">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d487.7251388091363!2d-77.08159160793049!3d-12.057201313094351!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c9470823c4f5%3A0xc528a60911019861!2sFacultad%20de%20Letras%20y%20Ciencias%20Humanas%20-%20UNMSM!5e0!3m2!1ses!2spe!4v1764687672723!5m2!1ses!2spe"
                            class="w-full h-full" style="border:0;" allowfullscreen
                            title="Ubicación de la Facultad de Letras y Ciencias Humanas - UNMSM"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </template>

                    <button type="button" x-show="!cargado" @click="cargado = true"
                        class="group absolute inset-0 w-full h-full flex flex-col items-center justify-center gap-3 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-dorado focus-visible:outline-offset-[-4px]">
                        {{-- Mapa real (teselas de OpenStreetMap, servidas desde el
                             propio sitio). Antes había una foto del campus
                             desenfocada al 45%: no se entendía que fuera un mapa.

                             La imagen está compuesta centrada exactamente en las
                             coordenadas de la Facultad (-12.0570461, -77.0814630,
                             el nodo «Facultad de Letras y Ciencias Humanas» de
                             OpenStreetMap). Ese centrado es lo que sostiene al
                             marcador de abajo: con `object-cover` y el
                             `object-position` por defecto (50% 50%), el centro de
                             la imagen cae siempre en el centro del bloque, sea cual
                             sea el ancho de la pantalla. Si se regenera la imagen,
                             debe seguir centrada en ese punto.

                             Se sirve en tres anchos porque el bloque llega a 1488
                             CSS px: la versión única de 720 px que había antes se
                             ampliaba 1,7x (3,4x en pantallas retina) y se veía
                             pixelada. Los `sizes` describen el ancho real del
                             contenedor en cada breakpoint —no `100vw`, que haría
                             pedir siempre el archivo más grande. --}}
                        <img src="{{ asset('images/mapa-preview.webp') }}"
                            srcset="{{ asset('images/mapa-preview.webp') }} 1200w,
                                    {{ asset('images/mapa-preview-1600.webp') }} 1600w,
                                    {{ asset('images/mapa-preview@2x.webp') }} 2400w"
                            sizes="(min-width: 1536px) 1488px,
                                   (min-width: 1280px) 1232px,
                                   (min-width: 1024px) 976px,
                                   (min-width: 768px) 720px,
                                   (min-width: 640px) 592px,
                                   calc(100vw - 3rem)"
                            alt="Mapa de la Ciudad Universitaria de la UNMSM con la ubicación de la Facultad de Letras y Ciencias Humanas"
                            class="absolute inset-0 w-full h-full object-cover"
                            loading="lazy" decoding="async" width="1200" height="340">

                        {{-- Marcador sobre la Facultad: la punta del triángulo queda
                             en el centro exacto del bloque, que es el punto de las
                             coordenadas (ver la nota de la imagen). --}}
                        <span class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-full flex flex-col items-center" aria-hidden="true">
                            <span class="w-9 h-9 rounded-full bg-unmsm-azul text-white flex items-center justify-center shadow-lg ring-4 ring-white/70 motion-safe:group-hover:scale-110 transition-transform">
                                <x-fas-location-dot class="text-sm" />
                            </span>
                            <span class="w-0 h-0 border-x-[6px] border-x-transparent border-t-[8px] border-t-unmsm-azul -mt-0.5"></span>
                        </span>

                        {{-- Llamada a la acción sobre un velo inferior: deja ver el
                             mapa y mantiene el texto legible --}}
                        <span class="absolute inset-x-0 bottom-0 z-10 flex items-center justify-between gap-3 px-4 py-3 bg-gradient-to-t from-black/85 via-black/60 to-transparent text-left">
                            <span class="flex items-center gap-2.5 text-white">
                                <span class="w-9 h-9 rounded-full bg-unmsm-azul flex items-center justify-center shadow motion-safe:group-hover:scale-110 transition-transform flex-shrink-0">
                                    <x-fas-map-location-dot class="text-sm" aria-hidden="true" />
                                </span>
                                <span>
                                    <span class="block font-bold text-sm leading-tight">Ver el mapa interactivo</span>
                                    <span class="block text-[11px] text-white/70 leading-tight">Se cargará desde Google Maps al pulsar</span>
                                </span>
                            </span>
                        </span>

                        {{-- Atribución exigida por la licencia de OpenStreetMap --}}
                        <span class="absolute right-1 top-1 z-10 px-1.5 py-0.5 rounded bg-white/75 text-[9px] text-gray-700 leading-none">
                            © OpenStreetMap
                        </span>
                    </button>

                    {{-- Respaldo sin JavaScript --}}
                    <noscript>
                        <a href="https://maps.google.com/?q=Facultad+de+Letras+y+Ciencias+Humanas+UNMSM"
                            target="_blank" rel="noopener noreferrer"
                            class="absolute inset-0 flex items-center justify-center text-white font-bold underline">
                            Ver ubicación en Google Maps
                        </a>
                    </noscript>
                </div>

        </div>
    </section>

    {{-- Popup de anuncios.

         Se administra en el panel (Anuncios de la portada) y vive solo aquí:
         es la única vista que lo incluye, así que no puede colarse en el resto
         del sitio. Antes era un array escrito en esta plantilla, y estaba
         comentado porque apagarlo exigía editar el código y desplegar.

         Si no hay ninguno vigente, `paraPopup()` devuelve un array vacío y el
         componente no pinta nada — ni marcado, ni CSS, ni JS. --}}
    @php
        $anunciosPopup = \App\Models\Anuncio::paraPopup();
        $ajustesPopup = \App\Models\SiteSetting::ajustesPopup();

        // Vista previa desde el panel: fuerza la aparición saltándose el
        // «una vez por sesión», que si no obliga a limpiar el navegador para
        // volver a verlo. Solo para administradores.
        $previsualizando = request()->boolean('previsualizar_anuncios')
            && auth()->user()?->isAdmin();
    @endphp
    @if ($anunciosPopup)
        <x-popup-announcements :anuncios="$anunciosPopup"
            :open_delay="$previsualizando ? 0 : $ajustesPopup['retardo']"
            :show_once_per_session="! $previsualizando && $ajustesPopup['frecuencia'] === 'sesion'"
            :cookie_hours="$previsualizando ? 1 : ($ajustesPopup['frecuencia'] === 'dia' ? 24 : 1)"
            :auto_advance="$ajustesPopup['autoAvance']" />
    @endif

@endsection

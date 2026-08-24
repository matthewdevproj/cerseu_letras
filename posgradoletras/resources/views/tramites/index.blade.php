@extends('layouts.public')

@section('title', 'Trámites - CERSEU Letras UNMSM')

@php
    // Correos y teléfono de contacto (ver config/contacts.php).
    $emailTramites = config('contacts.tramites', 'cerseu.letras@unmsm.edu.pe');
    $telefono = config('contacts.telefono', '982 085 037');
    $whatsapp = config('contacts.whatsapp', 'https://wa.me/51982085037');
@endphp

@push('styles')
    <style>
        .prose ul {
            list-style-type: disc;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose ol {
            list-style-type: decimal;
            padding-left: 1.5rem;
            margin-bottom: 1rem;
        }

        .prose li {
            margin-bottom: 0.4rem;
            color: #374151;
        }
    </style>
@endpush

@section('content')

    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Trámites" label="Atención al participante"
        subtitle="Procedimientos, constancias y certificados del CERSEU."
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/DJI_0007-Trim-frame-at-0m5s.jpg" />

    {{-- La página se arma entera desde el panel: tantas secciones como se
         carguen, en el orden que se les dé. Antes eran dos pestañas —grado de
         Magíster y de Doctor— con cuatro y cinco tarjetas escritas a mano en la
         plantilla, de modo que agregar o quitar un paso obligaba a tocar el
         código. El CERSEU no otorga grados académicos y sus trámites no tienen
         por qué venir en ese número ni repartidos en dos columnas. --}}
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-4 gap-8">

            <!-- BARRA LATERAL -->
            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-28">

                    @if ($secciones->isNotEmpty())
                        {{-- Índice: la página puede ser larga, y así se ve de un
                             vistazo el recorrido y se salta a cualquier punto. --}}
                        <nav class="hidden lg:block px-2" aria-label="Contenido de la página">
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 px-2">En esta página</h4>
                            <ol class="relative border-l border-gray-200 ml-3 space-y-1">
                                @foreach ($secciones as $i => $sec)
                                    <li class="relative pl-5">
                                        <span class="absolute -left-[5px] top-3 w-2 h-2 rounded-full bg-unmsm-azul" aria-hidden="true"></span>
                                        <a href="#tramite-{{ $i }}"
                                            class="block py-2 text-xs leading-snug text-gray-600 hover:text-unmsm-azul transition-colors">
                                            @if ($sec->numeral)
                                                <span class="font-bold">{{ $sec->numeral }}.</span>
                                            @endif
                                            {{ \Illuminate\Support\Str::limit($sec->titulo, 46) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif

                    <div class="hidden lg:block mt-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Informes</h4>
                        <div class="space-y-3 text-sm">
                            <a href="mailto:{{ $emailTramites }}"
                                class="flex items-center gap-2 text-gray-700 hover:text-unmsm-azul">
                                <x-far-envelope class="text-unmsm-azul" />
                                <span class="truncate">{{ $emailTramites }}</span>
                            </a>
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                                <x-fab-whatsapp class="text-green-500 text-lg" />
                                <span>{{ $telefono }}</span>
                            </a>
                        </div>
                    </div>

                    <div class="hidden lg:block mt-4 p-4 bg-unmsm-azul/5 rounded-lg border border-unmsm-azul/10">
                        <h4 class="text-xs font-bold text-unmsm-azul uppercase mb-3">Enlaces</h4>
                        <div class="space-y-2 text-xs">
                            <a href="https://sanmarket.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer"
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-azul transition">
                                <x-fas-credit-card class="text-green-600" />
                                SanMarket (Pagos)
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="lg:col-span-3 min-h-[500px]">
                <div class="space-y-8 fade-in">
                    @forelse ($secciones as $i => $sec)
                        <div id="tramite-{{ $i }}"
                            class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-4 border-unmsm-azul overflow-hidden">
                            <div
                                class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-unmsm-azul/[0.055] via-unmsm-azul/[0.015] to-transparent flex items-center gap-4">
                                <span
                                    class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-unmsm-azul to-unmsm-azul-dark text-white flex items-center justify-center font-bold">
                                    {{ $sec->numeral ?: $i + 1 }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <h3 class="font-bold text-gray-800 leading-snug">{{ $sec->titulo }}</h3>
                                </div>
                            </div>
                            <div class="p-6 prose prose-sm max-w-none text-gray-600">
{!! $sec->cuerpo_renderizado !!}
</div>
                        </div>
                    @empty
                        <x-empty-state icon="fa-file-alt" title="Próximamente"
                            description="Estamos preparando la información de trámites del CERSEU." />
                    @endforelse
                </div>
            </section>

            <!-- INFORMACIÓN Y RECURSOS (SOLO MÓVIL) -->
            <div class="lg:hidden space-y-4 mt-8">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Informes</h4>
                    <div class="space-y-3 text-sm">
                        <a href="mailto:{{ $emailTramites }}"
                            class="flex items-center gap-2 text-gray-700 hover:text-unmsm-azul">
                            <x-far-envelope class="text-unmsm-azul" />
                            <span class="truncate">{{ $emailTramites }}</span>
                        </a>
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                            <x-fab-whatsapp class="text-green-500 text-lg" />
                            <span>{{ $telefono }}</span>
                        </a>
                    </div>
                </div>

                <div class="p-4 bg-unmsm-azul/5 rounded-lg border border-unmsm-azul/10">
                    <h4 class="text-xs font-bold text-unmsm-azul uppercase mb-3">Enlaces</h4>
                    <div class="space-y-2 text-xs">
                        <a href="https://sanmarket.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer"
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-azul transition">
                            <x-fas-credit-card class="text-green-600" />
                            SanMarket (Pagos)
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

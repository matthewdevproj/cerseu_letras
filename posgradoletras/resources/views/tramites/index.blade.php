@extends('layouts.public')

@section('title', 'Trámites - Obtención de Grado - Posgrado Letras UNMSM')

{{-- ========================================================
CORREOS DE CONTACTO - MODIFICAR AQUÍ O EN config/contacts.php
======================================================== --}}
@php
    // Correo para Trámites (Grados, Títulos, Certificados)
    $emailTramites = config('contacts.tramites', 'upg.letras@unmsm.edu.pe');

    // Teléfono / WhatsApp
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
    <x-hero-section title="Obtención de Grado" label="Trámites Académicos"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/DJI_0007-Trim-frame-at-0m5s.jpg" />

    <!-- LAYOUT SIDEBAR + CONTENIDO -->
    <div class="container mx-auto px-4 py-12" x-data="{ currentTab: 'maestria' }">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- SIDEBAR: NAVEGACIÓN -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 lg:sticky lg:top-28">
                    <nav class="grid grid-cols-2 lg:flex lg:flex-col gap-2 lg:gap-1">
                        <button @click="currentTab = 'maestria'"
                            :class="{ 'bg-unmsm-guinda text-white shadow-md': currentTab === 'maestria', 'text-gray-600 hover:bg-gray-50': currentTab !== 'maestria' }"
                            class="flex items-center justify-center lg:justify-start gap-2 lg:gap-3 px-3 lg:px-4 py-3 rounded-lg text-xs lg:text-sm font-bold transition-all w-full text-center lg:text-left">
                            <x-fas-graduation-cap class="text-base lg:text-lg" x-bind:class="{ 'text-unmsm-dorado': currentTab === 'maestria', 'text-gray-400': currentTab !== 'maestria' }" />
                            Grado de Magíster
                        </button>

                        <button @click="currentTab = 'doctorado'"
                            :class="{ 'bg-gray-900 text-white shadow-md': currentTab === 'doctorado', 'text-gray-600 hover:bg-gray-50': currentTab !== 'doctorado' }"
                            class="flex items-center justify-center lg:justify-start gap-2 lg:gap-3 px-3 lg:px-4 py-3 rounded-lg text-xs lg:text-sm font-bold transition-all w-full text-center lg:text-left">
                            <x-fas-user-graduate class="text-base lg:text-lg" x-bind:class="{ 'text-white': currentTab === 'doctorado', 'text-gray-400': currentTab !== 'doctorado' }" />
                            Grado de Doctor
                        </button>
                    </nav>


                    {{-- Índice de pasos: la página es larga y así se ve de un
                         vistazo el recorrido completo y se salta a cualquier
                         punto. Los títulos salen del panel, igual que las
                         tarjetas; el índice se arma solo. --}}
                    <nav class="hidden lg:block mt-6 px-2" aria-label="Pasos del trámite">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-3 px-2">En esta página</h4>
                        <ol class="relative border-l border-gray-200 ml-3 space-y-1">
                            @foreach ($secciones as $i => $sec)
                                @php $esDoctorado = $sec->grupo === 'doctorado'; @endphp
                                <li x-show="currentTab === '{{ $sec->grupo }}'" x-cloak class="relative pl-5">
                                    <span class="absolute -left-[5px] top-3 w-2 h-2 rounded-full {{ $esDoctorado ? 'bg-gray-900' : 'bg-unmsm-guinda' }}" aria-hidden="true"></span>
                                    <a href="#paso-{{ $i }}"
                                        class="block py-2 text-xs leading-snug text-gray-600 hover:text-unmsm-guinda transition-colors">
                                        @if ($sec->numeral)
                                            <span class="font-bold">{{ $sec->numeral }}.</span>
                                        @endif
                                        {{ \Illuminate\Support\Str::limit($sec->titulo, 46) }}
                                    </a>
                                </li>
                            @endforeach
                        </ol>
                    </nav>

                    <div class="hidden lg:block mt-6 p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Informes</h4>
                        <div class="space-y-3 text-sm">
                            <a href="mailto:{{ $emailTramites }}"
                                class="flex items-center gap-2 text-gray-700 hover:text-unmsm-guinda">
                                <x-far-envelope class="text-unmsm-guinda" />
                                <span class="truncate">{{ $emailTramites }}</span>
                            </a>
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                                <x-fab-whatsapp class="text-green-500 text-lg" />
                                <span>{{ $telefono }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Recursos Rápidos -->
                    <div class="hidden lg:block mt-4 p-4 bg-unmsm-guinda/5 rounded-lg border border-unmsm-guinda/10">
                        <h4 class="text-xs font-bold text-unmsm-guinda uppercase mb-3">Documentos</h4>
                        <div class="space-y-2 text-xs">
                            {{-- <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/06/Plantilla-oficial-de-Proyecto-de-Tesis.pdf"
                                target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                                <x-fas-file-pdf class="text-red-500" />
                                Plantilla Proyecto
                            </a> --}}
                            <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/03/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-1-1.pdf"
                                target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                                <x-fas-file-pdf class="text-red-500" />
                                Estructura de Tesis
                            </a>
                            <a href="https://sanmarket.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer" 
                                class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                                <x-fas-credit-card class="text-green-600" />
                                SanMarket (Pagos)
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL -->
            <section class="lg:col-span-3 min-h-[500px]">

                <!-- ======================= PESTAÑA MAESTRÍA ======================= -->
                <div x-show="currentTab === 'maestria'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-8 fade-in">

                    <div class="border-b pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-unmsm-guinda">
                            Requisitos para la Obtención del Grado de Magíster
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">
                            Proceso completo: inscripción de proyecto, declaración de expedito, sustentación y trámite de
                            diploma.
                        </p>
                    </div>

                    <!-- PASO I - MAGÍSTER -->
                    <div id="paso-0" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-unmsm-guinda/[0.055] via-unmsm-guinda/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-unmsm-guinda to-[#5a161a] text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                I
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso I</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[0] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 prose prose-sm max-w-none text-gray-600">
{!! ($secciones[0] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO II - MAGÍSTER -->
                    <div id="paso-1" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-unmsm-guinda/[0.055] via-unmsm-guinda/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-unmsm-guinda to-[#5a161a] text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                II
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso II</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[1] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
{!! ($secciones[1] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO III - MAGÍSTER -->
                    <div id="paso-2" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-unmsm-guinda/[0.055] via-unmsm-guinda/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-unmsm-guinda to-[#5a161a] text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                III
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso III</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[2] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 text-gray-600 text-sm">
{!! ($secciones[2] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO IV - MAGÍSTER -->
                    <div id="paso-3" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-unmsm-guinda/[0.055] via-unmsm-guinda/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-unmsm-guinda to-[#5a161a] text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                IV
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso IV</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[3] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
{!! ($secciones[3] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>
                </div>

                <!-- ======================= PESTAÑA DOCTORADO ======================= -->
                <div x-show="currentTab === 'doctorado'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                    class="space-y-8 fade-in" style="display: none;">

                    <div class="border-b pb-4 mb-6">
                        <h2 class="text-2xl font-serif font-bold text-gray-900">
                            Requisitos para la Obtención del Grado de Doctor
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">
                            Proceso académico para la obtención del máximo grado: Doctor.
                        </p>
                    </div>

                    <!-- PASO I DOCTORADO -->
                    <div id="paso-4" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-unmsm-guinda/[0.055] via-unmsm-guinda/[0.015] to-transparent flex items-start gap-4">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[4] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 prose prose-sm max-w-none text-gray-600">
{!! ($secciones[4] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO I DOCTORADO -->
                    <div id="paso-5" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-gray-900/50 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-gray-900">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-gray-900/[0.055] via-gray-900/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-gray-900 to-gray-700 text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                I
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso I</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[5] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 prose prose-sm max-w-none text-gray-600">
{!! ($secciones[5] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO II DOCTORADO -->
                    <div id="paso-6" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-gray-900/50 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-gray-900">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-gray-900/[0.055] via-gray-900/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-gray-900 to-gray-700 text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                II
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso II</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[6] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
{!! ($secciones[6] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO III DOCTORADO -->
                    <div id="paso-7" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-gray-900/50 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-gray-900">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-gray-900/[0.055] via-gray-900/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-gray-900 to-gray-700 text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                III
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso III</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[7] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 text-gray-600 text-sm">
{!! ($secciones[7] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                    <!-- PASO IV DOCTORADO -->
                    <div id="paso-8" class="group scroll-mt-28 bg-white/70 backdrop-blur-sm rounded-xl shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-gray-900/50 overflow-hidden transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-gray-900">
                        <div class="px-6 py-5 border-b border-gray-900/[0.05] bg-gradient-to-r from-gray-900/[0.055] via-gray-900/[0.015] to-transparent flex items-start gap-4">
                            <span class="flex-shrink-0 w-11 h-11 rounded-xl bg-gradient-to-br from-gray-900 to-gray-700 text-white flex items-center justify-center font-serif font-bold text-lg shadow-lg ring-1 ring-white/25 motion-safe:group-hover:scale-105 motion-safe:group-hover:-rotate-3 transition-transform duration-300">
                                IV
                            </span>
                            <div class="min-w-0 flex-1">
                                <span class="block text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Paso IV</span>
                                <h3 class="font-bold text-gray-800 leading-snug">{{ ($secciones[8] ?? null)?->titulo }}</h3>
                            </div>
                        </div>
                        <div class="p-6 text-gray-600 text-sm space-y-4">
{!! ($secciones[8] ?? null)?->cuerpo_renderizado !!}
</div>
                    </div>

                </div>

            </section>

            <!-- INFORMACIÓN Y RECURSOS (SOLO MÓVIL) -->
            <div class="lg:hidden space-y-4 mt-8">
                <div class="p-4 bg-gray-50 rounded-lg border border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase mb-2">Informes</h4>
                    <div class="space-y-3 text-sm">
                        <a href="mailto:{{ $emailTramites }}"
                            class="flex items-center gap-2 text-gray-700 hover:text-unmsm-guinda">
                            <x-far-envelope class="text-unmsm-guinda" />
                            <span class="truncate">{{ $emailTramites }}</span>
                        </a>
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-700 hover:text-green-600">
                            <x-fab-whatsapp class="text-green-500 text-lg" />
                            <span>{{ $telefono }}</span>
                        </a>
                    </div>
                </div>

                <div class="p-4 bg-unmsm-guinda/5 rounded-lg border border-unmsm-guinda/10">
                    <h4 class="text-xs font-bold text-unmsm-guinda uppercase mb-3">Documentos Rápidos</h4>
                    <div class="space-y-2 text-xs">
                        {{-- <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/06/Plantilla-oficial-de-Proyecto-de-Tesis.pdf"
                            target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                            <x-fas-file-pdf class="text-red-500" />
                            Plantilla Proyecto
                        </a> --}}
                        <a href="https://letras.unmsm.edu.pe/wp-content/uploads/2022/03/Directiva-de-Estrctura-de-tesis-Maestria-y-DoctoradoFFFFFFFFFF-1-1.pdf"
                            target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                            <x-fas-file-pdf class="text-red-500" />
                            Estructura de Tesis
                        </a>
                        <a href="https://sanmarket.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer" 
                            class="flex items-center gap-2 text-gray-600 hover:text-unmsm-guinda transition">
                            <x-fas-credit-card class="text-green-600" />
                            SanMarket (Pagos)
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

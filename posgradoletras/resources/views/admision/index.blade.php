@extends('layouts.public')

@section('title', 'Admisión 2026-I - Posgrado Letras UNMSM')

{{-- ========================================================
CORREOS DE CONTACTO - MODIFICAR AQUÍ
======================================================== --}}
@php
    // Correo para Admisión (envío de expediente, consultas de inscripción)
    $emailAdmision = config('contacts.admision');

    // Correo general para otras consultas
    $emailGeneral = config('contacts.general');

    // Teléfono / WhatsApp
    $telefono = config('contacts.telefono');
    $whatsapp = config('contacts.whatsapp');
@endphp

@push('styles')
    <style>
        .step-card {
            border-left: 4px solid var(--brand);
            transition: all 0.3s ease;
        }

        .step-card:hover {
            border-left-color: #d4af37;
            transform: translateX(4px);
        }

        .step-number {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.25rem;
            flex-shrink: 0;
        }

        .requirement-list li {
            position: relative;
            padding-left: 1.5rem;
        }

        .requirement-list li::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: var(--brand);
            font-weight: bold;
        }

        .tab-btn.active {
            background: var(--brand);
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        .cronograma-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cronograma-table th,
        .cronograma-table td {
            padding: 0.9rem 1rem;
            text-align: left;
            border-bottom: 1px solid #eef0f2;
            vertical-align: middle;
        }

        .cronograma-table thead th {
            background: #faf7f2;
            font-weight: 700;
            color: #6B1E20;
            font-size: 0.7rem;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-bottom: 2px solid rgba(182, 163, 80, 0.4);
        }

        .cronograma-table tbody tr {
            transition: background-color 0.15s ease;
        }

        .cronograma-table tbody tr:nth-child(even) {
            background: #fcfbf9;
        }

        .cronograma-table tbody tr:hover {
            background: #f8f1ec;
        }
    </style>
@endpush

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Admisión 2026-I" label="Proceso de Inscripción"
        subtitle="Inicia tu camino hacia la excelencia académica con la Decana de América"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/IMG_1565-scaled.jpg" />

    <section class="container mx-auto px-6 py-16 fade-in">

        <!-- Fecha límite destacada -->
        <div class="bg-gradient-to-r from-unmsm-guinda to-red-900 text-white rounded-2xl p-6 mb-12 shadow-xl">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white/80 text-sm uppercase tracking-wider">Inscripciones abiertas</p>
                        <p class="text-2xl font-bold">05 de enero al 02 de abril del 2026</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-white/80 text-sm">Publicación de resultados</p>
                    <p class="text-xl font-bold text-unmsm-dorado">09 de abril del 2026</p>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-8 items-start">
            <!-- Columna Principal -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Cronograma -->
                <div class="bg-white/70 backdrop-blur-sm rounded-xl overflow-hidden shadow-sm ring-1 ring-gray-900/[0.06] transition-shadow duration-300 hover:shadow-lg">
                    <div class="relative overflow-hidden bg-gradient-to-br from-unmsm-guinda to-[#5a161a] text-white px-5 py-5">
                        <div class="pointer-events-none absolute -right-6 -top-8 opacity-10">
                            <x-fas-calendar-days class="text-[7rem]" aria-hidden="true" />
                        </div>
                        <div class="relative flex items-start gap-3">
                            <span class="mt-0.5 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-white/10">
                                <x-fas-calendar-days class="text-lg text-unmsm-dorado" aria-hidden="true" />
                            </span>
                            <div>
                                <h3 class="font-serif text-lg font-bold leading-tight">{{ ($secciones[0] ?? null)?->titulo }}</h3>
{!! ($secciones[0] ?? null)?->cuerpo_renderizado !!}
</div>
                <div class="group bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                    <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif tracking-tight">{{ ($secciones[1] ?? null)?->titulo }}</h3>
{!! ($secciones[1] ?? null)?->cuerpo_renderizado !!}
</div>

                <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif mt-6">Procedimiento de pago de
                    inscripción</h3>

                <div class="space-y-6">
                    <!-- Paso 1: Generar ticket -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2">Generar ticket en SanMarket-UNMSM</h4>
                                <p class="text-sm text-gray-600 mb-3">Registrarse con correo de dominio Gmail.</p>
                            </div>
                        </div>
                        <x-video-embed id="wDpbuHt1xg4" title="Tutorial: Generar ticket en SanMarket-UNMSM" />
                    </div>

                    <!-- Paso 2: Realizar el pago -->
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start gap-3 mb-3">
                            <span
                                class="flex-shrink-0 w-8 h-8 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 mb-2">Realizar el pago en BCP o Yape</h4>
                                <p class="text-sm text-gray-600 mb-3">Puedes pagar a través de la App BCP, en un agente
                                    BCP o mediante Yape.</p>
                            </div>
                        </div>
                        <x-video-embed id="feg7DN0pSLM" title="Tutorial: Realizar el pago en BCP o Yape" />
                    </div>
                </div>
            </div>

            {{-- Requisitos de postulación.

                 Comparte fila con el cronograma, que mide 726 px más, así que
                 debajo queda columna vacía. Se probó a rellenarla y ninguna
                 alternativa compensa: a una columna la página pasa de 3613 a
                 5142 px, con el cronograma a ancho completo a 5531, y volver
                 pegajosa esta tarjeta (1696 px) la congelaría en un viewport
                 de ~900 px dejando su mitad inferior fuera de alcance.
                 El hueco se queda: es el arreglo más barato de los cuatro. --}}
            <div class="group bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif tracking-tight">{{ ($secciones[3] ?? null)?->titulo }}</h3>
{!! ($secciones[3] ?? null)?->cuerpo_renderizado !!}
</div>

            <!-- Paso 3: Requisitos -->
            <div class="group self-stretch bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                <h3 class="font-bold text-lg text-unmsm-guinda mb-6 font-serif">{{ ($secciones[2] ?? null)?->titulo }}</h3>
{!! ($secciones[2] ?? null)?->cuerpo_renderizado !!}
</div>

            <!-- Envío de Expediente -->
            <div class="group self-stretch bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif tracking-tight">{{ ($secciones[4] ?? null)?->titulo }}</h3>
{!! ($secciones[4] ?? null)?->cuerpo_renderizado !!}
</div>

            <!-- Resultados -->
            <div class="group self-stretch bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 space-y-6 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">

                <!-- PASO 4 -->
                <div>
                    <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif tracking-tight">{{ ($secciones[5] ?? null)?->titulo }}</h3>
{!! ($secciones[5] ?? null)?->cuerpo_renderizado !!}
</div>


        </div>

        <div class="lg:col-span-1 space-y-6">
            <!-- Contacto -->
            <div class="bg-unmsm-guinda text-white rounded-2xl p-6 shadow-xl sticky top-24">
                <h3 class="font-bold mb-4 font-serif text-xl">¿Necesitas ayuda?</h3>
                <p class="text-sm mb-4 text-white/80">Contáctanos para resolver cualquier duda sobre el proceso de
                    admisión.</p>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-white/60 text-xs">Email</span>
                            <p class="font-medium">{{ $emailAdmision }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-white/60 text-xs">WhatsApp</span>
                            <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" 
                                class="font-medium hover:text-white/80 transition-colors flex items-center gap-1">
                                {{ $telefono }} <x-fas-external-link-alt class="text-xs" />
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enlaces útiles -->
            <div class="group bg-white/70 backdrop-blur-sm rounded-xl p-6 shadow-sm ring-1 ring-gray-900/[0.06] border-l-[3px] border-unmsm-guinda/60 transition-all duration-300 hover:bg-white/90 hover:shadow-lg hover:border-unmsm-guinda">
                <h4 class="font-bold text-gray-800 mb-4">Enlaces útiles</h4>
                <div class="space-y-2">
                    <a href="https://posgrado.unmsm.edu.pe/admision/inscripcion/subir_Voucher/Subir/index.php"
                        target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 text-sm text-unmsm-guinda hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Subir comprobante de pago
                    </a>
                    <a href="https://sanmarket.unmsm.edu.pe" target="_blank" rel="noopener noreferrer" 
                        class="flex items-center gap-2 text-sm text-unmsm-guinda hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        SanMarket UNMSM
                    </a>
                    <a href="https://posgrado.unmsm.edu.pe/" target="_blank" rel="noopener noreferrer" 
                        class="flex items-center gap-2 text-sm text-unmsm-guinda hover:underline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        DGEP - Posgrado UNMSM
                    </a>
                </div>
            </div>
        </div>
        </div>

    </section>
@endsection

@push('scripts')
    <script>
        function showTab(tab) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.classList.remove('active');
                el.classList.add('bg-gray-200', 'text-gray-700');
                el.classList.remove('bg-unmsm-guinda', 'text-white');
            });

            document.getElementById('content-' + tab).classList.add('active');
            const btn = document.getElementById('tab-' + tab);
            btn.classList.add('active');
            btn.classList.remove('bg-gray-200', 'text-gray-700');
        }
    </script>
@endpush

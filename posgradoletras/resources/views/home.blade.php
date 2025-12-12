@extends('layouts.public')

@section('title', 'Inicio - Posgrado Letras UNMSM')

@push('styles')
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

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>
@endpush

@section('content')
    <!-- HERO PRINCIPAL - Deja espacio para las estadísticas abajo -->
    <header class="relative w-full h-[calc(95vh-115px)] min-h-[500px] overflow-hidden flex items-center">
        <div class="absolute inset-0 z-0">
            <img src="{{ asset('storage/letrasfondo.jpeg') }}" alt="Biblioteca UNMSM" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-r from-black/90 via-unmsm-guinda/80 to-transparent"></div>
        </div>

        <div class="container mx-auto px-6 relative z-10 text-white animate-fade-in pt-32">
            <h1 class="text-3xl md:text-5xl lg:text-6xl font-serif font-bold leading-tight mb-4 max-w-4xl drop-shadow-lg">
                UPG de Letras y <br> <span class="text-white/95">Ciencias Humanas</span>
            </h1>
            <p class="text-base md:text-lg text-gray-200 max-w-lg mb-6 font-light leading-snug">
                Formamos investigadores y profesionales comprometidos con el desarrollo cultural y social del país, a través
                de programas de Maestría y Doctorado de alto rigor académico.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#programas"
                    class="px-6 py-2.5 bg-unmsm-dorado text-unmsm-guinda font-bold rounded hover:bg-yellow-400 transition shadow-lg transform hover:-translate-y-1 text-sm md:text-base">
                    Ver Programas
                </a>
                <a href="#admision"
                    class="px-6 py-2.5 border border-white text-white font-bold rounded hover:bg-white/10 transition text-sm md:text-base">
                    Admisión 2025
                </a>
            </div>
        </div>
    </header>

    <!-- ESTADÍSTICAS -->
    <section class="bg-neutral-900 text-white py-6 border-b-4 border-unmsm-dorado shadow-2xl relative z-20">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center divide-x divide-gray-700/50">
                <div class="p-1 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-unmsm-dorado mb-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
                    </svg>
                    <div class="text-2xl md:text-3xl font-bold">{{ count($maestrias) }}</div>
                    <div class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider">Maestrías</div>
                </div>
                <div class="p-1 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-unmsm-dorado mb-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.221 69.17 69.17 0 00-2.923.897M6 10.5v5.5a2.25 2.25 0 002.25 2.25h11.5a2.25 2.25 0 002.25-2.25v-5.5" />
                    </svg>
                    <div class="text-2xl md:text-3xl font-bold">{{ count($doctorados) }}</div>
                    <div class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider">Doctorados</div>
                </div>
                <div class="p-1 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-unmsm-dorado mb-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <div class="text-2xl md:text-3xl font-bold">80+</div>
                    <div class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider">Docentes Renacyt</div>
                </div>
                <div class="p-1 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-unmsm-dorado mb-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                    <div class="text-2xl md:text-3xl font-bold">473</div>
                    <div class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider">Años de Historia</div>
                </div>
            </div>
        </div>
    </section>
    <!-- CRONOGRAMA DE ADMISIÓN -->
    <section id="admision" class="py-12 bg-gradient-to-br bg-gray-800 from-gray-800 to-gray-900 text-white relative">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-10">
                <span class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-1 block">Convocatoria
                    2025-I</span>
                <h2 class="text-3xl font-light mb-2 font-serif">Cronograma de Admisión</h2>
                <div class="w-16 h-1 bg-unmsm-dorado mx-auto mt-2 rounded-full"></div>
            </div>

            <div class="relative">
                <!-- Timeline Móvil -->
                <div class="lg:hidden">
                    <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gray-600"></div>

                    <!-- Item 1 -->
                    <div class="relative mb-8 last:mb-0">
                        <div class="flex items-center">
                            <div
                                class="relative z-10 w-12 h-12 bg-unmsm-guinda rounded-full flex items-center justify-center text-white shadow-lg flex-shrink-0 border-2 border-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="bg-white rounded-lg p-4 border-l-4 border-unmsm-guinda shadow-lg text-gray-800">
                                    <h3 class="text-base font-bold text-gray-900">Inscripción y Pago</h3>
                                    <p class="text-unmsm-guinda font-bold text-xs">15 Jul - 08 Ago</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 2 -->
                    <div class="relative mb-8 last:mb-0">
                        <div class="flex items-center">
                            <div
                                class="relative z-10 w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center text-white shadow-lg flex-shrink-0 border-2 border-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="bg-gray-700 rounded-lg p-4 border-l-4 border-gray-500 shadow-lg">
                                    <h3 class="text-base font-bold text-white">Envío de Expediente</h3>
                                    <p class="text-unmsm-dorado font-bold text-xs">09 Ago - 15 Ago</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="relative mb-8 last:mb-0">
                        <div class="flex items-center">
                            <div
                                class="relative z-10 w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center text-white shadow-lg flex-shrink-0 border-2 border-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="bg-gray-700 rounded-lg p-4 border-l-4 border-gray-500 shadow-lg">
                                    <h3 class="text-base font-bold text-white">Examen de Aptitud</h3>
                                    <p class="text-unmsm-dorado font-bold text-xs">20 Ago - 25 Ago</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Item 4 -->
                    <div class="relative mb-8 last:mb-0">
                        <div class="flex items-center">
                            <div
                                class="relative z-10 w-12 h-12 bg-gray-600 rounded-full flex items-center justify-center text-white shadow-lg flex-shrink-0 border-2 border-gray-800">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.221 69.17 69.17 0 00-2.923.897M6 10.5v5.5a2.25 2.25 0 002.25 2.25h11.5a2.25 2.25 0 002.25-2.25v-5.5" />
                                </svg>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="bg-gray-700 rounded-lg p-4 border-l-4 border-gray-500 shadow-lg">
                                    <h3 class="text-base font-bold text-white">Resultados</h3>
                                    <p class="text-unmsm-dorado font-bold text-xs">28 Ago - 05 Sep</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Desktop -->
                <div class="hidden lg:block">
                    <div class="absolute top-24 left-0 right-0 h-1 bg-gray-600 z-0"></div>
                    <div class="grid grid-cols-4 gap-6 relative z-10">

                        <!-- Card 1 -->
                        <div class="relative group">
                            <div
                                class="bg-white rounded-xl p-5 border-b-4 border-unmsm-guinda shadow-lg h-48 flex flex-col justify-start transition-all duration-300 hover:-translate-y-1 hover:shadow-xl text-gray-800">
                                <div class="text-center w-full">
                                    <div
                                        class="w-14 h-14 bg-unmsm-guinda rounded-full flex items-center justify-center text-white mx-auto mb-3 shadow-lg group-hover:scale-110 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold mb-1">Inscripción</h3>
                                    <p class="text-unmsm-guinda font-bold text-xs mb-1">15 Jul - 08 Ago</p>
                                    <p class="text-gray-500 text-[10px]">Pago en Bco. Nación</p>
                                </div>
                            </div>
                            <div
                                class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-5 h-5 bg-unmsm-guinda rounded-full border-4 border-gray-800 shadow-lg z-20">
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="relative group">
                            <div
                                class="bg-gray-700 rounded-xl p-5 border-b-4 border-gray-500 shadow-lg h-48 flex flex-col justify-start transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:bg-gray-600">
                                <div class="text-center w-full">
                                    <div
                                        class="w-14 h-14 bg-gray-600 rounded-full flex items-center justify-center text-white mx-auto mb-3 shadow-lg group-hover:scale-110 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold mb-1">Expediente</h3>
                                    <p class="text-unmsm-dorado font-bold text-xs mb-1">09 Ago - 15 Ago</p>
                                    <p class="text-gray-400 text-[10px]">Carga PDF</p>
                                </div>
                            </div>
                            <div
                                class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-5 h-5 bg-gray-400 rounded-full border-4 border-gray-800 shadow-lg z-20">
                            </div>
                        </div>

                        <!-- Card 3 -->
                        <div class="relative group">
                            <div
                                class="bg-gray-700 rounded-xl p-5 border-b-4 border-gray-500 shadow-lg h-48 flex flex-col justify-start transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:bg-gray-600">
                                <div class="text-center w-full">
                                    <div
                                        class="w-14 h-14 bg-gray-600 rounded-full flex items-center justify-center text-white mx-auto mb-3 shadow-lg group-hover:scale-110 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold mb-1">Evaluación</h3>
                                    <p class="text-unmsm-dorado font-bold text-xs mb-1">20 Ago - 25 Ago</p>
                                    <p class="text-gray-400 text-[10px]">Entrevista y examen</p>
                                </div>
                            </div>
                            <div
                                class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-5 h-5 bg-gray-400 rounded-full border-4 border-gray-800 shadow-lg z-20">
                            </div>
                        </div>

                        <!-- Card 4 -->
                        <div class="relative group">
                            <div
                                class="bg-gray-700 rounded-xl p-5 border-b-4 border-gray-500 shadow-lg h-48 flex flex-col justify-start transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:bg-gray-600">
                                <div class="text-center w-full">
                                    <div
                                        class="w-14 h-14 bg-gray-600 rounded-full flex items-center justify-center text-white mx-auto mb-3 shadow-lg group-hover:scale-110 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.499 5.221 69.17 69.17 0 00-2.923.897M6 10.5v5.5a2.25 2.25 0 002.25 2.25h11.5a2.25 2.25 0 002.25-2.25v-5.5" />
                                        </svg>
                                    </div>
                                    <h3 class="text-base font-bold mb-1">Resultados</h3>
                                    <p class="text-unmsm-dorado font-bold text-xs mb-1">28 Ago - 05 Sep</p>
                                    <p class="text-gray-400 text-[10px]">Matrícula</p>
                                </div>
                            </div>
                            <div
                                class="absolute -top-3 left-1/2 transform -translate-x-1/2 w-5 h-5 bg-gray-400 rounded-full border-4 border-gray-800 shadow-lg z-20">
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Botón Principal -->
            <div class="flex flex-col items-center mt-8">
                <button
                    class="bg-gradient-to-r from-unmsm-guinda to-red-900 hover:from-red-800 hover:to-unmsm-guinda text-white px-8 py-3 rounded-xl font-bold transition-all duration-300 transform hover:scale-105 shadow-2xl flex items-center gap-3 border border-red-800/50">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                    </svg>
                    Iniciar Inscripción
                </button>
            </div>
        </div>
    </section>
    <!-- MISIÓN Y VISIÓN -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div
                    class="bg-white p-10 rounded-2xl shadow-sm border-t-4 border-unmsm-guinda relative overflow-hidden group hover:shadow-xl transition-shadow">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-32 h-32 text-unmsm-guinda">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zM12 2.25V4.5m5.834.166l-1.591 1.591M20.25 10.5H18M7.757 14.743l-1.59 1.59M6 10.5H3.75m4.007-4.243l-1.59-1.59" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-unmsm-guinda mb-4 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                        </svg>
                        Misión
                    </h3>
                    <p class="text-gray-600 leading-relaxed text-justify">
                        Formar profesionales humanistas altamente especializados en investigación rigurosa, pensamiento
                        crítico y producción de conocimiento, capaces de responder a los desafíos culturales, sociales y
                        académicos del país y la región.
                    </p>
                </div>

                <div
                    class="bg-white p-10 rounded-2xl shadow-sm border-t-4 border-blue-900 relative overflow-hidden group hover:shadow-xl transition-shadow">
                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-32 h-32 text-blue-900">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-serif font-bold text-blue-900 mb-4 flex items-center gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Visión
                    </h3>
                    <p class="text-gray-600 leading-relaxed text-justify">
                        Ser un referente nacional e internacional en estudios humanísticos, consolidando una comunidad
                        académica innovadora, crítica y comprometida con la transformación de la realidad desde las letras y
                        las ciencias humanas.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- PROGRAMAS ACADÉMICOS (REDISEÑO CON BADGES DENTRO) -->
    <section id="programas" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <!-- Encabezado -->
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-5xl font-bold text-unmsm-guinda mb-6 font-serif">Nuestros Programas</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                    Excelencia académica para investigadores y líderes en Humanidades.
                </p>
            </div>

            <!-- Filtros -->
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <button onclick="filterPrograms('todos')" id="filter-todos"
                    class="filter-btn flex items-center gap-2 px-6 py-2.5 rounded-full font-semibold transition-all duration-300 bg-unmsm-guinda text-white shadow-lg transform scale-105">
                    <i class="fas fa-globe"></i> Todos
                </button>
                <button onclick="filterPrograms('maestria')" id="filter-maestria"
                    class="filter-btn flex items-center gap-2 px-6 py-2.5 rounded-full font-semibold transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100 shadow-sm">
                    <i class="fas fa-graduation-cap"></i> Maestrías
                </button>
                <button onclick="filterPrograms('doctorado')" id="filter-doctorado"
                    class="filter-btn flex items-center gap-2 px-6 py-2.5 rounded-full font-semibold transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100 shadow-sm">
                    <i class="fas fa-book-reader"></i> Doctorados
                </button>
            </div>

            <!-- Grid de Programas -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6" id="programas-grid">
                <!-- Maestrías -->
                @foreach($maestrias as $programa)
                    <article
                        class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden h-full program-card"
                        data-type="maestria">
                        <a href="{{ route('programas.show', $programa->slug ?? $programa->codigo) }}" class="block">
                            <div class="h-60 relative overflow-hidden">
                                <!-- Imagen -->
                                <img src="{{ $programa->imagen_url }}" alt="{{ $programa->nombre }}"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">

                                <!-- Gradiente de fondo para contraste -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-90">
                                </div>

                                <!-- "Ver más detalle" Overlay (Aparece en Hover) -->
                                <div
                                    class="absolute inset-0 flex items-center justify-center z-30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px]">
                                    <span
                                        class="px-6 py-2 border border-white text-white font-bold rounded-full hover:bg-white hover:text-unmsm-guinda transition-colors shadow-2xl transform scale-90 group-hover:scale-100 duration-300">
                                        Ver más detalle
                                    </span>
                                </div>

                                <!-- Badge Tipo (Arriba) -->
                                <div class="absolute top-4 left-4 z-20">
                                    <span
                                        class="px-3 py-1 bg-unmsm-guinda text-white text-xs font-bold rounded shadow-lg">Maestría</span>
                                </div>

                                <!-- Badges Info (Abajo, dentro de la imagen) -->
                                <div
                                    class="absolute bottom-4 left-4 right-4 z-20 flex flex-wrap gap-2 transition-opacity duration-300 group-hover:opacity-10">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-unmsm-dorado text-unmsm-guinda text-xs font-bold rounded-full shadow-lg">
                                        <i class="far fa-clock"></i> 4 semestres
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 text-gray-800 text-xs font-bold rounded-full shadow-lg">
                                        <i class="fas fa-university"></i> Presencial
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3
                                    class="text-xl font-serif font-bold text-gray-800 mb-3 group-hover:text-unmsm-guinda transition-colors leading-tight">
                                    {{ $programa->nombre }}
                                </h3>
                                <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                    {{ $programa->sumilla ?? $programa->presentacion ?? 'Formación especializada con enfoque en investigación y desarrollo profesional.' }}
                                </p>
                            </div>
                        </a>
                    </article>
                @endforeach

                <!-- Doctorados -->
                @foreach($doctorados as $programa)
                    <article
                        class="group relative bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden h-full program-card"
                        data-type="doctorado">
                        <a href="{{ route('programas.show', $programa->slug ?? $programa->codigo) }}" class="block">
                            <div class="h-60 relative overflow-hidden">
                                <!-- Imagen -->
                                <img src="{{ $programa->imagen_url }}" alt="{{ $programa->nombre }}"
                                    class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">

                                <!-- Gradiente de fondo para contraste -->
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-90">
                                </div>

                                <!-- "Ver más detalle" Overlay (Aparece en Hover) -->
                                <div
                                    class="absolute inset-0 flex items-center justify-center z-30 opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px]">
                                    <span
                                        class="px-6 py-2 border border-white text-white font-bold rounded-full hover:bg-white hover:text-unmsm-guinda transition-colors shadow-2xl transform scale-90 group-hover:scale-100 duration-300">
                                        Ver más detalle
                                    </span>
                                </div>

                                <!-- Badge Tipo (Arriba) -->
                                <div class="absolute top-4 left-4 z-20">
                                    <span
                                        class="px-3 py-1 bg-gray-900 text-white text-xs font-bold rounded shadow-lg">Doctorado</span>
                                </div>

                                <!-- Badges Info (Abajo, dentro de la imagen) -->
                                <div
                                    class="absolute bottom-4 left-4 right-4 z-20 flex flex-wrap gap-2 transition-opacity duration-300 group-hover:opacity-10">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-unmsm-dorado text-unmsm-guinda text-xs font-bold rounded-full shadow-lg">
                                        <i class="far fa-clock"></i> 6 semestres
                                    </span>
                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 text-gray-800 text-xs font-bold rounded-full shadow-lg">
                                        <i class="fas fa-university"></i> Presencial
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <h3
                                    class="text-xl font-serif font-bold text-gray-800 mb-3 group-hover:text-unmsm-guinda transition-colors leading-tight">
                                    {{ $programa->nombre }}
                                </h3>
                                <p class="text-sm text-gray-600 leading-relaxed line-clamp-3">
                                    {{ $programa->sumilla ?? $programa->presentacion ?? 'Investigación de alto nivel para la generación de nuevo conocimiento.' }}
                                </p>
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>

        </div>
    </section>

    <script>
        // FILTRADO DE PROGRAMAS
        function filterPrograms(type) {
            const cards = document.querySelectorAll('.program-card');
            const buttons = document.querySelectorAll('.filter-btn');

            // Reset botones
            buttons.forEach(btn => {
                btn.classList.remove('bg-unmsm-guinda', 'text-white', 'shadow-lg', 'scale-105');
                btn.classList.add('bg-white', 'text-gray-600', 'hover:bg-gray-100');
            });

            // Activar botón seleccionado
            const activeBtn = document.getElementById('filter-' + type);
            if (activeBtn) {
                activeBtn.classList.remove('bg-white', 'text-gray-600', 'hover:bg-gray-100');
                activeBtn.classList.add('bg-unmsm-guinda', 'text-white', 'shadow-lg', 'scale-105');
            }

            // Filtrar cards con animación
            cards.forEach(card => {
                if (type === 'todos' || card.dataset.type === type) {
                    card.classList.remove('hidden');
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                } else {
                    card.classList.add('hidden');
                }
            });
        }
    </script>

    <!-- PLANA DOCENTE -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <h2 class="text-center section-title font-serif text-3xl font-bold mb-12">Plana Docente Destacada</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                @forelse($docentes as $docente)
                    <div class="text-center group cursor-pointer">
                        <div
                            class="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-4 border-gray-100 group-hover:border-unmsm-guinda transition-colors relative">
                            <img src="@if($docente->foto){{ asset('storage/' . $docente->foto) }}@else{{ 'https://ui-avatars.com/api/?name=' . urlencode($docente->nombres . '+' . $docente->apellidos) . '&background=random' }}@endif"
                                alt="{{ $docente->nombre_completo }}"
                                class="w-full h-full object-cover filter grayscale group-hover:grayscale-0 transition duration-500">
                        </div>
                        <h4 class="font-bold text-sm text-gray-800 group-hover:text-unmsm-guinda transition">
                            {{ $docente->nombre_completo }}
                        </h4>
                        <p class="text-xs text-gray-500">{{ $docente->grado ?? 'Docente' }}</p>
                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-500">
                        No hay docentes disponibles
                    </div>
                @endforelse
            </div>
        </div>
    </section>



    <!-- CONTACTO & FOOTER -->
    <section id="contacto" class="bg-gray-900 text-gray-300 py-16 border-t border-gray-800">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h3 class="text-white text-2xl font-serif font-bold mb-6 section-title">Contáctanos</h3>
                    <p class="mb-8 font-light">Estamos a tu disposición para resolver cualquier duda sobre nuestros
                        programas y procesos.</p>

                    <ul class="space-y-6">
                        <li class="flex items-center gap-4 group">
                            <div
                                class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center group-hover:bg-unmsm-guinda transition-colors text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-gray-500">Email</span>
                                <a href="mailto:posgrado.letras@unmsm.edu.pe"
                                    class="text-white hover:text-unmsm-dorado transition">posgrado.letras@unmsm.edu.pe</a>
                            </div>
                        </li>
                        <li class="flex items-center gap-4 group">
                            <div
                                class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center group-hover:bg-green-600 transition-colors text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-gray-500">WhatsApp</span>
                                <a href="#" class="text-white hover:text-green-500 transition">+51 982 085 037</a>
                            </div>
                        </li>
                        <li class="flex items-center gap-4 group">
                            <div
                                class="w-10 h-10 rounded bg-gray-800 flex items-center justify-center group-hover:bg-blue-600 transition-colors text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-gray-500">Ubicación</span>
                                <p class="text-white">Ciudad Universitaria, Av. Venezuela s/n, Lima.</p>
                            </div>
                        </li>
                    </ul>

                    <div class="mt-8 flex gap-4">
                        <a href="#"
                            class="w-10 h-10 rounded bg-white/5 flex items-center justify-center hover:bg-blue-600 transition text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded bg-white/5 flex items-center justify-center hover:bg-sky-500 transition text-white">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417a9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Mapa -->
                <div class="rounded-xl overflow-hidden bg-gray-800 h-[350px] shadow-lg border border-gray-700">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d487.7251388091363!2d-77.08159160793049!3d-12.057201313094351!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c9470823c4f5%3A0xc528a60911019861!2sFacultad%20de%20Letras%20y%20Ciencias%20Humanas%20-%20UNMSM!5e0!3m2!1ses!2spe!4v1764687672723!5m2!1ses!2spe"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </div>
    </section>
@endsection
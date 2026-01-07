@extends('layouts.public')

@section('title', $profesor->nombre_completo . ' - Posgrado Letras UNMSM')

@push('styles')
    <style>
        /* Tipografía serif para títulos */
        h1, h2, h3, .font-serif { font-family: 'Merriweather', serif; }
    </style>
@endpush

@section('content')
    <!-- HERO DE SECCIÓN (Estilo uniforme) -->
    <x-hero-section 
        title="{{ $profesor->nombre_completo }}" 
        label="Profesor Asesor"
        subtitle="{{ $profesor->grado ?? 'Docente investigador de la Facultad de Letras y Ciencias Humanas' }}"
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2025/12/DJI_0007-Trim-frame-at-0m5s.jpg" />

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-4 gap-8">

            <!-- SIDEBAR: Tarjeta de Perfil -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6 flex flex-col items-center lg:sticky lg:top-36 group/card hover:shadow-xl transition-shadow duration-300">

                    <!-- Avatar con efecto glow -->
                    <div class="relative w-32 h-32 mb-4">
                        <div class="absolute inset-0 bg-unmsm-dorado rounded-full blur-md opacity-30 group-hover/card:opacity-50 transition-opacity"></div>
                        @if($profesor->foto)
                            <img src="{{ asset('storage/' . $profesor->foto) }}"
                                 alt="{{ $profesor->nombre_completo }}"
                                 class="relative w-full h-full rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="relative w-full h-full rounded-full bg-gray-100 border-4 border-white shadow-lg flex items-center justify-center text-unmsm-guinda">
                                <i class="fas fa-user text-4xl"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Nombre -->
                    <h2 class="text-lg font-bold text-gray-900 text-center font-serif">
                        {{ $profesor->nombre_completo }}
                    </h2>

                    <!-- Grado Académico (Badge) -->
                    @if($profesor->grado)
                        <span class="inline-block bg-unmsm-guinda/10 text-unmsm-guinda text-xs font-bold px-3 py-1 rounded-full mt-2">
                            {{ $profesor->grado }}
                        </span>
                    @endif

                    <!-- Separador -->
                    <div class="w-full border-t border-gray-100 my-5"></div>

                    <!-- Datos de contacto -->
                    <div class="w-full space-y-2">
                        @if($profesor->email)
                            <a href="mailto:{{ $profesor->email }}"
                               class="flex items-center gap-3 text-sm border border-gray-200 px-4 py-3 rounded-xl
                                      text-gray-700 hover:bg-unmsm-guinda hover:text-white hover:border-unmsm-guinda
                                      transition-all group">
                                <i class="fas fa-envelope text-unmsm-guinda group-hover:text-white"></i>
                                <span class="truncate">{{ $profesor->email }}</span>
                            </a>
                        @endif

                        @if($profesor->telefono)
                            <div class="flex items-center gap-3 text-sm border border-gray-200 px-4 py-3 rounded-xl text-gray-700 bg-gray-50">
                                <i class="fas fa-phone text-unmsm-guinda"></i>
                                <span>{{ $profesor->telefono }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Perfiles Académicos -->
                    @if($profesor->linkedin || $profesor->orcid || $profesor->cti_vitae)
                        <div class="w-full mt-5">
                            <p class="text-[11px] font-bold text-gray-400 uppercase mb-3 tracking-widest">
                                Perfiles Académicos
                            </p>
                            <div class="flex flex-col gap-3">
                                @if($profesor->cti_vitae)
                                    <a href="{{ $profesor->cti_vitae }}" target="_blank" 
                                       class="flex items-center gap-3 text-sm border border-blue-100 bg-blue-50/50 px-4 py-2.5 rounded-xl text-blue-700 hover:bg-blue-700 hover:text-white hover:border-blue-700 transition-all group">
                                        <i class="fas fa-file-alt text-lg w-6 text-center"></i>
                                        <span class="font-medium">CTI Vitae</span>
                                        <i class="fas fa-external-link-alt ml-auto text-xs opacity-50 group-hover:opacity-100"></i>
                                    </a>
                                @endif
                                @if($profesor->orcid)
                                    <a href="{{ $profesor->orcid }}" target="_blank" 
                                       class="flex items-center gap-3 text-sm border border-green-100 bg-green-50/50 px-4 py-2.5 rounded-xl text-green-700 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all group">
                                        <i class="fab fa-orcid text-lg w-6 text-center"></i>
                                        <span class="font-medium">ORCID</span>
                                        <i class="fas fa-external-link-alt ml-auto text-xs opacity-50 group-hover:opacity-100"></i>
                                    </a>
                                @endif
                                @if($profesor->linkedin)
                                    <a href="{{ $profesor->linkedin }}" target="_blank" 
                                       class="flex items-center gap-3 text-sm border border-blue-100 bg-blue-50/50 px-4 py-2.5 rounded-xl text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all group">
                                        <i class="fab fa-linkedin text-lg w-6 text-center"></i>
                                        <span class="font-medium">LinkedIn</span>
                                        <i class="fas fa-external-link-alt ml-auto text-xs opacity-50 group-hover:opacity-100"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Botón volver -->
                    <div class="w-full mt-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('profesores.index') }}"
                           class="inline-flex items-center text-sm font-medium text-unmsm-guinda hover:text-unmsm-dorado transition-colors group">
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                            Volver al listado
                        </a>
                    </div>
                </div>
            </aside>

            <!-- COLUMNA PRINCIPAL: Información Detallada -->
            <section class="lg:col-span-3 space-y-6">
                
                <!-- Card Biografía -->
                <article class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8 hover:shadow-lg transition-shadow duration-300">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3 font-serif">
                        <div class="w-10 h-10 rounded-full bg-unmsm-guinda/10 flex items-center justify-center">
                            <i class="fas fa-user-circle text-unmsm-guinda"></i>
                        </div>
                        Biografía
                    </h2>
                    <div class="text-sm leading-relaxed text-gray-700 text-justify">
                        @if($profesor->biografia)
                            {!! nl2br(e($profesor->biografia)) !!}
                        @else
                            <p class="text-gray-500 italic">Información biográfica no disponible.</p>
                        @endif
                    </div>
                </article>

                <!-- Card Líneas de Investigación -->
                @php
                    $lineas = $profesor->lineas_investigacion;
                    $hasLineas = !empty($lineas) && (is_array($lineas) ? count($lineas) > 0 : strlen(trim($lineas)) > 0);
                @endphp
                @if($hasLineas)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8 hover:shadow-lg transition-shadow duration-300">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3 font-serif">
                            <div class="w-10 h-10 rounded-full bg-unmsm-guinda/10 flex items-center justify-center">
                                <i class="fas fa-flask text-unmsm-guinda"></i>
                            </div>
                            Líneas de Investigación
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $lineasArray = is_array($lineas) ? $lineas : explode(',', $lineas);
                            @endphp
                            @foreach($lineasArray as $linea)
                                <span class="bg-gray-100 border border-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium">
                                    {{ trim($linea) }}
                                </span>
                            @endforeach
                        </div>
                    </article>
                @endif

                <!-- Card Grupo de Investigación -->
                @if($profesor->grupo_investigacion)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8 hover:shadow-lg transition-shadow duration-300">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3 font-serif">
                            <div class="w-10 h-10 rounded-full bg-unmsm-guinda/10 flex items-center justify-center">
                                <i class="fas fa-users text-unmsm-guinda"></i>
                            </div>
                            Grupo de Investigación
                        </h3>
                        <div class="bg-gradient-to-r from-unmsm-guinda/5 to-transparent rounded-xl p-5 border-l-4 border-unmsm-guinda">
                            <p class="text-gray-800 font-semibold flex items-center gap-2">
                                <i class="fas fa-bookmark text-unmsm-dorado"></i>
                                @php
                                    $grupo = $profesor->grupo_investigacion;
                                    $nombreGrupo = is_array($grupo) ? ($grupo['nombre'] ?? '') : $grupo;
                                    $linkGrupo = is_array($grupo) ? ($grupo['link'] ?? '') : null;
                                @endphp

                                @if($linkGrupo)
                                    <a href="{{ $linkGrupo }}" target="_blank" class="hover:text-unmsm-guinda transition-colors underline decoration-unmsm-dorado/40">
                                        {{ $nombreGrupo }}
                                        <i class="fas fa-external-link-alt text-[10px] ml-1 opacity-50"></i>
                                    </a>
                                @else
                                    {{ $nombreGrupo }}
                                @endif
                            </p>
                        </div>
                    </article>
                @endif

                <!-- Card Programas de Posgrado -->
                @if($profesor->programas && $profesor->programas->count() > 0)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8 hover:shadow-lg transition-shadow duration-300">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-3 font-serif">
                            <div class="w-10 h-10 rounded-full bg-unmsm-guinda/10 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-unmsm-guinda"></i>
                            </div>
                            Programas de Posgrado
                        </h3>
                        <div class="grid md:grid-cols-2 gap-4">
                            @foreach($profesor->programas as $programa)
                                <a href="{{ route('programas.show', $programa->slug) }}"
                                   class="flex items-center justify-between bg-gray-50 rounded-xl border border-gray-200 p-4 hover:border-unmsm-guinda hover:bg-white hover:shadow-md transition-all group">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 group-hover:text-unmsm-guinda transition-colors">
                                            {{ $programa->nombre }}
                                        </p>
                                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-0.5">
                                            <p class="text-xs text-gray-500">{{ $programa->grado }}</p>
                                            @if($programa->pivot->rol)
                                                <span class="text-[10px] text-gray-400">•</span>
                                                <p class="text-xs font-medium text-unmsm-guinda/70 italic">{{ $programa->pivot->rol }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    @if($programa->pivot->es_coordinador)
                                        <span class="text-[10px] bg-unmsm-dorado text-white rounded-full px-3 py-1 font-bold uppercase shadow-sm">
                                            Coordinador
                                        </span>
                                    @else
                                        <i class="fas fa-arrow-right text-gray-400 group-hover:text-unmsm-guinda group-hover:translate-x-1 transition-all"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </article>
                @endif

            </section>
        </div>
    </div>
@endsection

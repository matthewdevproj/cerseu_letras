@extends('layouts.public')

@section('title', $profesor->nombre_completo . ' - Posgrado Letras UNMSM')

@section('content')
    <!-- HERO DE SECCIÓN (igual que profesores index y programas) -->
    <section class="relative w-full h-[40vh] min-h-[320px] flex items-center justify-center bg-gray-900 overflow-hidden">
        <!-- Imagen de Fondo -->
        <div class="absolute inset-0 opacity-50">
            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1920&auto=format&fit=crop"
                alt="Profesores" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-gray-900/90"></div>
        </div>

        <!-- Texto Hero -->
        <div class="relative z-10 text-center text-white px-4 mt-20">
            <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">Profesor Asesor</p>
            <h1 class="text-3xl md:text-5xl font-serif font-bold mb-4 drop-shadow-lg">{{ $profesor->nombre_completo }}</h1>
            <p class="text-gray-200 max-w-2xl mx-auto font-light text-lg leading-relaxed">
                {{ $profesor->especialidad ?? 'Docente investigador de la Facultad de Letras y Ciencias Humanas' }}
            </p>
        </div>
    </section>

    <!-- CONTENIDO PRINCIPAL -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-4 gap-8">

            <!-- Columna izquierda: tarjeta de perfil / contacto -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center sticky top-28">

                    <!-- Avatar circular -->
                    <div class="w-28 h-28 rounded-full bg-gray-100 flex items-center justify-center text-unmsm-guinda text-3xl font-bold mb-4 overflow-hidden border-4 border-white shadow-lg">
                        @if($profesor->foto)
                            <img src="{{ asset('storage/' . $profesor->foto) }}"
                                 alt="{{ $profesor->nombres }}"
                                 class="w-full h-full object-cover">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        @endif
                    </div>

                    <!-- Nombre -->
                    <h2 class="text-lg font-bold text-gray-900 text-center">
                        {{ $profesor->nombre_completo }}
                    </h2>

                    <!-- Especialidad -->
                    @if($profesor->especialidad)
                        <p class="text-sm text-unmsm-dorado font-medium mt-1 text-center">
                            {{ $profesor->especialidad }}
                        </p>
                    @endif

                    <!-- Programas donde participa -->
                    @if($profesor->programas && $profesor->programas->count() > 0)
                        <div class="mt-4 w-full">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase mb-2 tracking-wide">
                                Programas de posgrado
                            </p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($profesor->programas as $programa)
                                    <span class="inline-flex items-center rounded-full border border-unmsm-guinda/20
                                                  px-3 py-1 text-[11px] font-medium text-unmsm-guinda bg-unmsm-guinda/5">
                                        {{ $programa->nombre }}
                                        @if($programa->pivot->es_coordinador)
                                            <span class="ml-1 text-[9px] uppercase font-bold text-unmsm-dorado">
                                                (Coord.)
                                            </span>
                                        @endif
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Separador -->
                    <div class="w-full border-t border-gray-100 my-5"></div>

                    <!-- Datos de contacto -->
                    <div class="w-full space-y-2">
                        @if($profesor->email)
                            <a href="mailto:{{ $profesor->email }}"
                               class="flex items-center gap-3 text-sm border border-gray-200 px-4 py-3 rounded-lg
                                      text-gray-700 hover:bg-unmsm-guinda hover:text-white hover:border-unmsm-guinda
                                      transition-all group">
                                <i class="fas fa-envelope text-unmsm-guinda group-hover:text-white"></i>
                                <span class="truncate">{{ $profesor->email }}</span>
                            </a>
                        @endif

                        @if($profesor->telefono)
                            <div class="flex items-center gap-3 text-sm border border-gray-200 px-4 py-3 rounded-lg text-gray-700 bg-gray-50">
                                <i class="fas fa-phone text-unmsm-guinda"></i>
                                <span>{{ $profesor->telefono }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Redes sociales -->
                    @if($profesor->linkedin || $profesor->orcid || $profesor->cti_vitae)
                        <div class="w-full mt-4">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase mb-2 tracking-wide">
                                Perfiles académicos
                            </p>
                            <div class="flex gap-2">
                                @if($profesor->linkedin)
                                    <a href="{{ $profesor->linkedin }}" target="_blank"
                                       class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all"
                                       title="LinkedIn">
                                        <i class="fab fa-linkedin-in"></i>
                                    </a>
                                @endif

                                @if($profesor->orcid)
                                    <a href="{{ $profesor->orcid }}" target="_blank"
                                       class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all"
                                       title="ORCID">
                                        <i class="fab fa-orcid"></i>
                                    </a>
                                @endif

                                @if($profesor->cti_vitae)
                                    <a href="{{ $profesor->cti_vitae }}" target="_blank"
                                       class="w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-blue-700 hover:text-white hover:border-blue-700 transition-all"
                                       title="CTI Vitae">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Botón volver -->
                    <div class="w-full mt-6 pt-4 border-t border-gray-100">
                        <a href="{{ route('profesores.index') }}"
                           class="inline-flex items-center text-sm font-medium text-unmsm-guinda hover:text-unmsm-dorado transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Volver al listado
                        </a>
                    </div>
                </div>
            </aside>

            <!-- Columna derecha: biografía y detalle académico -->
            <section class="lg:col-span-3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 md:p-8 space-y-6">

                    <!-- Biografía -->
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-user-circle text-unmsm-guinda"></i>
                            Biografía
                        </h2>
                        <div class="text-sm leading-relaxed text-gray-700 text-justify">
                            @if($profesor->biografia)
                                {!! nl2br(e($profesor->biografia)) !!}
                            @else
                                <p class="text-gray-500 italic">Información biográfica no disponible.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Líneas de investigación -->
                    @if($profesor->lineas_investigacion && count($profesor->lineas_investigacion) > 0)
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-flask text-unmsm-guinda"></i>
                                Líneas de investigación
                            </h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-100">
                                <ul class="list-disc list-inside text-sm text-gray-700 space-y-2">
                                    @foreach($profesor->lineas_investigacion as $linea)
                                        <li>{{ $linea }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <!-- Grupo de investigación -->
                    @if($profesor->grupo_investigacion)
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                <i class="fas fa-users text-unmsm-guinda"></i>
                                Grupo de investigación
                            </h3>
                            <div class="bg-unmsm-guinda/5 rounded-lg p-4 border border-unmsm-guinda/10">
                                <p class="text-sm text-gray-700 font-medium">
                                    {{ $profesor->grupo_investigacion }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <!-- Programas de posgrado -->
                    @if($profesor->programas && $profesor->programas->count() > 0)
                        <div class="border-t border-gray-100 pt-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-graduation-cap text-unmsm-guinda"></i>
                                Programas de posgrado
                            </h3>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($profesor->programas as $programa)
                                    <a href="{{ route('programas.show', $programa->slug ?? $programa->codigo) }}"
                                       class="flex items-center justify-between bg-white rounded-lg border border-gray-200 p-4 hover:border-unmsm-guinda hover:shadow-md transition-all group">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-unmsm-guinda transition-colors">
                                                {{ $programa->nombre }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $programa->grado }}</p>
                                        </div>
                                        @if($programa->pivot->es_coordinador)
                                            <span class="text-[10px] bg-unmsm-dorado text-white rounded-full px-2 py-1 font-bold uppercase">
                                                Coordinador
                                            </span>
                                        @else
                                            <i class="fas fa-arrow-right text-gray-400 group-hover:text-unmsm-guinda transition-colors"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </section>
        </div>
    </div>
@endsection

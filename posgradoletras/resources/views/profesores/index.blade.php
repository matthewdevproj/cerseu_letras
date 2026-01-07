@extends('layouts.public')

@section('title', 'Plana Docente - Posgrado Letras UNMSM')

@push('styles')
    <style>
        .fade-in {
            animation: fadeIn 0.4s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@section('content')

    <!-- HERO DE SECCIÓN -->
    <x-hero-section 
        title="Profesores Asesores" 
        label="Plana Docente"
        subtitle="Investigadores de alto nivel comprometidos con la excelencia académica."
        image="https://letras.unmsm.edu.pe/wp-content/uploads/2020/11/frontis-letras.jpg" />

    <!-- LAYOUT SIDEBAR + CONTENIDO -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-4 gap-8">

            <!-- SIDEBAR: Navegación de Programas -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-36">
                    <h2 class="text-xs font-bold text-gray-400 mb-4 uppercase tracking-widest border-b border-gray-100 pb-2">
                        Programas
                    </h2>
                    
                    <!-- Maestrías -->
                    <div class="mb-4">
                        <p class="text-xs font-bold text-unmsm-dorado mb-2 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-graduation-cap"></i> Maestrías
                        </p>
                        <div class="space-y-1">
                            @foreach($maestrias as $prog)
                                @php
                                    $isActive = isset($selectedPrograma) && $selectedPrograma && $selectedPrograma->id === $prog->id;
                                @endphp
                                <a href="{{ route('profesores.programa', ['slug' => $prog->slug]) }}" 
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all {{ $isActive 
                                       ? 'bg-unmsm-guinda text-white shadow-md' 
                                       : 'text-gray-600 hover:bg-gray-50 hover:text-unmsm-guinda group' }}">
                                    <i class="fas fa-book text-sm {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-unmsm-guinda' }}"></i>
                                    <span class="text-sm font-medium leading-tight">{{ $prog->nombre }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Doctorados -->
                    <div>
                        <p class="text-xs font-bold text-gray-800 mb-2 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-user-graduate"></i> Doctorados
                        </p>
                        <div class="space-y-1">
                            @foreach($doctorados as $prog)
                                @php
                                    $isActive = isset($selectedPrograma) && $selectedPrograma && $selectedPrograma->id === $prog->id;
                                @endphp
                                <a href="{{ route('profesores.programa', ['slug' => $prog->slug]) }}" 
                                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg transition-all {{ $isActive 
                                       ? 'bg-unmsm-guinda text-white shadow-md' 
                                       : 'text-gray-600 hover:bg-gray-50 hover:text-unmsm-guinda group' }}">
                                    <i class="fas fa-book text-sm {{ $isActive ? 'text-white' : 'text-gray-400 group-hover:text-unmsm-guinda' }}"></i>
                                    <span class="text-sm font-medium leading-tight">{{ $prog->nombre }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </aside>

            <!-- CONTENIDO PRINCIPAL: LISTA DE PROFESORES -->
            <section class="lg:col-span-3 min-h-[500px]" id="main-content">

                @if(isset($selectedPrograma) && $selectedPrograma)
                    <!-- Contenido con programa seleccionado -->
                    <div class="fade-in">
                        <div class="mb-8 border-b border-gray-200 pb-4">

                            <h2 class="text-2xl md:text-3xl font-serif font-bold text-gray-900 mb-1">
                                {{ $selectedPrograma->nombre }}
                            </h2>
                            <p class="text-gray-500 text-sm">Plana Docente e Investigadores</p>
                        </div>

                        @if($profesores->count() > 0)
                            <div class="space-y-6">
                                @foreach($profesores as $profesor)
                                    @php
                                        $lineas = $profesor->lineas_investigacion;
                                        $hasLineas = !empty($lineas) && (is_array($lineas) ? count($lineas) > 0 : strlen(trim($lineas)) > 0);
                                        $lineasArray = is_array($lineas) ? $lineas : ($lineas ? explode(',', $lineas) : []);
                                    @endphp
                                    
                                    <article class="bg-white border border-gray-200 rounded-2xl p-6 md:p-8 hover:shadow-xl hover:border-unmsm-guinda/30 transition-all duration-300 group relative overflow-hidden">
                                        
                                        <!-- Decoración Hover (barra izquierda) -->
                                        <div class="absolute top-0 left-0 w-1 h-full bg-unmsm-guinda opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                                        <div class="flex flex-col md:flex-row gap-6">
                                            
                                            <!-- Columna Foto + Botones Sociales -->
                                            <div class="flex-shrink-0 flex flex-col items-center space-y-3">
                                                <div class="relative w-24 h-24 md:w-28 md:h-28">
                                                    <div class="absolute inset-0 bg-unmsm-dorado rounded-full blur opacity-20 group-hover:opacity-40 transition-opacity"></div>
                                                    @if($profesor->foto)
                                                        <img src="{{ asset('storage/' . $profesor->foto) }}" 
                                                             alt="{{ $profesor->nombre_completo }}" 
                                                             class="relative w-full h-full rounded-full object-cover border-4 border-white shadow-md group-hover:scale-105 transition-transform duration-500">
                                                    @else
                                                        <div class="relative w-full h-full rounded-full bg-gray-100 border-4 border-white shadow-md flex items-center justify-center text-unmsm-guinda group-hover:scale-105 transition-transform duration-500">
                                                            <i class="fas fa-user text-3xl"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                
                                                <!-- Botones sociales compactos debajo de la foto -->
                                                <div class="flex gap-2 justify-center w-full">
                                                    @if($profesor->cti_vitae)
                                                        <a href="{{ $profesor->cti_vitae }}" target="_blank" 
                                                           class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-colors text-xs" 
                                                           title="CTI Vitae">
                                                            <i class="fas fa-file-alt"></i>
                                                        </a>
                                                    @endif
                                                    @if($profesor->orcid)
                                                        <a href="{{ $profesor->orcid }}" target="_blank" 
                                                           class="w-8 h-8 flex items-center justify-center rounded-full bg-green-50 text-green-600 hover:bg-green-600 hover:text-white transition-colors text-xs" 
                                                           title="ORCID">
                                                            <i class="fab fa-orcid"></i>
                                                        </a>
                                                    @endif
                                                    @if($profesor->linkedin)
                                                        <a href="{{ $profesor->linkedin }}" target="_blank" 
                                                           class="w-8 h-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-700 hover:bg-blue-700 hover:text-white transition-colors text-xs" 
                                                           title="LinkedIn">
                                                            <i class="fab fa-linkedin-in"></i>
                                                        </a>
                                                    @endif
                                                    @if($profesor->email)
                                                        <a href="mailto:{{ $profesor->email }}" 
                                                           class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-50 text-gray-600 hover:bg-unmsm-guinda hover:text-white transition-colors text-xs" 
                                                           title="Email">
                                                            <i class="fas fa-envelope"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Columna Info -->
                                            <div class="flex-1 min-w-0">
                                                
                                                <!-- Header: Nombre + Link perfil -->
                                                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2 mb-3">
                                                    <div>
                                                        <h3 class="text-xl md:text-2xl font-bold text-gray-900 font-serif group-hover:text-unmsm-guinda transition-colors">
                                                            <a href="{{ route('profesores.show', $profesor->slug) }}" class="hover:underline">
                                                                {{ $profesor->nombre_completo }}
                                                            </a>
                                                        </h3>
                                                        @if($profesor->grado)
                                                            <span class="inline-block bg-unmsm-guinda/10 text-unmsm-guinda text-xs font-bold px-2 py-0.5 rounded mt-1">
                                                                {{ $profesor->grado }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('profesores.show', $profesor->slug) }}" 
                                                       class="hidden md:inline-flex items-center text-xs font-semibold text-gray-500 hover:text-unmsm-guinda transition group/link">
                                                        Ver perfil completo 
                                                        <i class="fas fa-arrow-right ml-1 transform group-hover/link:translate-x-1 transition-transform"></i>
                                                    </a>
                                                </div>

                                                <!-- Biografía -->
                                                @if($profesor->biografia)
                                                    <p class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                                        {{ $profesor->biografia }}
                                                    </p>
                                                @endif

                                                <!-- Enlaces visibles -->
                                                @if($profesor->cti_vitae || $profesor->orcid || $profesor->linkedin || $profesor->email)
                                                    <div class="flex flex-col gap-1.5 mb-4 text-xs">
                                                        @if($profesor->cti_vitae)
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-bold text-gray-500 w-16">CTI Vitae:</span>
                                                                <a href="{{ $profesor->cti_vitae }}" target="_blank" 
                                                                   class="flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline truncate">
                                                                    <span class="truncate">{{ $profesor->cti_vitae }}</span>
                                                                    <i class="fas fa-external-link-alt text-[10px] opacity-60 flex-shrink-0"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if($profesor->orcid)
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-bold text-gray-500 w-16">ORCID:</span>
                                                                <a href="{{ $profesor->orcid }}" target="_blank" 
                                                                   class="flex items-center gap-1 text-green-600 hover:text-green-800 hover:underline truncate">
                                                                    <span class="truncate">{{ $profesor->orcid }}</span>
                                                                    <i class="fas fa-external-link-alt text-[10px] opacity-60 flex-shrink-0"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if($profesor->linkedin)
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-bold text-gray-500 w-16">LinkedIn:</span>
                                                                <a href="{{ $profesor->linkedin }}" target="_blank" 
                                                                   class="flex items-center gap-1 text-blue-700 hover:text-blue-900 hover:underline truncate">
                                                                    <span class="truncate">{{ $profesor->linkedin }}</span>
                                                                    <i class="fas fa-external-link-alt text-[10px] opacity-60 flex-shrink-0"></i>
                                                                </a>
                                                            </div>
                                                        @endif
                                                        @if($profesor->email)
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-bold text-gray-500 w-16">Email:</span>
                                                                <a href="mailto:{{ $profesor->email }}" 
                                                                   class="text-unmsm-guinda hover:text-unmsm-dorado hover:underline">
                                                                    {{ $profesor->email }}
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- Grid de Datos (Líneas + Grupo) -->
                                                @if($hasLineas || $profesor->grupo_investigacion)
                                                    <div class="grid md:grid-cols-2 gap-4 text-sm bg-gray-50 rounded-lg p-4 mb-4 border border-gray-100">
                                                        @if($hasLineas)
                                                            <div>
                                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Líneas de Investigación</p>
                                                                <div class="flex flex-wrap gap-2">
                                                                    @foreach($lineasArray as $linea)
                                                                        <span class="bg-white border border-gray-200 text-gray-600 px-2 py-0.5 rounded text-xs font-medium">
                                                                            {{ trim($linea) }}
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($profesor->grupo_investigacion)
                                                            <div>
                                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Grupo de Investigación</p>
                                                                <div class="flex items-center gap-2">
                                                                    <i class="fas fa-users text-unmsm-dorado"></i>
                                                                    <span class="font-medium text-gray-700">
                                                                        @php
                                                                            $grupo = $profesor->grupo_investigacion;
                                                                            echo is_array($grupo) ? ($grupo['nombre'] ?? '') : $grupo;
                                                                        @endphp
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- Botón Móvil (solo visible en pantallas pequeñas) -->
                                                <a href="{{ route('profesores.show', $profesor->slug) }}" 
                                                   class="md:hidden w-full block text-center bg-gray-100 text-gray-700 font-bold py-2 rounded text-sm hover:bg-unmsm-guinda hover:text-white transition-colors">
                                                    Ver perfil completo
                                                </a>

                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-white border border-gray-200 rounded-xl p-8 text-center fade-in">
                                <div class="inline-block p-4 rounded-full bg-gray-100 mb-4">
                                    <i class="fas fa-user-slash text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">Sin asesores registrados</h3>
                                <p class="text-gray-500">Aún no se han registrado asesores para este programa.</p>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Estado inicial: sin programa seleccionado -->
                    <div
                        class="flex flex-col items-center justify-center h-full text-gray-400 border-2 border-dashed border-gray-200 rounded-xl p-12 bg-gray-50">
                        <i class="fas fa-chalkboard-teacher text-5xl mb-4 text-gray-300"></i>
                        <p class="text-lg text-center">Selecciona un programa en el menú de la izquierda para ver su plana
                            docente.</p>
                    </div>
                @endif

            </section>

        </div>
    </div>

@endsection
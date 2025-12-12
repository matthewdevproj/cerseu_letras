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

    <!-- HERO DE SECCIÓN (igual que programas) -->
    <section class="relative w-full h-[50vh] min-h-[400px] flex items-center justify-center bg-gray-900 overflow-hidden">
        <!-- Imagen de Fondo -->
        <div class="absolute inset-0 opacity-50">
            <img src="https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1920&auto=format&fit=crop"
                alt="Profesores" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-gray-900/90"></div>
        </div>

        <!-- Texto Hero -->
        <div class="relative z-10 text-center text-white px-4 mt-20">
            <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">Plana Docente</p>
            <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 drop-shadow-lg">Profesores Asesores</h1>
            <p class="text-gray-200 max-w-2xl mx-auto font-light text-lg leading-relaxed">
                Conoce a los docentes e investigadores que asesoran tesis en nuestras maestrías y doctorados.
            </p>
        </div>
    </section>

    <!-- LAYOUT SIDEBAR + CONTENIDO -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid lg:grid-cols-4 gap-8">

            <!-- SIDEBAR: LISTA DE PROGRAMAS -->
            <aside class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 sticky top-28">
                    <h2 class="text-sm font-bold text-gray-800 mb-4 uppercase tracking-wide border-b border-gray-100 pb-2">
                        Programas de Posgrado
                    </h2>

                    <!-- Maestrías -->
                    <div class="mb-6">
                        <p
                            class="text-xs font-bold text-unmsm-dorado mb-3 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-graduation-cap"></i> Maestrías
                        </p>
                        <ul class="space-y-1">
                            @foreach($maestrias as $prog)
                                                <li>
                                                    <a href="{{ route('profesores.programa', ['slug' => $prog->slug]) }}" class="program-link block text-sm px-3 py-2.5 rounded-md transition-all
                                                                                                                                                                                                                                                                                                                                              {{ isset($selectedPrograma) && $selectedPrograma && $selectedPrograma->id === $prog->id
                                ? 'bg-unmsm-guinda text-white shadow-md'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-unmsm-guinda' }}">
                                                        {{ $prog->nombre }}
                                                    </a>
                                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <!-- Doctorados -->
                    <div>
                        <p class="text-xs font-bold text-gray-800 mb-3 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-user-graduate"></i> Doctorados
                        </p>
                        <ul class="space-y-1">
                            @foreach($doctorados as $prog)
                                                <li>
                                                    <a href="{{ route('profesores.programa', ['slug' => $prog->slug]) }}" class="program-link block text-sm px-3 py-2.5 rounded-md transition-all
                                                                                                                                                                                                                                                                                                                                              {{ isset($selectedPrograma) && $selectedPrograma && $selectedPrograma->id === $prog->id
                                ? 'bg-unmsm-guinda text-white shadow-md'
                                : 'text-gray-600 hover:bg-gray-50 hover:text-unmsm-guinda' }}">
                                                        {{ $prog->nombre }}
                                                    </a>
                                                </li>
                            @endforeach
                        </ul>
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
                                    <article
                                        class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300 group">
                                        <div class="flex flex-col md:flex-row gap-5">
                                            <!-- Avatar / Foto -->
                                            <div
                                                class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-unmsm-guinda shrink-0 border border-gray-100 group-hover:border-unmsm-dorado transition-colors overflow-hidden">
                                                @if($profesor->foto)
                                                    <img src="{{ asset('storage/' . $profesor->foto) }}"
                                                        alt="{{ $profesor->nombre_completo }}" class="w-full h-full object-cover">
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                    </svg>
                                                @endif
                                            </div>

                                            <!-- Info -->
                                            <div class="flex-1">
                                                <h3
                                                    class="text-xl font-bold text-gray-800 font-serif mb-1 group-hover:text-unmsm-guinda transition-colors">
                                                    <a href="{{ route('profesores.show', $profesor->id) }}" class="hover:underline">
                                                        {{ $profesor->nombre_completo }}
                                                    </a>
                                                </h3>

                                                @if($profesor->grado)
                                                    <p class="text-unmsm-dorado text-sm font-medium mb-2">{{ $profesor->grado }}</p>
                                                @endif

                                                @if($profesor->biografia)
                                                    <p class="text-gray-700 text-sm mb-4 text-justify leading-relaxed">
                                                        {{ Str::limit($profesor->biografia, 300) }}
                                                    </p>
                                                @endif

                                                @if(
                                                        ($profesor->lineas_investigacion && count($profesor->lineas_investigacion) > 0) ||
                                                        $profesor->grupo_investigacion
                                                    )
                                                    <div class="pt-4 border-t border-gray-100 space-y-2">
                                                        @if($profesor->lineas_investigacion && count($profesor->lineas_investigacion) > 0)
                                                            <div class="text-xs">
                                                                <span class="font-bold text-unmsm-guinda">Líneas de investigación:</span>
                                                                <span class="text-gray-600 text-sm">
                                                                    {{ implode(', ', $profesor->lineas_investigacion) }}
                                                                </span>
                                                            </div>
                                                        @endif

                                                    </div>
                                                @endif

                                                {{-- Contacto y redes --}}
                                                <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-gray-100">
                                                    {{-- Email visible --}}
                                                    @if($profesor->grupo_investigacion)
                                                        <div class="text-xs mr-auto">
                                                            <span class="font-bold text-unmsm-guinda">Grupo de investigación:</span>
                                                            <span class="text-gray-600 text-sm">{{ $profesor->grupo_investigacion }}</span>
                                                        </div>
                                                    @endif
                                                    @if($profesor->email)
                                                        <a href="mailto:{{ $profesor->email }}"
                                                            class="flex items-center gap-2 text-sm text-gray-700 hover:text-unmsm-guinda transition-colors">
                                                            <i class="fas fa-envelope text-unmsm-guinda"></i>
                                                            <span>{{ $profesor->email }}</span>
                                                        </a>
                                                    @endif

                                                    {{-- Redes sociales y enlaces --}}
                                                    <div class="flex gap-2 ml-auto">
                                                        @if($profesor->linkedin)
                                                            <a href="{{ $profesor->linkedin }}" target="_blank"
                                                                class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all"
                                                                title="LinkedIn">
                                                                <i class="fab fa-linkedin-in"></i>
                                                            </a>
                                                        @endif

                                                        @if($profesor->orcid)
                                                            <a href="{{ $profesor->orcid }}" target="_blank"
                                                                class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all"
                                                                title="ORCID">
                                                                <i class="fab fa-orcid"></i>
                                                            </a>
                                                        @endif

                                                        @if($profesor->cti_vitae)
                                                            <a href="{{ $profesor->cti_vitae }}" target="_blank"
                                                                class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-blue-700 hover:text-white hover:border-blue-700 transition-all"
                                                                title="CTI Vitae">
                                                                <i class="fas fa-file-alt"></i>
                                                            </a>
                                                        @endif

                                                        @if($profesor->email)
                                                            <a href="mailto:{{ $profesor->email }}"
                                                                class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-unmsm-guinda hover:text-white hover:border-unmsm-guinda transition-all"
                                                                title="Enviar correo">
                                                                <i class="far fa-envelope"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>

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
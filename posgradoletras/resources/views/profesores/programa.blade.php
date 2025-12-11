@extends('layouts.public')

@section('title', 'Profesores de ' . $programa['titulo'] . ' - Posgrado Letras UNMSM')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <a href="{{ route('programas.show', $programa['slug']) }}"
            class="text-unmsm-dorado hover:text-unmsm-guinda flex items-center gap-2 mb-6 text-sm font-medium transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            Volver al programa
        </a>

        <h2 class="text-2xl md:text-3xl font-bold text-unmsm-guinda mb-2 font-serif">
            Profesores Asesores
        </h2>
        <p class="text-gray-600 mb-8">{{ $programa['titulo'] }}</p>

        @if(count($profesores) > 0)
            <div class="space-y-6">
                @foreach($profesores as $profesor)
                    <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-start gap-4 mb-4">
                            <div
                                class="w-20 h-20 bg-unmsm-guinda/10 rounded-full flex items-center justify-center text-unmsm-guinda shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-grow">
                                <h3 class="text-xl font-bold text-gray-800 mb-1">{{ $profesor['nombre'] }}</h3>
                                <p class="text-sm text-unmsm-dorado font-bold uppercase mb-2">{{ $profesor['grado'] }}</p>
                                @if($profesor['email'])
                                    <p class="text-sm text-gray-600 flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ $profesor['email'] }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        @if($profesor['biografia'])
                            <div class="mb-4">
                                <h4 class="font-bold text-gray-700 mb-2">Biografía</h4>
                                <p class="text-gray-600 leading-relaxed text-justify">{{ $profesor['biografia'] }}</p>
                            </div>
                        @endif

                        @if(count($profesor['lineas_investigacion']) > 0)
                            <div class="mb-4">
                                <h4 class="font-bold text-gray-700 mb-2">Líneas de Investigación</h4>
                                <ul class="space-y-1">
                                    @foreach($profesor['lineas_investigacion'] as $linea)
                                        <li class="flex items-start gap-2 text-gray-600">
                                            <span class="w-1.5 h-1.5 bg-unmsm-guinda rounded-full mt-2 shrink-0"></span>
                                            {{ $linea }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="flex gap-3 pt-4 border-t border-gray-100">
                            @if($profesor['cti_vitae'])
                                <a href="{{ $profesor['cti_vitae'] }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-sm border border-gray-200 px-4 py-2 rounded hover:bg-unmsm-guinda/5 text-gray-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Ver CTI Vitae
                                </a>
                            @endif
                            @if($profesor['orcid'])
                                <a href="{{ $profesor['orcid'] }}" target="_blank"
                                    class="inline-flex items-center gap-2 text-sm border border-gray-200 px-4 py-2 rounded hover:bg-unmsm-guinda/5 text-gray-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Ver ORCID
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                <p class="text-gray-500">No hay profesores asignados a este programa actualmente.</p>
            </div>
        @endif
    </div>
@endsection

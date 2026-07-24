@extends('layouts.public')

@section('title', 'Directorio de Posgrado - Posgrado Letras UNMSM')

@section('content')

    <!-- HERO DE SECCIÓN -->
    <x-hero-section 
        title="Directorio" 
        label="Unidad de Posgrado"
        subtitle="Conoce al equipo que conforma la Unidad de Posgrado de la Facultad de Letras y Ciencias Humanas."
        :image="asset('images/campus-aerea-2.jpg')" />

    <!-- CONTENIDO PRINCIPAL -->
    <section class="container mx-auto px-6 py-16">

        @if($grupos->count() > 0)
            @foreach($grupos as $unidad => $personas)
                <div class="mb-16 fade-in">
                    <!-- Título de la Sección -->
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-unmsm-guinda rounded-full flex items-center justify-center text-white">
                            @if($unidad == 'AUTORIDADES')
                                <x-fas-user-tie class="text-xl" />
                            @else
                                <x-fas-users class="text-xl" />
                            @endif
                        </div>
                        <div>
                            <h2 class="text-2xl md:text-3xl font-serif font-bold text-gray-900">{{ $unidad }}</h2>
                            <p class="text-gray-500 text-sm">{{ $personas->count() }}
                                {{ $personas->count() == 1 ? 'persona' : 'personas' }}</p>
                        </div>
                    </div>

                    <!-- Tabla de Personal -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Cargo</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Nombre</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Correo</th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">
                                            Teléfono/Anexo</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($personas as $persona)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-5">
                                                <span class="text-sm text-gray-800 font-medium">{{ $persona->cargo }}</span>
                                            </td>
                                            <td class="px-6 py-5">
                                                <span class="text-sm text-gray-900 font-semibold">{{ $persona->nombre_persona }}</span>
                                            </td>
                                            <td class="px-6 py-5">
                                                @if($persona->correo_persona)
                                                    <a href="mailto:{{ $persona->correo_persona }}"
                                                        class="text-sm text-unmsm-guinda hover:text-unmsm-dorado transition-colors flex items-center gap-2">
                                                        <x-fas-envelope class="text-xs" />
                                                        {{ $persona->correo_persona }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-sm">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-5">
                                                @if($persona->anexo)
                                                    <span class="text-sm text-gray-700 flex items-center gap-2">
                                                        <x-fas-phone class="text-xs text-gray-400" />
                                                        {{ $persona->anexo }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400 text-sm">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Sin datos -->
            <div class="text-center py-20">
                <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
                    <x-fas-address-book class="text-4xl text-gray-400" />
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay información disponible</h3>
                <p class="text-gray-500">El directorio aún no ha sido configurado.</p>
            </div>
        @endif

    </section>

@endsection
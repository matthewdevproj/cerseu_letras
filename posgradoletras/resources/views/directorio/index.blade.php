@extends('layouts.public')

@section('title', 'Directorio de Posgrado - Posgrado Letras UNMSM')

@push('styles')
    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
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
    <section class="relative w-full h-[50vh] min-h-[400px] flex items-center justify-center bg-gray-900 overflow-hidden">
        <!-- Imagen de Fondo -->
        <div class="absolute inset-0 opacity-50">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80"
                alt="Oficina" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-gray-900/90"></div>
        </div>

        <!-- Texto Hero -->
        <div class="relative z-10 text-center text-white px-4 mt-20">
            <p class="text-unmsm-dorado font-bold tracking-widest uppercase text-sm mb-3">Unidad de Posgrado</p>
            <h1 class="text-4xl md:text-6xl font-serif font-bold mb-6 drop-shadow-lg">Directorio</h1>
            <p class="text-gray-200 max-w-2xl mx-auto font-light text-lg leading-relaxed">
                Conoce al equipo que conforma la Unidad de Posgrado de la Facultad de Letras y Ciencias Humanas.
            </p>
        </div>
    </section>

    <!-- CONTENIDO PRINCIPAL -->
    <section class="container mx-auto px-6 py-16">

        @if($grupos->count() > 0)
            @foreach($grupos as $unidad => $personas)
                <div class="mb-16 fade-in">
                    <!-- Título de la Sección -->
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-unmsm-guinda rounded-full flex items-center justify-center text-white">
                            @if($unidad == 'AUTORIDADES')
                                <i class="fas fa-user-tie text-xl"></i>
                            @else
                                <i class="fas fa-users text-xl"></i>
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
                                                        <i class="fas fa-envelope text-xs"></i>
                                                        {{ $persona->correo_persona }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 text-sm">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-5">
                                                @if($persona->anexo)
                                                    <span class="text-sm text-gray-700 flex items-center gap-2">
                                                        <i class="fas fa-phone text-xs text-gray-400"></i>
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
                    <i class="fas fa-address-book text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay información disponible</h3>
                <p class="text-gray-500">El directorio aún no ha sido configurado.</p>
            </div>
        @endif

    </section>

@endsection
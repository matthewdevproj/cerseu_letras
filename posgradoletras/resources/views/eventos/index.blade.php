@extends('layouts.public')

@section('title', 'Eventos - Posgrado Letras UNMSM')

@section('content')
    <!-- Hero Section -->
    <x-hero-section title="Eventos" label="Actividades Académicas"
        subtitle="Conferencias, seminarios, talleres y actividades de la Unidad de Posgrado"
        image="https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80" />

    <div class="container mx-auto px-4 py-12">
        @if($eventos->count() > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($eventos as $evento)
                    <article
                        class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden">
                        <!-- Imagen del evento -->
                        <div class="relative h-56 overflow-hidden">
                            <img src="{{ $evento->imagen_url }}" alt="{{ $evento->titulo }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">

                            <!-- Badge de fecha -->
                            <div
                                class="absolute top-4 left-4 bg-unmsm-guinda text-white px-3 py-2 rounded-lg text-center shadow-lg">
                                <span class="block text-2xl font-bold leading-none">{{ $evento->fecha_inicio->format('d') }}</span>
                                <span class="block text-xs uppercase">{{ $evento->fecha_inicio->translatedFormat('M') }}</span>
                            </div>

                            @if($evento->tiene_url)
                                <div class="absolute top-4 right-4">
                                    @if($evento->es_pdf)
                                        <span class="bg-red-600 text-white px-2 py-1 rounded text-xs font-bold">
                                            <i class="fas fa-file-pdf mr-1"></i> PDF
                                        </span>
                                    @else
                                        <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold">
                                            <i class="fas fa-external-link-alt mr-1"></i> Enlace
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Contenido -->
                        <div class="p-5">
                            <h3
                                class="text-lg font-bold text-gray-900 mb-2 group-hover:text-unmsm-guinda transition-colors line-clamp-2">
                                {{ $evento->titulo }}
                            </h3>

                            @if($evento->descripcion)
                                <p class="text-gray-600 text-sm line-clamp-2 mb-4">
                                    {{ $evento->descripcion }}
                                </p>
                            @endif

                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    <i class="far fa-calendar-alt text-unmsm-dorado mr-1"></i>
                                    {{ $evento->fecha_formateada }}
                                </div>

                                @if($evento->tiene_url)
                                    <a href="{{ $evento->url }}" target="_blank"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-unmsm-guinda text-white text-sm font-bold rounded-lg hover:bg-red-800 transition-colors">
                                        {{ $evento->es_pdf ? 'Descargar' : 'Ver más' }}
                                        <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Paginación -->
            @if($eventos->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $eventos->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-calendar-alt text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No hay eventos disponibles</h3>
                <p class="text-gray-600">Próximamente publicaremos nuevas actividades.</p>
            </div>
        @endif
    </div>
@endsection
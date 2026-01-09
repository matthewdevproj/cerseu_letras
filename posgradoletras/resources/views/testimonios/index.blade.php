@extends('layouts.public')

@section('title', 'Testimonios - Posgrado Letras UNMSM')

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
    <x-hero-section title="Testimonios" label="Nuestros Egresados"
        subtitle="Conoce las experiencias de quienes han pasado por nuestros programas de posgrado"
        image="https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=75&fm=webp" />

    <section class="container mx-auto px-6 py-16">
        @if(count($testimonios) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 fade-in">
                @foreach($testimonios as $testimonio)
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 border border-gray-100 flex flex-col h-full">
                        {{-- Header con gradiente --}}
                        <div class="bg-gradient-to-r from-unmsm-guinda to-unmsm-guinda-light p-5">
                            <h3 class="text-white font-serif font-bold text-lg mb-1">{{ $testimonio->nombre }}</h3>
                            @if($testimonio->programa)
                                <p class="text-white/80 text-sm">{{ $testimonio->programa->titulo_completo }}</p>
                            @endif
                        </div>

                        {{-- Contenido --}}
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-gray-600 italic leading-relaxed flex-1 mb-4">
                                "{{ $testimonio->contenido }}"
                            </p>

                            {{-- Footer --}}
                            <div class="border-t border-gray-100 pt-4 mt-auto flex justify-between items-center">
                                @if($testimonio->programa)
                                    <span class="text-unmsm-dorado font-bold text-sm">
                                        Egresado
                                    </span>
                                    <a href="{{ route('programas.show', $testimonio->programa->slug ?? '#') }}"
                                        class="text-unmsm-guinda hover:text-unmsm-dorado text-sm font-medium transition-colors flex items-center gap-1">
                                        Ver programa <i class="fas fa-arrow-right text-xs"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 fade-in">
                <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-quote-left text-4xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay testimonios disponibles</h3>
                <p class="text-gray-500">Pronto agregaremos testimonios de nuestros egresados.</p>
            </div>
        @endif
    </section>
@endsection
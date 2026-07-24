@extends('layouts.public')

@section('title', 'Testimonios - Posgrado Letras UNMSM')

@section('content')
    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Testimonios" label="Nuestros Egresados"
        subtitle="Conoce las experiencias de quienes han pasado por nuestros programas de posgrado"
        :image="asset('images/campus-fachada.jpg')" />

    <section class="container mx-auto px-6 py-16">
        @if(count($testimonios) > 0)
            <div data-reveal-stagger class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($testimonios as $testimonio)
                    <div
                        class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl motion-safe:hover:-translate-y-1.5 transition-all duration-300 border border-gray-100 flex flex-col h-full">
                        {{-- Header con gradiente y foto --}}
                        <div class="bg-gradient-to-r from-unmsm-guinda to-unmsm-guinda-light p-5">
                            <div class="flex items-center gap-4">
                                {{-- Foto del egresado --}}
                                <div class="flex-shrink-0">
                                    @if($testimonio->photo)
                                        <img src="{{ $testimonio->photo_url }}"
                                            alt="{{ $testimonio->nombre }}" loading="lazy" decoding="async"
                                            class="w-20 h-20 rounded-full object-cover border-3 border-white/30 shadow-lg">
                                    @else
                                        <div class="w-20 h-20 rounded-full bg-white/20 flex items-center justify-center border-3 border-white/30">
                                            <span class="text-white font-bold text-2xl">
                                                {{ strtoupper(substr($testimonio->nombre, 0, 2)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                {{-- Nombre y programa --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-white font-serif font-bold text-lg mb-1 truncate">{{ $testimonio->nombre }}</h3>
                                    @if($testimonio->programa)
                                        <p class="text-white/80 text-sm truncate">{{ $testimonio->programa->titulo_completo }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Contenido --}}
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-gray-600 italic leading-relaxed flex-1 mb-4">
                                "{{ $testimonio->contenido }}"
                            </p>

                            {{-- Footer --}}
                            <div class="border-t border-gray-100 pt-4 mt-auto flex justify-between items-center">
                                @if($testimonio->programa)
                                    {{-- <span class="text-unmsm-dorado font-bold text-sm">
                                        Egresado
                                    </span> --}}
                                    <a href="{{ route('programas.show', $testimonio->programa->slug ?? '#') }}"
                                        class="text-unmsm-guinda hover:text-unmsm-dorado text-sm font-medium transition-colors flex items-center gap-1">
                                        Ver programa <x-fas-arrow-right class="text-xs" />
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
                    <x-fas-quote-left class="text-4xl text-gray-400" />
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay testimonios disponibles</h3>
                <p class="text-gray-500">Pronto agregaremos testimonios de nuestros egresados.</p>
            </div>
        @endif
    </section>
@endsection
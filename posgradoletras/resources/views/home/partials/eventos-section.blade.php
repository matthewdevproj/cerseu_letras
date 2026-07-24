@php
    $eventos = \App\Http\Controllers\EventoController::getForHome(4);
@endphp

@if($eventos->count() > 0)
    <section class="relative py-16 bg-gradient-to-b from-gray-900 to-[#1a0e10] text-white overflow-hidden">
        <div class="absolute -bottom-40 left-1/2 -translate-x-1/2 w-[46rem] h-[46rem] rounded-full bg-unmsm-guinda/20 blur-3xl"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center mb-8">
                <h2 class="text-3xl md:text-4xl font-serif font-bold mb-3">Eventos</h2>
                <div class="w-20 h-1 bg-unmsm-dorado mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($eventos as $evento)
                    <article class="group rounded-xl overflow-hidden bg-white/5 border border-white/10 hover:border-unmsm-dorado/50 transition-all duration-300 hover:-translate-y-1 cursor-pointer"
                        @if($evento->tiene_url) onclick="window.open('{{ $evento->url }}', '_blank')" @endif>
                        
                        <!-- Imagen (sin degradado, limpia) -->
                        <div class="relative aspect-[3/4] overflow-hidden">
                            <img src="{{ $evento->imagen_url }}" alt="{{ $evento->titulo }}" loading="lazy" decoding="async"
                                class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">

                            <!-- Badge tipo (solo en hover) -->
                            @if($evento->tiene_url)
                                <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="bg-unmsm-dorado text-gray-900 px-2 py-1 rounded text-xs font-bold">
                                        {{ $evento->es_pdf ? 'PDF' : 'Enlace' }}
                                    </span>
                                </div>
                            @endif

                            <!-- Fecha en esquina -->
                            <div class="absolute bottom-3 left-3">
                                <div class="bg-unmsm-guinda text-white px-3 py-2 rounded-lg text-center shadow-lg">
                                    <span class="block text-2xl font-bold leading-none">{{ $evento->fecha_inicio->format('d') }}</span>
                                    <span class="block text-xs uppercase mt-1">{{ $evento->fecha_inicio->translatedFormat('M') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Título abajo de la imagen -->
                        <div class="p-4">
                            <h3 class="font-bold text-white group-hover:text-unmsm-dorado transition-colors line-clamp-2 text-sm leading-tight">
                                {{ $evento->titulo }}
                            </h3>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
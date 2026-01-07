<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-3">
                <i class="fas fa-file-alt text-unmsm-guinda mr-3"></i>
                Documentos y Recursos
            </h2>
            <p class="text-gray-600 text-lg">Accede a reglamentos, directivas e información institucional</p>
            <div class="w-20 h-1 bg-unmsm-dorado mx-auto mt-4 rounded-full"></div>
        </div>

        @php
            $informativos = \App\Http\Controllers\InformativoController::getForHome();
        @endphp

        @if($informativos->count() > 0)
            <div class="space-y-10">
                @foreach($informativos as $categoria => $items)
                    <div>
                        {{-- Título de categoría --}}
                        <h3 class="flex items-center gap-3 text-xl font-bold text-gray-900 uppercase tracking-wide mb-5 pb-3 border-b-2 border-unmsm-guinda">
                            <span class="w-8 h-8 rounded-full bg-unmsm-guinda/10 flex items-center justify-center text-unmsm-guinda">
                                <i class="{{ $items->first()->icono }}"></i>
                            </span>
                            {{ $categoria }}
                        </h3>

                        {{-- Lista de documentos --}}
                        <div class="grid gap-3">
                            @foreach($items as $item)
                                <a href="{{ $item->url }}" 
                                   target="{{ $item->es_enlace ? '_blank' : '_self' }}" 
                                   rel="nofollow"
                                   class="group flex items-center gap-4 p-4 bg-gray-50 hover:bg-unmsm-guinda/5 border border-gray-200 hover:border-unmsm-guinda/30 rounded-xl transition-all duration-300">
                                    
                                    {{-- Icono PDF o enlace (Oculto en móvil) --}}
                                    <div class="flex-shrink-0 hidden md:flex">
                                        @if($item->es_pdf)
                                            <div class="w-12 h-12 rounded-lg bg-red-50 group-hover:bg-red-100 flex items-center justify-center transition-colors">
                                                <i class="fas fa-file-pdf text-2xl text-red-600"></i>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center transition-colors">
                                                <i class="fas fa-external-link-alt text-xl text-blue-600"></i>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Contenido --}}
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 group-hover:text-unmsm-guinda transition-colors leading-tight">
                                            {{ $item->titulo }}
                                        </h4>
                                        <p class="text-sm text-gray-500 mt-0.5 hidden md:flex items-center gap-1">
                                            <i class="{{ $item->icono }} text-xs"></i>
                                            {{ $item->categoria }}
                                        </p>
                                    </div>

                                    {{-- Botón/Flecha --}}
                                    <div class="flex-shrink-0">
                                        <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold rounded-lg transition-all duration-300
                                            {{ $item->es_pdf 
                                                ? 'bg-unmsm-guinda text-white group-hover:bg-red-800' 
                                                : 'bg-gray-200 text-gray-700 group-hover:bg-unmsm-guinda group-hover:text-white' }}">
                                            {{ $item->texto_boton }}
                                            <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('informativos.index') }}"
                    class="inline-flex items-center gap-2 bg-white border-2 border-unmsm-guinda text-unmsm-guinda px-8 py-3 rounded-xl font-bold hover:bg-unmsm-guinda hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                    Ver todos los documentos
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No hay documentos disponibles en este momento.</p>
            </div>
        @endif
    </div>
</section>
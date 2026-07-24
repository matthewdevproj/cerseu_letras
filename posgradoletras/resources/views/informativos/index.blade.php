@extends('layouts.public')

@section('title', 'Documentos y Recursos - Posgrado Letras UNMSM')

@section('content')

    <!-- HERO -->
    <x-hero-section title="Documentos y Recursos" label="Área Informativa"
        subtitle="Reglamentos, directivas e información institucional del Posgrado."
        image="https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&w=1200&q=75&fm=webp" />

    <!-- CONTENIDO -->
    <div class="container mx-auto px-4 py-12 max-w-6xl">

        <!-- Buscador -->
        <div class="mb-10 max-w-2xl mx-auto">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Buscar documento..." 
                    class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:border-unmsm-guinda focus:ring focus:ring-unmsm-guinda/20 shadow-sm transition-all text-gray-700 text-lg">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <x-fas-search class="text-gray-400 text-xl" />
                </div>
            </div>
        </div>

        @if($informativos->count() > 0)
            <div id="documentsContainer">
            @foreach($informativos as $categoria => $items)
                <section class="mb-12 category-section">
                    <!-- Título de Sección -->
                    <h2 class="flex items-center gap-3 text-2xl font-bold text-gray-800 mb-6 pb-2 border-b-2 border-unmsm-guinda/10">
                        <span class="w-10 h-10 rounded-lg bg-unmsm-guinda/10 flex items-center justify-center text-unmsm-guinda text-lg">
                            <x-fa-icon :icon="$items->first()->icono ?? 'fas fa-folder'" />
                        </span>
                        {{ strtoupper($categoria) }}
                    </h2>

                    <!-- Grid de Documentos -->
                    <div class="grid md:grid-cols-2 gap-6">
                        @foreach($items as $item)
                            <div class="document-card group bg-white border border-gray-200 rounded-xl p-5 hover:shadow-lg hover:border-unmsm-guinda/30 transition-all duration-300 flex items-start gap-4">
                                <!-- Icono -->
                                <div class="flex-shrink-0">
                                    @if($item->es_pdf)
                                        <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                            <x-fas-file-pdf />
                                        </div>
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                                            <x-fas-link />
                                        </div>
                                    @endif
                                </div>

                                <!-- Contenido -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="document-title font-bold text-gray-800 group-hover:text-unmsm-guinda transition-colors mb-1 line-clamp-2" title="{{ $item->titulo }}">
                                        {{ $item->titulo }}
                                    </h3>
                                    @if($item->categoria)
                                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold mb-2">{{ $item->categoria }}</p>
                                    @endif
                                    
                                    <a href="{{ $item->url }}" target="{{ $item->es_enlace ? '_blank' : '_self' }}" rel="nofollow"
                                        class="inline-flex items-center gap-2 text-sm font-medium {{ $item->es_pdf ? 'text-red-600 hover:text-red-800' : 'text-blue-600 hover:text-blue-800' }}">
                                        @if($item->es_pdf)
                                            <span>Descargar PDF</span>
                                            <x-fas-download class="text-xs" />
                                        @else
                                            <span>Visitar enlace</span>
                                            <x-fas-external-link-alt class="text-xs" />
                                        @endif
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
            </div>

            <!-- Estado Vacío (Búsqueda) -->
            <div id="noResults" class="hidden text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-3xl">
                    <x-fas-search />
                </div>
                <h3 class="text-lg font-bold text-gray-700">No se encontraron resultados</h3>
                <p class="text-gray-500">Intenta con otros términos de búsqueda.</p>
            </div>

        @else
            <div class="text-center py-20 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                <x-fas-folder-open class="text-6xl text-gray-300 mb-4" />
                <p class="text-gray-500 font-medium">No hay documentos disponibles en este momento.</p>
            </div>
        @endif

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            if(!searchInput) return;

            const cards = document.querySelectorAll('.document-card');
            const sections = document.querySelectorAll('.category-section');
            const noResults = document.getElementById('noResults');
            const documentsContainer = document.getElementById('documentsContainer');

            searchInput.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase().trim();
                let hasVisibleResults = false;

                cards.forEach(card => {
                    const title = card.querySelector('.document-title').textContent.toLowerCase();
                    // Buscar también en la categoría visible si existe
                    const categoryEl = card.querySelector('p');
                    const category = categoryEl ? categoryEl.textContent.toLowerCase() : '';
                    
                    if (title.includes(term) || category.includes(term)) {
                        card.style.display = ''; // Restaurar display original (flex)
                        // Asegurar que use flex, ya que al ocultar usamos 'none'
                        card.classList.add('flex');
                        card.classList.remove('hidden');
                    } else {
                        card.style.display = 'none';
                        card.classList.remove('flex');
                        card.classList.add('hidden');
                    }
                });

                // Ocultar secciones vacías
                sections.forEach(section => {
                    const visibleCards = section.querySelectorAll('.document-card:not(.hidden)');
                    if (visibleCards.length > 0) {
                        section.style.display = '';
                        hasVisibleResults = true;
                    } else {
                        section.style.display = 'none';
                    }
                });

                // Mostrar mensaje si no hay resultados
                if (!hasVisibleResults && term !== '') {
                    noResults.classList.remove('hidden');
                    documentsContainer.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    documentsContainer.classList.remove('hidden');
                }
            });
        });
    </script>
    @endpush

@endsection
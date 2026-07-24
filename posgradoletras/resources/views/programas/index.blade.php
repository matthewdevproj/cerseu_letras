@extends('layouts.public')

@section('title', 'Programas Académicos - Posgrado Letras UNMSM')

@push('styles')
    <style>
        .program-card {
            display: block;
        }

        .program-card.hidden-filter {
            display: none;
        }
    </style>
@endpush

@section('content')

    <!-- HERO DE SECCIÓN -->
    <x-hero-section title="Nuestros Programas" label="Oferta Académica "
        subtitle="Especialízate con la excelencia académica de la Decana de América y transforma tu futuro profesional."
        :image="asset('images/campus-aerea.jpg')" />

    <!-- SECCIÓN DE GRID Y FILTROS -->
    <section class="container mx-auto px-6 py-16">

        <!-- Barra de Herramientas (Filtros y Buscador) -->
        <div
            class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 sticky top-24 z-30">

            <!-- Botones de Filtro -->
            <div class="flex p-1 bg-gray-100 rounded-lg overflow-x-auto max-w-full">
                <button onclick="filterPrograms('todos')"
                    class="filter-btn px-4 md:px-6 py-2 rounded-md text-sm font-bold transition-all whitespace-nowrap {{ $tipoFiltro == 'todos' ? 'bg-white text-unmsm-guinda shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-200' }}"
                    id="btn-todos">
                    Todos ({{ count($maestrias) + count($doctorados) }})
                </button>
                <button onclick="filterPrograms('maestria')"
                    class="filter-btn px-4 md:px-6 py-2 rounded-md text-sm font-bold transition-all whitespace-nowrap {{ $tipoFiltro == 'maestria' ? 'bg-white text-unmsm-guinda shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-200' }}"
                    id="btn-maestria">
                    Maestrías ({{ count($maestrias) }})
                </button>
                <button onclick="filterPrograms('doctorado')"
                    class="filter-btn px-4 md:px-6 py-2 rounded-md text-sm font-bold transition-all whitespace-nowrap {{ $tipoFiltro == 'doctorado' ? 'bg-white text-unmsm-guinda shadow-sm' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-200' }}"
                    id="btn-doctorado">
                    Doctorados ({{ count($doctorados) }})
                </button>
            </div>

            <!-- Buscador -->
            <div class="relative w-full md:w-72 group">
                <input type="text" id="searchInput" placeholder="Buscar programa..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-unmsm-guinda focus:ring-1 focus:ring-unmsm-guinda text-sm transition-all">
                <div class="absolute left-3 top-3 text-gray-400 group-focus-within:text-unmsm-guinda transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- GRID DE PROGRAMAS -->
        <div id="programsGrid" data-reveal class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            {{-- Maestrías --}}
            @foreach($maestrias as $programa)
                <x-program-card :programa="$programa" tipo="maestria" badge-label="Maestría"
                    badge-color="bg-unmsm-guinda" :duracion-default="4" />
            @endforeach

            {{-- Doctorados --}}
            @foreach($doctorados as $programa)
                <x-program-card :programa="$programa" tipo="doctorado" badge-label="Doctorado"
                    badge-color="bg-gray-900" :duracion-default="6" />
            @endforeach

        </div>

        <!-- Mensaje Sin Resultados -->
        <div id="noResults" class="hidden text-center py-20">
            <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No se encontraron programas</h3>
            <p class="text-gray-500">Intenta buscar con otros términos o cambia el filtro.</p>
            <button onclick="filterPrograms('todos'); document.getElementById('searchInput').value=''"
                class="mt-4 text-unmsm-guinda font-bold hover:underline">
                Ver todos los programas
            </button>
        </div>

        @if(count($maestrias) == 0 && count($doctorados) == 0)
            <div class="text-center py-20">
                <div class="inline-block p-6 rounded-full bg-gray-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay programas disponibles</h3>
                <p class="text-gray-500">Pronto se agregarán nuevos programas académicos.</p>
            </div>
        @endif

    </section>

@endsection

@push('scripts')
    <script>
        const grid = document.getElementById('programsGrid');
        const noResults = document.getElementById('noResults');
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.program-card');
        let currentFilter = '{{ $tipoFiltro }}';

        function updateVisibility() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;

            cards.forEach(card => {
                const type = card.dataset.type;
                const title = card.dataset.title;
                const desc = card.dataset.desc;

                const matchesFilter = currentFilter === 'todos' || type === currentFilter;
                const matchesSearch = searchTerm === '' ||
                    title.includes(searchTerm) ||
                    desc.includes(searchTerm);

                if (matchesFilter && matchesSearch) {
                    card.classList.remove('hidden-filter');
                    visibleCount++;
                } else {
                    card.classList.add('hidden-filter');
                }
            });

            // Mostrar/ocultar mensaje de sin resultados
            if (visibleCount === 0 && cards.length > 0) {
                noResults.classList.remove('hidden');
                grid.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                grid.classList.remove('hidden');
            }
        }

        // Filtrado por botones
        function filterPrograms(type) {
            currentFilter = type;

            // Actualizar estilos botones
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'text-unmsm-guinda', 'shadow-sm');
                btn.classList.add('text-gray-500', 'hover:bg-gray-200');
            });

            const activeBtn = document.getElementById(`btn-${type}`);
            activeBtn.classList.remove('text-gray-500', 'hover:bg-gray-200');
            activeBtn.classList.add('bg-white', 'text-unmsm-guinda', 'shadow-sm');

            updateVisibility();
        }

        // Buscador
        searchInput.addEventListener('input', updateVisibility);

        // Init
        document.addEventListener('DOMContentLoaded', updateVisibility);
    </script>
@endpush
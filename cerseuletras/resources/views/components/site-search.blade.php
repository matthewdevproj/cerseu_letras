{{--
    Buscador global (Obs. N.º 5).

    El ícono vive en la parte superior derecha del menú y, al pulsarlo, despliega
    un panel con un campo visible. Los resultados aparecen mientras se escribe,
    ordenados por relevancia, identificando la categoría de cada uno y con acceso
    directo a su página; si no hay coincidencias se muestra un mensaje.

    Una única instancia sirve a computadoras, tabletas y celulares (el panel es
    fluido), así que no hay paneles duplicados ni trampas de foco anidadas.

    No añade dependencias: Alpine ya está en el bundle y las peticiones van por
    `fetch`, cancelando la anterior en cada pulsación.
--}}
<div x-data="siteSearch('{{ route('search.suggest') }}')" class="relative">

    {{-- Disparador --}}
    {{-- La clase `nav-item` y el token exacto `text-white` son necesarios: al
         hacer scroll, el script de la cabecera vuelve blanco el fondo y cambia
         `text-white` → `text-gray-800` en los `.nav-item`. Sin ellos, el ícono
         quedaba blanco sobre blanco. --}}
    <button type="button" @click="abrir()" x-ref="trigger" data-site-search-trigger
        :aria-expanded="open.toString()" aria-haspopup="dialog"
        aria-label="Buscar en el portal"
        class="nav-item flex items-center justify-center w-10 h-10 rounded-full text-white hover:text-unmsm-azul-soft transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-current focus-visible:outline-offset-2">
        <x-fas-magnifying-glass aria-hidden="true" />
    </button>

    {{-- Panel de búsqueda (se teletransporta al body para escapar del stacking
         context de la cabecera fija) --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-[100]" role="dialog" aria-modal="true"
            aria-label="Buscar en el portal" @keydown.escape="cerrar()">

            {{-- Fondo --}}
            <div x-show="open" x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-150"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="absolute inset-0 bg-black/70 backdrop-blur-sm" @click="cerrar()"></div>

            {{-- Caja --}}
            <div x-show="open" x-trap.inert.noscroll="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-4"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-4"
                class="relative mx-auto mt-[6vh] sm:mt-[8vh] w-[94%] max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-unmsm-azul">

                {{-- Campo --}}
                <form action="{{ route('search') }}" method="GET" @submit="if (!q.trim()) $event.preventDefault()"
                    class="flex items-center gap-3 px-4 sm:px-5 py-4 border-b border-gray-100">
                    <x-fas-magnifying-glass class="text-unmsm-azul text-lg flex-shrink-0" aria-hidden="true" />
                    <label for="site-search-input" class="sr-only">Buscar cursos, trámites e información</label>
                    <input id="site-search-input" type="search" name="q" x-ref="input" x-model="q"
                        @input.debounce.220ms="buscar()"
                        @keydown.arrow-down.prevent="mover(1)" @keydown.arrow-up.prevent="mover(-1)"
                        @keydown.enter="irAlActivo($event)"
                        autocomplete="off" role="combobox" aria-autocomplete="list"
                        :aria-expanded="(resultados.length > 0).toString()"
                        aria-controls="site-search-results"
                        :aria-activedescendant="activo >= 0 ? 'site-search-opt-' + activo : null"
                        placeholder="Buscar cursos, talleres, trámites…"
                        class="flex-1 min-w-0 border-0 p-0 text-base text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none bg-transparent">
                    <button type="button" @click="cerrar()" aria-label="Cerrar búsqueda"
                        class="flex-shrink-0 text-gray-400 hover:text-unmsm-azul transition-colors p-1 rounded focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-azul">
                        <x-fas-times aria-hidden="true" />
                    </button>
                </form>

                {{-- Resultados --}}
                <div class="max-h-[60vh] overflow-y-auto overscroll-contain">

                    {{-- Estado inicial: accesos rápidos --}}
                    <template x-if="q.trim().length < 2">
                        <div class="px-4 sm:px-5 py-6">
                            <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Accesos rápidos</p>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('talleres.index') }}" class="px-3 py-1.5 rounded-full bg-gray-100 text-sm text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Talleres</a>
                                <a href="{{ route('talleres.admision') }}" class="px-3 py-1.5 rounded-full bg-gray-100 text-sm text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Admisión de talleres</a>
                                <a href="{{ route('cursos.index') }}" class="px-3 py-1.5 rounded-full bg-gray-100 text-sm text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Cursos</a>
                                <a href="{{ route('cursos.admision') }}" class="px-3 py-1.5 rounded-full bg-gray-100 text-sm text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Admisión de cursos</a>
                                <a href="{{ route('tramites') }}" class="px-3 py-1.5 rounded-full bg-gray-100 text-sm text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Trámites</a>
                            </div>
                        </div>
                    </template>

                    {{-- Cargando --}}
                    <template x-if="q.trim().length >= 2 && cargando">
                        <div class="px-5 py-8 text-center text-sm text-gray-400">
                            <x-fas-circle-notch class="animate-spin mr-2" aria-hidden="true" /> Buscando…
                        </div>
                    </template>

                    {{-- Sin coincidencias --}}
                    <template x-if="q.trim().length >= 2 && !cargando && resultados.length === 0">
                        <div class="px-5 py-10 text-center">
                            <x-fas-magnifying-glass class="text-3xl text-gray-200 mb-3" aria-hidden="true" />
                            <p class="text-gray-700 font-semibold">No encontramos resultados para “<span x-text="q"></span>”</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Prueba con otras palabras o revisa la
                                <a href="{{ route('cursos.index') }}" class="text-unmsm-azul font-semibold underline underline-offset-2">oferta académica completa</a>.
                            </p>
                        </div>
                    </template>

                    {{-- Lista de resultados --}}
                    <ul id="site-search-results" role="listbox" aria-label="Resultados de búsqueda"
                        x-show="!cargando && resultados.length > 0" class="py-2">
                        <template x-for="(r, i) in resultados" :key="r.url + i">
                            <li role="option" :id="'site-search-opt-' + i" :aria-selected="(activo === i).toString()">
                                <a :href="r.url" @mouseenter="activo = i"
                                    class="flex items-start gap-3 px-4 sm:px-5 py-3 transition-colors"
                                    :class="activo === i ? 'bg-unmsm-azul/[0.07]' : 'hover:bg-gray-50'">
                                    <span class="mt-2 flex-shrink-0 w-2 h-2 rounded-full bg-unmsm-dorado" aria-hidden="true"></span>
                                    <span class="min-w-0 flex-1">
                                        <span class="flex flex-wrap items-center gap-2">
                                            <span class="font-semibold text-gray-900 leading-snug" x-text="r.titulo"></span>
                                            {{-- Categoría a la que pertenece el resultado --}}
                                            <span class="px-2 py-0.5 rounded-full bg-gray-100 text-[10px] font-bold uppercase tracking-wide text-gray-500 flex-shrink-0"
                                                x-text="r.categoria"></span>
                                        </span>
                                        <span x-show="r.descripcion" class="block text-sm text-gray-500 mt-0.5 line-clamp-2" x-text="r.descripcion"></span>
                                    </span>
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>

                {{-- Pie --}}
                <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-500">
                    <span class="hidden sm:flex items-center gap-3">
                        <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded font-sans">↑</kbd>
                            <kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded font-sans">↓</kbd> navegar</span>
                        <span><kbd class="px-1.5 py-0.5 bg-white border border-gray-300 rounded font-sans">Esc</kbd> cerrar</span>
                    </span>
                    <a x-show="q.trim().length >= 2" :href="'{{ route('search') }}?q=' + encodeURIComponent(q)"
                        class="ml-auto font-semibold text-unmsm-azul hover:underline underline-offset-2">
                        Ver todos los resultados →
                    </a>
                </div>
            </div>
        </div>
    </template>
</div>

@push('scripts')
    <script>
        // Atajo "/" para abrir el buscador, salvo si ya se está escribiendo.
        document.addEventListener('keydown', (e) => {
            if (e.key !== '/' || e.metaKey || e.ctrlKey || e.altKey) return;
            if (e.target.closest('input, textarea, select, [contenteditable]')) return;
            const trigger = document.querySelector('[data-site-search-trigger]');
            if (trigger) {
                e.preventDefault();
                trigger.click();
            }
        });
    </script>
@endpush

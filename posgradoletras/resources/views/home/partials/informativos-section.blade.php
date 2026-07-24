<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 mb-3">
                <x-fas-file-alt class="text-unmsm-guinda mr-3" />
                Documentos y Recursos
            </h2>
            <p class="text-gray-600 text-lg">Accede a reglamentos, directivas e información institucional</p>
            <div class="w-20 h-1 bg-unmsm-dorado mx-auto mt-4 rounded-full"></div>
        </div>

        @php
            $informativos = \App\Http\Controllers\InformativoController::getForHome();
        @endphp

        @if($informativos->count() > 0)
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 md:gap-8 justify-center">
                @foreach($informativos as $categoria => $items)
                    <a href="{{ route('informativos.index') }}"
                        class="group bg-white p-4 md:p-8 rounded-2xl border border-gray-100 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col items-center text-center relative overflow-hidden">

                        <!-- Decoración de fondo -->
                        <div
                            class="absolute top-0 right-0 w-24 h-24 bg-unmsm-guinda/5 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-150">
                        </div>

                        <div
                            class="w-14 h-14 md:w-20 md:h-20 rounded-2xl bg-unmsm-guinda/5 text-unmsm-guinda flex items-center justify-center text-2xl md:text-4xl mb-4 md:mb-6 group-hover:bg-unmsm-guinda group-hover:text-white transition-colors shadow-sm relative z-10">
                            <x-fa-icon :icon="$items->first()->icono ?? 'fas fa-folder'" />
                        </div>

                        <h3
                            class="text-xl font-serif font-bold text-gray-900 mb-2 group-hover:text-unmsm-guinda transition-colors relative z-10">
                            {{ $categoria }}
                        </h3>

                        <p class="text-gray-500 mb-6 relative z-10 font-medium">
                            {{ $items->count() }} documentos disponibles
                        </p>

                        <span
                            class="inline-flex items-center gap-2 text-unmsm-guinda font-bold text-sm bg-unmsm-guinda/5 px-4 py-2 rounded-full group-hover:bg-unmsm-guinda group-hover:text-white transition-all relative z-10">
                            Explorar <x-fas-arrow-right class="text-xs group-hover:translate-x-1 transition-transform" />
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('informativos.index') }}"
                    class="inline-flex items-center gap-2 bg-white border-2 border-unmsm-guinda text-unmsm-guinda px-8 py-3 rounded-xl font-bold hover:bg-unmsm-guinda hover:text-white transition-all duration-300 shadow-sm hover:shadow-lg">
                    Ver todos los documentos
                    <x-fas-arrow-right />
                </a>
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <x-fas-folder-open class="text-4xl text-gray-300 mb-4" />
                <p class="text-gray-500">No hay documentos disponibles en este momento.</p>
            </div>
        @endif
    </div>
</section>
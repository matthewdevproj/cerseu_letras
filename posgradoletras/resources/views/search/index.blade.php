@extends('layouts.public')

@section('title', $q !== '' ? 'Resultados para "' . $q . '" - CERSEU Letras UNMSM' : 'Buscar - CERSEU Letras UNMSM')
@section('meta_description', 'Busca cursos, trámites e información institucional del CERSEU de la Facultad de Letras y Ciencias Humanas de la UNMSM.')

@section('content')
    <x-hero-section title="Buscar" subtitle="Encuentra cursos, trámites e información del CERSEU" />

    <section class="py-12 md:py-16 bg-gray-50 min-h-[50vh]">
        <div class="container mx-auto px-6 max-w-4xl">

            {{-- Campo de búsqueda --}}
            <form action="{{ route('search') }}" method="GET" role="search"
                class="flex items-center gap-3 bg-white rounded-xl shadow-md border border-gray-100 px-5 py-4 mb-8 focus-within:ring-2 focus-within:ring-unmsm-azul/40 transition">
                <x-fas-magnifying-glass class="text-unmsm-azul text-lg flex-shrink-0" aria-hidden="true" />
                <label for="q" class="sr-only">Buscar en el portal</label>
                <input id="q" type="search" name="q" value="{{ $q }}" autofocus
                    placeholder="Buscar cursos, talleres, trámites…"
                    class="flex-1 min-w-0 border-0 p-0 text-base text-gray-900 placeholder-gray-400 focus:ring-0 focus:outline-none bg-transparent">
                <button type="submit"
                    class="flex-shrink-0 px-5 py-2 rounded-lg bg-unmsm-azul text-white font-bold text-sm hover:bg-unmsm-azul-dark transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-azul focus-visible:outline-offset-2">
                    Buscar
                </button>
            </form>

            @if ($q === '')
                {{-- Sin término: se ofrecen los destinos más consultados --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                    <h2 class="font-serif text-xl font-bold text-gray-900 mb-1">¿Qué estás buscando?</h2>
                    <p class="text-gray-500 text-sm mb-6">Escribe una palabra clave o entra directamente a una sección.</p>
                    <div class="grid sm:grid-cols-2 gap-3">
                        @foreach ([
                            ['Talleres', 'Oferta vigente de talleres', route('talleres.index')],
                            ['Admisión de diplomados', 'Requisitos, cronograma e inscripción', route('talleres.admision')],
                            ['Cursos', 'Oferta vigente de cursos', route('cursos.index')],
                            ['Admisión de cursos', 'Requisitos, cronograma e inscripción', route('cursos.admision')],
                            ['Trámites', 'Obtención del grado', route('tramites')],
                            ['Documentos y Recursos', 'Reglamentos y formatos', route('informativos.index')],
                        ] as [$titulo, $desc, $url])
                            <a href="{{ $url }}"
                                class="group flex items-center justify-between gap-3 p-4 rounded-lg border border-gray-100 hover:border-unmsm-azul/40 hover:bg-unmsm-azul/[0.03] transition-colors">
                                <span>
                                    <span class="block font-semibold text-gray-900 group-hover:text-unmsm-azul transition-colors">{{ $titulo }}</span>
                                    <span class="block text-sm text-gray-500">{{ $desc }}</span>
                                </span>
                                <x-fas-arrow-right class="text-gray-300 group-hover:text-unmsm-azul motion-safe:group-hover:translate-x-1 transition-all" aria-hidden="true" />
                            </a>
                        @endforeach
                    </div>
                </div>
            @elseif ($resultados->isEmpty())
                {{-- Mensaje cuando no se encuentran coincidencias --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <x-fas-magnifying-glass class="text-4xl text-gray-200 mb-4" aria-hidden="true" />
                    <h2 class="font-serif text-xl font-bold text-gray-900 mb-2">
                        No encontramos resultados para «{{ $q }}»
                    </h2>
                    <p class="text-gray-500 mb-6 max-w-md mx-auto">
                        Revisa la ortografía, prueba con menos palabras o usa un término más general.
                    </p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <a href="{{ route('talleres.index') }}" class="px-4 py-2 rounded-full bg-gray-100 text-sm font-medium text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Ver talleres</a>
                        <a href="{{ route('cursos.index') }}" class="px-4 py-2 rounded-full bg-gray-100 text-sm font-medium text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Ver todos los cursos</a>
                        <a href="{{ route('admision') }}" class="px-4 py-2 rounded-full bg-gray-100 text-sm font-medium text-gray-700 hover:bg-unmsm-azul hover:text-white transition-colors">Proceso de admisión</a>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 mb-5" role="status">
                    <strong class="text-gray-900">{{ $resultados->count() }}</strong>
                    {{ $resultados->count() === 1 ? 'resultado' : 'resultados' }} para
                    «<strong class="text-gray-900">{{ $q }}</strong>», ordenados por relevancia.
                </p>

                {{-- Filtros por categoría (anclas dentro de la misma página) --}}
                @if ($porCategoria->count() > 1)
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach ($porCategoria as $categoria => $items)
                            <a href="#cat-{{ Str::slug($categoria) }}"
                                class="px-3 py-1.5 rounded-full bg-white border border-gray-200 text-xs font-semibold text-gray-600 hover:border-unmsm-azul hover:text-unmsm-azul transition-colors">
                                {{ $categoria }} <span class="text-gray-400">({{ $items->count() }})</span>
                            </a>
                        @endforeach
                    </div>
                @endif

                <div class="space-y-8">
                    @foreach ($porCategoria as $categoria => $items)
                        <section id="cat-{{ Str::slug($categoria) }}" class="scroll-mt-28">
                            <h2 class="text-xs font-bold uppercase tracking-wider text-unmsm-azul mb-3">
                                {{ $categoria }}
                            </h2>
                            <ul class="bg-white rounded-xl shadow-sm border border-gray-100 divide-y divide-gray-100 overflow-hidden">
                                @foreach ($items as $r)
                                    <li>
                                        <a href="{{ $r['url'] }}"
                                            class="group flex items-start gap-4 p-5 hover:bg-unmsm-azul/[0.03] transition-colors">
                                            <span class="mt-2 flex-shrink-0 w-2 h-2 rounded-full bg-unmsm-dorado" aria-hidden="true"></span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block font-semibold text-gray-900 group-hover:text-unmsm-azul transition-colors leading-snug">
                                                    {{ $r['titulo'] }}
                                                </span>
                                                @if ($r['descripcion'])
                                                    <span class="block text-sm text-gray-500 mt-1 line-clamp-2">{{ $r['descripcion'] }}</span>
                                                @endif
                                            </span>
                                            <x-fas-arrow-right class="mt-1 flex-shrink-0 text-gray-300 group-hover:text-unmsm-azul motion-safe:group-hover:translate-x-1 transition-all" aria-hidden="true" />
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endsection

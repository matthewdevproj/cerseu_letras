@props([
    'programa',
    'tipo' => null,
    'badgeLabel' => null,
    'badgeColor' => 'bg-unmsm-azul',
    'duracionDefault' => null,
    'duracionUnit' => 'semestres',
    'primaryCtaLabel' => 'Ver Plan de Estudios',
    'showBrochure' => false,
])

@php
    // Sin duración no hay chip: concatenar a secas dejaba la píldora con la
    // unidad suelta —un «meses» sin número— en la oferta que se mide en horas.
    $numeroDuracion = $programa->duracion ?? $duracionDefault;
    $duracion = $programa->duracion_formateada
        ?? ($numeroDuracion ? $numeroDuracion . ' ' . $duracionUnit : null);
    $descripcion = $programa->presentacion ?? $programa->sumilla ?? 'Sin descripción disponible';
    // Los cursos anunciados como próxima oferta se marcan y llevan a su
    // aviso, no a una ficha vacía.
    $esProximamente = $programa->es_proximamente ?? false;
    if ($esProximamente) {
        $descripcion = $programa->sumilla ?: 'Pronto publicaremos el plan de estudios, la inversión y las fechas de admisión de este curso.';
        $primaryCtaLabel = 'Recibir información';
    }
@endphp

<div {{ $attributes->merge(['class' => 'program-card fade-in bg-white rounded-xl shadow-md overflow-hidden hover:shadow-2xl motion-safe:hover:-translate-y-1.5 transition-all duration-300 border border-gray-100 group flex flex-col h-full']) }}
    @if($tipo) data-type="{{ $tipo }}" @endif
    data-title="{{ strtolower($programa->titulo_completo) }}"
    data-desc="{{ strtolower($descripcion) }}">

    <div class="relative h-48 overflow-hidden">
        <img src="{{ $programa->imagen_url }}" alt="{{ $programa->titulo_completo }}" loading="lazy" decoding="async"
            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">

        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-90"></div>

        <a href="{{ $programa->url }}"
            aria-label="Ver más detalle de {{ $programa->titulo_completo }}"
            class="absolute inset-0 flex items-center justify-center z-30 opacity-0 group-hover:opacity-100 group-focus-visible:opacity-100 focus-visible:opacity-100 transition-opacity duration-300 bg-black/40 backdrop-blur-[2px] focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-[-4px]">
            <span class="px-6 py-2 border border-white text-white font-bold rounded-full hover:bg-white hover:text-unmsm-azul transition-colors shadow-2xl transform scale-90 group-hover:scale-100 group-focus-visible:scale-100 duration-300">
                {{ $esProximamente ? 'Próximamente' : 'Ver más detalle' }}
            </span>
        </a>

        <div class="absolute top-4 left-4 z-20 flex flex-wrap gap-2">
            <span class="px-3 py-1 {{ $badgeColor }} text-white text-xs font-bold rounded shadow-lg">{{ $badgeLabel }}</span>
            @if ($esProximamente)
                <span class="px-3 py-1 bg-unmsm-dorado text-unmsm-azul text-xs font-bold rounded shadow-lg">
                    Próximamente
                </span>
            @endif
        </div>

        <div class="absolute bottom-4 left-4 right-4 z-20 flex flex-wrap gap-2 transition-opacity duration-300 group-hover:opacity-10">
            @if($duracion)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-unmsm-dorado text-unmsm-azul text-xs font-bold rounded-full shadow-lg">
                    <x-far-clock aria-hidden="true" /> {{ $duracion }}
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 text-gray-800 text-xs font-bold rounded-full shadow-lg">
                <x-fas-university aria-hidden="true" /> {{ $programa->modalidad }}
            </span>
            @if($programa->horas_academicas)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/95 text-gray-800 text-xs font-bold rounded-full shadow-lg">
                    <x-fas-hourglass-half aria-hidden="true" /> {{ $programa->horas_academicas }} horas
                </span>
            @endif
        </div>
    </div>

    <div class="p-6 flex flex-col flex-1">
        <h3 class="text-xl font-bold text-gray-900 mb-3 leading-tight group-hover:text-unmsm-azul transition-colors font-serif">
            {{ $programa->titulo_completo }}
        </h3>
        <p class="text-gray-600 text-sm mb-6 leading-relaxed flex-1 line-clamp-4">
            {{ $descripcion }}
        </p>

        <div class="mt-auto space-y-2">
            <a href="{{ $programa->url }}"
                class="block w-full text-center py-2.5 rounded-lg border border-unmsm-azul text-unmsm-azul font-bold text-sm hover:bg-unmsm-azul hover:text-white focus-visible:bg-unmsm-azul focus-visible:text-white transition-all duration-300">
                {{ $primaryCtaLabel }}
            </a>
            @if($showBrochure && $programa->brochure_link)
                <a href="{{ $programa->brochure_link }}" target="_blank" rel="noopener noreferrer"
                    class="block w-full text-center py-2.5 rounded-lg bg-unmsm-dorado/10 text-unmsm-azul font-bold text-sm hover:bg-unmsm-dorado hover:text-white focus-visible:bg-unmsm-dorado focus-visible:text-white transition-all duration-300">
                    <x-fas-file-pdf class="mr-1" aria-hidden="true" /> Brochure
                </a>
            @endif
        </div>
    </div>
</div>

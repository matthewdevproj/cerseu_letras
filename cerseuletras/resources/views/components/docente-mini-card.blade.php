{{-- `denominacion` permite rotular a la persona responsable como «Coordinador»
     o «Coordinadora» según el programa (Obs. N.º 1). --}}
@props(['profesor', 'variant' => 'list', 'esCoordinador' => false, 'denominacion' => 'Coordinador'])

@php
    $isCoordinador = $variant === 'coordinador';
@endphp

<div @class([
    'rounded-xl p-4 md:p-5' => $isCoordinador,
    'bg-gradient-to-r from-unmsm-azul/5 to-unmsm-dorado/5 border border-unmsm-azul/20' => $isCoordinador,
    'group border border-gray-200 rounded-lg p-4 hover:shadow-lg hover:border-unmsm-azul/30 transition-all duration-300 cursor-pointer relative' => !$isCoordinador,
])>
    @unless($isCoordinador)
        <a href="{{ route('profesores.show', $profesor->slug) }}" class="absolute inset-0 z-0 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-unmsm-azul rounded-lg" aria-label="Ver perfil de {{ $profesor->nombre }}"></a>
    @endunless

    <div class="flex items-start gap-{{ $isCoordinador ? '3' : '4' }}">
        @if($profesor->foto)
            <img src="{{ $profesor->foto_url ?? asset('storage/' . $profesor->foto) }}" alt="{{ $profesor->nombre_completo ?? $profesor->nombre }}" loading="lazy" decoding="async"
                class="{{ $isCoordinador ? 'w-12 h-12' : 'w-12 h-12' }} rounded-full object-cover flex-shrink-0 border-2 border-white shadow">
        @else
            <div @class([
                'rounded-full flex items-center justify-center flex-shrink-0 transition-colors duration-300',
                'w-12 h-12 bg-unmsm-azul text-white' => $isCoordinador,
                'w-12 h-12 bg-unmsm-azul/10 group-hover:bg-unmsm-azul group-hover:text-white text-unmsm-azul font-bold text-lg' => !$isCoordinador,
            ])>
                @if($isCoordinador)
                    <x-fas-user-tie aria-hidden="true" />
                @else
                    {{ substr($profesor->nombre, 0, 1) }}
                @endif
            </div>
        @endif

        <div class="flex-1">
            @if($isCoordinador)
                {{-- Solo el encabezado: la etiqueta con la estrella que lo
                     repetía al lado se retiró por duplicar la denominación. --}}
                <div class="flex items-center gap-2 mb-1">
                    <h4 class="text-sm font-bold text-unmsm-azul uppercase tracking-wide">{{ $denominacion }} del Curso</h4>
                </div>
                <p class="text-base font-semibold text-gray-900">{{ $profesor->nombre }}</p>
            @else
                <div class="flex items-center justify-between gap-3 mb-2">
                    <div class="flex items-center gap-3 flex-1">
                        <h5 class="font-bold text-gray-800 group-hover:text-unmsm-azul text-base transition-colors duration-300">
                            {{ $profesor->nombre }}
                        </h5>
                        @if($esCoordinador)
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-unmsm-azul text-white text-xs font-semibold rounded flex-shrink-0">
                                <x-fas-star aria-hidden="true" /> {{ $denominacion }}
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('profesores.show', $profesor->slug) }}"
                       class="inline-flex items-center gap-2 px-3 py-1.5 bg-unmsm-azul/5 text-unmsm-azul hover:bg-unmsm-azul hover:text-white focus-visible:bg-unmsm-azul focus-visible:text-white text-xs font-semibold rounded-lg transition-all duration-300 relative z-10 group/btn flex-shrink-0">
                        <span class="whitespace-nowrap">Ver más información</span>
                        <x-fas-arrow-right class="text-xs group-hover/btn:translate-x-1 transition-transform" aria-hidden="true" />
                    </a>
                </div>
            @endif

            @if($profesor->email)
                <a href="mailto:{{ $profesor->email }}"
                    class="inline-flex items-center gap-1 text-sm text-unmsm-dorado hover:underline {{ $isCoordinador ? 'mt-1' : 'mb-2 relative z-10' }}"
                    @unless($isCoordinador) onclick="event.stopPropagation();" @endunless>
                    <x-fas-envelope class="text-xs" aria-hidden="true" />
                    <span class="select-text">{{ $profesor->email }}</span>
                </a>
            @endif

            <div class="flex flex-wrap items-center gap-{{ $isCoordinador ? '2' : '3' }} {{ $isCoordinador ? 'mt-3' : 'mt-3' }}">
                @if($profesor->orcid)
                    <a href="{{ $isCoordinador ? 'https://orcid.org/' . $profesor->orcid : $profesor->orcid }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-{{ $isCoordinador ? '2.5' : '3' }} py-1{{ $isCoordinador ? '' : '.5' }} bg-green-50 text-green-700 text-xs font-medium rounded-lg hover:bg-green-100 transition-colors relative z-10"
                       @unless($isCoordinador) onclick="event.stopPropagation();" @endunless>
                        <x-fab-orcid aria-hidden="true" /> <span>ORCID</span>
                    </a>
                @endif
                @if($profesor->cti_vitae)
                    <a href="{{ $profesor->cti_vitae }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-{{ $isCoordinador ? '2.5' : '3' }} py-1{{ $isCoordinador ? '' : '.5' }} bg-unmsm-azul/10 text-unmsm-azul text-xs font-medium rounded-lg hover:bg-unmsm-azul/20 transition-colors relative z-10"
                       @unless($isCoordinador) onclick="event.stopPropagation();" @endunless>
                        <x-fas-user-graduate aria-hidden="true" /> <span>CTI Vitae</span>
                    </a>
                @endif
                @if($profesor->linkedin)
                    <a href="{{ $profesor->linkedin }}" target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-1.5 px-{{ $isCoordinador ? '2.5' : '3' }} py-1{{ $isCoordinador ? '' : '.5' }} bg-indigo-50 text-indigo-700 text-xs font-medium rounded-lg hover:bg-indigo-100 transition-colors relative z-10"
                       @unless($isCoordinador) onclick="event.stopPropagation();" @endunless>
                        <x-fab-linkedin aria-hidden="true" /> <span>LinkedIn</span>
                    </a>
                @endif
                @if($isCoordinador)
                    <a href="{{ route('profesores.show', $profesor->slug) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1 bg-unmsm-azul/5 text-unmsm-azul hover:bg-unmsm-azul hover:text-white focus-visible:bg-unmsm-azul focus-visible:text-white text-xs font-semibold rounded-lg transition-all duration-300 ml-auto">
                        Ver más información <x-fas-arrow-right class="text-xs" aria-hidden="true" />
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

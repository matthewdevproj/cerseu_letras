@props([
    // 'escritorio' | 'movil'
    'variante' => 'escritorio',
])

@php
    $menu = \App\Models\MenuItem::arbol();
@endphp

@if ($variante === 'escritorio')
    @foreach ($menu as $item)
        @php $destino = $item->enlace; @endphp

        @if ($item->es_desplegable)
            <div class="relative group h-full flex items-center">
                {{-- Con destino propio es un enlace; sin él, solo abre el
                     desplegable y debe seguir siendo alcanzable por teclado. --}}
                @if ($destino)
                    <a href="{{ $destino }}" aria-haspopup="true"
                        @if ($item->nueva_pestana) target="_blank" rel="noopener noreferrer" @endif
                        class="nav-item text-white font-medium hover:text-unmsm-azul-soft transition py-4 flex items-center gap-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2 rounded {{ $item->esta_activo ? 'text-unmsm-azul-soft font-bold' : '' }}">
                        {{ $item->etiqueta }} <x-fas-angle-down class="text-xs mt-0.5" aria-hidden="true" />
                    </a>
                @else
                    <span tabindex="0" role="button" aria-haspopup="true"
                        class="nav-item text-white font-medium hover:text-unmsm-azul-soft transition py-4 flex items-center gap-1 cursor-pointer focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2 rounded">
                        {{ $item->etiqueta }} <x-fas-angle-down class="text-xs mt-0.5" aria-hidden="true" />
                    </span>
                @endif

                <div class="absolute top-full left-0 w-64 bg-white shadow-xl border-t-4 border-unmsm-azul rounded-b-md hidden group-hover:block group-focus-within:block text-gray-700 text-sm z-50">
                    @foreach ($item->hijos as $hijo)
                        @continue(! $hijo->enlace)
                        <a href="{{ $hijo->enlace }}"
                            @if ($hijo->nueva_pestana) target="_blank" rel="noopener noreferrer" @endif
                            class="block px-5 py-3 hover:bg-gray-50 focus-visible:bg-gray-50 {{ $loop->last ? '' : 'border-b border-gray-100' }}">
                            @if ($hijo->icono)
                                <x-dynamic-component :component="$hijo->icono" class="mr-2 text-unmsm-azul" aria-hidden="true" />
                            @endif{{ $hijo->etiqueta }}
                        </a>
                    @endforeach
                </div>
            </div>
        @elseif ($destino)
            <a href="{{ $destino }}"
                @if ($item->nueva_pestana) target="_blank" rel="noopener noreferrer" @endif
                class="nav-item text-white font-medium hover:text-unmsm-azul-soft transition py-4 flex items-center gap-1 focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2 rounded {{ $item->esta_activo ? 'text-unmsm-azul-soft font-bold' : '' }}">
                {{ $item->etiqueta }}
                @if ($item->nueva_pestana)
                    <x-fas-external-link-alt class="text-[10px]" aria-hidden="true" />
                @endif
            </a>
        @endif
    @endforeach
@else
    @foreach ($menu as $item)
        @php $destino = $item->enlace; @endphp

        @if ($item->es_desplegable)
            <div x-data="{ expanded: false }" class="border-b border-gray-50">
                <button @click="expanded = !expanded" type="button" :aria-expanded="expanded.toString()"
                    class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-unmsm-azul">
                    <span class="flex items-center font-semibold text-base">
                        @if ($item->icono)
                            <x-dynamic-component :component="$item->icono" class="w-6 text-center text-unmsm-azul/80 group-hover:text-unmsm-azul mr-3 transition-colors" aria-hidden="true" />
                        @endif
                        {{ $item->etiqueta }}
                    </span>
                    <x-fas-chevron-down class="text-xs text-gray-400 group-hover:text-unmsm-azul transition-transform duration-300"
                        x-bind:class="expanded ? 'rotate-180' : ''" aria-hidden="true" />
                </button>
                <div x-show="expanded" x-collapse class="bg-gray-50/80">
                    {{-- En escritorio la cabecera del desplegable es un enlace,
                         pero en móvil es el botón que despliega, así que su
                         destino quedaba inalcanzable: desde el menú no se podía
                         llegar a /programas, solo a las fichas filtradas. Se
                         ofrece como primera opción de la lista. --}}
                    @if ($destino)
                        <a href="{{ $destino }}" @click="mobileMenuOpen = false"
                            @if ($item->nueva_pestana) target="_blank" rel="noopener noreferrer" @endif
                            class="block px-6 py-3 pl-14 text-gray-700 hover:text-unmsm-azul hover:bg-unmsm-azul/5 text-sm font-semibold transition-colors border-l-2 border-transparent hover:border-unmsm-azul">
                            Ver todo: {{ $item->etiqueta }}
                        </a>
                    @endif
                    @foreach ($item->hijos as $hijo)
                        @continue(! $hijo->enlace)
                        {{-- Una subentrada que repite el destino del padre sobra:
                             ya está arriba como «Ver todo». --}}
                        @continue($hijo->enlace === $destino)
                        <a href="{{ $hijo->enlace }}" @click="mobileMenuOpen = false"
                            @if ($hijo->nueva_pestana) target="_blank" rel="noopener noreferrer" @endif
                            class="block px-6 py-3 pl-14 text-gray-600 hover:text-unmsm-azul hover:bg-unmsm-azul/5 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-unmsm-azul">{{ $hijo->etiqueta }}</a>
                    @endforeach
                </div>
            </div>
        @elseif ($destino)
            <a href="{{ $destino }}" @click="mobileMenuOpen = false"
                @if ($item->nueva_pestana) target="_blank" rel="noopener noreferrer" @endif
                class="flex items-center px-6 py-4 text-gray-800 font-semibold text-base hover:bg-gray-50 transition-colors border-b border-gray-50 group">
                @if ($item->icono)
                    <x-dynamic-component :component="$item->icono" class="w-6 text-center text-unmsm-azul/80 group-hover:text-unmsm-azul mr-3 transition-colors" aria-hidden="true" />
                @endif
                {{ $item->etiqueta }}
            </a>
        @endif
    @endforeach
@endif

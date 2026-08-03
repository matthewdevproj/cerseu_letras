@extends('admin.layout.app')

@section('title', 'Gestión de Testimonios')

@section('content')
    <!-- Header with Actions -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Testimonios de Egresados
            </h2>
            <p class="mt-1 text-sm text-gray-500">Gestiona las experiencias y comentarios de la comunidad estudiantil.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.testimonios.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand-navy hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-navy transition-colors">
                <x-fas-comment-medical class="mr-2" />
                Nuevo Testimonio
            </a>
        </div>
    </div>

    <!-- Filters / Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
        <form method="GET" action="{{ route('admin.testimonios.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-fas-search class="text-gray-400" />
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-brand-navy focus:border-brand-navy sm:text-sm transition duration-150 ease-in-out" placeholder="Buscar por autor o contenido...">
            </div>
            <div class="w-full md:w-48">
                <select name="estado" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-brand-navy focus:border-brand-navy sm:text-sm">
                    <option value="">Todos los Estados</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Publicado</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Oculto</option>
                </select>
            </div>
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-navy">
                <x-fas-filter class="mr-2" />
                Filtrar
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Autor
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif w-2/5">
                            Testimonio
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Programa
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Estado
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Acciones</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($testimonios as $testimonio)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($testimonio->photo)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $testimonio->photo_url }}" alt="{{ $testimonio->nombre }}" loading="lazy" decoding="async">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-brand-navy text-white flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($testimonio->nombre, 0, 2)) }}
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $testimonio->nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ $testimonio->created_at->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-600 italic line-clamp-2">
                                    <x-fas-quote-left class="text-brand-gold mr-1 opacity-60" />
                                    {{ Str::limit($testimonio->contenido, 100) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($testimonio->programa)
                                    <div class="flex flex-col">
                                        <span class="text-sm font-medium text-gray-900">{{ $testimonio->programa->nombre }}</span>
                                        @if($testimonio->programa->mencion)
                                            <span class="text-xs text-gray-500">{{ $testimonio->programa->mencion }}</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">Sin programa</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($testimonio->estado)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Publicado
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        Oculto
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.testimonios.edit', $testimonio) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Editar" aria-label="Editar">
                                        <x-fas-pencil-alt class="text-lg" />
                                    </a>
                                    <form action="{{ route('admin.testimonios.toggle', $testimonio) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="{{ $testimonio->estado ? 'text-orange-500 hover:text-orange-700' : 'text-green-500 hover:text-green-700' }} transition-colors" title="{{ $testimonio->estado ? 'Ocultar' : 'Publicar' }}" aria-label="{{ $testimonio->estado ? 'Ocultar' : 'Publicar' }}">
                                            <x-dynamic-component :component="'fas-' . ($testimonio->estado ? 'eye-slash' : 'eye')" class="text-lg" />
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('confirm-delete', { detail: { action: '{{ route('admin.testimonios.destroy', $testimonio) }}', name: '{{ addslashes($testimonio->nombre) }}', title: '¿Eliminar testimonio?' } }))"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            title="Eliminar" aria-label="Eliminar">
                                        <x-fas-trash class="text-lg" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <x-fas-quote-right class="text-2xl text-gray-400" />
                                    </div>
                                    <p class="text-gray-500">No hay testimonios registrados</p>
                                    <a href="{{ route('admin.testimonios.create') }}" class="mt-3 text-brand-navy text-sm font-medium hover:underline">
                                        Agregar el primer testimonio
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Paginación: el listado ya no trae todas las filas de golpe. --}}
        <div class="px-4 py-3 border-t border-gray-100">
            {!! $testimonios->onEachSide(1)->links() !!}
        </div>
        
        </div>
    </div>

@endsection
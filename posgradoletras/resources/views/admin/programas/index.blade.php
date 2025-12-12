@extends('admin.layout.app')

@section('title', 'Gestión de Programas')

@section('content')
    <!-- Header with Actions -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Programas Académicos
            </h2>
            <p class="mt-1 text-sm text-gray-500">Gestiona la oferta de Maestrías y Doctorados.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.programas.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand-red hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-colors">
                <i class="ph-bold ph-plus mr-2"></i>
                Nuevo Programa
            </a>
        </div>
    </div>

    <!-- Filters / Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
        <form method="GET" action="{{ route('admin.programas.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-brand-red focus:border-brand-red sm:text-sm transition duration-150 ease-in-out" placeholder="Buscar por nombre...">
            </div>
            <div class="w-full md:w-48">
                <select name="tipo" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-brand-red focus:border-brand-red sm:text-sm">
                    <option value="">Todos los tipos</option>
                    <option value="maestria" {{ request('tipo') == 'maestria' ? 'selected' : '' }}>Maestrías</option>
                    <option value="doctorado" {{ request('tipo') == 'doctorado' ? 'selected' : '' }}>Doctorados</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red">
                <i class="ph ph-funnel mr-2"></i>
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
                            Nombre del Programa
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Tipo
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Mención
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
                    @forelse($programas as $programa)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 {{ $programa->grado == 'maestria' ? 'bg-yellow-50 text-brand-gold' : 'bg-blue-50 text-brand-navy' }} rounded-full flex items-center justify-center">
                                        <i class="ph-fill {{ $programa->grado == 'maestria' ? 'ph-scroll' : 'ph-medal' }} text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $programa->nombre }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $programa->grado == 'maestria' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                    {{ ucfirst($programa->grado) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $programa->mencion ?? 'Sin mención' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($programa->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <span class="w-1.5 h-1.5 bg-gray-500 rounded-full mr-1.5"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('programas.show', $programa->slug) }}" target="_blank" class="text-brand-navy hover:text-brand-gold transition-colors" title="Ver en sitio web">
                                        <i class="ph-bold ph-eye text-lg"></i>
                                    </a>
                                    <a href="{{ route('admin.programas.edit', $programa) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Editar">
                                        <i class="ph-bold ph-pencil-simple text-lg"></i>
                                    </a>
                                    <form action="{{ route('admin.programas.toggle', $programa) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="{{ $programa->is_active ? 'text-orange-500 hover:text-orange-700' : 'text-green-500 hover:text-green-700' }} transition-colors" title="{{ $programa->is_active ? 'Desactivar' : 'Activar' }}">
                                            <i class="ph-bold ph-{{ $programa->is_active ? 'lock' : 'lock-open' }} text-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.programas.destroy', $programa) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este programa?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 transition-colors" title="Eliminar">
                                            <i class="ph-bold ph-trash text-lg"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ph ph-graduation-cap text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500">No hay programas registrados</p>
                                    <a href="{{ route('admin.programas.create') }}" class="mt-3 text-brand-red text-sm font-medium hover:underline">
                                        Crear el primer programa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($programas->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando <span class="font-medium">{{ $programas->firstItem() }}</span> a <span class="font-medium">{{ $programas->lastItem() }}</span> de <span class="font-medium">{{ $programas->total() }}</span> resultados
                        </p>
                    </div>
                    <div>
                        {{ $programas->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
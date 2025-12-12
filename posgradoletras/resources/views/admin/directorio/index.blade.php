@extends('admin.layout.app')

@section('title', 'Gestión de Directorio')

@section('content')
    <!-- Header with Actions -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Directorio de Posgrado
            </h2>
            <p class="mt-1 text-sm text-gray-500">Administra el personal del directorio de la Unidad de Posgrado.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.directorio.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold transition-colors">
                <i class="ph-bold ph-user-plus mr-2"></i>
                Nuevo Personal
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3">
            <i class="ph-fill ph-check-circle text-green-600 text-xl"></i>
            <span class="text-green-800 text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Filters / Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
        <form method="GET" action="{{ route('admin.directorio.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ph ph-magnifying-glass text-gray-400"></i>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-brand-gold focus:border-brand-gold sm:text-sm transition duration-150 ease-in-out" placeholder="Buscar por nombre o cargo...">
            </div>
            <div class="w-full md:w-56">
                <select name="unidad" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm">
                    <option value="">Todas las Unidades</option>
                    @foreach($unidades as $unidad)
                        <option value="{{ $unidad }}" {{ request('unidad') == $unidad ? 'selected' : '' }}>{{ $unidad }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold">
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
                                Unidad
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                                Nombre
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                                Cargo
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                                Contacto
                            </th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                                Mover
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
                    @forelse($directorio as $persona)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $persona->unidad_nombre == 'AUTORIDADES' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $persona->unidad_nombre }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-brand-navy text-white flex items-center justify-center font-bold text-sm">
                                        {{ strtoupper(substr($persona->nombre_persona, 0, 2)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $persona->nombre_persona }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 max-w-xs truncate" title="{{ $persona->cargo }}">{{ $persona->cargo }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm">
                                    @if($persona->correo_persona)
                                        <div class="text-gray-600 flex items-center gap-1">
                                            <i class="ph ph-envelope text-xs"></i>
                                            <span class="truncate max-w-[150px]" title="{{ $persona->correo_persona }}">{{ $persona->correo_persona }}</span>
                                        </div>
                                    @endif
                                    @if($persona->anexo)
                                        <div class="text-gray-500 flex items-center gap-1">
                                            <i class="ph ph-phone text-xs"></i>
                                            {{ $persona->anexo }}
                                        </div>
                                    @endif
                                    @if(!$persona->correo_persona && !$persona->anexo)
                                        <span class="text-gray-400">Sin contacto</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                <div class="flex justify-center gap-1">
                                    <form action="{{ route('admin.directorio.moveUp', $persona) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Subir">
                                            <i class="ph ph-arrow-up"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.directorio.moveDown', $persona) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Bajar">
                                            <i class="ph ph-arrow-down"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($persona->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.directorio.edit', $persona) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Editar">
                                        <i class="ph-bold ph-pencil-simple text-lg"></i>
                                    </a>
                                    <form action="{{ route('admin.directorio.toggle', $persona) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="{{ $persona->activo ? 'text-orange-500 hover:text-orange-700' : 'text-green-500 hover:text-green-700' }} transition-colors" title="{{ $persona->activo ? 'Desactivar' : 'Activar' }}">
                                            <i class="ph-bold ph-{{ $persona->activo ? 'lock' : 'lock-open' }} text-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.directorio.destroy', $persona) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este registro?');">
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
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ph ph-address-book text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500">No hay personal registrado en el directorio</p>
                                    <a href="{{ route('admin.directorio.create') }}" class="mt-3 text-brand-gold text-sm font-medium hover:underline">
                                        Agregar el primer registro
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($directorio->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 flex items-center justify-between sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando <span class="font-medium">{{ $directorio->firstItem() }}</span> a <span class="font-medium">{{ $directorio->lastItem() }}</span> de <span class="font-medium">{{ $directorio->total() }}</span> resultados
                        </p>
                    </div>
                    <div>
                        {{ $directorio->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

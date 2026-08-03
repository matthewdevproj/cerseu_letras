@extends('admin.layout.app')

@section('title', 'Gestión de Docentes')

@section('content')
    <!-- Header with Actions -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Plana Docente
            </h2>
            <p class="mt-1 text-sm text-gray-500">Administra los perfiles de los catedráticos e investigadores.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.docentes.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold transition-colors">
                <x-fas-user-plus class="mr-2" />
                Nuevo Docente
            </a>
        </div>
    </div>

    <!-- Filters / Search -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 p-4">
        <form method="GET" action="{{ route('admin.docentes.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-fas-search class="text-gray-400" />
                </div>
                <input type="text" name="search" value="{{ request('search') }}" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-brand-gold focus:border-brand-gold sm:text-sm transition duration-150 ease-in-out" placeholder="Buscar por nombre...">
            </div>
            <div class="w-full md:w-48">
                <select name="grado" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm">
                    <option value="">Todos los Grados</option>
                    <option value="Dr." {{ request('grado') == 'Dr.' ? 'selected' : '' }}>Doctor</option>
                    <option value="Mg." {{ request('grado') == 'Mg.' ? 'selected' : '' }}>Magíster</option>
                    <option value="Lic." {{ request('grado') == 'Lic.' ? 'selected' : '' }}>Licenciado</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold">
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
                            Docente
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Grado Académico
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Programas
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
                    @forelse($docentes as $docente)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($docente->foto)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $docente->foto) }}" alt="{{ $docente->nombre_completo }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-brand-navy text-white flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($docente->nombres, 0, 1)) }}{{ strtoupper(substr($docente->apellidos, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $docente->nombre_completo }}</div>
                                        <div class="text-xs text-gray-500">{{ $docente->email ?? 'Sin email' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if(str_contains($docente->grado ?? '', 'Dr'))
                                        <x-fas-medal class="text-brand-gold mr-2" />
                                    @else
                                        <x-fas-certificate class="text-gray-400 mr-2" />
                                    @endif
                                    <span class="text-sm text-gray-900">{{ $docente->grado ?? 'Sin grado' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($docente->programas->count() > 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $docente->programas->count() }} programa(s)
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-500">
                                        Sin asignar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($docente->estado)
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
                                    <a href="{{ route('admin.docentes.edit', $docente) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Editar" aria-label="Editar">
                                        <x-fas-edit class="text-lg" />
                                    </a>
                                    <form action="{{ route('admin.docentes.toggle', $docente) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="{{ $docente->estado ? 'text-orange-500 hover:text-orange-700' : 'text-green-500 hover:text-green-700' }} transition-colors" title="{{ $docente->estado ? 'Desactivar' : 'Activar' }}" aria-label="{{ $docente->estado ? 'Desactivar' : 'Activar' }}">
                                            <x-dynamic-component :component="'fas-' . ($docente->estado ? 'lock' : 'lock-open')" class="text-lg" />
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="window.dispatchEvent(new CustomEvent('confirm-delete', { detail: { action: '{{ route('admin.docentes.destroy', $docente) }}', name: '{{ addslashes($docente->nombre_completo) }}', title: '¿Eliminar docente?' } }))"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            title="Eliminar" aria-label="Eliminar">
                                        <x-fas-trash-alt class="text-lg" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <x-fas-chalkboard-teacher class="text-2xl text-gray-400" />
                                    </div>
                                    <p class="text-gray-500">No hay docentes registrados</p>
                                    <a href="{{ route('admin.docentes.create') }}" class="mt-3 text-brand-gold text-sm font-medium hover:underline">
                                        Registrar el primer docente
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
            {!! $docentes->onEachSide(1)->links() !!}
        </div>
        
        </div>
    </div>

@endsection
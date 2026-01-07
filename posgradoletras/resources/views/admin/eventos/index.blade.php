@extends('admin.layout.app')

@section('title', 'Gestión de Eventos')

@section('content')
    <!-- Header with Actions -->
    <div class="md:flex md:items-center md:justify-between mb-8">
        <div class="flex-1 min-w-0">
            <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
                Eventos
            </h2>
            <p class="mt-1 text-sm text-gray-500">Gestiona los eventos y actividades de la Unidad de Posgrado.</p>
        </div>
        <div class="mt-4 flex md:mt-0 md:ml-4">
            <a href="{{ route('admin.eventos.create') }}" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-brand-navy hover:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-navy transition-colors">
                <i class="fas fa-calendar-plus mr-2"></i>
                Nuevo Evento
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Evento
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Fechas
                        </th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider font-serif">
                            Enlace
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
                    @forelse($eventos as $evento)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($evento->imagen)
                                        <img class="h-16 w-24 rounded-lg object-cover" src="{{ asset('storage/' . $evento->imagen) }}" alt="{{ $evento->titulo }}">
                                    @else
                                        <div class="h-16 w-24 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <i class="fas fa-calendar-alt text-2xl text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $evento->titulo }}</div>
                                        @if($evento->descripcion)
                                            <div class="text-xs text-gray-500 line-clamp-1">{{ Str::limit($evento->descripcion, 60) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    <i class="far fa-calendar text-brand-gold mr-1"></i>
                                    {{ $evento->fecha_inicio->format('d/m/Y') }}
                                </div>
                                @if($evento->fecha_fin && $evento->fecha_fin->ne($evento->fecha_inicio))
                                    <div class="text-xs text-gray-500">
                                        hasta {{ $evento->fecha_fin->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($evento->tiene_url)
                                    @if($evento->es_pdf)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-file-pdf mr-1"></i> PDF
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-link mr-1"></i> Enlace
                                        </span>
                                    @endif
                                @else
                                    <span class="text-gray-400 text-xs">Sin enlace</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($evento->activo)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Activo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                        Inactivo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-3">
                                    <a href="{{ route('admin.eventos.edit', $evento) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Editar">
                                        <i class="fas fa-pencil-alt text-lg"></i>
                                    </a>
                                    <button type="button"
                                            onclick="openDeleteModal({{ $evento->id }}, '{{ addslashes($evento->titulo) }}')"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            title="Eliminar">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-calendar-alt text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500">No hay eventos registrados</p>
                                    <a href="{{ route('admin.eventos.create') }}" class="mt-3 text-brand-navy text-sm font-medium hover:underline">
                                        Agregar el primer evento
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($eventos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $eventos->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDeleteModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
                <div class="p-6 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-exclamation-triangle text-3xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">¿Eliminar evento?</h3>
                    <p class="text-gray-600" id="deleteModalMessage">Esta acción no se puede deshacer.</p>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" 
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 font-medium transition-colors">
                        Cancelar
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 font-medium transition-colors">
                            Sí, eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openDeleteModal(id, titulo) {
        document.getElementById('deleteForm').action = '/admin/eventos/' + id;
        document.getElementById('deleteModalMessage').innerHTML = '¿Estás seguro de eliminar el evento <strong>"' + titulo + '"</strong>? Esta acción no se puede deshacer.';
        document.getElementById('deleteModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeDeleteModal(); });
</script>
@endpush

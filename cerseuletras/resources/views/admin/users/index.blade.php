@extends('admin.layout.app')

@section('title', 'Usuarios')

@section('content')
    <div class="max-w-5xl mx-auto">

        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-800 mb-1">Usuarios del panel</h1>
                <p class="text-sm text-gray-500">
                    Quién puede entrar a la administración del sitio.
                </p>
            </div>
            <a href="{{ route('admin.users.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark text-sm font-medium">
                <x-fas-plus aria-hidden="true" /> Nuevo usuario
            </a>
        </div>

        @if ($adminsActivos <= 1)
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6 text-sm text-amber-800">
                <x-fas-triangle-exclamation class="mt-0.5 flex-shrink-0" aria-hidden="true" />
                <span>
                    Solo hay <strong>un administrador activo</strong>. Si pierdes el acceso a esa cuenta,
                    nadie podrá entrar al panel. Conviene crear un segundo administrador.
                </span>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-600 uppercase tracking-wider">
                        <tr>
                            <th class="px-4 py-3 text-left font-bold">Usuario</th>
                            <th class="px-4 py-3 text-left font-bold">Rol</th>
                            <th class="px-4 py-3 text-center font-bold">Estado</th>
                            <th class="px-4 py-3 text-right font-bold w-32">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($usuarios as $u)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <span class="block font-semibold text-gray-900">
                                        {{ $u->name }}
                                        @if ($u->id === auth()->id())
                                            <span class="ml-1 text-xs font-normal text-gray-400">(tú)</span>
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-500 break-all">{{ $u->email }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $u->isAdmin() ? 'bg-unmsm-azul/10 text-unmsm-azul' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $u->isAdmin() ? 'Administrador' : 'Usuario' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $u->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $u->is_active ? 'bg-green-600' : 'bg-gray-500' }}"></span>
                                        {{ $u->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('admin.users.edit', $u) }}"
                                            class="text-gray-400 hover:text-unmsm-azul transition-colors"
                                            aria-label="Editar {{ $u->name }}">
                                            <x-fas-pen-to-square aria-hidden="true" />
                                        </a>

                                        @if ($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle', $u) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="{{ $u->is_active ? 'text-orange-500 hover:text-orange-700' : 'text-green-500 hover:text-green-700' }} transition-colors"
                                                    aria-label="{{ $u->is_active ? 'Desactivar' : 'Activar' }} {{ $u->name }}">
                                                    <x-dynamic-component :component="'fas-' . ($u->is_active ? 'lock' : 'lock-open')" aria-hidden="true" />
                                                </button>
                                            </form>

                                            <button type="button"
                                                onclick="window.dispatchEvent(new CustomEvent('confirm-delete', {
                                                    detail: {
                                                        action: '{{ route('admin.users.destroy', $u) }}',
                                                        name: '{{ addslashes($u->name) }}',
                                                        title: '¿Eliminar usuario?'
                                                    }
                                                }))"
                                                class="text-red-500 hover:text-red-700 transition-colors"
                                                aria-label="Eliminar {{ $u->name }}">
                                                <x-fas-trash aria-hidden="true" />
                                            </button>
                                        @else
                                            {{-- Sobre la propia cuenta no se ofrecen baja ni borrado:
                                                 el controlador también los rechaza. --}}
                                            <span class="text-gray-200" title="No puedes desactivar ni eliminar tu propia cuenta">
                                                <x-fas-lock aria-hidden="true" />
                                            </span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

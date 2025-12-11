@extends('admin.layout.app')

@section('title', 'Nuevo Personal - Directorio')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="ph ph-house mr-1"></i> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ph ph-caret-right text-gray-400 mx-2"></i>
                        <a href="{{ route('admin.directorio.index') }}"
                            class="text-gray-500 hover:text-gray-700 text-sm">Directorio</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="ph ph-caret-right text-gray-400 mx-2"></i>
                        <span class="text-gray-700 text-sm font-medium">Nuevo Personal</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
            Agregar Personal al Directorio
        </h2>
        <p class="mt-1 text-sm text-gray-500">Completa la información del nuevo miembro del directorio.</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
        <form action="{{ route('admin.directorio.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Unidad -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="unidad_nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Unidad/Sección <span class="text-red-500">*</span>
                    </label>
                    <select name="unidad_nombre" id="unidad_nombre"
                        class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm"
                        onchange="toggleNuevaUnidad(this)">
                        <option value="">Seleccionar...</option>
                        @foreach($unidades as $unidad)
                            <option value="{{ $unidad }}" {{ old('unidad_nombre') == $unidad ? 'selected' : '' }}>{{ $unidad }}
                            </option>
                        @endforeach
                        <option value="__nueva__" {{ old('unidad_nombre') == '__nueva__' ? 'selected' : '' }}>+ Crear nueva
                            unidad</option>
                    </select>
                    @error('unidad_nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div id="nuevaUnidadContainer" class="hidden">
                    <label for="nueva_unidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Nueva Unidad
                    </label>
                    <input type="text" name="nueva_unidad" id="nueva_unidad" value="{{ old('nueva_unidad') }}"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm"
                        placeholder="Ej: SECRETARÍA GENERAL">
                </div>
            </div>

            <!-- Nombre y Cargo -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="nombre_persona" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre_persona" id="nombre_persona" value="{{ old('nombre_persona') }}"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm"
                        placeholder="Ej: Dra. María García López" required>
                    @error('nombre_persona')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="cargo" class="block text-sm font-medium text-gray-700 mb-1">
                        Cargo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="cargo" id="cargo" value="{{ old('cargo') }}"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm"
                        placeholder="Ej: Directora de la Unidad de Posgrado" required>
                    @error('cargo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Contacto -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="correo_persona" class="block text-sm font-medium text-gray-700 mb-1">
                        Correo Electrónico
                    </label>
                    <input type="email" name="correo_persona" id="correo_persona" value="{{ old('correo_persona') }}"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm"
                        placeholder="correo@unmsm.edu.pe">
                    @error('correo_persona')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="anexo" class="block text-sm font-medium text-gray-700 mb-1">
                        Teléfono / Anexo
                    </label>
                    <input type="text" name="anexo" id="anexo" value="{{ old('anexo') }}"
                        class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm"
                        placeholder="Ej: 2803">
                    @error('anexo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Orden -->
            <div class="w-full md:w-1/4">
                <label for="orden" class="block text-sm font-medium text-gray-700 mb-1">
                    Orden de aparición
                </label>
                <input type="number" name="orden" id="orden" value="{{ old('orden', 0) }}" min="0"
                    class="block w-full py-2 px-3 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-brand-gold focus:border-brand-gold sm:text-sm">
                <p class="mt-1 text-xs text-gray-500">Números menores aparecen primero.</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.directorio.index') }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-gold">
                    <i class="ph-bold ph-floppy-disk mr-2"></i>
                    Guardar
                </button>
            </div>
        </form>
    </div>

    <script>
        function toggleNuevaUnidad(select) {
            const container = document.getElementById('nuevaUnidadContainer');
            if (select.value === '__nueva__') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        // Check on page load
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('unidad_nombre');
            if (select.value === '__nueva__') {
                document.getElementById('nuevaUnidadContainer').classList.remove('hidden');
            }
        });
    </script>
@endsection
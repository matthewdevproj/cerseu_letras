@extends('admin.layout.app')

@section('title', 'Nuevo Personal - Directorio')

@push('styles')
    <style>
        :root {
            --primary-color: #761e23;
            --accent-color: #d4af37;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .form-label {
            font-weight: 600;
            color: #344767;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-full bg-gradient-to-br from-brand-gold to-yellow-500 flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-user-plus text-2xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
                    Agregar Personal al Directorio
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Completa la información del nuevo miembro del directorio.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.directorio.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Unidad -->
                        <div>
                            <label for="unidad_nombre"
                                class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                Unidad/Sección <span class="text-red-500">*</span>
                            </label>
                            <select name="unidad_nombre" id="unidad_nombre"
                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                onchange="toggleNuevaUnidad(this)">
                                <option value="">Seleccionar...</option>
                                @foreach($unidades as $unidad)
                                    <option value="{{ $unidad }}" {{ old('unidad_nombre') == $unidad ? 'selected' : '' }}>
                                        {{ $unidad }}
                                    </option>
                                @endforeach
                                <option value="__nueva__" {{ old('unidad_nombre') == '__nueva__' ? 'selected' : '' }}>+ Crear
                                    nueva unidad</option>
                            </select>
                            @error('unidad_nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="nuevaUnidadContainer" class="hidden">
                            <label for="nueva_unidad"
                                class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                Nueva Unidad
                            </label>
                            <input type="text" name="nueva_unidad" id="nueva_unidad" value="{{ old('nueva_unidad') }}"
                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                placeholder="Ej: SECRETARÍA GENERAL">
                        </div>

                        <!-- Nombre y Cargo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombre_persona"
                                    class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Nombre Completo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nombre_persona" id="nombre_persona"
                                    value="{{ old('nombre_persona') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                    placeholder="Ej: Dra. María García López" required>
                                @error('nombre_persona')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cargo" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Cargo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="cargo" id="cargo" value="{{ old('cargo') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                    placeholder="Ej: Directora de la Unidad de Posgrado" required>
                                @error('cargo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Contacto -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="correo_persona"
                                    class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Correo Electrónico
                                </label>
                                <input type="email" name="correo_persona" id="correo_persona"
                                    value="{{ old('correo_persona') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                    placeholder="correo@unmsm.edu.pe">
                                @error('correo_persona')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="anexo" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Teléfono / Anexo
                                </label>
                                <input type="text" name="anexo" id="anexo" value="{{ old('anexo') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                    placeholder="Ej: 2803">
                                @error('anexo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Orden -->
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <label for="orden" class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                Orden de Aparición
                            </label>
                            <input type="number" name="orden" id="orden" value="{{ old('orden', 0) }}" min="0"
                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
                            <p class="mt-2 text-xs text-gray-500">Números menores aparecen primero dentro de la misma
                                unidad.</p>
                        </div>

                        <!-- Tips -->
                        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                            <h4 class="text-xs font-bold text-blue-800 uppercase mb-2 flex items-center gap-2">
                                <i class="fas fa-lightbulb"></i> Consejos
                            </h4>
                            <ul class="text-xs text-blue-700 space-y-2">
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                                    <span>Incluye el grado académico en el nombre (Ej: Dr., Dra., Mg.)</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                                    <span>El cargo debe ser descriptivo y completo</span>
                                </li>
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check-circle text-blue-500 mt-0.5"></i>
                                    <span>Puedes crear nuevas unidades según sea necesario</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.directorio.index') }}"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Personal
                    </button>
                </div>
            </form>
        </div>
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
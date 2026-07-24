@extends('admin.layout.app')

@section('title', 'Nuevo Testimonio')

@push('styles')
<style>
    :root {
        /* --brand ya viene de admin.layout.app; se reutiliza aquí */
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
        <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
            Agregar Nuevo Testimonio
        </h2>
        <p class="mt-1 text-sm text-gray-500">Los testimonios ayudan a transmitir la experiencia de nuestros egresados.</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.testimonios.store') }}" method="POST" enctype="multipart/form-data"
                x-data="{ submitting: false }" @submit="submitting = true">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                Nombre del Autor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                placeholder="Ej: María García López" required>
                            @error('nombre')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Programa -->
                        <div>
                            <label for="programa_id" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                Programa <span class="text-red-500">*</span>
                            </label>
                            <select name="programa_id" id="programa_id"
                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold" required>
                                <option value="">Seleccionar programa...</option>
                                @foreach($programas as $programa)
                                    <option value="{{ $programa->id }}" {{ old('programa_id') == $programa->id ? 'selected' : '' }}>
                                        {{ $programa->grado }} en {{ $programa->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('programa_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contenido -->
                        <div>
                            <label for="contenido" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                Testimonio <span class="text-red-500">*</span>
                            </label>
                            <textarea name="contenido" id="contenido" rows="6"
                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                placeholder="Escribe el testimonio del egresado aquí..." required>{{ old('contenido') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">Se recomienda entre 50 y 200 palabras.</p>
                            @error('contenido')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1 space-y-6">
                        <!-- Foto -->
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <x-admin-file-upload mode="direct" name="photo" accept="image/*" layout="inline"
                                with-live-preview preview-size="w-24 h-24" placeholder-icon="fa-user" stack-on-narrow label="Foto del Autor"
                                file-button-class="file:bg-brand-gold file:text-white hover:file:bg-yellow-600"
                                help-text="JPG, PNG. Máximo 2MB." />
                        </div>

                        <!-- Estado -->
                        <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                            <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                Estado de Publicación
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="hidden" name="estado" value="0">
                                <input type="checkbox" name="estado" value="1"
                                    class="h-5 w-5 text-brand-gold focus:ring-brand-gold border-gray-300 rounded"
                                    {{ old('estado', true) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    Publicado
                                </span>
                            </label>
                            <p class="mt-2 text-xs text-gray-500">Los testimonios publicados aparecerán en el sitio web.</p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.testimonios.index') }}"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        <x-fas-arrow-left class="mr-2" /> Volver al Listado
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg hover:shadow-xl transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                        <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak aria-hidden="true" />
                        <x-fas-save class="mr-2" x-show="!submitting" aria-hidden="true" />
                        <span x-text="submitting ? 'Guardando...' : 'Guardar Testimonio'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

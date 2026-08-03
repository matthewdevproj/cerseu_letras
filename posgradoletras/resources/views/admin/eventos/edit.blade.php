@extends('admin.layout.app')

@section('title', 'Editar Evento')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.eventos.index') }}"
                class="text-brand-red hover:underline text-sm mb-2 inline-flex items-center">
                <x-fas-arrow-left class="mr-2" /> Volver a Eventos
            </a>
            <h2 class="text-2xl font-serif font-bold text-gray-900">Editar Evento</h2>
            <p class="mt-1 text-sm text-gray-500">Modifique los datos del evento.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.eventos.update', $evento) }}" method="POST" data-avisar-sin-guardar enctype="multipart/form-data"
            class="bg-white shadow-sm border border-gray-200 rounded-lg"
            x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            @method('PUT')

            <div class="p-6 space-y-6">
                <!-- Título -->
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                        Título del Evento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $evento->titulo) }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red @error('titulo') border-red-300 @enderror"
                        placeholder="Ej: Conferencia de Literatura Peruana">
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción (opcional)
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="3"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red"
                        placeholder="Breve descripción del evento...">{{ old('descripcion', $evento->descripcion) }}</textarea>
                </div>

                <!-- Imagen Actual + Nueva -->
                <x-admin-file-upload mode="direct" name="imagen" label="Imagen/Afiche" accept="image/*"
                    layout="inline" with-live-preview preview-size="w-24 h-24"
                    :current-path="$evento->imagen"
                    help-text="PNG, JPG, WEBP, GIF hasta 5MB. Deja vacío para mantener la imagen actual." />

                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio"
                            value="{{ old('fecha_inicio', $evento->fecha_inicio->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red">
                        @error('fecha_inicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Fin (opcional)
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin"
                            value="{{ old('fecha_fin', $evento->fecha_fin?->format('Y-m-d')) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red">
                        @error('fecha_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- URL y Tipo -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="tipo_url" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Enlace
                        </label>
                        <select name="tipo_url" id="tipo_url"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red">
                            <option value="">Sin enlace</option>
                            <option value="externo" {{ old('tipo_url', $evento->tipo_url) == 'externo' ? 'selected' : '' }}>
                                Enlace externo</option>
                            <option value="pdf" {{ old('tipo_url', $evento->tipo_url) == 'pdf' ? 'selected' : '' }}>PDF
                            </option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL del Enlace
                        </label>
                        <input type="text" name="url" id="url" value="{{ old('url', $evento->url) }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red"
                            placeholder="https://ejemplo.com o URL del PDF">
                        @error('url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Orden y Estado -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="orden" class="block text-sm font-medium text-gray-700 mb-1">
                            Orden (para home)
                        </label>
                        <input type="number" name="orden" id="orden" value="{{ old('orden', $evento->orden) }}" min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-red focus:ring-brand-red">
                        <p class="mt-1 text-xs text-gray-500">Menor número = aparece primero</p>
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', $evento->activo) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-red shadow-sm focus:border-brand-red focus:ring-brand-red">
                            <span class="ml-2 text-sm text-gray-700">Evento activo (visible en el sitio)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 rounded-b-lg">
                <a data-salir-sin-guardar href="{{ route('admin.eventos.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" :disabled="submitting"
                    class="px-6 py-2 bg-brand-red text-white rounded-lg hover:bg-red-800 font-medium transition-colors disabled:opacity-60 disabled:cursor-not-allowed">
                    <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak aria-hidden="true" />
                    <x-fas-save class="mr-2" x-show="!submitting" aria-hidden="true" />
                    <span x-text="submitting ? 'Actualizando...' : 'Actualizar Evento'"></span>
                </button>
            </div>
        </form>
    </div>
@endsection
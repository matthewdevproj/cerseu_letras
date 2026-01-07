@extends('admin.layout.app')

@section('title', 'Nuevo Evento')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('admin.eventos.index') }}"
                class="text-brand-navy hover:underline text-sm mb-2 inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i> Volver a Eventos
            </a>
            <h2 class="text-2xl font-serif font-bold text-gray-900">Nuevo Evento</h2>
            <p class="mt-1 text-sm text-gray-500">Complete el formulario para agregar un nuevo evento.</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.eventos.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white shadow-sm border border-gray-200 rounded-lg">
            @csrf

            <div class="p-6 space-y-6">
                <!-- Título -->
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                        Título del Evento <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy @error('titulo') border-red-300 @enderror"
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
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy"
                        placeholder="Breve descripción del evento...">{{ old('descripcion') }}</textarea>
                </div>

                <!-- Imagen -->
                <div>
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-1">
                        Imagen/Afiche
                    </label>

                    <!-- Preview Container (hidden by default) -->
                    <div id="imagePreviewContainer" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-4">
                            <img id="imagePreview" src="" alt="Preview" class="h-24 w-auto rounded-lg object-cover">
                            <div class="flex-1">
                                <p id="imageName" class="text-sm font-medium text-gray-900"></p>
                                <p id="imageSize" class="text-xs text-gray-500"></p>
                            </div>
                            <button type="button" onclick="removeImage()" class="text-red-500 hover:text-red-700 p-2">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div id="uploadArea"
                        class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-brand-navy transition-colors cursor-pointer">
                        <div class="space-y-1 text-center">
                            <i class="fas fa-image text-4xl text-gray-400"></i>
                            <div class="flex text-sm text-gray-600">
                                <label for="imagen"
                                    class="relative cursor-pointer bg-white rounded-md font-medium text-brand-navy hover:underline focus-within:outline-none">
                                    <span>Subir imagen</span>
                                    <input id="imagen" name="imagen" type="file" class="sr-only" accept="image/*"
                                        onchange="previewImage(event)">
                                </label>
                                <p class="pl-1">o arrastrar y soltar</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, WEBP, GIF hasta 5MB</p>
                        </div>
                    </div>
                    @error('imagen')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="fecha_inicio" id="fecha_inicio" value="{{ old('fecha_inicio') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy">
                        @error('fecha_inicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha de Fin (opcional)
                        </label>
                        <input type="date" name="fecha_fin" id="fecha_fin" value="{{ old('fecha_fin') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy">
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
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy">
                            <option value="">Sin enlace</option>
                            <option value="externo" {{ old('tipo_url') == 'externo' ? 'selected' : '' }}>Enlace externo
                            </option>
                            <option value="pdf" {{ old('tipo_url') == 'pdf' ? 'selected' : '' }}>PDF</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="url" class="block text-sm font-medium text-gray-700 mb-1">
                            URL del Enlace
                        </label>
                        <input type="text" name="url" id="url" value="{{ old('url') }}"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy"
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
                        <input type="number" name="orden" id="orden" value="{{ old('orden', 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-brand-navy focus:ring-brand-navy">
                        <p class="mt-1 text-xs text-gray-500">Menor número = aparece primero</p>
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="activo" value="1" {{ old('activo', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-brand-navy shadow-sm focus:border-brand-navy focus:ring-brand-navy">
                            <span class="ml-2 text-sm text-gray-700">Evento activo (visible en el sitio)</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 rounded-b-lg">
                <a href="{{ route('admin.eventos.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-brand-navy text-white rounded-lg hover:bg-blue-900 font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i> Guardar Evento
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imageName').textContent = file.name;
                    document.getElementById('imageSize').textContent = formatFileSize(file.size);
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                    document.getElementById('uploadArea').classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            document.getElementById('imagen').value = '';
            document.getElementById('imagePreview').src = '';
            document.getElementById('imagePreviewContainer').classList.add('hidden');
            document.getElementById('uploadArea').classList.remove('hidden');
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    </script>
@endpush
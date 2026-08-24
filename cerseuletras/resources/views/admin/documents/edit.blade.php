@extends('admin.layout.app')

@section('title', 'Editar Documento')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                    <li><a href="{{ route('admin.documents.index') }}"
                            class="text-gray-500 hover:text-brand-gold">Documentos</a></li>
                    <li class="flex items-center"><x-fas-chevron-right class="text-gray-400 mx-2 text-xs" /><span
                            class="text-gray-700">Editar</span></li>
                </ol>
            </nav>
            <h2 class="text-2xl font-serif font-bold text-gray-900">Editar Documento</h2>
            <p class="mt-1 text-sm text-gray-500">Modifica la información del documento</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Current File Preview -->
            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200 flex items-center gap-4">
                @if($document->is_pdf)
                    <div class="h-12 w-12 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                        <x-fas-file-pdf class="text-xl" />
                    </div>
                @elseif($document->is_image)
                    <img class="h-12 w-12 rounded-lg object-cover" src="{{ $document->url }}"
                        alt="{{ $document->original_name }}">
                @else
                    <div class="h-12 w-12 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center">
                        <x-fas-file class="text-xl" />
                    </div>
                @endif
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">{{ $document->original_name }}</p>
                    <a href="{{ $document->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-600 hover:underline">Ver archivo
                        actual</a>
                </div>
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                <select name="type" id="type" required
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
                    <option value="plan" {{ old('type', $document->type) == 'plan' ? 'selected' : '' }}>Plan de Estudios
                    </option>
                    <option value="horario" {{ old('type', $document->type) == 'horario' ? 'selected' : '' }}>Horario</option>
                    <option value="imagen" {{ old('type', $document->type) == 'imagen' ? 'selected' : '' }}>Imagen</option>
                    <option value="general" {{ old('type', $document->type) == 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título (para mostrar)</label>
                <input type="text" name="title" id="title"
                    value="{{ old('title', $document->title) }}"
                    placeholder="Ej: Resolución Rectoral N° 001234"
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
                <p class="mt-1 text-xs text-gray-500">Si está vacío, se mostrará el nombre del archivo.</p>
            </div>

            <!-- Original Name -->
            <div>
                <label for="original_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Archivo</label>
                <input type="text" name="original_name" id="original_name"
                    value="{{ old('original_name', $document->original_name) }}" required
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
            </div>

            <!-- URL -->
            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL del Archivo</label>
                <input type="text" name="url" id="url" value="{{ old('url', $document->url) }}"
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
            </div>

            <!-- Replace File -->
            <div class="border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <x-fas-sync class="text-brand-azul mr-1" /> Reemplazar Archivo (opcional)
                </label>
                <div class="flex gap-2">
                    <input type="file" name="file" id="file" class="hidden">
                    <button type="button" onclick="document.getElementById('file').click()"
                        class="px-4 py-2.5 bg-brand-azul text-white rounded-lg hover:bg-unmsm-azul transition-all flex items-center gap-2">
                        <x-fas-upload /> Subir Nuevo Archivo
                    </button>
                </div>
                <div id="file_status" class="mt-2 text-xs text-gray-500"></div>
            </div>

            <!-- Published -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <label class="flex items-center cursor-pointer">
                    <input type="hidden" name="published" value="0">
                    <input type="checkbox" name="published" value="1"
                        class="h-5 w-5 text-brand-gold border-gray-300 rounded" {{ old('published', $document->published) ? 'checked' : '' }}>
                    <span class="ml-3 text-sm font-medium text-gray-700">Documento publicado</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                <a href="{{ route('admin.documents.index') }}"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <x-fas-arrow-left class="mr-2" /> Volver
                </a>
                <button type="submit"
                    class="inline-flex items-center px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg">
                    <x-fas-save class="mr-2" /> Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function () {
            if (this.files.length > 0) {
                document.getElementById('file_status').textContent = 'Archivo seleccionado: ' + this.files[0].name;
                document.getElementById('file_status').className = 'mt-2 text-xs text-green-600';
            }
        });
    </script>
@endsection
@extends('admin.layout.app')

@section('title', 'Nuevo Documento')

@section('content')
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3 text-sm">
                    <li><a href="{{ route('admin.documents.index') }}"
                            class="text-gray-500 hover:text-brand-gold">Documentos</a></li>
                    <li class="flex items-center"><x-fas-chevron-right class="text-gray-400 mx-2 text-xs" /><span
                            class="text-gray-700">Nuevo</span></li>
                </ol>
            </nav>
            <h2 class="text-2xl font-serif font-bold text-gray-900">Subir Documento</h2>
            <p class="mt-1 text-sm text-gray-500">Sube un archivo PDF o imagen al sistema</p>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-6">
            @csrf

            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-sm text-red-600 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Type -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                <select name="type" id="type" required
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
                    <option value="plan" {{ old('type') == 'plan' ? 'selected' : '' }}>Plan de Estudios</option>
                    <option value="horario" {{ old('type') == 'horario' ? 'selected' : '' }}>Horario</option>
                    <option value="imagen" {{ old('type') == 'imagen' ? 'selected' : '' }}>Imagen</option>
                    <option value="general" {{ old('type', 'general') == 'general' ? 'selected' : '' }}>General</option>
                </select>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Título (para mostrar)</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}"
                    placeholder="Ej: Resolución Rectoral N° 001234"
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
                <p class="mt-1 text-xs text-gray-500">Opcional. Si está vacío, se mostrará el nombre del archivo.</p>
            </div>

            <!-- File Upload -->
            <div class="border border-gray-200 rounded-lg p-4">
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    <x-fas-file-upload class="text-brand-red mr-1" /> Archivo
                </label>
                <div class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="text-xs text-gray-500 mb-1 block">Opción 1: URL externa</label>
                        <input type="url" name="url" id="url" value="{{ old('url') }}"
                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:border-brand-red transition-colors"
                            placeholder="https://ejemplo.com/archivo.pdf">
                    </div>
                    <div class="flex gap-2">
                        <input type="file" name="file" id="file" class="hidden">
                        <button type="button" onclick="document.getElementById('file').click()"
                            class="px-4 py-2.5 bg-brand-red text-white rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                            <x-fas-upload /> Subir Archivo
                        </button>
                    </div>
                </div>
                <div id="file_status" class="mt-2 text-xs text-gray-500"></div>
                <p class="mt-2 text-xs text-gray-400">Si subes un archivo, este tendrá prioridad sobre la URL. Máx 10MB.</p>
            </div>

            <!-- Published -->
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <label class="flex items-center cursor-pointer">
                    <input type="hidden" name="published" value="0">
                    <input type="checkbox" name="published" value="1"
                        class="h-5 w-5 text-brand-gold border-gray-300 rounded" {{ old('published', true) ? 'checked' : '' }}>
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
                    <x-fas-save class="mr-2" /> Guardar Documento
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
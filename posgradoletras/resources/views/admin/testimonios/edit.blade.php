@extends('admin.layout.app')

@section('title', 'Editar Testimonio')

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
            @if($testimonio->photo)
                <img src="{{ asset('storage/' . $testimonio->photo) }}" alt="{{ $testimonio->nombre }}" 
                     class="w-14 h-14 object-cover rounded-full border-2 border-brand-gold">
            @else
                <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                    <i class="ph ph-user text-2xl"></i>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
                    Editar Testimonio
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $testimonio->nombre }} · {{ $testimonio->programa->nombre ?? 'Sin programa' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.testimonios.update', $testimonio) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                Nombre del Autor <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $testimonio->nombre) }}"
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
                                    <option value="{{ $programa->id }}" {{ old('programa_id', $testimonio->programa_id) == $programa->id ? 'selected' : '' }}>
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
                                placeholder="Escribe el testimonio del egresado aquí..." required>{{ old('contenido', $testimonio->contenido) }}</textarea>
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
                            <label for="photo" class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                Foto del Autor
                            </label>
                            <div class="flex flex-col items-center gap-4">
                                @if($testimonio->photo)
                                    <img id="photo-preview" src="{{ asset('storage/' . $testimonio->photo) }}" alt="{{ $testimonio->nombre }}" 
                                         class="w-24 h-24 rounded-full object-cover border-2 border-brand-gold">
                                    <div id="photo-preview-placeholder" class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300 hidden">
                                        <i class="ph ph-user text-4xl"></i>
                                    </div>
                                @else
                                    <div id="photo-preview-placeholder" class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 border-2 border-dashed border-gray-300">
                                        <i class="ph ph-user text-4xl"></i>
                                    </div>
                                    <img id="photo-preview" src="" alt="Vista previa" class="w-24 h-24 rounded-full object-cover border-2 border-brand-gold hidden">
                                @endif
                                <input type="file" name="photo" id="photo" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-brand-gold file:text-white hover:file:bg-yellow-600 cursor-pointer"
                                    onchange="previewPhoto(this)">
                                <p class="text-xs text-gray-500 text-center">JPG, PNG. Máximo 2MB. Dejar vacío para mantener la foto actual.</p>
                            </div>
                            @error('photo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                    {{ old('estado', $testimonio->estado) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    Publicado
                                </span>
                            </label>
                            <p class="mt-2 text-xs text-gray-500">Los testimonios publicados aparecerán en el sitio web.</p>
                        </div>

                        <!-- Info Metadata -->
                        <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                            <h4 class="text-xs font-bold text-blue-800 uppercase mb-2">Información</h4>
                            <div class="text-xs text-blue-700 space-y-1">
                                <p><strong>Creado:</strong> {{ $testimonio->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Actualizado:</strong> {{ $testimonio->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.testimonios.index') }}"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        <i class="ph ph-arrow-left mr-2"></i> Volver al Listado
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg hover:shadow-xl transition-all">
                        <i class="ph-bold ph-floppy-disk mr-2"></i>
                        Actualizar Testimonio
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewPhoto(input) {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-preview-placeholder');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if (placeholder) {
                        placeholder.classList.add('hidden');
                    }
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection

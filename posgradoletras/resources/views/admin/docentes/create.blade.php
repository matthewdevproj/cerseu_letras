@extends('admin.layout.app')

@section('title', 'Nuevo Docente')

@push('styles')
<style>
    :root {
        --primary-color: #761e23;
        --primary-dark: #5a161a;
        --accent-color: #d4af37;
        --border-radius: 1rem;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        background: white;
    }

    .nav-tabs {
        border-bottom: 1px solid #f0f2f5;
        gap: 0.5rem;
        padding: 0 1rem;
    }

    .nav-tabs .nav-link {
        border: none;
        border-radius: 0.5rem 0.5rem 0 0;
        color: #8392ab;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.875rem 1.5rem;
        transition: var(--transition);
        background: transparent;
    }

    .nav-tabs .nav-link:hover {
        background: #f8f9fa;
        color: var(--primary-color);
    }

    .nav-tabs .nav-link.active {
        background: var(--primary-color);
        color: white;
        box-shadow: 0 4px 6px rgba(118, 30, 35, 0.3);
    }

    .form-label {
        font-weight: 600;
        color: #344767;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control, .form-select {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 0.625rem 1rem;
        transition: var(--transition);
        font-size: 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(118, 30, 35, 0.15);
    }

    .btn-primary {
        background: var(--primary-color);
        border: none;
        box-shadow: 0 4px 6px rgba(118, 30, 35, 0.3);
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
    }

    .programa-checkbox {
        transition: var(--transition);
    }

    .programa-checkbox:hover {
        background: #f8f9fa;
        border-color: var(--primary-color);
    }

    .programa-checkbox.checked {
        background: rgba(118, 30, 35, 0.05);
        border-color: var(--primary-color);
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .tab-pane.show { animation: slideIn 0.3s ease-out; }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
            Agregar Nuevo Docente
        </h2>
        <p class="mt-1 text-sm text-gray-500">Completa la información del nuevo docente en las diferentes secciones.</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.docentes.store') }}" method="POST" enctype="multipart/form-data" id="form-docente">
                @csrf

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-6 flex border-b border-gray-200" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active px-4 py-3 text-sm font-medium border-b-2 border-brand-red text-brand-red" 
                           data-tab="personal" href="#personal" onclick="switchTab(event, 'personal')">
                            <i class="fas fa-user mr-2"></i> Datos Personales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           data-tab="contacto" href="#contacto" onclick="switchTab(event, 'contacto')">
                            <i class="fas fa-envelope mr-2"></i> Contacto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           data-tab="academico" href="#academico" onclick="switchTab(event, 'academico')">
                            <i class="fas fa-book-open mr-2"></i> Info Académica
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           data-tab="programas" href="#programas" onclick="switchTab(event, 'programas')">
                            <i class="fas fa-graduation-cap mr-2"></i> Programas
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB 1: Datos Personales -->
                    <div id="personal" class="tab-pane show">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombres" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Nombres <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nombres" id="nombres" value="{{ old('nombres') }}"
                                    class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                    placeholder="Ej: María Elena" required>
                                @error('nombres')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="apellidos" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Apellidos <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos') }}"
                                    class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold"
                                    placeholder="Ej: García López" required>
                                @error('apellidos')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="grado" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Grado Académico
                                </label>
                                <select name="grado" id="grado"
                                    class="form-select block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:ring-brand-gold focus:border-brand-gold">
                                    <option value="">Seleccionar...</option>
                                    <option value="Dr." {{ old('grado') == 'Dr.' ? 'selected' : '' }}>Dr. (Doctor)</option>
                                    <option value="Dra." {{ old('grado') == 'Dra.' ? 'selected' : '' }}>Dra. (Doctora)</option>
                                    <option value="Mg." {{ old('grado') == 'Mg.' ? 'selected' : '' }}>Mg. (Magíster)</option>
                                    <option value="Lic." {{ old('grado') == 'Lic.' ? 'selected' : '' }}>Lic. (Licenciado/a)</option>
                                </select>
                                @error('grado')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="foto" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Foto de Perfil
                                </label>
                                <input type="file" name="foto" id="foto" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-gold file:text-white hover:file:bg-yellow-600 cursor-pointer">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG. Máximo 2MB.</p>
                                @error('foto')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="hidden" name="estado" value="0">
                                <input type="checkbox" name="estado" value="1"
                                    class="h-5 w-5 text-brand-gold focus:ring-brand-gold border-gray-300 rounded"
                                    {{ old('estado', true) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    Docente activo <span class="text-gray-500">(visible en el sitio web)</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- TAB 2: Contacto -->
                    <div id="contacto" class="tab-pane hidden">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="email" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Correo Electrónico
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}"
                                    class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                    placeholder="docente@unmsm.edu.pe">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-bold text-gray-700 mb-4 flex items-center gap-2">
                                <i class="fas fa-globe text-brand-gold"></i> Perfiles Académicos
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="orcid" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                        ORCID
                                    </label>
                                    <input type="text" name="orcid" id="orcid" value="{{ old('orcid') }}"
                                        class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                        placeholder="https://orcid.org/0000-0000-0000-0000">
                                    @error('orcid')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cti_vitae" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                        CTI Vitae
                                    </label>
                                    <input type="url" name="cti_vitae" id="cti_vitae" value="{{ old('cti_vitae') }}"
                                        class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                        placeholder="https://ctivitae.concytec.gob.pe/...">
                                    @error('cti_vitae')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="linkedin" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                        LinkedIn
                                    </label>
                                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin') }}"
                                        class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                        placeholder="https://linkedin.com/in/...">
                                    @error('linkedin')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Info Académica -->
                    <div id="academico" class="tab-pane hidden">
                        <div class="space-y-6">
                            <div>
                                <label for="biografia" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Biografía
                                </label>
                                <textarea name="biografia" id="biografia" rows="5"
                                    class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                    placeholder="Breve descripción del perfil académico del docente...">{{ old('biografia') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Descripción general del docente, su trayectoria y logros.</p>
                                @error('biografia')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="lineas_investigacion" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                        Líneas de Investigación
                                    </label>
                                    <textarea name="lineas_investigacion" id="lineas_investigacion" rows="4"
                                        class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                        placeholder="Una línea por renglón...">{{ old('lineas_investigacion') }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Ingrese cada línea de investigación en un renglón separado.</p>
                                    @error('lineas_investigacion')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Grupo de Investigación</label>
                                    <div class="grid grid-cols-1 gap-3">
                                        <input type="text" name="grupo_investigacion[nombre]" id="grupo_investigacion_nombre"
                                               value="{{ old('grupo_investigacion.nombre') }}"
                                               class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                               placeholder="Nombre del grupo">
                                        <input type="url" name="grupo_investigacion[link]" id="grupo_investigacion_link"
                                               value="{{ old('grupo_investigacion.link') }}"
                                               class="form-control block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                               placeholder="URL del grupo (https://...)">
                                    </div>
                                    @error('grupo_investigacion.nombre')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                    @error('grupo_investigacion.link')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: Programas -->
                    <div id="programas" class="tab-pane hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2 text-blue-800">
                                <i class="fas fa-info-circle text-xl"></i>
                                <p class="text-sm font-medium">Seleccione los programas de posgrado donde participa este docente.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($programas as $programa)
                                <label class="programa-checkbox flex items-center p-4 border border-gray-200 rounded-lg hover:border-brand-red cursor-pointer transition-all">
                                    <input type="checkbox" name="programas[]" value="{{ $programa->id }}"
                                        class="h-5 w-5 text-brand-gold focus:ring-brand-gold border-gray-300 rounded"
                                        {{ in_array($programa->id, old('programas', [])) ? 'checked' : '' }}
                                        onchange="this.closest('.programa-checkbox').classList.toggle('checked', this.checked)">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-gray-800 block">{{ $programa->nombre }}</span>
                                        <span class="text-xs text-gray-500">{{ $programa->grado }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('programas')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.docentes.index') }}"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all">
                        <i class="fas fa-arrow-left mr-2"></i> Volver al Listado
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg hover:shadow-xl transition-all">
                        <i class="fas fa-save mr-2"></i>
                        Guardar Docente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(event, tabId) {
            event.preventDefault();
            
            // Hide all tab panes
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('show');
            });
            
            // Remove active state from all nav links
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active', 'border-b-2', 'border-brand-red', 'text-brand-red');
                link.classList.add('text-gray-500');
            });
            
            // Show selected tab pane
            const selectedPane = document.getElementById(tabId);
            if (selectedPane) {
                selectedPane.classList.remove('hidden');
                selectedPane.classList.add('show');
            }
            
            // Activate clicked nav link
            event.target.classList.add('active', 'border-b-2', 'border-brand-red', 'text-brand-red');
            event.target.classList.remove('text-gray-500');
        }
    </script>
@endsection

@extends('admin.layout.app')

@section('title', 'Nuevo Docente')

@push('styles')
<style>
    :root {
        /* --brand y --brand-dark ya vienen de admin.layout.app; se reutilizan aquí */
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
        color: var(--brand);
    }

    .nav-tabs .nav-link.active {
        background: var(--brand);
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
        border-color: var(--brand);
        box-shadow: 0 0 0 0.2rem rgba(118, 30, 35, 0.15);
    }

    .btn-primary {
        background: var(--brand);
        border: none;
        box-shadow: 0 4px 6px rgba(118, 30, 35, 0.3);
    }

    .btn-primary:hover {
        background: var(--brand-dark);
        transform: translateY(-2px);
    }

    .programa-checkbox {
        transition: var(--transition);
    }

    .programa-checkbox:hover {
        background: #f8f9fa;
        border-color: var(--brand);
    }

    .programa-checkbox.checked {
        background: rgba(118, 30, 35, 0.05);
        border-color: var(--brand);
    }

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
            <form action="{{ route('admin.docentes.store') }}" method="POST" enctype="multipart/form-data" id="form-docente"
                x-data="{ submitting: false, tab: 'personal' }" @submit="submitting = true">
                @csrf

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-6 flex border-b border-gray-200" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium" :class="tab === 'personal' ? 'active border-b-2 border-brand-red text-brand-red' : 'text-gray-500 hover:text-brand-red'"
                           href="#personal" @click.prevent="tab = 'personal'">
                            <x-fas-user class="mr-2" aria-hidden="true" /> Datos Personales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium" :class="tab === 'contacto' ? 'active border-b-2 border-brand-red text-brand-red' : 'text-gray-500 hover:text-brand-red'"
                           href="#contacto" @click.prevent="tab = 'contacto'">
                            <x-fas-envelope class="mr-2" aria-hidden="true" /> Contacto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium" :class="tab === 'academico' ? 'active border-b-2 border-brand-red text-brand-red' : 'text-gray-500 hover:text-brand-red'"
                           href="#academico" @click.prevent="tab = 'academico'">
                            <x-fas-book-open class="mr-2" aria-hidden="true" /> Info Académica
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium" :class="tab === 'programas' ? 'active border-b-2 border-brand-red text-brand-red' : 'text-gray-500 hover:text-brand-red'"
                           href="#programas" @click.prevent="tab = 'programas'">
                            <x-fas-graduation-cap class="mr-2" aria-hidden="true" /> Programas
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB 1: Datos Personales -->
                    <div id="personal" x-show="tab === 'personal'" x-cloak>
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
                                <x-admin-file-upload mode="direct" name="foto" label="Foto de Perfil"
                                    accept="image/*" layout="inline" with-live-preview preview-size="w-20 h-20"
                                    help-text="JPG, PNG. Máximo 2MB." />
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
                    <div id="contacto" x-show="tab === 'contacto'" x-cloak>
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
                                <x-fas-globe class="text-brand-gold" /> Perfiles Académicos
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
                    <div id="academico" x-show="tab === 'academico'" x-cloak>
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
                    <div id="programas" x-show="tab === 'programas'" x-cloak>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2 text-blue-800">
                                <x-fas-info-circle class="text-xl" />
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
                                        <span class="text-sm font-medium text-gray-800 block">{{ $programa->titulo_completo }}</span>
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
                        <x-fas-arrow-left class="mr-2" /> Volver al Listado
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-6 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg hover:shadow-xl transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                        <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak aria-hidden="true" />
                        <x-fas-save class="mr-2" x-show="!submitting" aria-hidden="true" />
                        <span x-text="submitting ? 'Guardando...' : 'Guardar Docente'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

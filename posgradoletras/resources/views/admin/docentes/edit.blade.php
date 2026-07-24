@extends('admin.layout.app')

@section('title', 'Editar Docente')

@push('styles')
<style>
    :root {
        /* --brand y --brand-dark ya vienen de admin.layout.app; se reutilizan aquí */
        --accent-color: #d4af37;
        --border-radius: 1rem;
        --transition: all 0.25s ease;
        --muted: #6b7280;
        --border: #e5e7eb;
        --soft: #f9fafb;
    }

    /* Card */
    .card {
        border: 1px solid var(--border);
        border-radius: var(--border-radius);
        box-shadow: 0 10px 25px -15px rgba(0, 0, 0, 0.25);
        background: white;
        overflow: hidden;
    }

    /* Tabs */
    .nav-tabs {
        gap: .25rem;
        border-bottom: 1px solid var(--border) !important;
        padding-bottom: .25rem;
    }
    .nav-tabs .nav-link {
        border: none !important;
        border-radius: .75rem .75rem 0 0;
        color: var(--muted);
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1rem;
        transition: var(--transition);
        user-select: none;
        text-decoration: none;
    }
    .nav-tabs .nav-link:hover {
        color: var(--brand);
        background: #fff5f5;
    }
    .nav-tabs .nav-link.active {
        background: var(--brand) !important;
        color: white !important;
        box-shadow: 0 8px 18px rgba(118, 30, 35, 0.25);
    }

    /* Labels */
    .form-label {
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }

    /* Inputs consistent */
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="url"],
    select,
    textarea {
        border: 1px solid #d1d5db !important;
        border-radius: .75rem !important;
        transition: var(--transition);
        outline: none !important;
    }
    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    select:focus,
    textarea:focus {
        border-color: var(--accent-color) !important;
        box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.18) !important;
    }

    textarea {
        min-height: 120px;
        resize: vertical;
        background: white;
    }

    /* Tab panes */
    .tab-content { padding-top: .5rem; }

    /* Soft info blocks */
    .soft-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: .9rem;
        padding: 1rem;
    }
    .soft-warn {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: .9rem;
        padding: 1rem;
    }

    /* Avatar / photo */
    .current-photo {
        border: 3px solid #eef2f7;
        transition: var(--transition);
    }
    .current-photo:hover {
        border-color: var(--accent-color);
        transform: translateY(-1px);
    }

    /* Table style similar to docentes */
    .docentes-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--border);
        border-radius: 0.9rem;
        overflow: hidden;
        background: white;
    }
    .docentes-table thead th {
        background: #f8fafc;
        color: #374151;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: .06em;
        padding: .85rem .9rem;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    .docentes-table tbody td {
        padding: .75rem .9rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .docentes-table tbody tr:hover td { background: var(--soft); }
    .docentes-table tbody tr:last-child td { border-bottom: none; }

    @media (max-width: 768px) {
        .nav-tabs .nav-link { border-radius: .75rem; }
        .docentes-table thead { display: none; }
        .docentes-table, .docentes-table tbody, .docentes-table tr, .docentes-table td { display: block; width: 100%; }
        .docentes-table tr { border-bottom: 1px solid #f1f5f9; }
        .docentes-table td {
            border: none !important;
            padding: .6rem .85rem;
        }
        .docentes-table td[data-label]::before {
            content: attr(data-label);
            display: block;
            font-size: .7rem;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: .35rem;
        }
    }
</style>
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            @if($docente->foto)
                <img src="{{ asset('storage/' . $docente->foto) }}" alt="{{ $docente->nombre_completo }}"
                     class="current-photo w-16 h-16 object-cover rounded-full">
            @else
                <div class="w-16 h-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                    <x-fas-user class="text-2xl" />
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
                    {{ $docente->nombre_completo }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $docente->grado ?? 'Docente' }} · {{ $docente->programas->count() }} programa(s) asociado(s)
                </p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.docentes.update', $docente) }}" method="POST" enctype="multipart/form-data" id="form-docente"
                x-data="{ submitting: false, tab: 'personal' }" @submit="submitting = true">
                @csrf
                @method('PUT')

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-6 flex flex-wrap" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link" :class="tab === 'personal' ? 'active' : ''" href="#personal" @click.prevent="tab = 'personal'">
                            <x-fas-user aria-hidden="true" /> Datos Personales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="tab === 'contacto' ? 'active' : ''" href="#contacto" @click.prevent="tab = 'contacto'">
                            <x-fas-envelope aria-hidden="true" /> Contacto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="tab === 'academico' ? 'active' : ''" href="#academico" @click.prevent="tab = 'academico'">
                            <x-fas-book-open aria-hidden="true" /> Info Académica
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" :class="tab === 'programas' ? 'active' : ''" href="#programas" @click.prevent="tab = 'programas'">
                            <x-fas-graduation-cap aria-hidden="true" /> Programas
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB 1: Datos Personales -->
                    <div id="personal" x-show="tab === 'personal'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nombres" class="form-label block">
                                    Nombres <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nombres" id="nombres" value="{{ old('nombres', $docente->nombres) }}"
                                    class="block w-full py-2.5 px-4" placeholder="Ej: María Elena" required>
                                @error('nombres')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="apellidos" class="form-label block">
                                    Apellidos <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="apellidos" id="apellidos" value="{{ old('apellidos', $docente->apellidos) }}"
                                    class="block w-full py-2.5 px-4" placeholder="Ej: García López" required>
                                @error('apellidos')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="grado" class="form-label block">Grado Académico</label>
                                <select name="grado" id="grado" class="block w-full py-2.5 px-4">
                                    <option value="">Seleccionar...</option>
                                    <option value="Dr." {{ old('grado', $docente->grado) == 'Dr.' ? 'selected' : '' }}>Dr. (Doctor)</option>
                                    <option value="Dra." {{ old('grado', $docente->grado) == 'Dra.' ? 'selected' : '' }}>Dra. (Doctora)</option>
                                    <option value="Mg." {{ old('grado', $docente->grado) == 'Mg.' ? 'selected' : '' }}>Mg. (Magíster)</option>
                                    <option value="Lic." {{ old('grado', $docente->grado) == 'Lic.' ? 'selected' : '' }}>Lic. (Licenciado/a)</option>
                                </select>
                                @error('grado')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-admin-file-upload mode="direct" name="foto" label="Foto de Perfil"
                                    accept="image/*" layout="inline" with-live-preview preview-size="w-20 h-20"
                                    :current-path="$docente->foto"
                                    help-text="JPG, PNG, WEBP, GIF. Máximo 5MB. Dejar vacío para mantener la foto actual." />
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="hidden" name="estado" value="0">
                                <input type="checkbox" name="estado" value="1"
                                    class="h-5 w-5 text-brand-gold border-gray-300 rounded"
                                    {{ old('estado', $docente->estado) ? 'checked' : '' }}>
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
                                <label for="email" class="form-label block">Correo Electrónico</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $docente->email) }}"
                                    class="block w-full py-2.5 px-4" placeholder="docente@unmsm.edu.pe">
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
                                    <label for="orcid" class="form-label block">ORCID</label>
                                    <input type="text" name="orcid" id="orcid" value="{{ old('orcid', $docente->orcid) }}"
                                        class="block w-full py-2.5 px-4" placeholder="https://orcid.org/0000-0000-0000-0000">
                                    @error('orcid')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="cti_vitae" class="form-label block">CTI Vitae</label>
                                    <input type="url" name="cti_vitae" id="cti_vitae" value="{{ old('cti_vitae', $docente->cti_vitae) }}"
                                        class="block w-full py-2.5 px-4" placeholder="https://ctivitae.concytec.gob.pe/...">
                                    @error('cti_vitae')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="linkedin" class="form-label block">LinkedIn</label>
                                    <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin', $docente->linkedin) }}"
                                        class="block w-full py-2.5 px-4" placeholder="https://linkedin.com/in/...">
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
                                <label for="biografia" class="form-label block">Biografía</label>
                                <textarea name="biografia" id="biografia" rows="5"
                                    class="block w-full py-2.5 px-4"
                                    placeholder="Breve descripción del perfil académico del docente...">{{ old('biografia', $docente->biografia) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Descripción general del docente, su trayectoria y logros.</p>
                                @error('biografia')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="lineas_investigacion" class="form-label block">Líneas de Investigación</label>
                                    <textarea name="lineas_investigacion" id="lineas_investigacion" rows="4"
                                        class="block w-full py-2.5 px-4"
                                        placeholder="Una línea por renglón...">{{ old('lineas_investigacion', is_array($docente->lineas_investigacion) ? implode("\n", $docente->lineas_investigacion) : $docente->lineas_investigacion) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500">Ingrese cada línea de investigación en un renglón separado.</p>
                                    @error('lineas_investigacion')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="form-label block">Grupo de Investigación</label>
                                    <div class="grid grid-cols-1 gap-3">
                                        <input type="text" name="grupo_investigacion[nombre]" id="grupo_investigacion_nombre"
                                               value="{{ old('grupo_investigacion.nombre', $docente->grupo_investigacion['nombre'] ?? '') }}"
                                               class="block w-full py-2.5 px-4"
                                               placeholder="Nombre del grupo">
                                        <input type="url" name="grupo_investigacion[link]" id="grupo_investigacion_link"
                                               value="{{ old('grupo_investigacion.link', $docente->grupo_investigacion['link'] ?? '') }}"
                                               class="block w-full py-2.5 px-4"
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

                    <!-- TAB 4: Programas (Solo Lectura) -->
                    <div id="programas" x-show="tab === 'programas'" x-cloak>
                        <div class="soft-warn mb-6">
                            <div class="flex items-center gap-2 text-amber-800">
                                <x-fas-info-circle class="text-xl" />
                                <p class="text-sm font-medium">
                                    La asignación de docentes a programas se gestiona desde la vista de edición de cada programa.
                                </p>
                            </div>
                        </div>

                        @if($docente->programas->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="docentes-table">
                                    <thead>
                                        <tr>
                                            <th style="width: 50%">Programa</th>
                                            <th style="width: 20%">Tipo</th>
                                            <th style="width: 20%">Rol</th>
                                            <th style="width: 10%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($docente->programas as $programa)
                                            <tr>
                                                <td data-label="Programa">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-lg bg-brand-red/10 flex items-center justify-center text-brand-red">
                                                            <x-fas-graduation-cap class="text-xl" />
                                                        </div>
                                                        <div>
                                                            <div class="text-sm font-semibold text-gray-800">{{ $programa->titulo_completo }}</div>
                                                            <div class="text-xs text-gray-500">{{ $programa->codigo ?? '' }}</div>
                                                            @if($programa->pivot->es_coordinador)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-brand-gold text-white mt-1">
                                                                    Coordinador
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>

                                                <td data-label="Tipo">
                                                    <span class="text-sm text-gray-700">{{ $programa->grado }}</span>
                                                </td>

                                                <td data-label="Rol">
                                                    <span class="text-sm text-gray-700">
                                                        {{ $programa->pivot->rol ?: '—' }}
                                                    </span>
                                                </td>

                                                <td data-label="Acción">
                                                    <a href="{{ route('admin.programas.edit', $programa) }}"
                                                       class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600"
                                                       title="Editar programa">
                                                        <x-fas-external-link-alt />
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                                <x-fas-graduation-cap class="text-5xl text-gray-300 mb-4" />
                                <p class="text-gray-500">Este docente no está asignado a ningún programa.</p>
                                <p class="text-sm text-gray-400 mt-1">
                                    Puedes asignarlo desde la vista de edición de un programa.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.docentes.index') }}"
                        class="inline-flex items-center px-4 h-11 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <x-fas-arrow-left class="mr-2" /> Volver al Listado
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-6 h-11 rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg disabled:opacity-60 disabled:cursor-not-allowed">
                        <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak aria-hidden="true" />
                        <x-fas-save class="mr-2" x-show="!submitting" aria-hidden="true" />
                        <span x-text="submitting ? 'Actualizando...' : 'Actualizar Docente'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

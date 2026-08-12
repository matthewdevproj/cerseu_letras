@extends('admin.layout.app')

@section('title', 'Editar Programa')

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
        .nav-tabs .nav-link.active i {
            color: white !important;
        }
        .nav-tabs .nav-link i {
            color: inherit;
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
        input[type="number"]:focus,
        input[type="url"]:focus,
        select:focus,
        textarea:focus {
            border-color: var(--accent-color) !important;
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.18) !important;
        }
        textarea {
            min-height: 110px;
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

        /* Ciclo cards */
        .ciclo-card {
            border: 1px solid var(--border);
            border-radius: 0.9rem;
            margin-bottom: 1.25rem;
            background: white;
            box-shadow: 0 8px 20px -16px rgba(0, 0, 0, 0.35);
            transition: var(--transition);
            overflow: hidden;
        }
        .ciclo-card:hover {
            box-shadow: 0 16px 30px -22px rgba(0, 0, 0, 0.45);
            transform: translateY(-1px);
        }
        .ciclo-header {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: white;
            padding: 1rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--accent-color);
        }
        .badge-ciclo {
            background-color: rgba(255, 255, 255, 0.18);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 700;
        }
        .cursos-list { background: white; }

        /* Curso row */
        .curso-row {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: grid;
            grid-template-columns: 2fr 110px 1.5fr 44px;
            gap: .75rem;
            align-items: center;
            transition: var(--transition);
        }
        .curso-row:hover { background: var(--soft); }
        .curso-row input {
            height: 42px;
            background: white;
        }
        .curso-row:last-child { border-bottom: none; }

        /* Tabla docentes */
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
            .curso-row { grid-template-columns: 1fr; }
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
                <div class="w-12 h-12 rounded-lg bg-brand-red flex items-center justify-center text-white">
                    <x-fas-graduation-cap class="text-2xl" />
                </div>
                <div>
                    <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
                        {{ $programa->titulo_completo }}
                    </h2>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card">
            <div class="p-6">
                <form action="{{ route('admin.programas.update', $programa) }}" method="POST" data-avisar-sin-guardar enctype="multipart/form-data" id="form-programa"
                    x-data="{ submitting: false, tab: 'basico' }" @submit="submitting = true">
                    @csrf
                    @method('PUT')

                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs mb-6 flex flex-wrap" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" :class="tab === 'basico' ? 'active' : ''" href="#basico" @click.prevent="tab = 'basico'">
                                <x-fas-info-circle /> Básico
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="tab === 'contenido' ? 'active' : ''" href="#contenido" @click.prevent="tab = 'contenido'">
                                <x-fas-file-alt /> Contenido
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="tab === 'plan' ? 'active' : ''" href="#plan" @click.prevent="tab = 'plan'">
                                <x-fas-book-open /> Plan de Estudios
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="tab === 'docentes' ? 'active' : ''" href="#docentes" @click.prevent="tab = 'docentes'">
                                <x-fas-users /> Plana Docente
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" :class="tab === 'config' ? 'active' : ''" href="#config" @click.prevent="tab = 'config'">
                                <x-fas-cog /> Config
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- TAB 1: Información Básica -->
                        <div id="basico" x-show="tab === 'basico'" x-cloak>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label for="grado" class="form-label block">
                                        Tipo <span class="text-red-500">*</span>
                                    </label>
                                    <select name="grado" id="grado" class="block w-full py-2.5 px-4" required>
                                        <option value="Maestría" {{ old('grado', $programa->grado) == 'Maestría' ? 'selected' : '' }}>Maestría</option>
                                        <option value="Doctorado" {{ old('grado', $programa->grado) == 'Doctorado' ? 'selected' : '' }}>Doctorado</option>
                                        <option value="Diplomado" {{ old('grado', $programa->grado) == 'Diplomado' ? 'selected' : '' }}>Diplomado</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="modalidad" class="form-label block">Modalidad</label>
                                    <select name="modalidad" id="modalidad" class="block w-full py-2.5 px-4">
                                        <option value="Presencial" {{ old('modalidad', $programa->modalidad) == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                                        <option value="Semipresencial" {{ old('modalidad', $programa->modalidad) == 'Semipresencial' ? 'selected' : '' }}>Semipresencial</option>
                                        <option value="Virtual" {{ old('modalidad', $programa->modalidad) == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                <div>
                                    <label for="nombre" class="form-label block">
                                        Nombre <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $programa->nombre) }}"
                                        class="block w-full py-2.5 px-4" placeholder="Ej: Lingüística" required>
                                </div>

                                <div>
                                    <label for="mencion" class="form-label block">Mención</label>
                                    <input type="text" name="mencion" id="mencion" value="{{ old('mencion', $programa->mencion) }}"
                                        class="block w-full py-2.5 px-4" placeholder="Ej: Lingüística Hispánica">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                                <div>
                                    <label for="vacantes" class="form-label block">Vacantes</label>
                                    <input type="number" name="vacantes" id="vacantes" value="{{ old('vacantes', $programa->vacantes) }}"
                                        class="block w-full py-2.5 px-4" min="0" placeholder="30">
                                </div>
                                <div>
                                    <label for="duracion" class="form-label block">Duración (sem)</label>
                                    <input type="number" name="duracion" id="duracion" value="{{ old('duracion', $programa->duracion) }}"
                                        class="block w-full py-2.5 px-4" min="1" placeholder="4">
                                </div>
                                <div>
                                    <label for="creditos" class="form-label block">Créditos</label>
                                    <input type="number" name="creditos" id="creditos" value="{{ old('creditos', $programa->creditos) }}"
                                        class="block w-full py-2.5 px-4" min="0" placeholder="72">
                                </div>
                                <div>
                                    <label for="horas_academicas" class="form-label block">Horas Académicas</label>
                                    <input type="number" name="horas_academicas" id="horas_academicas" value="{{ old('horas_academicas', $programa->horas_academicas) }}"
                                        class="block w-full py-2.5 px-4" min="0" placeholder="480">
                                    <p class="mt-1 text-xs text-gray-400">Se muestra en «Información Clave», debajo de Créditos. Vacío: no aparece.</p>
                                </div>
                            </div>

                            {{-- Denominación del título que otorga: rótulo y contenido,
                                 ambos editables y ambos opcionales (Obs. N.º 4). --}}
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                                <div>
                                    <label for="grado_otorga_label" class="form-label block">Denominación · rótulo</label>
                                    <input type="text" name="grado_otorga_label" id="grado_otorga_label" value="{{ old('grado_otorga_label', $programa->grado_otorga_label) }}"
                                        class="block w-full py-2.5 px-4" maxlength="100" placeholder="Otorga / Grado que otorga">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="grado_otorga" class="form-label block">Denominación · contenido</label>
                                    <input type="text" name="grado_otorga" id="grado_otorga" value="{{ old('grado_otorga', $programa->grado_otorga) }}"
                                        class="block w-full py-2.5 px-4" maxlength="255" placeholder="Diploma en Curaduría con Énfasis en...">
                                </div>
                                <p class="md:col-span-3 -mt-3 text-xs text-gray-400">
                                    Se muestra bajo el título en la portada del programa, como «Rótulo: contenido».
                                    Si dejas el contenido vacío no se muestra nada: no se completa solo con «Grado que otorga».
                                </p>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                                <div>
                                    <label for="fecha_limite_inscripcion" class="form-label block">Fecha Límite Inscripción</label>
                                    <input type="text" name="fecha_limite_inscripcion" id="fecha_limite_inscripcion" value="{{ old('fecha_limite_inscripcion', $programa->fecha_limite_inscripcion) }}"
                                        class="block w-full py-2.5 px-4" placeholder="25 de septiembre de 2026">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Contenido -->
                        <div id="contenido" x-show="tab === 'contenido'" x-cloak>
                            <div class="space-y-6">
                                <div>
                                    <label for="sumilla" class="form-label block">Sumilla</label>
                                    <textarea name="sumilla" id="sumilla" rows="3" class="block w-full py-2.5 px-4" placeholder="Breve descripción del programa...">{{ old('sumilla', $programa->sumilla) }}</textarea>
                                </div>
                                <div>
                                    <label for="por_que_text" class="form-label block">¿Por qué elegir este programa?</label>
                                    <textarea name="por_que_text" id="por_que_text" rows="4" class="block w-full py-2.5 px-4" placeholder="Razones para elegir...">{{ old('por_que_text', $programa->por_que_text) }}</textarea>
                                </div>

                                <!-- Objetivos Académicos (JSON) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <label class="form-label block mb-3">
                                        <x-fas-bullseye class="mr-1" /> Objetivos Académicos
                                    </label>
                                    <div id="objetivos-list" class="space-y-2"></div>
                                    <button type="button" onclick="agregarObjetivo()" 
                                        class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                        <x-fas-plus class="mr-1" /> Agregar Objetivo
                                    </button>
                                    <input type="hidden" id="objetivos_academicos" name="objetivos_academicos">
                                </div>

                                <!-- Perfil del Ingresante (JSON) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <label class="form-label block mb-3">
                                        <x-fas-user-graduate class="mr-1" /> Perfil del Ingresante
                                    </label>
                                    <div id="ingresante-list" class="space-y-2"></div>
                                    <button type="button" onclick="agregarIngresante()" 
                                        class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                        <x-fas-plus class="mr-1" /> Agregar Item
                                    </button>
                                    <input type="hidden" id="perfil_ingresante" name="perfil_ingresante">
                                </div>

                                <!-- Perfil del Graduado (JSON) -->
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <label class="form-label block mb-3">
                                        <x-fas-award class="mr-1" /> Perfil del Graduado
                                    </label>
                                    <div id="graduado-list" class="space-y-2"></div>
                                    <button type="button" onclick="agregarGraduado()" 
                                        class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                        <x-fas-plus class="mr-1" /> Agregar Item
                                    </button>
                                    <input type="hidden" id="perfil_graduado" name="perfil_graduado">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: Plan de Estudios -->
                        <div id="plan" x-show="tab === 'plan'" x-cloak>
                            <div class="soft-info mb-6">
                                <div class="flex items-center gap-2 text-blue-800">
                                    <x-fas-info-circle class="text-xl" />
                                    <p class="text-sm font-medium">Gestión Visual: Agrega cursos organizados por ciclo/semestre. Los datos se guardan como JSON.</p>
                                </div>
                            </div>

                            <div id="ciclos-container"></div>
                            <div id="electivos-container" class="mt-6"></div>

                            <div class="mt-4 flex flex-wrap gap-3">
                                <button type="button" onclick="agregarCiclo()"
                                    class="inline-flex items-center px-4 h-11 border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                    <x-fas-plus-circle class="mr-2" /> Agregar Ciclo
                                </button>
                                <button type="button" onclick="agregarSeccionElectivos()"
                                    class="inline-flex items-center px-4 h-11 border border-gray-400 text-gray-600 rounded-lg hover:bg-gray-100 transition-all">
                                    <x-fas-star class="mr-2" /> Agregar Electivos
                                </button>
                            </div>

                            <input type="hidden" id="plan_estudios" name="plan_estudios">
                        </div>

                        <!-- TAB 4: Plana Docente (TABLA) -->
                        <div id="docentes" x-show="tab === 'docentes'" x-cloak>
                            <div class="soft-info mb-6">
                                <div class="flex items-center gap-2 text-blue-800">
                                    <x-fas-info-circle class="text-xl" />
                                    <p class="text-sm font-medium">
                                        Asignación de Docentes: Selecciona los docentes que enseñan en este programa. Puedes indicar quién coordina, con qué denominación («Coordinador» o «Coordinadora»), su rol y el orden de visualización.
                                    </p>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="docentes-table" id="tabla-docentes">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%">Docente</th>
                                            <th style="width: 10%">Coordinador</th>
                                            <th style="width: 15%">Denominación</th>
                                            <th style="width: 22%">Rol / Cargo</th>
                                            <th style="width: 10%">Mover</th>
                                            <th style="width: 13%">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="docentes-body">
                                        {{-- filas por JS --}}
                                    </tbody>
                                </table>
                            </div>

                            <button type="button" onclick="agregarDocente()"
                                class="mt-4 inline-flex items-center px-4 h-11 border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                <x-fas-plus-circle class="mr-2" /> Agregar Docente
                            </button>
                        </div>

                        <!-- TAB 5: Configuración -->
                        <div id="config" x-show="tab === 'config'" x-cloak>
                            <div class="space-y-6">
                                <!-- Plan de Estudios -->
                                <x-admin-file-upload mode="ajax" name="plan" label="Plan de Estudios" icon="fas fa-book"
                                    accept=".pdf,application/pdf" :url-value="old('plan_url', $programa->plan_url)" />

                                <!-- Horario -->
                                <x-admin-file-upload mode="ajax" name="horario" label="Horario" icon="fas fa-calendar-alt"
                                    accept=".pdf,application/pdf"
                                    :url-value="old('horario_url', filter_var($programa->horario_url, FILTER_VALIDATE_URL) ? $programa->horario_url : '')"
                                    :current-file-url="$programa->horario_url ? (filter_var($programa->horario_url, FILTER_VALIDATE_URL) ? $programa->horario_url : asset('storage/' . $programa->horario_url)) : null"
                                    current-file-label="Ver horario actual" />

                                <!-- Brochure (Diplomados) -->
                                <x-admin-file-upload mode="ajax" name="brochure" label="Brochure (Diplomados)" icon="fas fa-file-pdf"
                                    accept=".pdf,application/pdf"
                                    :url-value="old('brochure_url', filter_var($programa->brochure_url, FILTER_VALIDATE_URL) ? $programa->brochure_url : '')"
                                    :current-file-url="$programa->brochure_url ? (filter_var($programa->brochure_url, FILTER_VALIDATE_URL) ? $programa->brochure_url : asset('storage/' . $programa->brochure_url)) : null"
                                    current-file-label="Ver brochure actual" />

                                <!-- PDF Proceso de Admisión -->
                                <x-admin-file-upload mode="ajax" name="admision_pdf" label="PDF Proceso de Admisión" icon="fas fa-file-signature"
                                    accept=".pdf,application/pdf"
                                    :url-value="old('admision_pdf_url', filter_var($programa->admision_pdf_url, FILTER_VALIDATE_URL) ? $programa->admision_pdf_url : '')"
                                    :current-file-url="$programa->admision_pdf_url ? (filter_var($programa->admision_pdf_url, FILTER_VALIDATE_URL) ? $programa->admision_pdf_url : asset('storage/' . $programa->admision_pdf_url)) : null"
                                    current-file-label="Ver PDF actual"
                                    help-text='Si se deja vacío, el botón "Ver Proceso de Admisión" enlazará a la página general de Admisión.' />

                                {{-- Tarifas por periodo. Antes vivían escritas en las plantillas de
                                     maestría y doctorado y se recalculaban aparte en la ficha del
                                     programa; ahora salen de aquí y alimentan ambos sitios. --}}
                                <div class="border border-gray-200 rounded-lg p-4 mb-4 hover:border-brand-red hover:shadow-sm transition-all"
                                    x-data="inversionPeriodos(
                                        {{ (int) ($programa->costo_por_credito ?? 0) }},
                                        {{ Illuminate\Support\Js::from($programa->semestres_inversion ?? []) }},
                                        '{{ $programa->grado === 'Diplomado' ? 'Módulo' : 'Semestre' }}'
                                    )">
                                    <label class="form-label block mb-3">
                                        <x-fas-calculator class="text-brand-red mr-1" /> Tarifas por {{ $programa->grado === 'Diplomado' ? 'módulo' : 'semestre' }}
                                    </label>

                                    <input type="hidden" name="costo_por_credito" :value="costoCredito">
                                    <input type="hidden" name="semestres_inversion" :value="JSON.stringify(periodos)">

                                    <div class="grid md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="text-xs text-gray-500 mb-1 block">Costo por crédito (S/)</label>
                                            <input type="number" min="0" x-model.number="costoCredito"
                                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" placeholder="160">
                                        </div>
                                        <div class="flex items-end">
                                            <p class="text-sm text-gray-600">
                                                Inversión total calculada:
                                                <strong class="text-brand-red" x-text="'S/ ' + total.toLocaleString('es-PE')"></strong>
                                            </p>
                                        </div>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm">
                                            <thead class="bg-gray-50 text-xs text-gray-600 uppercase">
                                                <tr>
                                                    <th class="px-3 py-2 text-left">{{ $programa->grado === 'Diplomado' ? 'Módulo' : 'Semestre' }}</th>
                                                    <th class="px-3 py-2 text-left">Matrícula (S/)</th>
                                                    <th class="px-3 py-2 text-left">Créditos</th>
                                                    <th class="px-3 py-2 text-left">Subtotal</th>
                                                    <th class="px-3 py-2 text-right w-12"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="(p, i) in periodos" :key="i">
                                                    <tr class="border-b">
                                                        <td class="px-3 py-2 text-gray-500" x-text="etiqueta + ' ' + (i + 1)"></td>
                                                        <td class="px-3 py-2">
                                                            <input type="number" min="0" x-model.number="p.matricula"
                                                                class="w-full border border-gray-200 rounded px-2 py-1">
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            <input type="number" min="0" x-model.number="p.creditos"
                                                                class="w-full border border-gray-200 rounded px-2 py-1">
                                                        </td>
                                                        <td class="px-3 py-2 text-gray-700"
                                                            x-text="'S/ ' + ((p.matricula || 0) + (p.creditos || 0) * (costoCredito || 0)).toLocaleString('es-PE')"></td>
                                                        <td class="px-3 py-2 text-right">
                                                            <button type="button" @click="eliminar(i)"
                                                                class="text-red-500 hover:text-red-700" aria-label="Quitar periodo">
                                                                <x-fas-trash />
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>

                                    <button type="button" @click="agregar()"
                                        class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-red-700 text-red-700 rounded-lg hover:bg-red-50">
                                        <x-fas-plus class="mr-1" /> Agregar periodo
                                    </button>
                                    <p class="text-xs text-gray-400 mt-2">
                                        Déjalo sin periodos para que la página muestre «Inversión por definir».
                                    </p>
                                </div>

                                <!-- Inversión Económica (Diplomados) -->
                                @php
                                    $inv = $programa->inversion_economica ?? [];
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-red hover:shadow-sm transition-all">
                                    <label class="form-label block mb-3">
                                        <x-fas-money-bill-wave class="text-brand-red mr-1" /> Inversión Económica (Diplomados)
                                    </label>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="text-xs text-gray-500 mb-1 block">Derecho de inscripción · Bachiller UNMSM (S/)</label>
                                            <input type="number" id="inv_derecho_bachiller" value="{{ $inv['derecho_inscripcion']['bachiller_unmsm'] ?? '' }}"
                                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="200">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 mb-1 block">Derecho de inscripción · Otras universidades (S/)</label>
                                            <input type="number" id="inv_derecho_otras" value="{{ $inv['derecho_inscripcion']['otras_universidades'] ?? '' }}"
                                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="280">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 mb-1 block">Costo total del diplomado (S/)</label>
                                            <input type="number" id="inv_costo_total" value="{{ $inv['costo_total'] ?? '' }}"
                                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="3000">
                                        </div>
                                        <div>
                                            <label class="text-xs text-gray-500 mb-1 block">Costo del diploma (S/)</label>
                                            <input type="number" id="inv_costo_diploma" value="{{ $inv['costo_diploma'] ?? '' }}"
                                                class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="650">
                                        </div>
                                    </div>

                                    {{-- Resto de la versión anterior: las modalidades se
                                         escribían como una lista de texto separada por
                                         comas. Ahora se editan abajo con sus cuotas,
                                         montos y fechas; el valor antiguo se conserva
                                         para no perderlo y solo se muestra en la ficha
                                         (bajo «Condiciones de pago») mientras el
                                         diplomado no tenga cargadas las nuevas. --}}
                                    <input type="hidden" id="inv_modalidades_pago"
                                        value="{{ !empty($inv['modalidades_pago']) ? implode(', ', $inv['modalidades_pago']) : '' }}">

                                    <div class="mt-4">
                                        <label class="text-xs text-gray-500 mb-1 block">Descuentos o beneficios</label>
                                        <input type="text" id="inv_descuentos" value="{{ $inv['descuentos'] ?? '' }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                            placeholder="Ej: 10% de descuento por pago adelantado">
                                    </div>

                                    <div class="mt-4">
                                        <label class="text-xs text-gray-500 mb-1 block">Observaciones</label>
                                        <textarea id="inv_observaciones" rows="2"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                            placeholder="Condiciones adicionales...">{{ $inv['observaciones'] ?? '' }}</textarea>
                                    </div>

                                    <input type="hidden" id="inversion_economica" name="inversion_economica">
                                </div>

                                @include('admin.programas._modalidades-pago', [
                                    'modalidades' => $programa->modalidades_de_pago,
                                ])

                                <!-- Imagen del Programa -->
                                <x-admin-file-upload mode="ajax" name="imagen" label="Imagen del Programa" icon="fas fa-image"
                                    accept="image/*"
                                    :url-value="old('imagen_url', filter_var($programa->imagen, FILTER_VALIDATE_URL) ? $programa->imagen : '')"
                                    :current-file-url="$programa->imagen ? (filter_var($programa->imagen, FILTER_VALIDATE_URL) ? $programa->imagen : asset('storage/' . $programa->imagen)) : null"
                                    current-file-label="Imagen actual" current-preview-type="image" />
                                </div>
                            </div>

                            <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">Estado de publicación</label>
                                <select id="estado" name="estado" x-data="{ estado: '{{ old('estado', $programa->estado) }}' }" x-model="estado"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    @foreach (\App\Models\Programa::ESTADOS as $valor => $info)
                                        <option value="{{ $valor }}" @selected(old('estado', $programa->estado) === $valor)>{{ $info['label'] }}</option>
                                    @endforeach
                                </select>
                                <template x-for="(info, valor) in {{ Illuminate\Support\Js::from(collect(\App\Models\Programa::ESTADOS)->map(fn ($i) => $i['ayuda'])) }}" :key="valor">
                                    <p x-show="estado === valor" class="text-xs text-gray-500 mt-2" x-text="info"></p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                        <a href="{{ route('admin.programas.index') }}"
                            class="inline-flex items-center px-4 h-11 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <x-fas-arrow-left class="mr-2" /> Volver
                        </a>
                        <button type="submit" :disabled="submitting"
                            class="inline-flex items-center px-6 h-11 rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg disabled:opacity-60 disabled:cursor-not-allowed">
                            <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak />
                            <x-fas-save class="mr-2" x-show="!submitting" />
                            <span x-text="submitting ? 'Guardando...' : 'Actualizar Programa'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal de Confirmación para Eliminar (FIX VISUAL) -->
        <div id="modalEliminar" class="fixed inset-0 z-[9999] hidden">
            <div class="absolute inset-0 bg-black/60" onclick="cerrarModal()"></div>

            <div class="relative min-h-screen flex items-center justify-center p-4">
                <div class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex items-start gap-4">
                        <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                            <x-fas-exclamation-triangle class="text-red-600 text-xl" />
                        </div>

                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900" id="modalEliminarTitulo">Confirmar Eliminación</h3>
                            <p class="mt-1 text-sm text-gray-600" id="modalEliminarMensaje">¿Estás seguro de que deseas eliminar este elemento?</p>
                            <p class="mt-1 text-xs text-gray-400" id="modalEliminarDetalle">Esta acción no se puede deshacer.</p>
                        </div>

                        <button type="button" onclick="cerrarModal()"
                            class="h-10 w-10 rounded-lg flex items-center justify-center hover:bg-gray-100 text-gray-500">
                            <x-fas-times />
                        </button>
                    </div>

                    <div class="p-4 bg-gray-50 flex flex-col sm:flex-row-reverse gap-2">
                        <button type="button" onclick="eliminarElemento()"
                            class="inline-flex items-center justify-center rounded-lg px-4 h-11 bg-red-600 text-white font-medium hover:bg-red-700">
                            Eliminar
                        </button>
                        <button type="button" onclick="cerrarModal()"
                            class="inline-flex items-center justify-center rounded-lg px-4 h-11 bg-white border border-gray-200 text-gray-700 font-medium hover:bg-gray-100">
                            Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Script para datos del plan -->
        <script id="plan-data" type="application/json">{!! json_encode($programa->plan_estudios ?? []) !!}</script>

        <!-- Script para datos docentes existentes -->
        <script id="docentes-data" type="application/json">
            {!! json_encode($programa->docentes->map(function ($d) {
        return [
            'id' => $d->id,
            'rol' => $d->pivot->rol,
            'orden' => $d->pivot->orden,
            'es_coordinador' => (bool) $d->pivot->es_coordinador,
            'coordinador_denominacion' => $d->pivot->coordinador_denominacion,
        ];
    })->values()) !!}
        </script>

        <!-- Scripts para datos JSON de contenido -->
        <script id="objetivos-data" type="application/json">{!! json_encode($programa->objetivos_academicos ?? []) !!}</script>
        <script id="ingresante-data" type="application/json">{!! json_encode($programa->perfil_ingresante ?? []) !!}</script>
        <script id="graduado-data" type="application/json">{!! json_encode($programa->perfil_graduado ?? []) !!}</script>

        <script>


            // ============================
            //   PLAN DE ESTUDIOS
            // ============================
            var planEstudios = [];
            var cicloCounter = 0;
            var cursoCounter = 0;

            (function() {
                var planDataEl = document.getElementById('plan-data');
                if (planDataEl) {
                    try {
                        planEstudios = JSON.parse(planDataEl.textContent || '[]');
                        if (!Array.isArray(planEstudios)) planEstudios = [];
                    } catch (e) {
                        planEstudios = [];
                    }
                }
            })();

            function cargarPlanExistente() {
                if (!planEstudios.length) return;

                var cursosPorCiclo = {};
                var cursosElectivos = [];

                planEstudios.forEach(function(curso) {
                    if (curso.tipo === 'ELECTIVO') {
                        cursosElectivos.push(curso);
                    } else {
                        var ciclo = curso.ciclo || '1';
                        if (!cursosPorCiclo[ciclo]) cursosPorCiclo[ciclo] = [];
                        cursosPorCiclo[ciclo].push(curso);
                    }
                });

                Object.keys(cursosPorCiclo).sort().forEach(function(ciclo) {
                    agregarCicloConCursos(ciclo, cursosPorCiclo[ciclo]);
                });

                if (cursosElectivos.length) {
                    agregarSeccionElectivos();
                    var list = document.getElementById('electivos-section-cursos');
                    if (list) {
                        list.innerHTML = '';
                        cursosElectivos.forEach(function(c, i) {
                            list.insertAdjacentHTML('beforeend', generarCursoRow('electivos-section', i, c));
                        });
                    }
                }
            }

            function agregarCiclo() {
                cicloCounter++;
                agregarCicloConCursos(cicloCounter, []);
            }

            function agregarCicloConCursos(numeroCiclo, cursos) {
                var container = document.getElementById('ciclos-container');
                if (!container) return;

                var cicloId = 'ciclo-' + Date.now() + '-' + Math.random().toString(36).substr(2, 5);
                var cicloCard = document.createElement('div');
                cicloCard.className = 'ciclo-card';
                cicloCard.id = cicloId;
                cicloCard.setAttribute('data-ciclo', numeroCiclo);

                var cursosHtml = cursos.map(function(c, i) {
                    return generarCursoRow(cicloId, i, c);
                }).join('');

                var numVisible = container.querySelectorAll('.ciclo-card').length + 1;

                cicloCard.innerHTML =
                    '<div class="ciclo-header">' +
                        '<div class="flex items-center gap-2">' +
                            '<strong>Ciclo/Semestre:</strong>' +
                            '<span class="badge-ciclo">' + numVisible + '</span>' +
                        '</div>' +
                        '<div class="flex gap-2">' +
                            '<button type="button" class="px-3 py-1.5 bg-white text-gray-700 rounded text-sm hover:bg-gray-100" onclick="agregarCurso(\'' + cicloId + '\')">' +
                                '<x-fas-plus class="mr-1" /> Curso' +
                            '</button>' +
                            '<button type="button" class="px-3 py-1.5 bg-red-500 text-white rounded text-sm hover:bg-red-600" onclick="eliminarCiclo(\'' + cicloId + '\')">' +
                                '<x-fas-trash />' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="cursos-list" id="' + cicloId + '-cursos">' +
                        (cursosHtml || '<div class="p-4 text-gray-400 text-center text-sm">No hay cursos. Haz clic en "+ Curso".</div>') +
                    '</div>';

                container.appendChild(cicloCard);
                actualizarNumeracionCiclos();
            }

            function generarCursoRow(cicloId, index, curso) {
                curso = curso || {};
                cursoCounter++;
                var cursoId = cicloId + '-curso-' + cursoCounter;
                var nombre = (curso.nombre || '').replace(/"/g, '&quot;');
                var creditos = curso.creditos || '';
                var sumilla = (curso.sumilla || '').replace(/"/g, '&quot;');

                return '<div class="curso-row" id="' + cursoId + '">' +
                    '<input type="text" class="py-2 px-3 text-sm" placeholder="Nombre del curso" value="' + nombre + '" data-field="nombre">' +
                    '<input type="number" class="py-2 px-3 text-sm" placeholder="Créd." value="' + creditos + '" data-field="creditos" min="1">' +
                    '<input type="text" class="py-2 px-3 text-sm" placeholder="Sumilla (opcional)" value="' + sumilla + '" data-field="sumilla">' +
                    '<button type="button" class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200" onclick="eliminarCurso(\'' + cursoId + '\')">' +
                        '<x-fas-times />' +
                    '</button>' +
                '</div>';
            }

            function agregarCurso(cicloId) {
                var list = document.getElementById(cicloId + '-cursos');
                if (!list) return;

                var empty = list.querySelector('.text-gray-400');
                if (empty) empty.remove();

                list.insertAdjacentHTML('beforeend', generarCursoRow(cicloId, list.querySelectorAll('.curso-row').length));
            }

            // ============================
            //   MODAL DE CONFIRMACIÓN
            // ============================
            var elementoAEliminar = null;
            var tipoElemento = null;

            function mostrarModal() {
                document.getElementById('modalEliminar').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function cerrarModal() {
                document.getElementById('modalEliminar').classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                elementoAEliminar = null;
                tipoElemento = null;
            }

            function confirmarEliminar(elementoId, tipo) {
                elementoAEliminar = elementoId;
                tipoElemento = tipo;

                if (tipo === 'ciclo') {
                    document.getElementById('modalEliminarTitulo').textContent = 'Eliminar Ciclo/Semestre';
                    document.getElementById('modalEliminarMensaje').textContent = '¿Estás seguro de que deseas eliminar este ciclo completo?';
                    document.getElementById('modalEliminarDetalle').textContent = 'Esta acción eliminará todos los cursos contenidos en este ciclo.';
                } else if (tipo === 'curso') {
                    document.getElementById('modalEliminarTitulo').textContent = 'Eliminar Curso';
                    document.getElementById('modalEliminarMensaje').textContent = '¿Estás seguro de que deseas eliminar este curso?';
                    document.getElementById('modalEliminarDetalle').textContent = 'Esta acción no se puede deshacer.';
                } else if (tipo === 'electivos') {
                    document.getElementById('modalEliminarTitulo').textContent = 'Eliminar Sección Electivos';
                    document.getElementById('modalEliminarMensaje').textContent = '¿Estás seguro de que deseas eliminar toda la sección de electivos?';
                    document.getElementById('modalEliminarDetalle').textContent = 'Esta acción eliminará todos los cursos electivos.';
                } else if (tipo === 'docente') {
                    document.getElementById('modalEliminarTitulo').textContent = 'Eliminar Docente';
                    document.getElementById('modalEliminarMensaje').textContent = '¿Estás seguro de que deseas eliminar esta fila de docente?';
                    document.getElementById('modalEliminarDetalle').textContent = 'Esta acción no se puede deshacer.';
                }

                mostrarModal();
            }

            function eliminarElemento() {
                if (!elementoAEliminar) return;

                if (tipoElemento === 'ciclo' || tipoElemento === 'electivos') {
                    var elemento = document.getElementById(elementoAEliminar);
                    if (elemento) {
                        elemento.remove();
                        if (tipoElemento === 'ciclo') actualizarNumeracionCiclos();
                    }
                } else if (tipoElemento === 'curso') {
                    var curso = document.getElementById(elementoAEliminar);
                    if (curso) {
                        var list = curso.parentNode;
                        curso.remove();
                        if (!list.querySelector('.curso-row')) {
                            list.innerHTML = '<div class="p-4 text-gray-400 text-center text-sm">No hay cursos. Haz clic en "+ Curso".</div>';
                        }
                    }
                } else if (tipoElemento === 'docente') {
                    var row = document.getElementById(elementoAEliminar);
                    if (row) row.remove();
                }

                cerrarModal();
            }

            function eliminarCurso(cursoId) { confirmarEliminar(cursoId, 'curso'); }
            function eliminarCiclo(cicloId) { confirmarEliminar(cicloId, 'ciclo'); }

            function actualizarNumeracionCiclos() {
                var container = document.getElementById('ciclos-container');
                if (!container) return;
                container.querySelectorAll('.ciclo-card').forEach(function(c, i) {
                    var badge = c.querySelector('.badge-ciclo');
                    if (badge) badge.textContent = (i + 1);
                    c.setAttribute('data-ciclo', (i + 1));
                });
            }

            // ============================
            //   ELECTIVOS
            // ============================
            function agregarSeccionElectivos() {
                if (document.getElementById('electivos-section')) {
                    alert('Ya existe la sección de electivos.');
                    return;
                }

                var container = document.getElementById('electivos-container');
                var card = document.createElement('div');
                card.className = 'ciclo-card';
                card.id = 'electivos-section';
                card.setAttribute('data-tipo', 'ELECTIVO');

                card.innerHTML =
                    '<div class="ciclo-header" style="background: linear-gradient(135deg, #374151 0%, #1f2937 100%);">' +
                        '<strong>Cursos Electivos</strong>' +
                        '<div class="flex gap-2">' +
                            '<button type="button" class="px-3 py-1.5 bg-white text-gray-700 rounded text-sm hover:bg-gray-100" onclick="agregarCursoElectivo()">' +
                                '<x-fas-plus class="mr-1" /> Electivo' +
                            '</button>' +
                            '<button type="button" class="px-3 py-1.5 bg-red-500 text-white rounded text-sm hover:bg-red-600" onclick="eliminarSeccionElectivos()">' +
                                '<x-fas-trash />' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="cursos-list" id="electivos-section-cursos">' +
                        '<div class="p-4 text-gray-400 text-center text-sm">No hay electivos. Haz clic en "+ Electivo".</div>' +
                    '</div>';

                container.appendChild(card);
            }

            function agregarCursoElectivo() {
                var list = document.getElementById('electivos-section-cursos');
                if (!list) return;

                var empty = list.querySelector('.text-gray-400');
                if (empty) empty.remove();

                list.insertAdjacentHTML('beforeend', generarCursoRow('electivos-section', list.querySelectorAll('.curso-row').length));
            }

            function eliminarSeccionElectivos() {
                confirmarEliminar('electivos-section', 'electivos');
            }

            // ============================
            //   DOCENTES (TABLA)
            // ============================
            let docenteRowCounter = 0;

            // Denominaciones admitidas para el responsable académico; la lista
            // la define el modelo para no mantenerla en dos sitios.
            const DENOMINACIONES_COORDINADOR = @json(\App\Models\Programa::DENOMINACIONES_COORDINADOR);

            function escapeHtml(str) {
                return String(str ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function generarFilaDocente(data = {}) {
                docenteRowCounter++;
                const rowId = `docente-row-${Date.now()}-${docenteRowCounter}`;

                const selectedId = data.id ?? '';
                const rol = escapeHtml(data.rol ?? '');
                const esCoord = !!data.es_coordinador;
                const denominacion = DENOMINACIONES_COORDINADOR.includes(data.coordinador_denominacion)
                    ? data.coordinador_denominacion
                    : DENOMINACIONES_COORDINADOR[0];

                return `
                    <tr id="${rowId}" class="docente-row">
                        <td data-label="Docente">
                            <select name="docentes_asignados[]" class="block w-full py-2 px-3 text-sm">
                                <option value="">Seleccionar docente...</option>
                                @foreach($docentes as $d)
                                    <option value="{{ $d->id }}" ${selectedId == {{ $d->id }} ? 'selected' : ''}>
                                        {{ $d->grado }} {{ $d->apellidos }}, {{ $d->nombres }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td data-label="Coordinador">
                            <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer">
                                <input type="hidden" name="docentes_coordinador[]" value="${esCoord ? '1' : '0'}">
                                <input type="checkbox" name="docentes_coordinador_checkbox" value="1" class="h-4 w-4 text-brand-gold border-gray-300 rounded docente-coord-checkbox" ${esCoord ? 'checked' : ''}>
                                <span>Sí</span>
                            </label>
                        </td>

                        {{-- Nunca se deshabilita: los campos viajan como arrays
                             paralelos y un select deshabilitado no se envía,
                             desalineando el resto de las columnas. Solo se
                             atenúa cuando el docente no coordina. --}}
                        <td data-label="Denominación">
                            <select name="docentes_coordinador_denominacion[]"
                                class="block w-full py-2 px-3 text-sm docente-denominacion-select ${esCoord ? '' : 'opacity-50'}">
                                ${DENOMINACIONES_COORDINADOR.map(d => `<option value="${d}" ${d === denominacion ? 'selected' : ''}>${d}</option>`).join('')}
                            </select>
                        </td>

                        <td data-label="Rol / Cargo">
                            <input type="text" name="docentes_rol[]" value="${rol}" class="block w-full py-2 px-3 text-sm" placeholder="Rol / Cargo">
                        </td>

                        <td data-label="Mover">
                            <input type="hidden" name="docentes_orden[]" value="0" class="docente-orden-input">
                            <div class="flex gap-1 justify-center">
                                <button type="button" onclick="moverDocenteArriba('${rowId}')"
                                    class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Subir">
                                    <x-fas-arrow-up />
                                </button>
                                <button type="button" onclick="moverDocenteAbajo('${rowId}')"
                                    class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded hover:bg-gray-200 transition-colors" title="Bajar">
                                    <x-fas-arrow-down />
                                </button>
                            </div>
                        </td>

                        <td data-label="Acción">
                            <button type="button"
                                class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200"
                                onclick="confirmarEliminar('${rowId}', 'docente')">
                                <x-fas-times />
                            </button>
                        </td>
                    </tr>
                `;
            }

            function moverDocenteArriba(rowId) {
                const row = document.getElementById(rowId);
                if (!row) return;
                const prev = row.previousElementSibling;
                if (prev) {
                    row.parentNode.insertBefore(row, prev);
                }
            }

            function moverDocenteAbajo(rowId) {
                const row = document.getElementById(rowId);
                if (!row) return;
                const next = row.nextElementSibling;
                if (next) {
                    row.parentNode.insertBefore(next, row);
                }
            }

            function recalcularOrdenDocentes() {
                const rows = document.querySelectorAll('#docentes-body .docente-row');
                rows.forEach((row, index) => {
                    const ordenInput = row.querySelector('.docente-orden-input');
                    if (ordenInput) {
                        ordenInput.value = index + 1;
                    }
                });
            }

            function agregarDocente(data = {}) {
                const body = document.getElementById('docentes-body');
                if (!body) return;
                body.insertAdjacentHTML('beforeend', generarFilaDocente(data));
            }

            function cargarDocentesExistentes() {
                const el = document.getElementById('docentes-data');
                if (!el) return;

                let docentes = [];
                try {
                    docentes = JSON.parse(el.textContent || '[]');
                    if (!Array.isArray(docentes)) docentes = [];
                } catch (e) {
                    docentes = [];
                }

                if (!docentes.length) {
                    // Si no hay, deja una fila vacía opcional
                    agregarDocente();
                    return;
                }

                docentes.forEach(d => agregarDocente(d));
            }

            // ============================
            //   JSON ANTES DE ENVIAR
            // ============================
            function prepararPlanAntesDeEnviar() {
                var form = document.getElementById('form-programa');
                if (!form) return;

                form.addEventListener('submit', function() {
                    // Recalculate docentes order based on their position in the table
                    recalcularOrdenDocentes();

                    var todos = [];

                    // Ciclos obligatorios
                    document.querySelectorAll('#ciclos-container .ciclo-card').forEach(function(cicloCard) {
                        var numCiclo = cicloCard.getAttribute('data-ciclo') || '';
                        cicloCard.querySelectorAll('.curso-row').forEach(function(row) {
                            var nombre = (row.querySelector('[data-field="nombre"]')?.value || '').trim();
                            var cred = row.querySelector('[data-field="creditos"]')?.value || '';
                            var sum = (row.querySelector('[data-field="sumilla"]')?.value || '').trim();

                            if (nombre) {
                                todos.push({
                                    ciclo: numCiclo,
                                    nombre: nombre,
                                    creditos: cred ? parseInt(cred, 10) : null,
                                    tipo: 'OBLIGATORIO',
                                    sumilla: sum || null
                                });
                            }
                        });
                    });

                    // Electivos
                    var electivos = document.getElementById('electivos-section');
                    if (electivos) {
                        electivos.querySelectorAll('.curso-row').forEach(function(row) {
                            var nombre = (row.querySelector('[data-field="nombre"]')?.value || '').trim();
                            var cred = row.querySelector('[data-field="creditos"]')?.value || '';
                            var sum = (row.querySelector('[data-field="sumilla"]')?.value || '').trim();

                            if (nombre) {
                                todos.push({
                                    ciclo: 'Electivo',
                                    nombre: nombre,
                                    creditos: cred ? parseInt(cred, 10) : null,
                                    tipo: 'ELECTIVO',
                                    sumilla: sum || null
                                });
                            }
                        });
                    }

                    document.getElementById('plan_estudios').value = JSON.stringify(todos);
                });
            }

            function cargarListaExistente(dataId, listId) {
                var dataEl = document.getElementById(dataId);
                if (!dataEl) return;
                try {
                    var items = JSON.parse(dataEl.textContent || '[]');
                    if (Array.isArray(items)) {
                        items.forEach(function(item) {
                            crearItemLista(listId, item);
                        });
                    }
                } catch (e) {}
            }

            // ============================
            //   INIT
            // ============================
            document.addEventListener('DOMContentLoaded', function() {
                cargarPlanExistente();
                prepararPlanAntesDeEnviar();
                cargarDocentesExistentes();

                // Cargar listas JSON existentes
                cargarListaExistente('objetivos-data', 'objetivos-list');
                cargarListaExistente('ingresante-data', 'ingresante-list');
                cargarListaExistente('graduado-data', 'graduado-list');

                // Handle coordinator checkbox changes via event delegation
                document.getElementById('docentes-body').addEventListener('change', function(e) {
                    if (e.target.classList.contains('docente-coord-checkbox')) {
                        const hiddenInput = e.target.previousElementSibling;
                        if (hiddenInput && hiddenInput.type === 'hidden') {
                            hiddenInput.value = e.target.checked ? '1' : '0';
                        }

                        // La denominación solo aplica a quien coordina.
                        const select = e.target.closest('tr')?.querySelector('.docente-denominacion-select');
                        if (select) {
                            select.classList.toggle('opacity-50', !e.target.checked);
                        }
                    }
                });

                // Serializar listas JSON antes de enviar
                var form = document.getElementById('form-programa');
                if (form) {
                    form.addEventListener('submit', function() {
                        document.getElementById('objetivos_academicos').value = recogerListaJSON('objetivos-list');
                        document.getElementById('perfil_ingresante').value = recogerListaJSON('ingresante-list');
                        document.getElementById('perfil_graduado').value = recogerListaJSON('graduado-list');
                        document.getElementById('inversion_economica').value = recogerInversionEconomica();
                    });
                }
            });
        </script>

    @include('admin.programas._shared-form-scripts')
@endsection


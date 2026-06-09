@extends('admin.layout.app')

@section('title', 'Nuevo Programa')

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

        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
            box-shadow: 0 4px 6px rgba(118, 30, 35, 0.3);
        }
        .nav-tabs .nav-link.active i {
            color: white !important;
        }

        .form-label {
            font-weight: 600;
            color: #344767;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ciclo-card {
            border: 1px solid #e9ecef;
            border-radius: 0.75rem;
            margin-bottom: 1.5rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            overflow: hidden;
        }

        .ciclo-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--accent-color);
        }

        .badge-ciclo {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-weight: 600;
        }

        .curso-row {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f0f2f5;
            display: grid;
            grid-template-columns: 2fr 100px 1.5fr 50px;
            gap: 1rem;
            align-items: center;
            transition: var(--transition);
        }

        .curso-row:last-child { border-bottom: none; }
        .curso-row:hover { background: #f8f9fa; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-pane.show { animation: slideIn 0.3s ease-out; }

        @media (max-width: 768px) {
            .curso-row { grid-template-columns: 1fr; gap: 0.75rem; }
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
            Crear Nuevo Programa
        </h2>
        <p class="mt-1 text-sm text-gray-500">Completa la información del programa de posgrado.</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.programas.store') }}" method="POST" enctype="multipart/form-data" id="form-programa">
                @csrf

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-6 flex flex-wrap border-b border-gray-200" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active px-4 py-3 text-sm font-medium border-b-2 border-brand-red text-brand-red" 
                           href="#basico" onclick="switchTab(event, 'basico')">
                            <i class="fas fa-info-circle mr-2"></i> Básico
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           href="#contenido" onclick="switchTab(event, 'contenido')">
                            <i class="fas fa-file-alt mr-2"></i> Contenido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           href="#plan" onclick="switchTab(event, 'plan')">
                            <i class="fas fa-book-open mr-2"></i> Plan de Estudios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           href="#docentes" onclick="switchTab(event, 'docentes')">
                            <i class="fas fa-users mr-2"></i> Plana Docente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium text-gray-500 hover:text-brand-red" 
                           href="#config" onclick="switchTab(event, 'config')">
                            <i class="fas fa-cog mr-2"></i> Config
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB 1: Información Básica -->
                    <div id="basico" class="tab-pane show">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label for="grado" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Tipo <span class="text-red-500">*</span>
                                </label>
                                <select name="grado" id="grado"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" required>
                                    <option value="">Seleccionar...</option>
                                    <option value="Maestría" {{ old('grado') == 'Maestría' ? 'selected' : '' }}>Maestría</option>
                                    <option value="Doctorado" {{ old('grado') == 'Doctorado' ? 'selected' : '' }}>Doctorado</option>
                                    <option value="Diplomado" {{ old('grado') == 'Diplomado' ? 'selected' : '' }}>Diplomado</option>
                                </select>
                                @error('grado')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="modalidad" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Modalidad</label>
                                <select name="modalidad" id="modalidad"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg">
                                    <option value="Presencial" {{ old('modalidad') == 'Presencial' ? 'selected' : '' }}>Presencial</option>
                                    <option value="Semipresencial" {{ old('modalidad') == 'Semipresencial' ? 'selected' : '' }}>Semipresencial</option>
                                    <option value="Virtual" {{ old('modalidad') == 'Virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <div>
                                <label for="nombre" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" placeholder="Ej: Lingüística" required>
                                @error('nombre')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mencion" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Mención</label>
                                <input type="text" name="mencion" id="mencion" value="{{ old('mencion') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" placeholder="Ej: Lingüística Hispánica">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                            <div>
                                <label for="vacantes" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Vacantes</label>
                                <input type="number" name="vacantes" id="vacantes" value="{{ old('vacantes') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="30">
                            </div>
                            <div>
                                <label for="duracion" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Duración (sem)</label>
                                <input type="number" name="duracion" id="duracion" value="{{ old('duracion') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="1" placeholder="4">
                            </div>
                            <div>
                                <label for="creditos" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Créditos</label>
                                <input type="number" name="creditos" id="creditos" value="{{ old('creditos') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="72">
                            </div>
                            <div>
                                <label for="grado_otorga" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Grado Otorga</label>
                                <input type="text" name="grado_otorga" id="grado_otorga" value="{{ old('grado_otorga') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" placeholder="Magíster en...">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Contenido -->
                    <div id="contenido" class="tab-pane hidden">
                        <div class="space-y-6">
                            <div>
                                <label for="sumilla" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Sumilla</label>
                                <textarea name="sumilla" id="sumilla" rows="3"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                    placeholder="Breve descripción del programa...">{{ old('sumilla') }}</textarea>
                            </div>
                            <div>
                                <label for="por_que_text" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">¿Por qué elegir este programa?</label>
                                <textarea name="por_que_text" id="por_que_text" rows="4"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                    placeholder="Razones para elegir...">{{ old('por_que_text') }}</textarea>
                            </div>

                            <!-- Objetivos Académicos (JSON) -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <i class="fas fa-bullseye mr-1"></i> Objetivos Académicos
                                </label>
                                <div id="objetivos-list" class="space-y-2"></div>
                                <button type="button" onclick="agregarObjetivo()" 
                                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                    <i class="fas fa-plus mr-1"></i> Agregar Objetivo
                                </button>
                                <input type="hidden" id="objetivos_academicos" name="objetivos_academicos">
                            </div>

                            <!-- Perfil del Ingresante (JSON) -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <i class="fas fa-user-graduate mr-1"></i> Perfil del Ingresante
                                </label>
                                <div id="ingresante-list" class="space-y-2"></div>
                                <button type="button" onclick="agregarIngresante()" 
                                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                    <i class="fas fa-plus mr-1"></i> Agregar Item
                                </button>
                                <input type="hidden" id="perfil_ingresante" name="perfil_ingresante">
                            </div>

                            <!-- Perfil del Graduado (JSON) -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <i class="fas fa-award mr-1"></i> Perfil del Graduado
                                </label>
                                <div id="graduado-list" class="space-y-2"></div>
                                <button type="button" onclick="agregarGraduado()" 
                                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                    <i class="fas fa-plus mr-1"></i> Agregar Item
                                </button>
                                <input type="hidden" id="perfil_graduado" name="perfil_graduado">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Plan de Estudios -->
                    <div id="plan" class="tab-pane hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2 text-blue-800">
                                <i class="fas fa-info-circle text-xl"></i>
                                <p class="text-sm font-medium">Gestión Visual: Agrega cursos organizados por ciclo/semestre. Los datos se guardan como JSON.</p>
                            </div>
                        </div>

                        <div id="ciclos-container"></div>
                        <div id="electivos-container" class="mt-6"></div>

                        <div class="mt-4 flex gap-3">
                            <button type="button" onclick="agregarCiclo()" 
                                class="inline-flex items-center px-4 py-2 border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                                <i class="fas fa-plus-circle mr-2"></i> Agregar Ciclo
                            </button>
                            <button type="button" onclick="agregarSeccionElectivos()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-400 text-gray-600 rounded-lg hover:bg-gray-100 transition-all">
                                <i class="fas fa-star mr-2"></i> Agregar Electivos
                            </button>
                        </div>

                        <input type="hidden" id="plan_estudios" name="plan_estudios">
                    </div>

                    <!-- TAB 4: Plana Docente -->
                    <div id="docentes" class="tab-pane hidden">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2 text-blue-800">
                                <i class="fas fa-info-circle text-xl"></i>
                                <p class="text-sm font-medium">Asigna docentes a este programa. Puedes definir coordinador, rol y orden de aparición.</p>
                            </div>
                        </div>

                        <!-- Lista de docentes asignados -->
                        <div id="docentes-list" class="space-y-3"></div>

                        <button type="button" onclick="agregarDocente()" 
                            class="mt-4 inline-flex items-center px-4 py-2 border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
                            <i class="fas fa-plus-circle mr-2"></i> Agregar Docente
                        </button>
                    </div>

                    <!-- TAB 5: Configuración -->
                    <div id="config" class="tab-pane hidden">
                        <div class="space-y-6">
                            <!-- Plan de Estudios -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-red hover:shadow-sm transition-all">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <i class="fas fa-book text-brand-red mr-1"></i> Plan de Estudios
                                </label>
                                <div class="flex gap-3 items-end">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 mb-1 block">URL del Plan</label>
                                        <input type="url" name="plan_url" id="plan_url" value="{{ old('plan_url') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:border-brand-red transition-colors"
                                            placeholder="https://ejemplo.com/plan.pdf">
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="file" id="plan_file" accept=".pdf,application/pdf" class="hidden">
                                        <button type="button" onclick="document.getElementById('plan_file').click()"
                                            class="px-4 py-2.5 bg-brand-red text-white rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                                            <i class="fas fa-upload"></i> Subir PDF
                                        </button>
                                    </div>
                                </div>
                                <div id="plan_status" class="mt-2 text-xs hidden"></div>
                            </div>

                            <!-- Horario -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-red hover:shadow-sm transition-all">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <i class="fas fa-calendar-alt text-brand-red mr-1"></i> Horario
                                </label>
                                <div class="flex gap-3 items-end">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 mb-1 block">URL del Horario</label>
                                        <input type="url" name="horario_url" id="horario_url" value="{{ old('horario_url') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:border-brand-red transition-colors"
                                            placeholder="https://ejemplo.com/horario.pdf">
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="file" id="horario_file" accept=".pdf,application/pdf" class="hidden">
                                        <button type="button" onclick="document.getElementById('horario_file').click()"
                                            class="px-4 py-2.5 bg-brand-red text-white rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                                            <i class="fas fa-upload"></i> Subir PDF
                                        </button>
                                    </div>
                                </div>
                                <div id="horario_status" class="mt-2 text-xs hidden"></div>
                            </div>

                            <!-- Imagen del Programa -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-red hover:shadow-sm transition-all">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <i class="fas fa-image text-brand-red mr-1"></i> Imagen del Programa
                                </label>
                                <div class="flex gap-3 items-end">
                                    <div class="flex-1">
                                        <label class="text-xs text-gray-500 mb-1 block">URL de la Imagen</label>
                                        <input type="url" name="imagen_url" id="imagen_url" value="{{ old('imagen_url') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:border-brand-red transition-colors"
                                            placeholder="https://ejemplo.com/imagen.jpg">
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="file" id="imagen_file" accept="image/*" class="hidden">
                                        <button type="button" onclick="document.getElementById('imagen_file').click()"
                                            class="px-4 py-2.5 bg-brand-red text-white rounded-lg hover:bg-red-700 transition-all flex items-center gap-2">
                                            <i class="fas fa-upload"></i> Subir Imagen
                                        </button>
                                    </div>
                                </div>
                                <div id="imagen_status" class="mt-2 text-xs hidden"></div>
                            </div>
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label class="flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    class="h-5 w-5 text-brand-gold border-gray-300 rounded"
                                    {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-gray-700">Programa activo (visible en web)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.programas.index') }}"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                    <button type="submit"
                        class="inline-flex items-center px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg">
                        <i class="fas fa-save mr-2"></i> Guardar Programa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmación para Eliminar -->
    <div id="modalEliminar" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay con opacidad -->
        <div class="fixed inset-0 bg-black/60 transition-opacity" onclick="cerrarModal()"></div>
        
        <!-- Contenedor centrado -->
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <!-- Modal panel -->
            <div class="relative bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900" id="modalEliminarTitulo">
                                Confirmar Eliminación
                            </h3>
                            <p class="mt-2 text-sm text-gray-600" id="modalEliminarMensaje">
                                ¿Estás seguro de que deseas eliminar este elemento?
                            </p>
                            <p class="mt-1 text-xs text-gray-400" id="modalEliminarDetalle">
                                Esta acción no se puede deshacer.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3 px-6 py-4 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="cerrarModal()" 
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="eliminarElemento()" 
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ============================
        //   TABS
        // ============================
        function switchTab(event, tabId) {
            event.preventDefault();

            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.add('hidden');
                pane.classList.remove('show');
            });

            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active', 'border-b-2', 'border-brand-red', 'text-brand-red');
                link.classList.add('text-gray-500');
            });

            const selectedPane = document.getElementById(tabId);
            if (selectedPane) {
                selectedPane.classList.remove('hidden');
                selectedPane.classList.add('show');
            }

            event.target.classList.add('active', 'border-b-2', 'border-brand-red', 'text-brand-red');
            event.target.classList.remove('text-gray-500');
        }

        // ============================
        //   PLAN DE ESTUDIOS
        // ============================
        var cicloCounter = 0;
        var cursoCounter = 0;

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
                            '<i class="fas fa-plus mr-1"></i> Curso' +
                        '</button>' +
                        '<button type="button" class="px-3 py-1.5 bg-red-500 text-white rounded text-sm hover:bg-red-600" onclick="eliminarCiclo(\'' + cicloId + '\')">' +
                            '<i class="fas fa-trash"></i>' +
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
                '<input type="text" class="py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Nombre del curso" value="' + nombre + '" data-field="nombre">' +
                '<input type="number" class="py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Créd." value="' + creditos + '" data-field="creditos" min="1">' +
                '<input type="text" class="py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Sumilla (opcional)" value="' + sumilla + '" data-field="sumilla">' +
                '<button type="button" class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200" onclick="eliminarCurso(\'' + cursoId + '\')">' +
                    '<i class="fas fa-times"></i>' +
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
        }

        function cerrarModal() {
            document.getElementById('modalEliminar').classList.add('hidden');
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
            }

            mostrarModal();
        }

        function eliminarElemento() {
            if (!elementoAEliminar) return;

            if (tipoElemento === 'ciclo' || tipoElemento === 'electivos') {
                var elemento = document.getElementById(elementoAEliminar);
                if (elemento) {
                    elemento.remove();
                    if (tipoElemento === 'ciclo') {
                        actualizarNumeracionCiclos();
                    }
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
            }

            cerrarModal();
        }

        function eliminarCurso(cursoId) {
            confirmarEliminar(cursoId, 'curso');
        }

        function eliminarCiclo(cicloId) {
            confirmarEliminar(cicloId, 'ciclo');
        }

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
                            '<i class="fas fa-plus mr-1"></i> Electivo' +
                        '</button>' +
                        '<button type="button" class="px-3 py-1.5 bg-red-500 text-white rounded text-sm hover:bg-red-600" onclick="eliminarSeccionElectivos()">' +
                            '<i class="fas fa-trash"></i>' +
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
        //   PLANA DOCENTE
        // ============================
        function agregarDocente() {
            var list = document.getElementById('docentes-list');
            if (!list) return;

            var row = document.createElement('div');
            row.className = 'docente-row flex flex-wrap items-center gap-3 p-4 bg-white border border-gray-200 rounded-lg';
            
            row.innerHTML = `
                <div class="flex-1 min-w-[200px]">
                    <select name="docentes_asignados[]" class="docente-select block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm">
                        <option value="">Seleccionar docente...</option>
                        @foreach($docentes as $d)
                            <option value="{{ $d->id }}">{{ $d->grado }} {{ $d->apellidos }}, {{ $d->nombres }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-32">
                    <input type="text" name="docentes_rol[]" class="block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Rol">
                </div>
                <div class="w-20">
                    <input type="number" name="docentes_orden[]" class="block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Orden" min="1">
                </div>
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1 text-sm text-gray-700 cursor-pointer">
                        <input type="checkbox" name="docentes_coordinador[]" value="1" class="h-4 w-4 text-brand-gold border-gray-300 rounded">
                        <span>Coordinador</span>
                    </label>
                </div>
                <button type="button" onclick="eliminarDocente(this)" 
                    class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            list.appendChild(row);
        }

        function eliminarDocente(btn) {
            var row = btn.closest('.docente-row');
            if (row) row.remove();
        }

        // ============================
        //   LISTAS JSON (Objetivos, Ingresante, Graduado)
        // ============================
        function crearItemLista(listId, value = '') {
            var list = document.getElementById(listId);
            if (!list) return;

            var itemId = listId + '-item-' + Date.now();
            var row = document.createElement('div');
            row.className = 'flex items-center gap-2';
            row.id = itemId;
            row.innerHTML = `
                <input type="text" class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm lista-item" 
                       placeholder="Escribir item..." value="${value.replace(/"/g, '&quot;')}">
                <button type="button" onclick="eliminarItemLista('${itemId}')" 
                    class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(row);
        }

        function eliminarItemLista(itemId) {
            var item = document.getElementById(itemId);
            if (item) item.remove();
        }

        function agregarObjetivo() { crearItemLista('objetivos-list'); }
        function agregarIngresante() { crearItemLista('ingresante-list'); }
        function agregarGraduado() { crearItemLista('graduado-list'); }

        function recogerListaJSON(listId) {
            var items = [];
            document.querySelectorAll('#' + listId + ' .lista-item').forEach(function(input) {
                var val = input.value.trim();
                if (val) items.push(val);
            });
            return JSON.stringify(items);
        }

        // ============================
        //   JSON ANTES DE ENVIAR
        // ============================
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('form-programa');
            if (!form) return;

            form.addEventListener('submit', function() {
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

                // Serializar listas JSON
                document.getElementById('objetivos_academicos').value = recogerListaJSON('objetivos-list');
                document.getElementById('perfil_ingresante').value = recogerListaJSON('ingresante-list');
                document.getElementById('perfil_graduado').value = recogerListaJSON('graduado-list');
            });

            // ============================
            //   AJAX FILE UPLOAD
            // ============================
            setupFileUpload('plan_file', 'plan_url', 'plan_status', 'plan');
            setupFileUpload('horario_file', 'horario_url', 'horario_status', 'horario');
            setupFileUpload('imagen_file', 'imagen_url', 'imagen_status', 'imagen');
        });

        function setupFileUpload(fileInputId, urlInputId, statusId, type) {
            var fileInput = document.getElementById(fileInputId);
            if (!fileInput) return;

            fileInput.addEventListener('change', function() {
                if (this.files.length === 0) return;

                var file = this.files[0];
                var statusEl = document.getElementById(statusId);
                var urlInput = document.getElementById(urlInputId);

                // Show uploading status
                statusEl.classList.remove('hidden', 'text-green-600', 'text-red-600');
                statusEl.classList.add('text-blue-600');
                statusEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Subiendo ' + file.name + '...';

                var formData = new FormData();
                formData.append('file', file);
                formData.append('type', type);
                formData.append('program_name', document.getElementById('nombre').value || 'programa');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("admin.documents.uploadAjax") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        urlInput.value = data.url;
                        statusEl.classList.remove('text-blue-600');
                        statusEl.classList.add('text-green-600');
                        statusEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Subido: ' + data.filename;
                    } else {
                        statusEl.classList.remove('text-blue-600');
                        statusEl.classList.add('text-red-600');
                        statusEl.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Error: ' + (data.error || 'Error al subir');
                    }
                })
                .catch(error => {
                    statusEl.classList.remove('text-blue-600');
                    statusEl.classList.add('text-red-600');
                    statusEl.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Error de conexión';
                    console.error('Upload error:', error);
                });

                // Clear file input
                this.value = '';
            });
        }
    </script>
@endsection


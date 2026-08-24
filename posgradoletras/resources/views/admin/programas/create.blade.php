@extends('admin.layout.app')

@section('title', 'Nuevo Curso')

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

        .nav-tabs .nav-link.active {
            background: var(--brand);
            color: white;
            box-shadow: 0 4px 6px rgba(20, 59, 99, 0.3);
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
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
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

        @media (max-width: 768px) {
            .curso-row { grid-template-columns: 1fr; gap: 0.75rem; }
        }
    </style>
@endpush

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h2 class="text-2xl font-serif font-bold leading-7 text-gray-900 sm:text-3xl">
            Crear Nuevo Curso
        </h2>
        <p class="mt-1 text-sm text-gray-500">Completa la información del curso de posgrado.</p>
    </div>

    <!-- Form Card -->
    <div class="card">
        <div class="p-6">
            <form action="{{ route('admin.programas.store') }}" method="POST" data-avisar-sin-guardar enctype="multipart/form-data" id="form-programa"
                x-data="{ submitting: false, tab: 'basico' }" @submit="submitting = true">
                @csrf

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-6 flex flex-wrap border-b border-gray-200" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium"
                           :class="tab === 'basico' ? 'active border-b-2 border-brand-azul text-brand-azul' : 'text-gray-500 hover:text-brand-azul'"
                           href="#basico" @click.prevent="tab = 'basico'">
                            <x-fas-info-circle class="mr-2" /> Básico
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium"
                           :class="tab === 'contenido' ? 'active border-b-2 border-brand-azul text-brand-azul' : 'text-gray-500 hover:text-brand-azul'"
                           href="#contenido" @click.prevent="tab = 'contenido'">
                            <x-fas-file-alt class="mr-2" /> Contenido
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium"
                           :class="tab === 'plan' ? 'active border-b-2 border-brand-azul text-brand-azul' : 'text-gray-500 hover:text-brand-azul'"
                           href="#plan" @click.prevent="tab = 'plan'">
                            <x-fas-book-open class="mr-2" /> Plan de Estudios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium"
                           :class="tab === 'docentes' ? 'active border-b-2 border-brand-azul text-brand-azul' : 'text-gray-500 hover:text-brand-azul'"
                           href="#docentes" @click.prevent="tab = 'docentes'">
                            <x-fas-users class="mr-2" /> Plana Docente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-4 py-3 text-sm font-medium"
                           :class="tab === 'config' ? 'active border-b-2 border-brand-azul text-brand-azul' : 'text-gray-500 hover:text-brand-azul'"
                           href="#config" @click.prevent="tab = 'config'">
                            <x-fas-cog class="mr-2" /> Config
                        </a>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- TAB 1: Información Básica -->
                    <div id="basico" x-show="tab === 'basico'" x-cloak>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label for="grado" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">
                                    Tipo <span class="text-red-500">*</span>
                                </label>
                                <select name="grado" id="grado"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" required>
                                    <option value="">Seleccionar...</option>
                                    @foreach (\App\Models\TipoOferta::cases() as $t)
                                        <option value="{{ $t->grado() }}" {{ old('grado') == $t->grado() ? 'selected' : '' }}>{{ $t->singular() }}</option>
                                    @endforeach
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
                                <label for="horas_academicas" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Horas Académicas</label>
                                <input type="number" name="horas_academicas" id="horas_academicas" value="{{ old('horas_academicas') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="480">
                                <p class="mt-1 text-xs text-gray-400">Se muestra en «Información Clave», debajo de Créditos. Vacío: no aparece.</p>
                                @error('horas_academicas')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Denominación del título que otorga: rótulo y contenido,
                             ambos editables y ambos opcionales (Obs. N.º 4). --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                            <div>
                                <label for="grado_otorga_label" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Denominación · rótulo</label>
                                <input type="text" name="grado_otorga_label" id="grado_otorga_label" value="{{ old('grado_otorga_label') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" maxlength="100" placeholder="Otorga / Grado que otorga">
                            </div>
                            <div class="md:col-span-2">
                                <label for="grado_otorga" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Denominación · contenido</label>
                                <input type="text" name="grado_otorga" id="grado_otorga" value="{{ old('grado_otorga') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" maxlength="255" placeholder="Diploma en Curaduría con Énfasis en...">
                            </div>
                            <p class="md:col-span-3 -mt-3 text-xs text-gray-400">
                                Se muestra bajo el título en la portada del curso, como «Rótulo: contenido».
                                Si dejas el contenido vacío no se muestra nada: no se completa solo con «Grado que otorga».
                            </p>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
                            <div>
                                <label for="fecha_limite_inscripcion" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Fecha Límite Inscripción</label>
                                <input type="text" name="fecha_limite_inscripcion" id="fecha_limite_inscripcion" value="{{ old('fecha_limite_inscripcion') }}"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" placeholder="25 de septiembre de 2026">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Contenido -->
                    <div id="contenido" x-show="tab === 'contenido'" x-cloak>
                        <div class="space-y-6">
                            <div>
                                <label for="sumilla" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">Sumilla</label>
                                <textarea name="sumilla" id="sumilla" rows="3"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                    placeholder="Breve descripción del curso...">{{ old('sumilla') }}</textarea>
                            </div>
                            <div>
                                <label for="por_que_text" class="form-label block text-xs font-bold text-gray-600 uppercase mb-2">¿Por qué elegir este curso?</label>
                                <textarea name="por_que_text" id="por_que_text" rows="4"
                                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg"
                                    placeholder="Razones para elegir...">{{ old('por_que_text') }}</textarea>
                            </div>

                            <!-- Objetivos Académicos (JSON) -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <x-fas-bullseye class="mr-1" /> Objetivos Académicos
                                </label>
                                <div id="objetivos-list" class="space-y-2"></div>
                                <button type="button" onclick="agregarObjetivo()" 
                                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-azul text-brand-azul rounded-lg hover:bg-brand-azul hover:text-white transition-all">
                                    <x-fas-plus class="mr-1" /> Agregar Objetivo
                                </button>
                                <input type="hidden" id="objetivos_academicos" name="objetivos_academicos">
                            </div>

                            <!-- Perfil del Ingresante (JSON) -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <x-fas-user-graduate class="mr-1" /> Perfil del Ingresante
                                </label>
                                <div id="ingresante-list" class="space-y-2"></div>
                                <button type="button" onclick="agregarIngresante()" 
                                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-azul text-brand-azul rounded-lg hover:bg-brand-azul hover:text-white transition-all">
                                    <x-fas-plus class="mr-1" /> Agregar Item
                                </button>
                                <input type="hidden" id="perfil_ingresante" name="perfil_ingresante">
                            </div>

                            <!-- Perfil del Graduado (JSON) -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <x-fas-award class="mr-1" /> Perfil del Graduado
                                </label>
                                <div id="graduado-list" class="space-y-2"></div>
                                <button type="button" onclick="agregarGraduado()" 
                                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-azul text-brand-azul rounded-lg hover:bg-brand-azul hover:text-white transition-all">
                                    <x-fas-plus class="mr-1" /> Agregar Item
                                </button>
                                <input type="hidden" id="perfil_graduado" name="perfil_graduado">
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Plan de Estudios -->
                    <div id="plan" x-show="tab === 'plan'" x-cloak>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2 text-blue-800">
                                <x-fas-info-circle class="text-xl" />
                                <p class="text-sm font-medium">Gestión Visual: Agrega cursos organizados por ciclo/semestre. Los datos se guardan como JSON.</p>
                            </div>
                        </div>

                        <div id="ciclos-container"></div>
                        <div id="electivos-container" class="mt-6"></div>

                        <div class="mt-4 flex gap-3">
                            <button type="button" onclick="agregarCiclo()" 
                                class="inline-flex items-center px-4 py-2 border border-brand-azul text-brand-azul rounded-lg hover:bg-brand-azul hover:text-white transition-all">
                                <x-fas-plus-circle class="mr-2" /> Agregar Ciclo
                            </button>
                            <button type="button" onclick="agregarSeccionElectivos()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-400 text-gray-600 rounded-lg hover:bg-gray-100 transition-all">
                                <x-fas-star class="mr-2" /> Agregar Electivos
                            </button>
                        </div>

                        <input type="hidden" id="plan_estudios" name="plan_estudios">
                    </div>

                    <!-- TAB 4: Plana Docente -->
                    <div id="docentes" x-show="tab === 'docentes'" x-cloak>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center gap-2 text-blue-800">
                                <x-fas-info-circle class="text-xl" />
                                <p class="text-sm font-medium">Asigna docentes a este curso. Puedes definir coordinador, rol y orden de aparición.</p>
                            </div>
                        </div>

                        <!-- Lista de docentes asignados -->
                        <div id="docentes-list" class="space-y-3"></div>

                        <button type="button" onclick="agregarDocente()" 
                            class="mt-4 inline-flex items-center px-4 py-2 border border-brand-azul text-brand-azul rounded-lg hover:bg-brand-azul hover:text-white transition-all">
                            <x-fas-plus-circle class="mr-2" /> Agregar Docente
                        </button>
                    </div>

                    <!-- TAB 5: Configuración -->
                    <div id="config" x-show="tab === 'config'" x-cloak>
                        <div class="space-y-6">
                            <!-- Plan de Estudios -->
                            <x-admin-file-upload mode="ajax" name="plan" label="Plan de Estudios" icon="fas fa-book"
                                accept=".pdf,application/pdf" :url-value="old('plan_url')" />

                            <!-- Horario -->
                            <x-admin-file-upload mode="ajax" name="horario" label="Horario" icon="fas fa-calendar-alt"
                                accept=".pdf,application/pdf" :url-value="old('horario_url')" />

                            <!-- Brochure (Diplomados) -->
                            <x-admin-file-upload mode="ajax" name="brochure" label="Brochure (Talleres)" icon="fas fa-file-pdf"
                                accept=".pdf,application/pdf" :url-value="old('brochure_url')" />

                            <!-- PDF Proceso de Admisión -->
                            <x-admin-file-upload mode="ajax" name="admision_pdf" label="PDF Proceso de Admisión" icon="fas fa-file-signature"
                                accept=".pdf,application/pdf" :url-value="old('admision_pdf_url')"
                                help-text='Si se deja vacío, el botón "Ver Proceso de Admisión" enlazará a la página general de Admisión.' />

                            <!-- Inversión Económica (Diplomados) -->
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-azul hover:shadow-sm transition-all">
                                <label class="form-label block text-xs font-bold text-gray-600 uppercase mb-3">
                                    <x-fas-money-bill-wave class="text-brand-azul mr-1" /> Inversión Económica (Talleres)
                                </label>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Derecho de inscripción · Bachiller UNMSM (S/)</label>
                                        <input type="number" id="inv_derecho_bachiller" value="{{ old('inv_derecho_bachiller') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="200">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Derecho de inscripción · Otras universidades (S/)</label>
                                        <input type="number" id="inv_derecho_otras" value="{{ old('inv_derecho_otras') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="280">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Costo total del taller (S/)</label>
                                        <input type="number" id="inv_costo_total" value="{{ old('inv_costo_total') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="3000">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Costo del diploma (S/)</label>
                                        <input type="number" id="inv_costo_diploma" value="{{ old('inv_costo_diploma') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="650">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500 mb-1 block">Costo por matrícula (S/)</label>
                                        <input type="number" id="inv_costo_matricula" value="{{ old('inv_costo_matricula') }}"
                                            class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg" min="0" placeholder="200">
                                        <p class="mt-1 text-xs text-gray-400">Se muestra en la ficha debajo del pago de diploma. Vacío: no aparece.</p>
                                    </div>
                                </div>

                                {{-- Formato anterior de las modalidades (lista separada
                                     por comas). Se conserva oculto para no perderlo al
                                     guardar; las modalidades reales se editan abajo. --}}
                                <input type="hidden" id="inv_modalidades_pago" value="">

                                {{-- «Descuentos» y «Observaciones» pasaron a ser puntos
                                     de la lista de condiciones, que se edita abajo. --}}
                                <input type="hidden" id="inv_descuentos" value="">
                                <input type="hidden" id="inv_observaciones" value="">

                                <input type="hidden" id="inversion_economica" name="inversion_economica">
                            </div>

                            @include('admin.programas._modalidades-pago', ['modalidades' => []])

                            @include('admin.programas._condiciones-pago', ['condiciones' => []])

                            <!-- Imagen del Programa -->
                            <x-admin-file-upload mode="ajax" name="imagen" label="Imagen del Curso" icon="fas fa-image"
                                accept="image/*" :url-value="old('imagen_url')" />
                        </div>

                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <label for="estado" class="block text-sm font-semibold text-gray-700 mb-2">Estado de publicación</label>
                            <select id="estado" name="estado" x-data="{ estado: '{{ old('estado', \App\Models\Programa::ESTADO_PUBLICADO) }}' }" x-model="estado"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                @foreach (\App\Models\Programa::ESTADOS as $valor => $info)
                                    <option value="{{ $valor }}" @selected(old('estado', \App\Models\Programa::ESTADO_PUBLICADO) === $valor)>{{ $info['label'] }}</option>
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
                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <x-fas-arrow-left class="mr-2" /> Volver
                    </a>
                    <button type="submit" :disabled="submitting"
                        class="inline-flex items-center px-6 py-2.5 rounded-lg text-sm font-medium text-white bg-brand-gold hover:bg-yellow-600 shadow-lg disabled:opacity-60 disabled:cursor-not-allowed">
                        <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak />
                        <x-fas-save class="mr-2" x-show="!submitting" />
                        <span x-text="submitting ? 'Guardando...' : 'Guardar Programa'"></span>
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
                            <x-fas-exclamation-triangle class="text-red-600 text-2xl" />
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
                '<input type="text" class="py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Nombre del curso" value="' + nombre + '" data-field="nombre">' +
                '<input type="number" class="py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Créd." value="' + creditos + '" data-field="creditos" min="1">' +
                '<input type="text" class="py-2 px-3 border border-gray-300 rounded-lg text-sm" placeholder="Sumilla (opcional)" value="' + sumilla + '" data-field="sumilla">' +
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
        //   PLANA DOCENTE
        // ============================
        // Denominaciones admitidas para el responsable académico; la lista la
        // define el modelo para no mantenerla en dos sitios.
        const DENOMINACIONES_COORDINADOR = @json(\App\Models\Programa::DENOMINACIONES_COORDINADOR);

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
                {{-- La casilla no se envía cuando está desmarcada y desalinearía
                     los arrays paralelos, así que el valor viaja en un campo
                     oculto que la casilla actualiza (mismo patrón que edit). --}}
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-1 text-sm text-gray-700 cursor-pointer">
                        <input type="hidden" name="docentes_coordinador[]" value="0">
                        <input type="checkbox" value="1" class="h-4 w-4 text-brand-gold border-gray-300 rounded docente-coord-checkbox">
                        <span>Coordina</span>
                    </label>
                </div>
                <div class="w-36">
                    <select name="docentes_coordinador_denominacion[]" class="block w-full py-2 px-3 border border-gray-300 rounded-lg text-sm docente-denominacion-select opacity-50">
                        ${DENOMINACIONES_COORDINADOR.map(function (d) { return '<option value="' + d + '">' + d + '</option>'; }).join('')}
                    </select>
                </div>
                <button type="button" onclick="eliminarDocente(this)" 
                    class="w-10 h-10 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                    <x-fas-times />
                </button>
            `;
            
            list.appendChild(row);
        }

        function eliminarDocente(btn) {
            var row = btn.closest('.docente-row');
            if (row) row.remove();
        }

        // La casilla escribe en el campo oculto y habilita visualmente la
        // denominación, que solo tiene sentido en quien coordina.
        document.addEventListener('change', function (e) {
            if (!e.target.classList.contains('docente-coord-checkbox')) return;

            var row = e.target.closest('.docente-row');
            if (!row) return;

            var oculto = row.querySelector('input[name="docentes_coordinador[]"]');
            if (oculto) oculto.value = e.target.checked ? '1' : '0';

            var select = row.querySelector('.docente-denominacion-select');
            if (select) select.classList.toggle('opacity-50', !e.target.checked);
        });

        // ============================
        //   JSON ANTES DE ENVIAR
        // ============================
        document.addEventListener('DOMContentLoaded', function() {
            var form = document.getElementById('form-curso');
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
                document.getElementById('inversion_economica').value = recogerInversionEconomica();
            });
        });
    </script>

    @include('admin.programas._shared-form-scripts')
@endsection


@extends('admin.layout.app')

@section('title', 'Admisión Diplomados')

@push('styles')
    <style>
        /* --brand y --brand-dark ya vienen de admin.layout.app; se reutilizan aquí */
        .nav-tabs .nav-link.active {
            background: var(--brand);
            color: white;
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
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-xl font-bold text-gray-800 mb-1">Admisión de Diplomados</h1>
            <p class="text-sm text-gray-500 mb-6">Contenido de <code>/diplomados/admision</code> — todas las secciones son editables.</p>

            <form action="{{ route('admin.admision-diplomados.update') }}" method="POST" id="admisionForm" enctype="multipart/form-data"
                x-data="{ submitting: false, tab: 'tab-hero' }" @submit="submitting = true">
                @csrf
                @method('PUT')
                <input type="hidden" name="pasos" id="pasos_input">
                <input type="hidden" name="requisitos_lista" id="requisitos_lista_input">
                <input type="hidden" name="pago_instrucciones" id="pago_instrucciones_input">
                <input type="hidden" name="cronograma_items_payload" id="cronograma_items_payload">
                <input type="hidden" name="deleted_cronograma_items" id="deleted_cronograma_items" value="[]">

                <!-- Tabs -->
                <ul class="nav-tabs flex flex-wrap gap-2 border-b border-gray-200 mb-6" id="tabsNav">
                    <li><a href="#tab-hero" @click.prevent="tab = 'tab-hero'" :class="tab === 'tab-hero' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Hero</a></li>
                    <li><a href="#tab-guia" @click.prevent="tab = 'tab-guia'" :class="tab === 'tab-guia' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Guía (Pasos)</a></li>
                    <li><a href="#tab-cronograma" @click.prevent="tab = 'tab-cronograma'" :class="tab === 'tab-cronograma' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Cronograma</a></li>
                    <li><a href="#tab-requisitos" @click.prevent="tab = 'tab-requisitos'" :class="tab === 'tab-requisitos' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Requisitos</a></li>
                    <li><a href="#tab-pago" @click.prevent="tab = 'tab-pago'" :class="tab === 'tab-pago' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Pago</a></li>
                    <li><a href="#tab-resultados" @click.prevent="tab = 'tab-resultados'" :class="tab === 'tab-resultados' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Resultados</a></li>
                    <li><a href="#tab-contacto" @click.prevent="tab = 'tab-contacto'" :class="tab === 'tab-contacto' ? 'active' : 'text-gray-500'" class="nav-link inline-block px-4 py-2.5 rounded-t-lg text-sm font-medium">Contacto</a></li>
                </ul>

                <!-- TAB: Hero -->
                <div id="tab-hero" x-show="tab === 'tab-hero'" x-cloak>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="form-label block">Título del Hero</label>
                            <input type="text" name="hero_titulo" value="{{ old('hero_titulo', $settings->hero_titulo) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Convocatoria 2026-I">
                        </div>
                        <div>
                            <label class="form-label block">Subtítulo del Hero</label>
                            <input type="text" name="hero_subtitulo" value="{{ old('hero_subtitulo', $settings->hero_subtitulo) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="Sección Diplomados · Unidad de Posgrado">
                        </div>
                        <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                            <x-admin-file-upload mode="direct" name="hero_imagen" label="Imagen de fondo del Hero"
                                accept="image/*" layout="stacked" preview-size="w-32 h-20"
                                :current-path="$settings->hero_imagen" remove-checkbox-name="remove_hero_imagen"
                                remove-label="Eliminar imagen actual"
                                help-text="Si no se sube ninguna, se usará una imagen por defecto." />
                        </div>
                    </div>
                </div>

                <!-- TAB: Guía (Pasos) -->
                <div id="tab-guia" x-show="tab === 'tab-guia'" x-cloak>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                        <x-fas-info-circle class="mr-1" /> Los 6 pasos de la guía de admisión. Puedes editar el ícono (nombre de ícono FontAwesome, ej. <code>fa-calendar-days</code>).
                    </div>
                    <div id="pasos-list" class="space-y-3"></div>
                    <button type="button" onclick="agregarPaso()" class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-red-700 text-red-700 rounded-lg hover:bg-red-50">
                        <x-fas-plus class="mr-1" /> Agregar Paso
                    </button>
                </div>

                <!-- TAB: Cronograma -->
                <div id="tab-cronograma" x-show="tab === 'tab-cronograma'" x-cloak>
                    <div class="flex justify-between items-center mb-4">
                        <p class="text-sm text-gray-500"><x-fas-list-ol class="mr-1" />Usa las flechas para reordenar</p>
                        <button type="button" onclick="addCronogramaRow()" class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 text-sm">
                            <x-fas-plus class="mr-1" /> Nueva Fila
                        </button>
                    </div>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                                <tr>
                                    <th class="px-3 py-3 text-center w-14">Orden</th>
                                    <th class="px-3 py-3 text-left">Programa</th>
                                    <th class="px-3 py-3 text-left">Convocatoria</th>
                                    <th class="px-3 py-3 text-left">Fecha Inscripción</th>
                                    <th class="px-3 py-3 text-left">Fecha Límite</th>
                                    <th class="px-3 py-3 text-left">Estado</th>
                                    <th class="px-3 py-3 text-right w-16">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="cronograma-tbody">
                                @foreach($settings->cronogramaItems as $item)
                                    <tr class="cronograma-row border-b hover:bg-gray-50" data-id="{{ $item->id }}">
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex flex-col items-center gap-1">
                                                <button type="button" onclick="moveCronogramaRow(this, 'up')" class="text-gray-400 hover:text-red-700"><x-fas-chevron-up class="text-xs" /></button>
                                                <button type="button" onclick="moveCronogramaRow(this, 'down')" class="text-gray-400 hover:text-red-700"><x-fas-chevron-down class="text-xs" /></button>
                                            </div>
                                        </td>
                                        <td class="px-3 py-2"><input type="text" class="cg-programa w-full border border-gray-200 rounded px-2 py-1" value="{{ $item->programa }}"></td>
                                        <td class="px-3 py-2"><input type="text" class="cg-convocatoria w-full border border-gray-200 rounded px-2 py-1" value="{{ $item->convocatoria }}"></td>
                                        <td class="px-3 py-2"><input type="text" class="cg-fecha-inscripcion w-full border border-gray-200 rounded px-2 py-1" value="{{ $item->fecha_inscripcion }}"></td>
                                        <td class="px-3 py-2"><input type="text" class="cg-fecha-limite w-full border border-gray-200 rounded px-2 py-1" value="{{ $item->fecha_limite }}"></td>
                                        <td class="px-3 py-2">
                                            <select class="cg-estado w-full border border-gray-200 rounded px-2 py-1">
                                                <option value="Activo" {{ $item->estado === 'Activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="Próximamente" {{ $item->estado === 'Próximamente' ? 'selected' : '' }}>Próximamente</option>
                                                <option value="Cerrado" {{ $item->estado === 'Cerrado' ? 'selected' : '' }}>Cerrado</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <button type="button" onclick="removeCronogramaRow(this)" class="text-red-500 hover:text-red-700"><x-fas-trash /></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB: Requisitos -->
                <div id="tab-requisitos" x-show="tab === 'tab-requisitos'" x-cloak>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label block">Correo institucional</label>
                            <input type="email" name="requisitos_email" value="{{ old('requisitos_email', $settings->requisitos_email) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <label class="form-label block mb-3">Listado de requisitos</label>
                            <div id="requisitos-list" class="space-y-2"></div>
                            <button type="button" onclick="agregarRequisito()" class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-red-700 text-red-700 rounded-lg hover:bg-red-50">
                                <x-fas-plus class="mr-1" /> Agregar Requisito
                            </button>
                        </div>
                        <div>
                            <label class="form-label block">Observaciones</label>
                            <textarea name="requisitos_observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('requisitos_observaciones', $settings->requisitos_observaciones) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label block">Notas</label>
                            <textarea name="requisitos_notas" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('requisitos_notas', $settings->requisitos_notas) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TAB: Pago -->
                <div id="tab-pago" x-show="tab === 'tab-pago'" x-cloak>
                    <div class="space-y-4">
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label block">Costo</label>
                                <input type="text" name="pago_costo" value="{{ old('pago_costo', $settings->pago_costo) }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="S/ 200 (Bachiller UNMSM) · S/ 280 (otras universidades)">
                            </div>
                            <div>
                                <label class="form-label block">Enlace a San Market</label>
                                <input type="url" name="pago_link_sanmarket" value="{{ old('pago_link_sanmarket', $settings->pago_link_sanmarket) }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="form-label block">Descripción</label>
                            <textarea name="pago_descripcion" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('pago_descripcion', $settings->pago_descripcion) }}</textarea>
                        </div>
                        <div class="border border-gray-200 rounded-lg p-4">
                            <label class="form-label block mb-3">Pasos para el pago (con video opcional)</label>
                            <div id="pago-instrucciones-list" class="space-y-3"></div>
                            <button type="button" onclick="agregarPagoInstruccion()" class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-red-700 text-red-700 rounded-lg hover:bg-red-50">
                                <x-fas-plus class="mr-1" /> Agregar Paso
                            </button>
                        </div>
                        <div>
                            <label class="form-label block">Observaciones</label>
                            <textarea name="pago_observaciones" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('pago_observaciones', $settings->pago_observaciones) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TAB: Resultados -->
                <div id="tab-resultados" x-show="tab === 'tab-resultados'" x-cloak>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label block">Texto</label>
                            <textarea name="resultados_texto" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('resultados_texto', $settings->resultados_texto) }}</textarea>
                        </div>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label block">Enlace (opcional)</label>
                                <input type="url" name="resultados_enlace" value="{{ old('resultados_enlace', $settings->resultados_enlace) }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="form-label block">URL del PDF de resultados (opcional)</label>
                                <input type="text" name="resultados_pdf_url" value="{{ old('resultados_pdf_url', $settings->resultados_pdf_url) }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB: Contacto -->
                <div id="tab-contacto" x-show="tab === 'tab-contacto'" x-cloak>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="form-label block">Teléfono</label>
                            <input type="text" name="contacto_telefono" value="{{ old('contacto_telefono', $settings->contacto_telefono) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="form-label block">Correo</label>
                            <input type="email" name="contacto_correo" value="{{ old('contacto_correo', $settings->contacto_correo) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="form-label block">Dirección</label>
                            <textarea name="contacto_direccion" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('contacto_direccion', $settings->contacto_direccion) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label block">Sitio web</label>
                            <input type="url" name="contacto_sitio_web" value="{{ old('contacto_sitio_web', $settings->contacto_sitio_web) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="form-label block">Enlace de WhatsApp</label>
                            <input type="url" name="contacto_whatsapp" value="{{ old('contacto_whatsapp', $settings->contacto_whatsapp) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="https://wa.me/51982085037">
                        </div>
                        <div class="md:col-span-2 border border-gray-200 rounded-lg p-4">
                            <x-admin-file-upload mode="direct" name="qr" label="Código QR (dirige al WhatsApp institucional)"
                                accept="image/*" layout="stacked" preview-size="w-24 h-24" preview-fit="object-contain"
                                :current-path="$settings->contacto_qr_path" remove-checkbox-name="remove_qr"
                                remove-label="Eliminar QR actual"
                                help-text="Opcional: si no subes una imagen, el QR se genera automáticamente a partir del enlace de WhatsApp (número de teléfono) configurado arriba." />
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <button type="submit" :disabled="submitting"
                        class="px-6 py-3 bg-red-700 text-white rounded-lg hover:bg-red-800 font-medium disabled:opacity-60 disabled:cursor-not-allowed">
                        <x-fas-spinner class="animate-spin mr-2" x-show="submitting" x-cloak />
                        <x-fas-save class="mr-2" x-show="!submitting" />
                        <span x-text="submitting ? 'Guardando...' : 'Guardar Todo'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        var pasosData = @json($settings->pasos ?? []);
        var requisitosData = @json($settings->requisitos_lista ?? []);
        var pagoInstruccionesData = @json($settings->pago_instrucciones ?? []);
        var deletedCronogramaItems = [];

        // ===== PASOS =====
        function crearPasoRow(paso) {
            paso = paso || { numero: document.querySelectorAll('#pasos-list .paso-item').length + 1, titulo: '', descripcion: '', icono: '' };
            var div = document.createElement('div');
            div.className = 'paso-item border border-gray-200 rounded-lg p-3 grid md:grid-cols-12 gap-2 items-start';
            div.innerHTML =
                '<input type="number" class="paso-numero md:col-span-1 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (paso.numero ?? '') + '" placeholder="#">' +
                '<input type="text" class="paso-icono md:col-span-2 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (paso.icono ?? '') + '" placeholder="fa-calendar-days">' +
                '<input type="text" class="paso-titulo md:col-span-3 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (paso.titulo ?? '').replace(/"/g, '&quot;') + '" placeholder="Título">' +
                '<input type="text" class="paso-descripcion md:col-span-5 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (paso.descripcion ?? '').replace(/"/g, '&quot;') + '" placeholder="Descripción">' +
                '<button type="button" onclick="this.closest(\'.paso-item\').remove()" class="md:col-span-1 text-red-500 hover:text-red-700 justify-self-center"><x-fas-trash /></button>';
            document.getElementById('pasos-list').appendChild(div);
        }

        function agregarPaso() { crearPasoRow(); }

        // ===== REQUISITOS =====
        function crearRequisitoRow(valor) {
            valor = valor || '';
            var div = document.createElement('div');
            div.className = 'flex gap-2 items-center requisito-item';
            div.innerHTML =
                '<input type="text" class="requisito-valor flex-1 border border-gray-200 rounded px-2 py-1.5 text-sm" value="' + valor.replace(/"/g, '&quot;') + '">' +
                '<button type="button" onclick="this.closest(\'.requisito-item\').remove()" class="text-red-500 hover:text-red-700"><x-fas-trash /></button>';
            document.getElementById('requisitos-list').appendChild(div);
        }

        function agregarRequisito() { crearRequisitoRow(); }

        // ===== PAGO INSTRUCCIONES =====
        function crearPagoInstruccionRow(item) {
            item = item || { titulo: '', descripcion: '', video_url: '' };
            var div = document.createElement('div');
            div.className = 'pago-instruccion-item border border-gray-200 rounded-lg p-3 grid md:grid-cols-12 gap-2 items-start';
            div.innerHTML =
                '<input type="text" class="pi-titulo md:col-span-3 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (item.titulo ?? '').replace(/"/g, '&quot;') + '" placeholder="Título">' +
                '<input type="text" class="pi-descripcion md:col-span-5 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (item.descripcion ?? '').replace(/"/g, '&quot;') + '" placeholder="Descripción">' +
                '<input type="url" class="pi-video md:col-span-3 border border-gray-200 rounded px-2 py-1 text-sm" value="' + (item.video_url ?? '') + '" placeholder="URL video (embed)">' +
                '<button type="button" onclick="this.closest(\'.pago-instruccion-item\').remove()" class="md:col-span-1 text-red-500 hover:text-red-700 justify-self-center"><x-fas-trash /></button>';
            document.getElementById('pago-instrucciones-list').appendChild(div);
        }

        function agregarPagoInstruccion() { crearPagoInstruccionRow(); }

        // ===== CRONOGRAMA =====
        function addCronogramaRow() {
            var tbody = document.getElementById('cronograma-tbody');
            var tr = document.createElement('tr');
            tr.className = 'cronograma-row border-b hover:bg-gray-50';
            tr.dataset.isNew = '1';
            tr.innerHTML =
                '<td class="px-3 py-2 text-center"><div class="flex flex-col items-center gap-1">' +
                '<button type="button" onclick="moveCronogramaRow(this, \'up\')" class="text-gray-400 hover:text-red-700"><x-fas-chevron-up class="text-xs" /></button>' +
                '<button type="button" onclick="moveCronogramaRow(this, \'down\')" class="text-gray-400 hover:text-red-700"><x-fas-chevron-down class="text-xs" /></button></div></td>' +
                '<td class="px-3 py-2"><input type="text" class="cg-programa w-full border border-gray-200 rounded px-2 py-1"></td>' +
                '<td class="px-3 py-2"><input type="text" class="cg-convocatoria w-full border border-gray-200 rounded px-2 py-1"></td>' +
                '<td class="px-3 py-2"><input type="text" class="cg-fecha-inscripcion w-full border border-gray-200 rounded px-2 py-1"></td>' +
                '<td class="px-3 py-2"><input type="text" class="cg-fecha-limite w-full border border-gray-200 rounded px-2 py-1"></td>' +
                '<td class="px-3 py-2"><select class="cg-estado w-full border border-gray-200 rounded px-2 py-1">' +
                '<option value="Activo">Activo</option><option value="Próximamente">Próximamente</option><option value="Cerrado">Cerrado</option></select></td>' +
                '<td class="px-3 py-2 text-right"><button type="button" onclick="removeCronogramaRow(this)" class="text-red-500 hover:text-red-700"><x-fas-trash /></button></td>';
            tbody.appendChild(tr);
        }

        function removeCronogramaRow(btn) {
            var row = btn.closest('tr');
            if (row.dataset.id) deletedCronogramaItems.push(parseInt(row.dataset.id));
            row.remove();
        }

        function moveCronogramaRow(btn, direction) {
            var row = btn.closest('tr');
            var tbody = row.parentNode;
            if (direction === 'up' && row.previousElementSibling) tbody.insertBefore(row, row.previousElementSibling);
            if (direction === 'down' && row.nextElementSibling) tbody.insertBefore(row.nextElementSibling, row);
        }

        // ===== INIT =====
        document.addEventListener('DOMContentLoaded', function() {
            pasosData.forEach(function(p) { crearPasoRow(p); });
            requisitosData.forEach(function(r) { crearRequisitoRow(r); });
            pagoInstruccionesData.forEach(function(p) { crearPagoInstruccionRow(p); });
        });

        // ===== SERIALIZE BEFORE SUBMIT =====
        document.getElementById('admisionForm').addEventListener('submit', function() {
            var pasos = [];
            document.querySelectorAll('#pasos-list .paso-item').forEach(function(el) {
                pasos.push({
                    numero: parseInt(el.querySelector('.paso-numero').value) || 0,
                    icono: el.querySelector('.paso-icono').value.trim(),
                    titulo: el.querySelector('.paso-titulo').value.trim(),
                    descripcion: el.querySelector('.paso-descripcion').value.trim(),
                });
            });
            document.getElementById('pasos_input').value = JSON.stringify(pasos);

            var requisitos = [];
            document.querySelectorAll('#requisitos-list .requisito-item').forEach(function(el) {
                var val = el.querySelector('.requisito-valor').value.trim();
                if (val) requisitos.push(val);
            });
            document.getElementById('requisitos_lista_input').value = JSON.stringify(requisitos);

            var pagoInstrucciones = [];
            document.querySelectorAll('#pago-instrucciones-list .pago-instruccion-item').forEach(function(el) {
                pagoInstrucciones.push({
                    titulo: el.querySelector('.pi-titulo').value.trim(),
                    descripcion: el.querySelector('.pi-descripcion').value.trim(),
                    video_url: el.querySelector('.pi-video').value.trim(),
                });
            });
            document.getElementById('pago_instrucciones_input').value = JSON.stringify(pagoInstrucciones);

            var cronogramaItems = [];
            document.querySelectorAll('#cronograma-tbody .cronograma-row').forEach(function(row, index) {
                cronogramaItems.push({
                    id: row.dataset.id ? parseInt(row.dataset.id) : null,
                    is_new: row.dataset.isNew === '1',
                    programa: row.querySelector('.cg-programa').value.trim(),
                    convocatoria: row.querySelector('.cg-convocatoria').value.trim(),
                    fecha_inscripcion: row.querySelector('.cg-fecha-inscripcion').value.trim(),
                    fecha_limite: row.querySelector('.cg-fecha-limite').value.trim(),
                    estado: row.querySelector('.cg-estado').value,
                    orden: index,
                });
            });
            document.getElementById('cronograma_items_payload').value = JSON.stringify(cronogramaItems);
            document.getElementById('deleted_cronograma_items').value = JSON.stringify(deletedCronogramaItems);
        });
    </script>
@endsection

@extends('admin.layout.app')

@section('title', 'Cronograma Académico')

@section('content')
    <div class="max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('admin.cronograma.update') }}" method="POST" id="cronogramaForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Hidden inputs -->
            <div id="new-documents-container" class="hidden"></div>
            <input type="hidden" name="items_payload" id="items_payload" value="">
            <input type="hidden" name="deleted_items" id="deleted_items" value="">

            <div class="grid lg:grid-cols-3 gap-6">
                <!-- ========== COLUMNA IZQUIERDA ========== -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gray-50 border-b px-5 py-4">
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">Activo</span>
                            <h2 class="text-lg font-bold text-gray-800 mt-1">{{ $cronograma->code }}</h2>
                            <p class="text-xs text-gray-500">Configuración general</p>
                        </div>

                        <!-- Body -->
                        <div class="p-5 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Código</label>
                                <input type="text" name="code" value="{{ old('code', $cronograma->code) }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Título</label>
                                <input type="text" name="title" value="{{ old('title', $cronograma->title) }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" required>
                            </div>

                            <input type="hidden" name="effective_date" value="{{ $cronograma->effective_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}">

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Descripción</label>
                                <textarea name="description" rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">{{ old('description', $cronograma->description) }}</textarea>
                            </div>

                            <!-- Documentos PDF -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Documentos PDF</label>
                                <div id="documents-container" class="space-y-2 mb-3">
                                    @forelse($cronograma->documents as $doc)
                                        <div class="document-item flex items-center gap-2 p-3 bg-white border rounded-lg" data-id="{{ $doc->id }}">
                                            <input type="hidden" name="document_ids[]" value="{{ $doc->id }}">
                                            <x-fas-file-pdf class="text-red-600" />
                                            <span class="flex-1 text-xs font-medium truncate">{{ $doc->display_title }}</span>
                                            <button type="button" onclick="removeDocumentField(this)" class="text-red-500 hover:text-red-700">
                                                <x-fas-times />
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400 text-center py-2" id="no-docs-msg">Sin documentos vinculados</p>
                                    @endforelse
                                </div>
                                <div class="flex gap-2">
                                    <button type="button" onclick="openUploadModal()" class="flex-1 px-3 py-2 border border-unmsm-azul text-unmsm-azul rounded-lg hover:bg-unmsm-azul/5 text-xs">
                                        <x-fas-cloud-upload-alt class="mr-1" /> Subir Nuevo
                                    </button>
                                    <button type="button" onclick="openLinkModal()" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-xs">
                                        <x-fas-link class="mr-1" /> Vincular
                                    </button>
                                </div>
                            </div>

                            <!-- Botón Guardar -->
                            <button type="submit" class="w-full px-4 py-3 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark font-medium">
                                <x-fas-save class="mr-2" /> Guardar Todo
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ========== COLUMNA DERECHA: ÍTEMS ========== -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Header -->
                        <div class="bg-gray-50 border-b px-5 py-4 flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-bold text-gray-800">Ítems del Cronograma</h2>
                                <p class="text-xs text-gray-500"><x-fas-list-ol class="mr-1" />Usa las flechas para reordenar</p>
                            </div>
                            <button type="button" onclick="addItem()" class="px-4 py-2 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark text-sm">
                                <x-fas-plus class="mr-1" /> Nuevo Ítem
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full" id="items-table">
                                <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                                    <tr>
                                        <th class="px-3 py-3 text-center w-14">Orden</th>
                                        <th class="px-3 py-3 text-left">Actividad / Evento</th>
                                        <th class="px-3 py-3 text-left w-1/4">Fecha</th>
                                        <th class="px-3 py-3 text-right w-20">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                    @foreach($cronograma->items as $item)
                                        <tr class="item-row border-b {{ $item->is_section_heading ? 'bg-unmsm-azul/5 border-l-4 border-l-unmsm-azul' : 'hover:bg-gray-50' }}"
                                            data-id="{{ $item->id }}"
                                            data-is-section="{{ $item->is_section_heading ? '1' : '0' }}"
                                            data-section-val="{{ $item->section }}"
                                            data-actividad="{{ $item->actividad }}"
                                            data-fecha="{{ $item->fecha_text }}">
                                            
                                            <!-- Orden -->
                                            <td class="px-3 py-3 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    <button type="button" onclick="moveRow(this, 'up')" class="text-gray-400 hover:text-unmsm-azul">
                                                        <x-fas-chevron-up class="text-xs" />
                                                    </button>
                                                    <button type="button" onclick="moveRow(this, 'down')" class="text-gray-400 hover:text-unmsm-azul">
                                                        <x-fas-chevron-down class="text-xs" />
                                                    </button>
                                                </div>
                                            </td>

                                            @if($item->is_section_heading)
                                                <!-- Section Heading -->
                                                <td colspan="2" class="px-3 py-3">
                                                    <span class="font-bold text-unmsm-azul uppercase text-sm">{{ $item->actividad }}</span>
                                                </td>
                                            @else
                                                <!-- Normal Item -->
                                                <td class="px-3 py-3">
                                                    <span class="font-medium text-gray-800 text-sm">{{ $item->actividad }}</span>
                                                    @if($item->section)
                                                        <div class="text-xs text-gray-400 mt-1">Sección: {{ $item->section }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-3 py-3">
                                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 border rounded text-xs text-gray-700">
                                                        <x-far-calendar class="mr-1" /> {{ $item->fecha_text }}
                                                    </span>
                                                </td>
                                            @endif

                                            <!-- Acciones -->
                                            <td class="px-3 py-3 text-right">
                                                <button type="button" onclick="editItem(this)" class="text-blue-600 hover:text-blue-800 mr-2" title="Editar" aria-label="Editar">
                                                    <x-fas-edit />
                                                </button>
                                                <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700" title="Eliminar" aria-label="Eliminar">
                                                    <x-fas-trash />
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Modal Subir Documento -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeUploadModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Subir Nuevo PDF</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                        <x-fas-info-circle class="mr-1" />
                        El archivo se guardará y vinculará automáticamente al cronograma.
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título del Documento *</label>
                        <input type="text" id="new_doc_title" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Ej: Resolución Rectoral N°...">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Archivo PDF *</label>
                        <input type="file" id="new_doc_file" class="w-full border border-gray-300 rounded-lg px-3 py-2" accept="application/pdf">
                        <p class="text-xs text-gray-500 mt-1">Máximo 10MB.</p>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" onclick="closeUploadModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" onclick="confirmUploadDoc()" class="flex-1 px-4 py-2 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark">
                        <x-fas-check class="mr-1" /> Agregar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Vincular Documento -->
    <div id="linkModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeLinkModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Vincular Documento Existente</h3>
                    <div class="mb-4">
                        <input type="text" id="search_doc_input" oninput="filterDocs(this.value)" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Buscar por título...">
                    </div>
                    <div id="doc-search-results" class="max-h-64 overflow-y-auto border rounded-lg">
                    </div>
                </div>
                <div class="px-6 pb-6">
                    <button type="button" onclick="closeLinkModal()" class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar/Editar Ítem -->
    <div id="itemModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeItemModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4" id="itemModalTitle">Agregar Ítem</h3>
                    <input type="hidden" id="edit_item_id">
                    
                    <div class="mb-4">
                        <label class="flex items-center gap-2 p-3 bg-gray-50 border rounded-lg cursor-pointer">
                            <input type="checkbox" id="is_section_heading" class="rounded" onchange="toggleItemFields()">
                            <span class="text-sm font-medium">Es un encabezado de sección</span>
                        </label>
                    </div>

                    <div id="section-fields" class="hidden mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Sección</label>
                        <input type="text" id="section_name" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Ej: PROCESO DE MATRÍCULA">
                    </div>

                    <div id="normal-fields">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sección (Grupo)</label>
                            <select id="section_select" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">-- Sin Sección --</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Actividad *</label>
                            <textarea id="actividad" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Descripción de la actividad..."></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha (texto)</label>
                            <input type="text" id="fecha_text" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Ej: Del 10 al 15 de enero">
                        </div>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-3">
                    <button type="button" onclick="closeItemModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="button" onclick="saveItem()" class="flex-1 px-4 py-2 bg-unmsm-azul text-white rounded-lg hover:bg-unmsm-azul-dark">
                        <x-fas-check class="mr-1" /> Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var deletedItems = [];
        var newItemCounter = 0;
        var newDocCounter = 0;
        var availableDocuments = @json($documents->map(fn($d) => ['id' => $d->id, 'title' => $d->display_title]));

        // ===== DOCUMENTOS =====
        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
            document.getElementById('new_doc_title').value = '';
            document.getElementById('new_doc_file').value = '';
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }

        function openLinkModal() {
            document.getElementById('linkModal').classList.remove('hidden');
            document.getElementById('search_doc_input').value = '';
            filterDocs('');
        }

        function closeLinkModal() {
            document.getElementById('linkModal').classList.add('hidden');
        }

        function filterDocs(query) {
            const container = document.getElementById('doc-search-results');
            container.innerHTML = '';

            const linkedIds = [];
            document.querySelectorAll('input[name="document_ids[]"]').forEach(input => {
                if (input.value) linkedIds.push(parseInt(input.value));
            });

            const q = query.toLowerCase();
            const matches = availableDocuments.filter(doc => {
                return !linkedIds.includes(doc.id) && doc.title.toLowerCase().includes(q);
            });

            if (matches.length === 0) {
                container.innerHTML = '<p class="text-gray-500 text-center py-4">No se encontraron documentos.</p>';
            } else {
                matches.forEach(doc => {
                    const el = document.createElement('button');
                    el.type = 'button';
                    el.className = 'w-full text-left px-4 py-3 hover:bg-gray-100 border-b flex justify-between items-center';
                    el.innerHTML = '<span class="font-medium">' + doc.title + '</span><x-fas-plus class="text-green-600" />';
                    el.onclick = function() { selectDoc(doc); };
                    container.appendChild(el);
                });
            }
        }

        function selectDoc(doc) {
            createDocVisual(doc.title, doc.id, null);
            closeLinkModal();
        }

        function confirmUploadDoc() {
            const title = document.getElementById('new_doc_title').value.trim();
            const fileInput = document.getElementById('new_doc_file');

            if (!title) { alert('El título es obligatorio.'); return; }
            if (fileInput.files.length === 0) { alert('Debe seleccionar un archivo.'); return; }

            const originalParent = fileInput.parentNode;
            const newItemId = 'new_doc_' + (++newDocCounter);
            
            createDocVisual(title, null, newItemId);

            const container = document.getElementById('new-documents-container');
            const wrapper = document.createElement('div');
            wrapper.className = 'hidden doc-upload-wrapper';
            wrapper.dataset.itemId = newItemId;

            const cleanFileInput = document.createElement('input');
            cleanFileInput.type = 'file';
            cleanFileInput.id = 'new_doc_file';
            cleanFileInput.className = 'w-full border border-gray-300 rounded-lg px-3 py-2';
            cleanFileInput.accept = 'application/pdf';

            fileInput.name = 'new_document_files[]';
            fileInput.removeAttribute('id');

            const titleInput = document.createElement('input');
            titleInput.type = 'hidden';
            titleInput.name = 'new_document_titles[]';
            titleInput.value = title;

            wrapper.appendChild(fileInput);
            wrapper.appendChild(titleInput);
            container.appendChild(wrapper);

            originalParent.appendChild(cleanFileInput);
            
            closeUploadModal();
        }

        function createDocVisual(title, id, tempId) {
            const container = document.getElementById('documents-container');
            const empty = container.querySelector('#no-docs-msg');
            if (empty) empty.remove();

            const div = document.createElement('div');
            div.className = 'document-item flex items-center gap-2 p-3 bg-white border rounded-lg';

            let hiddenFields = '';
            let badge = '';
            if (id) {
                div.dataset.id = id;
                hiddenFields = '<input type="hidden" name="document_ids[]" value="' + id + '">';
            } else if (tempId) {
                div.dataset.tempId = tempId;
                badge = '<span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pendiente</span>';
            }

            div.innerHTML = hiddenFields +
                '<x-fas-file-pdf class="text-red-600" />' +
                '<span class="flex-1 text-xs font-medium truncate">' + title + '</span>' +
                badge +
                '<button type="button" onclick="removeDocumentField(this)" class="text-red-500 hover:text-red-700"><x-fas-times /></button>';
            
            container.appendChild(div);
        }

        function removeDocumentField(btn) {
            const item = btn.closest('.document-item');
            if (item.dataset.tempId) {
                const inputWrapper = document.querySelector('.doc-upload-wrapper[data-item-id="' + item.dataset.tempId + '"]');
                if (inputWrapper) inputWrapper.remove();
            }
            item.remove();
        }

        // ===== ÍTEMS =====
        function addItem() {
            document.getElementById('itemModalTitle').textContent = 'Agregar Ítem';
            document.getElementById('edit_item_id').value = '';
            document.getElementById('is_section_heading').checked = false;
            document.getElementById('section_name').value = '';
            document.getElementById('actividad').value = '';
            document.getElementById('fecha_text').value = '';
            document.getElementById('section_select').value = '';
            toggleItemFields();
            populateSectionSelect();
            document.getElementById('itemModal').classList.remove('hidden');
        }

        function editItem(btn) {
            const row = btn.closest('tr');
            document.getElementById('itemModalTitle').textContent = 'Editar Ítem';
            document.getElementById('edit_item_id').value = row.dataset.id;
            
            const isSection = row.dataset.isSection === '1';
            document.getElementById('is_section_heading').checked = isSection;
            toggleItemFields();
            populateSectionSelect();
            
            if (isSection) {
                document.getElementById('section_name').value = row.dataset.actividad;
            } else {
                document.getElementById('section_select').value = row.dataset.sectionVal || '';
                document.getElementById('actividad').value = row.dataset.actividad;
                document.getElementById('fecha_text').value = row.dataset.fecha;
            }
            
            document.getElementById('itemModal').classList.remove('hidden');
        }

        function closeItemModal() {
            document.getElementById('itemModal').classList.add('hidden');
        }

        function toggleItemFields() {
            const isSection = document.getElementById('is_section_heading').checked;
            document.getElementById('section-fields').classList.toggle('hidden', !isSection);
            document.getElementById('normal-fields').classList.toggle('hidden', isSection);
        }

        function populateSectionSelect() {
            const select = document.getElementById('section_select');
            select.innerHTML = '<option value="">-- Sin Sección --</option>';

            const sections = new Set();
            document.querySelectorAll('#items-tbody tr[data-is-section="1"]').forEach(row => {
                sections.add(row.dataset.actividad);
            });

            sections.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s;
                opt.textContent = s;
                select.appendChild(opt);
            });
        }

        function saveItem() {
            const isSection = document.getElementById('is_section_heading').checked;
            const editId = document.getElementById('edit_item_id').value;
            
            let sectionVal, actividad, fecha;
            
            if (isSection) {
                actividad = document.getElementById('section_name').value.trim();
                if (!actividad) { alert('El nombre de la sección es obligatorio'); return; }
                sectionVal = actividad;
                fecha = '—';
            } else {
                sectionVal = document.getElementById('section_select').value;
                actividad = document.getElementById('actividad').value.trim();
                fecha = document.getElementById('fecha_text').value.trim() || '—';
                if (!actividad) { alert('La actividad es obligatoria'); return; }
            }

            const tbody = document.getElementById('items-tbody');
            
            if (editId) {
                // Editar existente
                const row = document.querySelector(`tr[data-id="${editId}"]`);
                if (row) {
                    row.dataset.isSection = isSection ? '1' : '0';
                    row.dataset.sectionVal = sectionVal;
                    row.dataset.actividad = actividad;
                    row.dataset.fecha = fecha;
                    row.className = 'item-row border-b ' + (isSection ? 'bg-unmsm-azul/5 border-l-4 border-l-unmsm-azul' : 'hover:bg-gray-50');
                    updateRowHTML(row, isSection, actividad, sectionVal, fecha);
                }
            } else {
                // Crear nuevo
                const newId = 'new_' + (++newItemCounter);
                const tr = document.createElement('tr');
                tr.className = 'item-row border-b ' + (isSection ? 'bg-unmsm-azul/5 border-l-4 border-l-unmsm-azul' : 'hover:bg-gray-50');
                tr.dataset.id = newId;
                tr.dataset.isNew = '1';
                tr.dataset.isSection = isSection ? '1' : '0';
                tr.dataset.sectionVal = sectionVal;
                tr.dataset.actividad = actividad;
                tr.dataset.fecha = fecha;
                updateRowHTML(tr, isSection, actividad, sectionVal, fecha);
                tbody.appendChild(tr);
            }
            
            closeItemModal();
        }

        function updateRowHTML(row, isSection, actividad, sectionVal, fecha) {
            let html = `
                <td class="px-3 py-3 text-center">
                    <div class="flex flex-col items-center gap-1">
                        <button type="button" onclick="moveRow(this, 'up')" class="text-gray-400 hover:text-unmsm-azul">
                            <x-fas-chevron-up class="text-xs" />
                        </button>
                        <button type="button" onclick="moveRow(this, 'down')" class="text-gray-400 hover:text-unmsm-azul">
                            <x-fas-chevron-down class="text-xs" />
                        </button>
                    </div>
                </td>`;

            if (isSection) {
                html += `<td colspan="2" class="px-3 py-3"><span class="font-bold text-unmsm-azul uppercase text-sm">${actividad}</span></td>`;
            } else {
                html += `<td class="px-3 py-3"><span class="font-medium text-gray-800 text-sm">${actividad}</span>`;
                if (sectionVal) html += `<div class="text-xs text-gray-400 mt-1">Sección: ${sectionVal}</div>`;
                html += `</td><td class="px-3 py-3"><span class="inline-flex items-center px-2 py-1 bg-gray-100 border rounded text-xs text-gray-700"><x-far-calendar class="mr-1" /> ${fecha}</span></td>`;
            }

            html += `
                <td class="px-3 py-3 text-right">
                    <button type="button" onclick="editItem(this)" class="text-blue-600 hover:text-blue-800 mr-2" title="Editar" aria-label="Editar"><x-fas-edit /></button>
                    <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700" title="Eliminar" aria-label="Eliminar"><x-fas-trash /></button>
                </td>`;

            row.innerHTML = html;
        }

        function removeItem(btn) {
            if (!confirm('¿Eliminar este ítem?')) return;
            const row = btn.closest('tr');
            const id = row.dataset.id;
            if (id && !String(id).startsWith('new_')) {
                deletedItems.push(parseInt(id));
            }
            row.remove();
        }

        function moveRow(btn, direction) {
            const row = btn.closest('tr');
            const tbody = row.parentNode;
            if (direction === 'up' && row.previousElementSibling) {
                tbody.insertBefore(row, row.previousElementSibling);
            }
            if (direction === 'down' && row.nextElementSibling) {
                tbody.insertBefore(row.nextElementSibling, row);
            }
        }

        // Before submit
        document.getElementById('cronogramaForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('#items-tbody tr.item-row');
            const items = [];

            rows.forEach((row, index) => {
                items.push({
                    id: row.dataset.isNew ? null : parseInt(row.dataset.id),
                    is_new: row.dataset.isNew === '1',
                    section: row.dataset.sectionVal || '',
                    is_section_heading: row.dataset.isSection === '1',
                    actividad: row.dataset.actividad || '',
                    fecha_text: row.dataset.fecha || '',
                    orden: index
                });
            });

            document.getElementById('items_payload').value = JSON.stringify(items);
            document.getElementById('deleted_items').value = JSON.stringify(deletedItems);
        });
    </script>
@endsection

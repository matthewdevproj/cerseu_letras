@extends('admin.layout.app')

@section('title', 'Cronograma Académico')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Cronograma Académico</h1>
            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                {{ $cronograma->code }}
            </span>
        </div>

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

            <!-- Contenedor oculto para archivos nuevos -->
            <div id="new-documents-container" class="hidden"></div>

            <!-- Datos Generales -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Datos Generales</h2>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código *</label>
                        <input type="text" name="code" value="{{ old('code', $cronograma->code) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Ej: 2026-I" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
                        <input type="text" name="title" value="{{ old('title', $cronograma->title) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Ej: Cronograma Académico 2026-I" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="description" rows="2"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Descripción opcional">{{ old('description', $cronograma->description) }}</textarea>
                    </div>
                    <input type="hidden" name="effective_date" value="{{ $cronograma->effective_date?->format('Y-m-d') ?? now()->format('Y-m-d') }}">
                </div>
            </div>

            <!-- Documentos Asociados -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Documentos PDF Vinculados</h2>
                <div id="documents-container" class="space-y-2 mb-4">
                    @forelse($cronograma->documents as $doc)
                        <div class="document-item flex items-center gap-2 p-3 bg-gray-50 rounded-lg border" data-id="{{ $doc->id }}">
                            <input type="hidden" name="document_ids[]" value="{{ $doc->id }}">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="flex-1 text-sm font-medium">{{ $doc->display_title }}</span>
                            <button type="button" onclick="removeDocumentField(this)" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-2" id="no-docs-msg">Sin documentos vinculados</p>
                    @endforelse
                </div>
                
                <!-- Botones de acción -->
                <div class="flex gap-2">
                    <button type="button" onclick="openUploadModal()" class="flex-1 px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 text-sm">
                        <i class="fas fa-cloud-upload-alt mr-1"></i> Subir Nuevo
                    </button>
                    <button type="button" onclick="openLinkModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm">
                        <i class="fas fa-link mr-1"></i> Vincular Existente
                    </button>
                </div>
            </div>

            <!-- Ítems del Cronograma -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-700">Ítems del Cronograma</h2>
                    <button type="button" onclick="addItem()"
                        class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 text-sm">
                        + Agregar Ítem
                    </button>
                </div>

                <div id="items-container" class="space-y-2">
                    @foreach($cronograma->items as $item)
                        <div class="item-row border border-gray-200 rounded-lg p-3 {{ $item->is_section_heading ? 'bg-red-50 border-red-300' : 'bg-white' }}"
                            data-id="{{ $item->id }}">
                            <div class="flex items-start gap-3">
                                <div class="cursor-move text-gray-400 pt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 8h16M4 16h16" />
                                    </svg>
                                </div>
                                <div class="flex-1 grid md:grid-cols-12 gap-2">
                                    <div class="md:col-span-1">
                                        <label class="flex items-center gap-1 text-xs text-gray-500">
                                            <input type="checkbox" class="item-heading rounded"
                                                {{ $item->is_section_heading ? 'checked' : '' }}
                                                onchange="toggleHeading(this)">
                                            Sección
                                        </label>
                                    </div>
                                    <div class="md:col-span-5">
                                        <input type="text" class="item-actividad w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                            value="{{ $item->actividad }}" placeholder="Actividad">
                                    </div>
                                    <div class="md:col-span-3">
                                        <input type="text" class="item-fecha w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                            value="{{ $item->fecha_text }}" placeholder="Fecha">
                                    </div>
                                    <div class="md:col-span-2">
                                        <input type="text" class="item-section w-full border border-gray-300 rounded px-2 py-1 text-sm"
                                            value="{{ $item->section }}" placeholder="Sección (grupo)">
                                    </div>
                                    <div class="md:col-span-1 flex justify-end">
                                        <button type="button" onclick="removeItem(this)"
                                            class="text-red-500 hover:text-red-700">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="item-orden" value="{{ $item->orden }}">
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Hidden fields -->
            <input type="hidden" name="deleted_items" id="deleted_items" value="[]">
            <input type="hidden" name="items_payload" id="items_payload" value="[]">

            <!-- Submit -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('cronograma') }}" target="_blank"
                    class="px-6 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Vista Previa
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 font-medium">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <script>
        let deletedItems = [];
        let newItemCounter = 0;

        function addItem() {
            newItemCounter++;
            const container = document.getElementById('items-container');
            const html = `
                <div class="item-row border border-gray-200 rounded-lg p-3 bg-green-50 border-green-300" data-id="new_${newItemCounter}" data-new="true">
                    <div class="flex items-start gap-3">
                        <div class="cursor-move text-gray-400 pt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                            </svg>
                        </div>
                        <div class="flex-1 grid md:grid-cols-12 gap-2">
                            <div class="md:col-span-1">
                                <label class="flex items-center gap-1 text-xs text-gray-500">
                                    <input type="checkbox" class="item-heading rounded" onchange="toggleHeading(this)">
                                    Sección
                                </label>
                            </div>
                            <div class="md:col-span-5">
                                <input type="text" class="item-actividad w-full border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Actividad">
                            </div>
                            <div class="md:col-span-3">
                                <input type="text" class="item-fecha w-full border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Fecha">
                            </div>
                            <div class="md:col-span-2">
                                <input type="text" class="item-section w-full border border-gray-300 rounded px-2 py-1 text-sm" placeholder="Sección (grupo)">
                            </div>
                            <div class="md:col-span-1 flex justify-end">
                                <button type="button" onclick="removeItem(this)" class="text-red-500 hover:text-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" class="item-orden" value="${container.children.length}">
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        }

        function removeItem(btn) {
            const row = btn.closest('.item-row');
            const id = row.dataset.id;

            if (!row.dataset.new && id) {
                deletedItems.push(parseInt(id));
            }

            row.remove();
            updateOrders();
        }

        function toggleHeading(checkbox) {
            const row = checkbox.closest('.item-row');
            if (checkbox.checked) {
                row.classList.remove('bg-white', 'bg-green-50');
                row.classList.add('bg-red-50', 'border-red-300');
            } else {
                row.classList.remove('bg-red-50', 'border-red-300');
                row.classList.add('bg-white');
            }
        }

        function updateOrders() {
            const rows = document.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                row.querySelector('.item-orden').value = index;
            });
        }

        // ===== DOCUMENTOS =====
        var newDocCounter = 0;
        var availableDocuments = @json($documents->map(fn($d) => ['id' => $d->id, 'title' => $d->display_title]));

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

            // Get already linked IDs
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
                    el.innerHTML = '<span class="font-medium">' + doc.title + '</span><i class="fas fa-plus text-green-600"></i>';
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

            // Mover input al contenedor oculto
            const container = document.getElementById('new-documents-container');
            const wrapper = document.createElement('div');
            wrapper.className = 'hidden doc-upload-wrapper';
            wrapper.dataset.itemId = newItemId;

            const cleanFileInput = document.createElement('input');
            cleanFileInput.type = 'file';
            cleanFileInput.id = 'new_doc_file';
            cleanFileInput.className = 'hidden';
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
            div.className = 'document-item flex items-center gap-2 p-3 bg-gray-50 rounded-lg border';

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
                '<i class="fas fa-file-pdf text-red-600"></i>' +
                '<span class="flex-1 text-sm font-medium">' + title + '</span>' +
                badge +
                '<button type="button" onclick="removeDocumentField(this)" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>';
            
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

        // Before submit, collect all items
        document.getElementById('cronogramaForm').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.item-row');
            const items = [];

            rows.forEach((row, index) => {
                items.push({
                    id: row.dataset.new ? null : parseInt(row.dataset.id),
                    is_new: !!row.dataset.new,
                    section: row.querySelector('.item-section').value,
                    is_section_heading: row.querySelector('.item-heading').checked,
                    actividad: row.querySelector('.item-actividad').value,
                    fecha_text: row.querySelector('.item-fecha').value,
                    orden: index
                });
            });

            document.getElementById('items_payload').value = JSON.stringify(items);
            document.getElementById('deleted_items').value = JSON.stringify(deletedItems);
        });
    </script>

    <!-- Modal Subir Documento -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-black/50" onclick="closeUploadModal()"></div>
        <div class="fixed inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-md w-full">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Subir Nuevo PDF</h3>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
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
                    <button type="button" onclick="confirmUploadDoc()" class="flex-1 px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                        <i class="fas fa-check mr-1"></i> Agregar
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
                        <!-- Resultados JS -->
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
@endsection

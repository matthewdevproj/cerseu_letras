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
                        <div class="flex items-center gap-2 p-2 bg-gray-50 rounded document-row" data-id="{{ $doc->id }}">
                            <input type="hidden" name="document_ids[]" value="{{ $doc->id }}">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <span class="flex-1 text-sm">{{ $doc->display_title }}</span>
                            <button type="button" onclick="removeDocument(this)" class="text-red-500 hover:text-red-700 text-sm">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400" id="no-docs-msg">Sin documentos vinculados</p>
                    @endforelse
                </div>
                
                <!-- Vincular existente -->
                <div class="flex gap-2 mb-3">
                    <select id="addDocumentSelect" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="">Seleccionar documento existente...</option>
                        @foreach($documents as $doc)
                            <option value="{{ $doc->id }}" data-title="{{ $doc->display_title }}">{{ $doc->display_title }}</option>
                        @endforeach
                    </select>
                    <button type="button" onclick="addDocument()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm">
                        Vincular
                    </button>
                </div>
                
                <!-- Subir nuevo -->
                <div class="border-t pt-3 mt-3">
                    <p class="text-xs text-gray-500 mb-2"><i class="fas fa-cloud-upload-alt"></i> O sube un nuevo PDF:</p>
                    <div class="grid md:grid-cols-3 gap-2">
                        <input type="text" id="newDocTitle" placeholder="Título del documento" 
                            class="border border-gray-300 rounded-lg px-3 py-2 text-sm col-span-2">
                        <div class="flex gap-2">
                            <input type="file" id="newDocFile" accept=".pdf" class="hidden">
                            <button type="button" onclick="document.getElementById('newDocFile').click()" 
                                class="flex-1 px-3 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800 text-sm">
                                <i class="fas fa-upload"></i> Subir PDF
                            </button>
                        </div>
                    </div>
                    <p id="uploadStatus" class="text-xs text-gray-400 mt-2"></p>
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

        function addDocument() {
            const select = document.getElementById('addDocumentSelect');
            const docId = select.value;
            const docTitle = select.options[select.selectedIndex].dataset.title;

            if (!docId) return;

            // Verificar si ya existe
            const existing = document.querySelector(`.document-row[data-id="${docId}"]`);
            if (existing) {
                alert('Este documento ya está agregado');
                return;
            }

            const container = document.getElementById('documents-container');
            const position = container.children.length;

            const html = `
                <div class="flex items-center gap-2 p-2 bg-gray-50 rounded document-row" data-id="${docId}">
                    <input type="hidden" name="document_ids[]" value="${docId}">
                    <input type="hidden" name="document_positions[]" value="${position}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-600 cursor-move" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                    </svg>
                    <span class="flex-1">${docTitle}</span>
                    <button type="button" onclick="removeDocument(this)" class="text-red-500 hover:text-red-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            select.value = '';
        }

        function removeDocument(btn) {
            btn.closest('.document-row').remove();
        }

        // Subir nuevo documento via AJAX
        document.getElementById('newDocFile').addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;

            const title = document.getElementById('newDocTitle').value.trim() || file.name;
            const statusEl = document.getElementById('uploadStatus');
            
            statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subiendo...';
            statusEl.className = 'text-xs text-blue-600 mt-2';

            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', 'general');
            formData.append('title', title);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            try {
                const response = await fetch('{{ route("admin.documents.store") }}', {
                    method: 'POST',
                    body: formData
                });

                if (response.redirected) {
                    // Documento creado, ahora obtener su ID del redirect
                    statusEl.innerHTML = '<i class="fas fa-check"></i> Subido: ' + title;
                    statusEl.className = 'text-xs text-green-600 mt-2';
                    
                    // Recargar la página para ver el nuevo documento
                    setTimeout(() => location.reload(), 1000);
                } else {
                    throw new Error('Error al subir');
                }
            } catch (error) {
                statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error al subir';
                statusEl.className = 'text-xs text-red-600 mt-2';
            }

            // Reset
            this.value = '';
            document.getElementById('newDocTitle').value = '';
        });

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
@endsection

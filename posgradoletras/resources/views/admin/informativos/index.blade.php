@extends('admin.layout.app')

@section('title', 'Gestión de Informativos')

@section('content')
    <div class="max-w-7xl mx-auto">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded relative">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid lg:grid-cols-3 gap-6">
            <!-- ========== COLUMNA IZQUIERDA: CONFIGURACIÓN / AYUDA ========== -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-24">
                    <div class="bg-gray-50 border-b px-5 py-4">
                        <h2 class="text-lg font-bold text-gray-800">Recursos Informativos</h2>
                        <p class="text-xs text-gray-500">Gestión de documentos públicos</p>
                    </div>
                    <div class="p-5">
                       <p class="text-sm text-gray-600 mb-4">
                           Aquí puedes gestionar los documentos como Reglamentos, Directivas y otros recursos informativos.
                       </p>
                       <ul class="text-xs text-gray-500 list-disc list-inside space-y-1 mb-6">
                           <li>Usa las credenciales de categoría para agrupar.</li>
                           <li>Sube archivos PDF (max 10MB) o enlaces externos.</li>
                           <li>Arrastra las filas para reordenar (próximamente).</li>
                       </ul>
                       
                       <button onclick="openModal()" class="w-full px-4 py-3 bg-brand-red text-white rounded-lg hover:bg-red-800 font-medium shadow-sm transition-all flex items-center justify-center gap-2">
                           <x-fas-plus /> Nuevo Documento
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
                            <h2 class="text-lg font-bold text-gray-800">Listado de Documentos</h2>
                            <p class="text-xs text-gray-500"><x-fas-layer-group class="mr-1" />Agrupados por categoría</p>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                                <tr>
                                    <th class="px-3 py-3 text-left">Título / Documento</th>
                                    <th class="px-3 py-3 text-center w-24">Tipo</th>
                                    <th class="px-3 py-3 text-center w-24">Orden</th>
                                    <th class="px-3 py-3 text-right w-24">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($informativos as $categoria => $items)
                                    <!-- Category Header -->
                                    <tr class="bg-red-50/50">
                                        <td colspan="3" class="px-4 py-2 font-bold text-red-800 text-sm uppercase tracking-wide border-l-4 border-red-700">
                                            {{ $categoria }}
                                        </td>
                                    </tr>
                                    
                                    @foreach($items as $item)
                                        <tr class="hover:bg-gray-50 group transition-colors item-row" data-id="{{ $item->id }}" data-orden="{{ $item->orden }}">
                                            <td class="px-4 py-3">
                                                <div class="flex items-start gap-3">
                                                    <div class="flex-shrink-0 mt-1">
                                                        @if($item->es_pdf)
                                                            <x-fas-file-pdf class="text-red-600 text-lg" />
                                                        @else
                                                            <x-fas-link class="text-blue-600 text-lg" />
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <span class="font-medium text-gray-800 text-sm block">{{ $item->titulo }}</span>
                                                        @if($item->url)
                                                            <a href="{{ $item->url }}" target="_blank" rel="noopener noreferrer" class="text-xs text-blue-500 hover:underline truncate max-w-[200px] inline-block">
                                                                {{ $item->url }}
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase {{ $item->es_pdf ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                                    {{ $item->es_pdf ? 'PDF' : 'Link' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    <button type="button" onclick="moveItem(this, 'up')" class="text-gray-400 hover:text-red-700 p-1">
                                                        <x-fas-chevron-up class="text-xs" />
                                                    </button>
                                                    <button type="button" onclick="moveItem(this, 'down')" class="text-gray-400 hover:text-red-700 p-1">
                                                        <x-fas-chevron-down class="text-xs" />
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-right">
                                                <div class="flex justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                                    <button type="button" onclick='editItem(@json($item))' 
                                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Editar" aria-label="Editar">
                                                        <x-fas-edit />
                                                    </button>
                                                    <form action="{{ route('admin.informativos.destroy', $item) }}" method="POST" 
                                                        class="inline-block" onsubmit="return confirm('¿Eliminar este documento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded transition-colors" title="Eliminar" aria-label="Eliminar">
                                                            <x-fas-trash />
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-0">
                                            <x-empty-state icon="fa-folder-open" title="No hay documentos registrados"
                                                description="Usa el botón Nuevo Documento para agregar el primero." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL AGREGAR / EDITAR -->
    <div id="modalForm" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 transition-opacity backdrop-blur-sm" onclick="closeModal()"></div>
        
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    
                    <!-- Header -->
                    <div class="bg-red-700 px-4 py-3 sm:px-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold leading-6 text-white" id="modal-title">Nuevo Documento</h3>
                            <button type="button" onclick="closeModal()" class="text-white hover:text-gray-200">
                                <x-fas-times />
                            </button>
                        </div>
                    </div>

                    <!-- Form -->
                    <form id="informativo-form" action="{{ route('admin.informativos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_method" id="form-method" value="POST">
                        
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <!-- Categoría -->
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900">Categoría *</label>
                                <div class="mt-1">
                                    <!-- Hidden real input -->
                                    <input type="hidden" name="categoria" id="real_categoria" required>
                                    
                                    <!-- Select Wrapper -->
                                    <div id="cat-select-wrapper">
                                        <select id="cat_select" onchange="handleCatChange(this)" class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6">
                                            <option value="">-- Seleccionar Categoría --</option>
                                            @foreach($categorias as $cat)
                                                <option value="{{ $cat }}">{{ $cat }}</option>
                                            @endforeach
                                            <option value="__NEW__" class="font-bold text-red-600">➕ Crear nueva categoría...</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Text Input Wrapper -->
                                    <div id="cat-input-wrapper" class="hidden flex gap-2">
                                        <input type="text" id="cat_text" placeholder="Nombre de la nueva categoría" 
                                            oninput="updateRealCat(this.value)"
                                            class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6">
                                        <button type="button" onclick="cancelNewCat()" class="px-3 py-2 bg-gray-100 text-gray-600 rounded-md border border-gray-300 hover:bg-gray-200" title="Cancelar / Volver a lista" aria-label="Cancelar / Volver a lista">
                                            <x-fas-undo />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Título -->
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900">Título *</label>
                                <div class="mt-1">
                                    <input type="text" name="titulo" required
                                        class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>

                            <!-- Tipo -->
                            <div>
                                <label class="block text-sm font-medium leading-6 text-gray-900 mb-2">Tipo de Recurso</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer border rounded-lg p-3 flex items-center justify-center gap-2 hover:bg-gray-50 has-[:checked]:bg-red-50 has-[:checked]:border-red-200 has-[:checked]:text-red-700">
                                        <input type="radio" name="tipo" value="0" checked onchange="toggleUploadType(0)" class="sr-only">
                                        <x-fas-file-pdf />
                                        <span class="text-sm font-medium">Archivo PDF</span>
                                    </label>
                                    <label class="cursor-pointer border rounded-lg p-3 flex items-center justify-center gap-2 hover:bg-gray-50 has-[:checked]:bg-blue-50 has-[:checked]:border-blue-200 has-[:checked]:text-blue-700">
                                        <input type="radio" name="tipo" value="1" onchange="toggleUploadType(1)" class="sr-only">
                                        <x-fas-link />
                                        <span class="text-sm font-medium">Enlace Externo</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Inputs Dinámicos -->
                            <div id="pdf-input-group" class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium leading-6 text-gray-900">Subir PDF</label>
                                    <div class="mt-1 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-4">
                                        <div class="text-center">
                                            <div class="mt-0 flex text-sm leading-6 text-gray-600">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-red-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-red-600 focus-within:ring-offset-2 hover:text-red-500">
                                                    <span>Subir un archivo</span>
                                                    <input id="file-upload" name="file" type="file" class="sr-only" accept=".pdf">
                                                </label>
                                                <p class="pl-1">o arrastrar y soltar</p>
                                            </div>
                                            <p class="text-xs leading-5 text-gray-600">PDF hasta 10MB</p>
                                        </div>
                                    </div>
                                    <p id="file-name-display" class="text-xs text-gray-500 mt-1"></p>
                                </div>
                            </div>

                            <div id="link-input-group" class="hidden">
                                <label class="block text-sm font-medium leading-6 text-gray-900">URL del Recurso</label>
                                <div class="mt-1">
                                    <input type="url" name="url" placeholder="https://..."
                                        class="block w-full rounded-md border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-red-600 sm:text-sm sm:leading-6">
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                            <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                                Guardar
                            </button>
                            <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File Upload Display Name
        document.getElementById('file-upload').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            document.getElementById('file-name-display').textContent = fileName ? 'Archivo seleccionado: ' + fileName : '';
        });

        function toggleUploadType(tipo) {
            const pdfGroup = document.getElementById('pdf-input-group');
            const linkGroup = document.getElementById('link-input-group');
            const radioPdf = document.querySelector('input[name="tipo"][value="0"]');
            const radioLink = document.querySelector('input[name="tipo"][value="1"]');

            if (tipo == 0) {
                pdfGroup.classList.remove('hidden');
                linkGroup.classList.add('hidden');
                // Clean URL input if switching to PDF
                document.querySelector('#link-input-group input').value = '';
                radioPdf.checked = true;
            } else {
                pdfGroup.classList.add('hidden');
                linkGroup.classList.remove('hidden');
                radioLink.checked = true;
            }
        }

        // Category Logic
        function handleCatChange(select) {
            const val = select.value;
            if (val === '__NEW__') {
                document.getElementById('cat-select-wrapper').classList.add('hidden');
                document.getElementById('cat-input-wrapper').classList.remove('hidden');
                document.getElementById('cat-input-wrapper').classList.add('flex');
                document.getElementById('cat_text').value = '';
                document.getElementById('cat_text').focus();
                document.getElementById('real_categoria').value = '';
            } else {
                document.getElementById('real_categoria').value = val;
            }
        }

        function cancelNewCat() {
            document.getElementById('cat-select-wrapper').classList.remove('hidden');
            document.getElementById('cat-input-wrapper').classList.add('hidden');
            document.getElementById('cat-input-wrapper').classList.remove('flex');
            
            // Reset to first option or empty
            const select = document.getElementById('cat_select');
            select.value = '';
            document.getElementById('real_categoria').value = '';
        }

        function updateRealCat(val) {
            document.getElementById('real_categoria').value = val;
        }

        function openModal() {
            document.getElementById('modalForm').classList.remove('hidden');
            resetForm();
        }

        function closeModal() {
            document.getElementById('modalForm').classList.add('hidden');
        }

        function editItem(item) {
            const form = document.getElementById('informativo-form');
            
            // Set Update Action
            form.action = `/admin/informativos/${item.id}`;
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('modal-title').innerText = 'Editar Documento';

            // Fill Data
            // Category handling
            const select = document.getElementById('cat_select');
            const options = Array.from(select.options).map(o => o.value);
            
            if (options.includes(item.categoria)) {
                // Existing category
                cancelNewCat(); // ensure we are in select mode
                select.value = item.categoria;
                document.getElementById('real_categoria').value = item.categoria;
            } else {
                // It's a custom category or one not in the list (unlikely based on data, but possible)
                // Force "New" mode
                handleCatChange({ value: '__NEW__' });
                document.getElementById('cat_text').value = item.categoria;
                document.getElementById('real_categoria').value = item.categoria;
            }

            form.querySelector('[name="titulo"]').value = item.titulo;

            // Set Type & Toggle Views
            toggleUploadType(item.tipo);
            if (item.tipo == 1) {
                form.querySelector('#link-input-group input').value = item.url;
            }

            // Show Modal
            document.getElementById('modalForm').classList.remove('hidden');
        }

        function resetForm() {
            const form = document.getElementById('informativo-form');
            form.reset();
            form.action = "{{ route('admin.informativos.store') }}";
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').innerText = 'Nuevo Documento';
            document.getElementById('file-name-display').textContent = '';
            
            // Reset category visual
            cancelNewCat();
            
            toggleUploadType(0);
        }

        // Reordering Logic
        function moveItem(btn, direction) {
            const row = btn.closest('tr');
            const tbody = row.parentNode;
            
            // Only move providing we stay within the same category group
            // We assume category group ends when we hit another "Category Header" (tr with colspan)
            
            if (direction === 'up') {
                const prev = row.previousElementSibling;
                if (prev && !prev.querySelector('th') && !prev.querySelector('td[colspan]')) {
                    tbody.insertBefore(row, prev);
                    saveOrder();
                }
            } else if (direction === 'down') {
                const next = row.nextElementSibling;
                if (next && !next.querySelector('th') && !next.querySelector('td[colspan]')) {
                    tbody.insertBefore(next, row);
                    saveOrder();
                }
            }
        }

        function saveOrder() {
            const items = [];
            let currentOrder = 1;
            
            // Iterate all rows to recalculate global order or per-category order?
            // Controller seems to update absolute 'orden' field. 
            // We'll just grab all item-rows and send their IDs and new Sequence
            
            document.querySelectorAll('.item-row').forEach((row, index) => {
                const id = row.getAttribute('data-id');
                if(id) {
                    items.push({
                        id: id,
                        orden: index + 1
                    });
                }
            });

            // Send AJAX
            fetch('{{ route("admin.informativos.reorder") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ items: items })
            }).then(response => {
                if (response.ok) {
                    window.showToast('Orden actualizado');
                } else {
                    window.showToast('No se pudo guardar el nuevo orden', 'error');
                }
            });
        }
    </script>
@endsection
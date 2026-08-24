@props([
    'mode' => 'direct',
    'name',
    'label' => null,
    'icon' => null,
    'accept' => 'image/*',
    'helpText' => null,
    'currentPath' => null,
    'removeCheckboxName' => null,
    'removeLabel' => 'Eliminar actual',
    'withLivePreview' => false,
    'layout' => 'stacked',
    'previewSize' => 'w-24 h-24',
    'previewFit' => 'object-cover',
    'placeholderIcon' => 'fa-image',
    'fileButtonClass' => 'file:bg-unmsm-azul file:text-white hover:file:bg-unmsm-azul/90',
    'stackOnNarrow' => false,
    'urlValue' => null,
    'uploadType' => null,
    'currentFileUrl' => null,
    'currentFileLabel' => 'Ver archivo actual',
    'currentPreviewType' => 'pdf',
    'buttonLabel' => null,
])

@php
    $isPdf = str_contains($accept, 'pdf');
    $buttonLabel = $buttonLabel ?? ($isPdf ? 'Subir PDF' : 'Subir Imagen');
    $uploadType = $uploadType ?? $name;
    $fileInputId = $name . '_file';
    $urlInputId = $name . '_url';
    $statusId = $name . '_status';
    $previewId = $name . '_preview';
    $placeholderId = $name . '_placeholder';
    $hoverLabelId = $name . '_hover_label';
    $currentImageUrl = $currentPath ? asset('storage/' . $currentPath) : null;
@endphp

@once
    @push('scripts')
        <script>
            function adminFileUploadPreview(input, previewId, placeholderId, labelId) {
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                const label = labelId ? document.getElementById(labelId) : null;
                if (!input.files || !input.files[0]) return;
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (preview) {
                        preview.src = e.target.result;
                        preview.classList.remove('hidden');
                    }
                    if (placeholder) placeholder.classList.add('hidden');
                    if (label) label.textContent = 'Nueva';
                };
                reader.readAsDataURL(input.files[0]);
            }

            function adminFileUploadSetupAjax(fileInputId, urlInputId, statusId, type) {
                var fileInput = document.getElementById(fileInputId);
                if (!fileInput) return;

                fileInput.addEventListener('change', function() {
                    if (this.files.length === 0) return;

                    var file = this.files[0];
                    var statusEl = document.getElementById(statusId);
                    var urlInput = document.getElementById(urlInputId);

                    statusEl.classList.remove('hidden', 'text-green-600', 'text-red-600');
                    statusEl.classList.add('text-blue-600');
                    statusEl.innerHTML = '<x-fas-spinner class="animate-spin mr-1" /> Subiendo ' + file.name + '...';

                    var formData = new FormData();
                    formData.append('file', file);
                    formData.append('type', type);
                    var nombreField = document.getElementById('nombre');
                    formData.append('program_name', (nombreField ? nombreField.value : '') || 'programa');
                    formData.append('_token', '{{ csrf_token() }}');

                    fetch('{{ route("admin.documents.uploadAjax") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(data => {
                                throw new Error(data.error || data.message || 'Error de validación');
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            urlInput.value = data.url;
                            statusEl.classList.remove('text-blue-600');
                            statusEl.classList.add('text-green-600');
                            statusEl.innerHTML = '<x-fas-check-circle class="mr-1" /> Subido: ' + data.filename;
                        } else {
                            statusEl.classList.remove('text-blue-600');
                            statusEl.classList.add('text-red-600');
                            statusEl.innerHTML = '<x-fas-exclamation-circle class="mr-1" /> Error: ' + (data.error || 'Error al subir');
                        }
                    })
                    .catch(error => {
                        statusEl.classList.remove('text-blue-600');
                        statusEl.classList.add('text-red-600');
                        statusEl.innerHTML = '<x-fas-exclamation-circle class="mr-1" /> ' + error.message;
                        console.error('Upload error:', error);
                    });

                    this.value = '';
                });
            }
        </script>
    @endpush
@endonce

@if($mode === 'ajax')
    <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-azul hover:shadow-sm transition-all">
        @if($label)
            <label class="form-label block mb-3">
                @if($icon)<i class="{{ $icon }} text-brand-azul mr-1"></i>@endif {{ $label }}
            </label>
        @endif
        @if($currentFileUrl)
            @if($currentPreviewType === 'image')
                <div class="mb-3 p-3 bg-blue-50 rounded-lg border border-blue-200 flex items-center gap-4">
                    <img src="{{ $currentFileUrl }}" alt="{{ $currentFileLabel }}" class="w-20 h-14 object-cover rounded-lg shadow">
                    <span class="text-xs text-gray-600">{{ $currentFileLabel }}</span>
                </div>
            @else
                <div class="mb-3 p-3 bg-green-50 rounded-lg border border-green-200 flex items-center gap-4">
                    <x-fas-file-pdf class="text-red-600 text-2xl" />
                    <a href="{{ $currentFileUrl }}" target="_blank" rel="noopener noreferrer" class="text-sm text-blue-600 hover:underline font-medium">{{ $currentFileLabel }}</a>
                </div>
            @endif
        @endif
        <div class="flex gap-3 items-end">
            <div class="flex-1">
                <label class="text-xs text-gray-500 mb-1 block">URL{{ $label ? ' del ' . $label : '' }}</label>
                <input type="url" name="{{ $urlInputId }}" id="{{ $urlInputId }}" value="{{ $urlValue }}"
                    class="block w-full py-2.5 px-4 border border-gray-300 rounded-lg focus:border-brand-azul transition-colors"
                    placeholder="https://ejemplo.com/archivo">
            </div>
            <div class="flex gap-2">
                <input type="file" id="{{ $fileInputId }}" accept="{{ $accept }}" class="hidden">
                <button type="button" onclick="document.getElementById('{{ $fileInputId }}').click()"
                    class="px-4 py-2.5 bg-brand-azul text-white rounded-lg hover:bg-unmsm-azul transition-all flex items-center gap-2">
                    <x-fas-upload /> {{ $buttonLabel }}
                </button>
            </div>
        </div>
        <div id="{{ $statusId }}" class="mt-2 text-xs hidden"></div>
        @if($helpText)
            <p class="mt-2 text-xs text-gray-500">{{ $helpText }}</p>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                adminFileUploadSetupAjax('{{ $fileInputId }}', '{{ $urlInputId }}', '{{ $statusId }}', '{{ $uploadType }}');
            });
        </script>
    @endpush
@else
    <div>
        @if($label)
            <label class="block text-sm font-medium text-gray-700 mb-2">
                @if($icon)<i class="{{ $icon }} mr-1"></i>@endif {{ $label }}
            </label>
        @endif

        @if($layout === 'inline')
            <div @class(['flex gap-4', 'flex-col items-center text-center' => $stackOnNarrow, 'items-start' => !$stackOnNarrow])>
                <div class="flex-shrink-0">
                    <div class="relative group">
                        @if($currentImageUrl)
                            <img id="{{ $previewId }}" src="{{ $currentImageUrl }}" alt="{{ $label }} actual"
                                class="{{ $previewSize }} object-contain border border-gray-200 rounded-lg bg-gray-50 p-2">
                            <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                <span id="{{ $hoverLabelId }}" class="text-white text-xs">Actual</span>
                            </div>
                        @else
                            <div id="{{ $placeholderId }}" class="{{ $previewSize }} border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                <x-dynamic-component :component="'fas-' . str_replace('fa-', '', $placeholderIcon)" class="text-2xl text-gray-400" />
                            </div>
                            <img id="{{ $previewId }}" src="" alt="Preview {{ $label }}"
                                class="{{ $previewSize }} object-contain border border-gray-200 rounded-lg bg-gray-50 p-2 hidden">
                        @endif
                    </div>
                </div>
                <div class="flex-1">
                    <input type="file" name="{{ $name }}" id="{{ $name }}_input" accept="{{ $accept }}"
                        @if($withLivePreview) onchange="adminFileUploadPreview(this, '{{ $previewId }}', '{{ $placeholderId }}', '{{ $hoverLabelId }}')" @endif
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold {{ $fileButtonClass }} cursor-pointer">
                    @if($helpText)
                        <p class="text-xs text-gray-500 mt-1">{{ $helpText }}</p>
                    @endif
                    @error($name)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @if($currentPath && $removeCheckboxName)
                        <label class="inline-flex items-center mt-2 text-sm text-gray-600">
                            <input type="checkbox" name="{{ $removeCheckboxName }}" value="1"
                                class="rounded border-gray-300 text-unmsm-azul focus:ring-unmsm-azul mr-2">
                            {{ $removeLabel }}
                        </label>
                    @endif
                </div>
            </div>
        @else
            @if($currentImageUrl)
                <div class="mb-3 flex items-center gap-4">
                    <img src="{{ $currentImageUrl }}" alt="{{ $label }} actual" class="{{ $previewSize }} {{ $previewFit }} rounded-lg border">
                    @if($removeCheckboxName)
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="{{ $removeCheckboxName }}" value="1"> {{ $removeLabel }}
                        </label>
                    @endif
                </div>
            @endif
            <input type="file" name="{{ $name }}" accept="{{ $accept }}"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error($name) border-red-500 @enderror">
            @error($name)
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
            @if($helpText)
                <p class="mt-2 text-xs text-gray-500">{{ $helpText }}</p>
            @endif
        @endif
    </div>
@endif

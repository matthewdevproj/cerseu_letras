@extends('admin.layout.app')

@section('title', 'Configuración del Sitio')

@section('content')
    <div class="space-y-6">
        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Configuración del Sitio</h1>
            <p class="text-gray-600 mt-1">Personaliza la información de contacto y redes sociales de tu sitio web</p>
        </div>

        {{-- Mensajes de éxito --}}
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Información General --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-info-circle text-unmsm-guinda"></i>
                    Información General
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Sitio</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('site_name') border-red-500 @enderror">
                        @error('site_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <input type="text" name="site_description"
                            value="{{ old('site_description', $settings->site_description) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                    </div>
                </div>

                {{-- Logo y Favicon con subida de archivo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-image text-unmsm-guinda mr-1"></i> Logo del Sitio
                        </label>
                        <div class="flex items-start gap-4">
                            {{-- Preview --}}
                            <div class="flex-shrink-0">
                                <div id="logo_preview_container" class="relative group">
                                    @if($settings->logo_path)
                                        <img id="logo_preview" src="{{ asset('storage/' . $settings->logo_path) }}"
                                            alt="Logo actual"
                                            class="w-24 h-24 object-contain border border-gray-200 rounded-lg bg-gray-50 p-2">
                                        <div
                                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                            <span id="logo_label" class="text-white text-xs">Actual</span>
                                        </div>
                                    @else
                                        <div id="logo_placeholder"
                                            class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                            <i class="fas fa-image text-2xl text-gray-400"></i>
                                        </div>
                                        <img id="logo_preview" src="" alt="Preview logo"
                                            class="w-24 h-24 object-contain border border-gray-200 rounded-lg bg-gray-50 p-2 hidden">
                                    @endif
                                </div>
                            </div>
                            {{-- Input --}}
                            <div class="flex-1">
                                <input type="file" name="logo" id="logo_input" accept="image/*"
                                    onchange="previewImage(this, 'logo_preview', 'logo_placeholder', 'logo_label')"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-unmsm-guinda file:text-white hover:file:bg-unmsm-guinda/90 cursor-pointer">
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, SVG. Máx 2MB.</p>
                                @error('logo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @if($settings->logo_path)
                                    <label class="inline-flex items-center mt-2 text-sm text-gray-600">
                                        <input type="checkbox" name="remove_logo" value="1"
                                            class="rounded border-gray-300 text-unmsm-guinda focus:ring-unmsm-guinda mr-2">
                                        Eliminar logo actual
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Favicon --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-star text-unmsm-dorado mr-1"></i> Favicon
                        </label>
                        <div class="flex items-start gap-4">
                            {{-- Preview --}}
                            <div class="flex-shrink-0">
                                <div id="favicon_preview_container" class="relative group">
                                    @if($settings->favicon_path)
                                        <img id="favicon_preview" src="{{ asset('storage/' . $settings->favicon_path) }}"
                                            alt="Favicon actual"
                                            class="w-16 h-16 object-contain border border-gray-200 rounded-lg bg-gray-50 p-2">
                                        <div
                                            class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                            <span id="favicon_label" class="text-white text-xs">Actual</span>
                                        </div>
                                    @else
                                        <div id="favicon_placeholder"
                                            class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50">
                                            <i class="fas fa-star text-xl text-gray-400"></i>
                                        </div>
                                        <img id="favicon_preview" src="" alt="Preview favicon"
                                            class="w-16 h-16 object-contain border border-gray-200 rounded-lg bg-gray-50 p-2 hidden">
                                    @endif
                                </div>
                            </div>
                            {{-- Input --}}
                            <div class="flex-1">
                                <input type="file" name="favicon" id="favicon_input" accept="image/*,.ico"
                                    onchange="previewImage(this, 'favicon_preview', 'favicon_placeholder', 'favicon_label')"
                                    class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-unmsm-dorado file:text-unmsm-guinda hover:file:bg-unmsm-dorado/90 cursor-pointer">
                                <p class="text-xs text-gray-500 mt-1">ICO, PNG. 32x32 o 64x64 px.</p>
                                @error('favicon')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @if($settings->favicon_path)
                                    <label class="inline-flex items-center mt-2 text-sm text-gray-600">
                                        <input type="checkbox" name="remove_favicon" value="1"
                                            class="rounded border-gray-300 text-unmsm-guinda focus:ring-unmsm-guinda mr-2">
                                        Eliminar favicon actual
                                    </label>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                function previewImage(input, previewId, placeholderId, labelId) {
                    const preview = document.getElementById(previewId);
                    const placeholder = document.getElementById(placeholderId);
                    const label = document.getElementById(labelId);

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function (e) {
                            preview.src = e.target.result;
                            preview.classList.remove('hidden');

                            if (placeholder) {
                                placeholder.classList.add('hidden');
                            }

                            if (label) {
                                label.textContent = 'Nuevo';
                            }
                        }

                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>

            {{-- Información de Contacto --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-address-card text-unmsm-guinda"></i>
                    Información de Contacto
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Principal</label>
                        <input type="email" name="email" value="{{ old('email', $settings->email) }}"
                            placeholder="posgrado.letras@unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email de Admisión</label>
                        <input type="email" name="email_admision"
                            value="{{ old('email_admision', $settings->email_admision) }}"
                            placeholder="admisionposgrado.letras@unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('email_admision') border-red-500 @enderror">
                        @error('email_admision')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $settings->telefono) }}"
                            placeholder="982 085 037"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $settings->direccion) }}"
                            placeholder="Ciudad Universitaria, Av. Venezuela s/n, Lima"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario de Atención</label>
                    <input type="text" name="horario_atencion"
                        value="{{ old('horario_atencion', $settings->horario_atencion) }}"
                        placeholder="Lunes a Viernes de 8:00 am a 5:00 pm"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda">
                </div>
            </div>

            {{-- Redes Sociales --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-share-alt text-unmsm-guinda"></i>
                    Redes Sociales
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook
                        </label>
                        <input type="url" name="facebook" value="{{ old('facebook', $settings->facebook) }}"
                            placeholder="https://facebook.com/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('facebook') border-red-500 @enderror">
                        @error('facebook')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-instagram text-pink-600 mr-1"></i> Instagram
                        </label>
                        <input type="url" name="instagram" value="{{ old('instagram', $settings->instagram) }}"
                            placeholder="https://instagram.com/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('instagram') border-red-500 @enderror">
                        @error('instagram')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-linkedin text-blue-700 mr-1"></i> LinkedIn
                        </label>
                        <input type="url" name="linkedin" value="{{ old('linkedin', $settings->linkedin) }}"
                            placeholder="https://linkedin.com/company/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('linkedin') border-red-500 @enderror">
                        @error('linkedin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-twitter text-sky-500 mr-1"></i> X (Twitter)
                        </label>
                        <input type="url" name="twitter" value="{{ old('twitter', $settings->twitter) }}"
                            placeholder="https://x.com/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('twitter') border-red-500 @enderror">
                        @error('twitter')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-youtube text-red-600 mr-1"></i> YouTube
                        </label>
                        <input type="url" name="youtube" value="{{ old('youtube', $settings->youtube) }}"
                            placeholder="https://youtube.com/@..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('youtube') border-red-500 @enderror">
                        @error('youtube')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fab fa-tiktok mr-1"></i> TikTok
                        </label>
                        <input type="url" name="tiktok" value="{{ old('tiktok', $settings->tiktok) }}"
                            placeholder="https://tiktok.com/@..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('tiktok') border-red-500 @enderror">
                        @error('tiktok')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- URLs Externas --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-external-link-alt text-unmsm-guinda"></i>
                    Enlaces Externos
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Web de la Facultad</label>
                        <input type="url" name="web_facultad" value="{{ old('web_facultad', $settings->web_facultad) }}"
                            placeholder="https://letras.unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('web_facultad') border-red-500 @enderror">
                        @error('web_facultad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Directorio de la Facultad</label>
                        <input type="url" name="directorio_facultad"
                            value="{{ old('directorio_facultad', $settings->directorio_facultad) }}"
                            placeholder="https://letras.unmsm.edu.pe/directorio/"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-guinda focus:border-unmsm-guinda @error('directorio_facultad') border-red-500 @enderror">
                        @error('directorio_facultad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Botón Guardar --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="px-8 py-3 bg-unmsm-guinda text-white rounded-lg font-semibold hover:bg-unmsm-guinda/90 transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Guardar Configuración
                </button>
            </div>
        </form>
    </div>
@endsection
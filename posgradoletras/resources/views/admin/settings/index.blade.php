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


        <form action="{{ route('admin.settings.update') }}" method="POST" data-avisar-sin-guardar enctype="multipart/form-data" class="space-y-6"
            x-data="{ submitting: false }" @submit="submitting = true">
            @csrf
            @method('PUT')

            {{-- Información General --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-fas-info-circle class="text-unmsm-azul" />
                    Información General
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Sitio</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings->site_name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('site_name') border-red-500 @enderror">
                        @error('site_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <input type="text" name="site_description"
                            value="{{ old('site_description', $settings->site_description) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                </div>

                {{-- Logo y Favicon con subida de archivo --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-100">
                    <x-admin-file-upload mode="direct" name="logo" label="Logo del Sitio" icon="fas fa-image text-unmsm-azul"
                        accept="image/*" layout="inline" with-live-preview
                        :current-path="$settings->logo_path" remove-checkbox-name="remove_logo"
                        remove-label="Eliminar logo actual" help-text="PNG, JPG, SVG. Máx 2MB." />

                    <x-admin-file-upload mode="direct" name="favicon" label="Favicon" icon="fas fa-star text-unmsm-dorado"
                        accept="image/*,.ico" layout="inline" with-live-preview preview-size="w-16 h-16" placeholder-icon="fa-star"
                        file-button-class="file:bg-unmsm-dorado file:text-unmsm-azul hover:file:bg-unmsm-dorado/90"
                        :current-path="$settings->favicon_path" remove-checkbox-name="remove_favicon"
                        remove-label="Eliminar favicon actual" help-text="ICO, PNG. 32x32 o 64x64 px." />
                </div>
            </div>

            {{-- Información de Contacto --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-fas-address-card class="text-unmsm-azul" />
                    Información de Contacto
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Principal</label>
                        <input type="email" name="email" value="{{ old('email', $settings->email) }}"
                            placeholder="cerseu.letras@unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email de Admisión</label>
                        <input type="email" name="email_admision"
                            value="{{ old('email_admision', $settings->email_admision) }}"
                            placeholder="cerseu.letras@unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('email_admision') border-red-500 @enderror">
                        @error('email_admision')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email de Trámites</label>
                        <input type="email" name="email_tramites"
                            value="{{ old('email_tramites', $settings->email_tramites) }}"
                            placeholder="cerseu.letras@unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('email_tramites') border-red-500 @enderror">
                        <p class="text-xs text-gray-400 mt-1">Grados, títulos y certificados (página de Trámites).</p>
                        @error('email_tramites')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-500 -mt-2">
                    <x-fas-circle-info class="mr-1" aria-hidden="true" />
                    El enlace de WhatsApp del sitio se genera a partir del teléfono, no hace falta configurarlo aparte.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $settings->telefono) }}"
                            placeholder="982 085 037"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                        <input type="text" name="direccion" value="{{ old('direccion', $settings->direccion) }}"
                            placeholder="Ciudad Universitaria, Av. Venezuela s/n, Lima"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Horario de Atención</label>
                    <input type="text" name="horario_atencion"
                        value="{{ old('horario_atencion', $settings->horario_atencion) }}"
                        placeholder="Lunes a Viernes de 8:00 am a 5:00 pm"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                </div>
            </div>

            {{-- Encabezado de la portada --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-fas-house class="text-unmsm-azul" />
                    Encabezado de la portada
                </h2>
                <p class="text-sm text-gray-500 -mt-2">
                    Lo primero que se ve al entrar al sitio. Si dejas un campo vacío se
                    usa el texto que traía la página, así que se puede ir cambiando de
                    uno en uno sin dejar huecos.
                </p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Antetítulo</label>
                    <input type="text" name="home_hero_kicker"
                        value="{{ old('home_hero_kicker', $settings->home_hero_kicker) }}"
                        placeholder="Universidad Nacional Mayor de San Marcos · Decana de América"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Titular</label>
                    <input type="text" name="home_hero_titulo"
                        value="{{ old('home_hero_titulo', $settings->home_hero_titulo) }}"
                        placeholder="CERSEU de la Facultad de Letras y Ciencias Humanas"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bajada</label>
                    <textarea name="home_hero_texto" rows="3"
                        placeholder="Formamos investigadores y profesionales comprometidos con el desarrollo cultural y social del país…"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">{{ old('home_hero_texto', $settings->home_hero_texto) }}</textarea>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón principal — texto</label>
                        <input type="text" name="home_hero_cta1_texto"
                            value="{{ old('home_hero_cta1_texto', $settings->home_hero_cta1_texto) }}"
                            placeholder="Ver talleres"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón principal — enlace</label>
                        <input type="text" name="home_hero_cta1_url"
                            value="{{ old('home_hero_cta1_url', $settings->home_hero_cta1_url) }}"
                            placeholder="/diplomados"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón secundario — texto</label>
                        <input type="text" name="home_hero_cta2_texto"
                            value="{{ old('home_hero_cta2_texto', $settings->home_hero_cta2_texto) }}"
                            placeholder="Admisión de talleres"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Botón secundario — enlace</label>
                        <input type="text" name="home_hero_cta2_url"
                            value="{{ old('home_hero_cta2_url', $settings->home_hero_cta2_url) }}"
                            placeholder="/diplomados/admision"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Docentes RENACYT (contador del hero)</label>
                    <input type="number" name="home_stat_docentes" min="0" max="9999"
                        value="{{ old('home_stat_docentes', $settings->home_stat_docentes) }}"
                        placeholder="20"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                    <p class="text-xs text-gray-500 mt-1">
                        Se muestra con un «+» detrás. Las demás cifras del contador
                        (cursos, talleres y años de historia) se
                        calculan solas y no hay que tocarlas.
                    </p>
                </div>
            </div>

            {{-- Un encabezado por módulo: talleres y cursos tienen la misma
                 estructura, así que el bloque se repite en vez de duplicarse. --}}
            @foreach (\App\Models\TipoOferta::cases() as $tipoHero)
            @php $ph = $tipoHero->prefijoHero(); @endphp
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-fas-scroll class="text-unmsm-azul" />
                    Encabezado de {{ $tipoHero->plural() }}
                </h2>
                <p class="text-sm text-gray-500 -mt-2">Título, texto institucional y claim que se muestran en el hero de la página /{{ $tipoHero->slug() }}.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Título principal</label>
                    <input type="text" name="{{ $ph }}_hero_titulo"
                        value="{{ old($ph . '_hero_titulo', $settings->{$ph . '_hero_titulo'}) }}"
                        placeholder="{{ $tipoHero->plural() }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Texto institucional</label>
                    <textarea name="{{ $ph }}_hero_texto" rows="3"
                        placeholder="Especializa tus conocimientos con {{ mb_strtolower($tipoHero->plural()) }} diseñados para responder a los desafíos contemporáneos..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">{{ old($ph . '_hero_texto', $settings->{$ph . '_hero_texto'}) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Claim institucional</label>
                    <input type="text" name="{{ $ph }}_hero_claim"
                        value="{{ old($ph . '_hero_claim', $settings->{$ph . '_hero_claim'}) }}"
                        placeholder="El conocimiento evoluciona. Tu formación también."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul">
                </div>
                <x-admin-file-upload mode="direct" :name="$ph . '_hero_imagen'" label="Imagen de fondo del hero"
                    accept="image/*" layout="stacked" preview-size="w-32 h-20"
                    :current-path="$settings->{$ph . '_hero_imagen'}" :remove-checkbox-name="'remove_' . $ph . '_hero_imagen'"
                    remove-label="Eliminar imagen actual" />
            </div>
            @endforeach

            {{-- Redes Sociales --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-fas-share-alt class="text-unmsm-azul" />
                    Redes Sociales
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-fab-facebook class="text-blue-600 mr-1" /> Facebook
                        </label>
                        <input type="url" name="facebook" value="{{ old('facebook', $settings->facebook) }}"
                            placeholder="https://facebook.com/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('facebook') border-red-500 @enderror">
                        @error('facebook')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-fab-instagram class="text-pink-600 mr-1" /> Instagram
                        </label>
                        <input type="url" name="instagram" value="{{ old('instagram', $settings->instagram) }}"
                            placeholder="https://instagram.com/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('instagram') border-red-500 @enderror">
                        @error('instagram')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-fab-linkedin class="text-blue-700 mr-1" /> LinkedIn
                        </label>
                        <input type="url" name="linkedin" value="{{ old('linkedin', $settings->linkedin) }}"
                            placeholder="https://linkedin.com/company/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('linkedin') border-red-500 @enderror">
                        @error('linkedin')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-fab-twitter class="text-sky-500 mr-1" /> X (Twitter)
                        </label>
                        <input type="url" name="twitter" value="{{ old('twitter', $settings->twitter) }}"
                            placeholder="https://x.com/..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('twitter') border-red-500 @enderror">
                        @error('twitter')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-fab-youtube class="text-red-600 mr-1" /> YouTube
                        </label>
                        <input type="url" name="youtube" value="{{ old('youtube', $settings->youtube) }}"
                            placeholder="https://youtube.com/@..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('youtube') border-red-500 @enderror">
                        @error('youtube')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <x-fab-tiktok class="mr-1" /> TikTok
                        </label>
                        <input type="url" name="tiktok" value="{{ old('tiktok', $settings->tiktok) }}"
                            placeholder="https://tiktok.com/@..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('tiktok') border-red-500 @enderror">
                        @error('tiktok')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- URLs Externas --}}
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <x-fas-external-link-alt class="text-unmsm-azul" />
                    Enlaces Externos
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Web de la Facultad</label>
                        <input type="url" name="web_facultad" value="{{ old('web_facultad', $settings->web_facultad) }}"
                            placeholder="https://letras.unmsm.edu.pe"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('web_facultad') border-red-500 @enderror">
                        @error('web_facultad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Directorio de la Facultad</label>
                        <input type="url" name="directorio_facultad"
                            value="{{ old('directorio_facultad', $settings->directorio_facultad) }}"
                            placeholder="https://letras.unmsm.edu.pe/directorio/"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-unmsm-azul focus:border-unmsm-azul @error('directorio_facultad') border-red-500 @enderror">
                        @error('directorio_facultad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Botón Guardar --}}
            <div class="flex justify-end">
                <button type="submit" :disabled="submitting"
                    class="px-8 py-3 bg-unmsm-azul text-white rounded-lg font-semibold hover:bg-unmsm-azul/90 transition-colors flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <x-fas-spinner class="animate-spin" x-show="submitting" x-cloak />
                    <x-fas-save x-show="!submitting" />
                    <span x-text="submitting ? 'Guardando...' : 'Guardar Configuración'"></span>
                </button>
            </div>
        </form>
    </div>
@endsection
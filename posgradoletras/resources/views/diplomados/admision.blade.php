@extends('layouts.public')

@section('title', ($admisionSettings->hero_titulo ?? 'Admisión Diplomados') . ' - Posgrado Letras UNMSM')

@push('styles')
    <style>
        .admision-section {
            animation: admisionFadeIn 0.35s ease;
        }

        .admision-section.is-hidden {
            display: none;
        }

        @keyframes admisionFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .admision-nav-link {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .admision-nav-link.is-active {
            background: rgba(118, 30, 35, 0.08);
            border-left-color: var(--brand);
            color: var(--brand);
            font-weight: 700;
        }

        .step-card {
            border-left: 4px solid var(--brand);
            transition: all 0.3s ease;
        }

        .step-card:hover {
            border-left-color: #d4af37;
            transform: translateX(4px);
        }

        .step-number {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .cronograma-table {
            width: 100%;
            border-collapse: collapse;
        }

        .cronograma-table th,
        .cronograma-table td {
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        .cronograma-table th {
            background: #f9fafb;
            font-weight: 600;
            color: #374151;
            font-size: 0.75rem;
            text-transform: uppercase;
        }

        .estado-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.2rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .estado-activo { background: #dcfce7; color: #166534; }
        .estado-proximamente { background: #fef3c7; color: #92400e; }
        .estado-cerrado { background: #f3f4f6; color: #4b5563; }

        .mobile-side-nav::-webkit-scrollbar {
            display: none;
        }
    </style>
@endpush

@section('content')
    @php
        $s = $admisionSettings;
        $pasos = $s->pasos ?? [];
        $cronogramaItems = $s->cronogramaItems ?? collect();
        $requisitos = $s->requisitos_lista ?? [];
        $pagoInstrucciones = $s->pago_instrucciones ?? [];

        $estadoClases = [
            'Activo' => 'estado-activo',
            'Próximamente' => 'estado-proximamente',
            'Cerrado' => 'estado-cerrado',
        ];

        $navItems = [
            ['id' => 'guia', 'label' => 'Guía para el proceso de admisión'],
            ['id' => 'cronograma', 'label' => 'Cronograma de admisión'],
            ['id' => 'requisitos', 'label' => 'Requisitos para postular'],
            ['id' => 'pago', 'label' => 'Pago por derecho de inscripción'],
            ['id' => 'resultados', 'label' => 'Publicación de resultados'],
            ['id' => 'contacto', 'label' => 'Para más información'],
        ];

        $heroImagen = $s->hero_imagen
            ? (str_starts_with($s->hero_imagen, 'http') ? $s->hero_imagen : asset('storage/' . $s->hero_imagen))
            : asset('images/campus-fachada.jpg');
    @endphp

    <x-hero-section :title="$s->hero_titulo ?? 'Admisión Diplomados'"
        label="Guía para el proceso de admisión"
        :subtitle="$s->hero_subtitulo ?? 'Sección Diplomados · Unidad de Posgrado'"
        :image="$heroImagen" />

    {{-- Navegación móvil: barra horizontal pegajosa --}}
    <div class="lg:hidden sticky top-16 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="mobile-side-nav flex gap-1 overflow-x-auto px-4 py-2">
            @foreach($navItems as $item)
                <a href="#{{ $item['id'] }}" data-nav-target="{{ $item['id'] }}"
                    class="admision-nav-link whitespace-nowrap px-3 py-2 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 {{ $loop->first ? 'is-active' : '' }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </div>

    <section class="container mx-auto px-6 py-12">
        <div class="grid lg:grid-cols-4 gap-8">

            {{-- Sidebar (desktop) --}}
            <aside class="hidden lg:block lg:col-span-1">
                <nav class="sticky top-24 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    @foreach($navItems as $item)
                        <a href="#{{ $item['id'] }}" data-nav-target="{{ $item['id'] }}"
                            class="admision-nav-link block px-5 py-4 text-sm font-medium text-gray-700 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 {{ $loop->first ? 'is-active' : '' }}">
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </aside>

            {{-- Contenido --}}
            <div class="lg:col-span-3 space-y-10">

                {{-- Sección 1: Guía para el proceso de admisión --}}
                <section id="guia" class="admision-section bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-bold text-unmsm-guinda mb-6 font-serif">Guía para el proceso de admisión</h2>
                    <div class="space-y-5">
                        @forelse($pasos as $paso)
                            <div class="step-card bg-gray-50 rounded-r-lg p-4 flex gap-4">
                                <div class="step-number">{{ $paso['numero'] ?? '' }}</div>
                                <div>
                                    <h3 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                                        @if(!empty($paso['icono']))
                                            <x-dynamic-component :component="'fas-' . str_replace('fa-', '', $paso['icono'])" class="text-unmsm-guinda text-sm" />
                                        @endif
                                        {{ $paso['titulo'] ?? '' }}
                                    </h3>
                                    <p class="text-sm text-gray-600">{{ $paso['descripcion'] ?? '' }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 italic">La guía de admisión se encuentra en actualización.</p>
                        @endforelse
                    </div>
                </section>

                {{-- Sección 2: Cronograma de admisión --}}
                <section id="cronograma" class="admision-section is-hidden bg-white border border-gray-200 rounded-xl overflow-hidden shadow-md">
                    <div class="bg-unmsm-guinda text-white p-4">
                        <h2 class="font-bold text-lg font-serif">Cronograma de Admisión</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="cronograma-table">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Convocatoria</th>
                                    <th>Fecha de inscripción</th>
                                    <th>Fecha límite</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cronogramaItems as $item)
                                    <tr>
                                        <td class="font-medium text-gray-800">{{ $item->programa }}</td>
                                        <td class="text-gray-600">{{ $item->convocatoria }}</td>
                                        <td class="text-gray-600">{{ $item->fecha_inscripcion }}</td>
                                        <td class="text-unmsm-guinda font-semibold">{{ $item->fecha_limite }}</td>
                                        <td>
                                            <span class="estado-badge {{ $estadoClases[$item->estado] ?? 'estado-activo' }}">
                                                {{ $item->estado }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-gray-500 italic py-6">El cronograma se encuentra en actualización.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>

                {{-- Sección 3: Requisitos para postular --}}
                <section id="requisitos" class="admision-section is-hidden bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-bold text-unmsm-guinda mb-4 font-serif">Requisitos para postular</h2>
                    @if($s->requisitos_email)
                        <p class="text-gray-700 text-sm mb-4">
                            El postulante deberá enviar su expediente en archivo digital (formato PDF) al correo
                            <a href="mailto:{{ $s->requisitos_email }}" class="text-unmsm-guinda font-semibold hover:underline">{{ $s->requisitos_email }}</a>
                            con los siguientes documentos adjuntos:
                        </p>
                    @endif
                    <ul class="space-y-2 text-sm text-gray-700">
                        @foreach($requisitos as $requisito)
                            <li class="relative pl-6">
                                <span class="absolute left-0 text-unmsm-guinda font-bold">✓</span>{{ $requisito }}
                            </li>
                        @endforeach
                    </ul>
                    @if($s->requisitos_observaciones)
                        <div class="mt-4 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                            {{ $s->requisitos_observaciones }}
                        </div>
                    @endif
                    @if($s->requisitos_notas)
                        <p class="mt-4 text-xs text-gray-500">{{ $s->requisitos_notas }}</p>
                    @endif
                </section>

                {{-- Sección 4: Pago por derecho de inscripción --}}
                <section id="pago" class="admision-section is-hidden bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-bold text-unmsm-guinda mb-4 font-serif">Pago por derecho de inscripción</h2>
                    @if($s->pago_descripcion)
                        <div class="bg-unmsm-guinda/5 border-l-4 border-unmsm-dorado p-4 mb-6">
                            <p class="text-gray-700 text-sm">{{ $s->pago_descripcion }}</p>
                        </div>
                    @endif
                    @if($s->pago_costo)
                        <p class="text-2xl font-bold text-unmsm-guinda mb-6">{{ $s->pago_costo }}</p>
                    @endif

                    <div class="space-y-6">
                        @foreach($pagoInstrucciones as $index => $instruccion)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start gap-3 mb-3">
                                    <span class="flex-shrink-0 w-8 h-8 bg-unmsm-guinda text-white rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                                    <div class="flex-1">
                                        <h4 class="font-bold text-gray-800 mb-1">{{ $instruccion['titulo'] ?? '' }}</h4>
                                        @if(!empty($instruccion['descripcion']))
                                            <p class="text-sm text-gray-600">{{ $instruccion['descripcion'] }}</p>
                                        @endif
                                    </div>
                                </div>
                                @if(!empty($instruccion['video_url']))
                                    <div class="w-full aspect-video rounded-lg overflow-hidden shadow-md">
                                        <iframe class="w-full h-full" src="{{ $instruccion['video_url'] }}"
                                            title="{{ $instruccion['titulo'] ?? 'Video instructivo' }}" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                            allowfullscreen loading="lazy">
                                        </iframe>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($s->pago_link_sanmarket)
                        <div class="text-center mt-6">
                            <a href="{{ $s->pago_link_sanmarket }}" target="_blank" rel="noopener noreferrer" 
                                class="inline-flex items-center gap-2 px-6 py-3 bg-unmsm-guinda text-white font-bold rounded-lg hover:bg-red-900 transition-all shadow-md">
                                <x-fas-external-link-alt /> Ir a San Market UNMSM
                            </a>
                        </div>
                    @endif

                    @if($s->pago_observaciones)
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
                            <x-fas-triangle-exclamation class="mr-1" /> {{ $s->pago_observaciones }}
                        </div>
                    @endif
                </section>

                {{-- Sección 5: Publicación de resultados --}}
                <section id="resultados" class="admision-section is-hidden bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-bold text-unmsm-guinda mb-4 font-serif">Publicación de resultados</h2>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <x-fas-circle-check class="text-green-600 text-xl" />
                            </div>
                            <p class="text-green-800 font-medium">{{ $s->resultados_texto }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-3 mt-5">
                        @if($s->resultados_enlace)
                            <a href="{{ $s->resultados_enlace }}" target="_blank" rel="noopener noreferrer" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-unmsm-guinda text-white font-semibold rounded-lg hover:bg-red-900 transition-all text-sm">
                                <x-fas-arrow-up-right-from-square /> Ver publicación
                            </a>
                        @endif
                        @if($s->resultados_pdf_url)
                            <a href="{{ $s->resultados_pdf_url }}" target="_blank" rel="noopener noreferrer" 
                                class="inline-flex items-center gap-2 px-5 py-2.5 border border-unmsm-guinda text-unmsm-guinda font-semibold rounded-lg hover:bg-unmsm-guinda hover:text-white transition-all text-sm">
                                <x-fas-file-pdf /> Ver PDF de resultados
                            </a>
                        @endif
                    </div>
                </section>

                {{-- Sección 6: Para más información --}}
                <section id="contacto" class="admision-section is-hidden bg-white border border-gray-200 rounded-xl p-6 shadow-md">
                    <h2 class="text-xl font-bold text-unmsm-guinda mb-6 font-serif">Para más información</h2>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div class="space-y-4 text-sm">
                            @if($s->contacto_telefono)
                                <div class="flex items-center gap-3">
                                    <x-fas-phone class="text-unmsm-guinda" />
                                    <span class="text-gray-700">{{ $s->contacto_telefono }}</span>
                                </div>
                            @endif
                            @if($s->contacto_correo)
                                <div class="flex items-center gap-3">
                                    <x-fas-envelope class="text-unmsm-guinda" />
                                    <a href="mailto:{{ $s->contacto_correo }}" class="text-gray-700 hover:text-unmsm-guinda">{{ $s->contacto_correo }}</a>
                                </div>
                            @endif
                            @if($s->contacto_direccion)
                                <div class="flex items-start gap-3">
                                    <x-fas-location-dot class="text-unmsm-guinda mt-0.5" />
                                    <span class="text-gray-700">{{ $s->contacto_direccion }}</span>
                                </div>
                            @endif
                            @if($s->contacto_sitio_web)
                                <div class="flex items-center gap-3">
                                    <x-fas-globe class="text-unmsm-guinda" />
                                    <a href="{{ $s->contacto_sitio_web }}" target="_blank" rel="noopener noreferrer" class="text-gray-700 hover:text-unmsm-guinda">{{ $s->contacto_sitio_web }}</a>
                                </div>
                            @endif
                            @if($s->contacto_whatsapp)
                                <a href="{{ $s->contacto_whatsapp }}" target="_blank" rel="noopener noreferrer" 
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all text-sm mt-2">
                                    <x-fab-whatsapp class="text-lg" /> Escríbenos por WhatsApp
                                </a>
                            @endif
                        </div>
                        @if($s->contacto_qr_path)
                            <div class="flex flex-col items-center justify-center border border-gray-200 rounded-xl p-5 bg-gray-50">
                                <img src="{{ asset('storage/' . $s->contacto_qr_path) }}" alt="Código QR WhatsApp Posgrado Letras"
                                    class="w-40 h-40 object-contain mb-2">
                                <p class="text-xs text-gray-500 text-center">Escanea para mensajería instantánea</p>
                            </div>
                        @elseif($s->contacto_whatsapp)
                            {{-- Sin QR subido por el admin: se genera automáticamente a partir del enlace de WhatsApp (número de teléfono) --}}
                            <div class="flex flex-col items-center justify-center border border-gray-200 rounded-xl p-5 bg-gray-50">
                                <canvas id="whatsapp-qr-canvas" data-whatsapp-link="{{ $s->contacto_whatsapp }}" class="w-40 h-40"></canvas>
                                <p class="text-xs text-gray-500 text-center mt-2">Escanea para mensajería instantánea</p>
                            </div>
                        @endif
                    </div>
                </section>

            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function() {
            var links = document.querySelectorAll('[data-nav-target]');
            var sections = document.querySelectorAll('.admision-section');
            if (!links.length || !sections.length) return;

            var validIds = Array.prototype.map.call(sections, function(s) { return s.id; });

            function showSection(id) {
                if (validIds.indexOf(id) === -1) return;

                sections.forEach(function(section) {
                    section.classList.toggle('is-hidden', section.id !== id);
                });
                links.forEach(function(link) {
                    link.classList.toggle('is-active', link.dataset.navTarget === id);
                });
            }

            links.forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    var id = link.dataset.navTarget;
                    showSection(id);
                    history.replaceState(null, '', '#' + id);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });

            var initial = window.location.hash ? window.location.hash.substring(1) : '';
            showSection(validIds.indexOf(initial) !== -1 ? initial : validIds[0]);
        })();

        // QR de WhatsApp autogenerado a partir del enlace (número de teléfono) cuando no hay imagen subida.
        // window.renderQRCode carga la librería `qrcode` bajo demanda (chunk aparte), solo en esta vista.
        function renderWhatsappQr() {
            var canvas = document.getElementById('whatsapp-qr-canvas');
            if (!canvas || typeof window.renderQRCode !== 'function') return;

            window.renderQRCode(canvas, canvas.dataset.whatsappLink, { width: 160, margin: 1 })
                .catch(function(error) { console.error('No se pudo generar el QR de WhatsApp:', error); });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', renderWhatsappQr);
        } else {
            renderWhatsappQr();
        }
    </script>
@endpush

{{-- resources/views/layouts/partials/navbar.blade.php --}}
@php
    $navSettings = \App\Models\SiteSetting::get();
@endphp
<div id="navbar-inner" class="w-full bg-transparent">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex justify-between items-center h-24 transition-all duration-300" id="nav-height">

            {{-- Logo y Nombre --}}
            <div class="flex-shrink-0 flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img id="header-logo"
                        src="{{ $navSettings?->logo_path ? asset('storage/' . $navSettings->logo_path) : 'https://letras.unmsm.edu.pe/wp-content/uploads/2020/11/LOGO_LETRAS_AI.png' }}"
                        class="h-16 w-auto object-contain transition-all duration-300 brightness-0 invert"
                        alt="{{ $navSettings?->site_name ?? 'Logo Letras' }}" width="64" height="64"
                        fetchpriority="high" decoding="async">
                </a>
            </div>

            {{-- Desktop Menu --}}
            <nav class="hidden lg:flex space-x-5 items-center">

                {{-- Nosotros --}}
                <div class="relative group h-full flex items-center">
                    <span
                        class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1 cursor-pointer">
                        Nosotros <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </span>
                    <div
                        class="absolute top-full left-0 w-64 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('nosotros') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-info-circle mr-2 text-red-700"></i>Quiénes somos
                        </a>
                        
                        {{-- 
                        <a href="{{ route('profesores.index') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-chalkboard-teacher mr-2 text-red-700"></i>
                            Profesores Asesores
                        </a>
                        --}}

                        <a href="https://letras.unmsm.edu.pe/directorio/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-address-book mr-2 text-red-700"></i>Directorio FLCH
                        </a>
                        <a href="{{route('directorio') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-users mr-2 text-red-700"></i>Directorio Posgrado
                        </a>
                        <a href="{{ route('informativos.index') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-file-alt mr-2 text-red-700"></i>Documentos y Recursos
                        </a>
                        <a href="https://letras.unmsm.edu.pe/unidad-de-investigacion" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-flask mr-2 text-red-700"></i>Grupos de Investigación
                        </a>
                    </div>
                </div>

                {{-- Admisión --}}
                <div class="relative group h-full flex items-center">
                    <span
                        class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1 cursor-pointer">
                        Admisión <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </span>
                    <div
                        class="absolute top-full left-0 w-64 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('admision') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-user-plus mr-2 text-red-700"></i>Proceso de Admisión
                        </a>
                        <a href="https://posgrado.unmsm.edu.pe/doc/cuadro-de-vacantes-2026-i-f-1-f-1765884106-0" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-th-list mr-2 text-red-700"></i>Cuadro de Vacantes
                        </a>
                        <a href="https://posgrado.unmsm.edu.pe/doc/criterios-evaluacion-admision-2025" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-clipboard-check mr-2 text-red-700"></i>Criterios de Evaluación
                        </a>
                        <a href="{{ route('cronograma') }}" class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-calendar-alt mr-2 text-red-700"></i>Cronograma Académico
                        </a>
                    </div>
                </div>

                {{-- Programas --}}
                <div class="relative group h-full flex items-center">
                    <a href="{{ route('programas.index') }}" class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1
                              {{ request()->routeIs('programas.*') ? 'text-red-500 font-bold' : '' }}">
                        Programas <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </a>
                    <div
                        class="absolute top-full left-0 w-56 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('programas.index') }}?tipo=maestria"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-graduation-cap mr-2 text-red-700"></i>Maestrías
                        </a>
                        <a href="{{ route('programas.index') }}?tipo=doctorado"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-user-graduate mr-2 text-red-700"></i>Doctorados
                        </a>
                        <a href="{{ route('programas.index') }}?tipo=diplomado"
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-scroll mr-2 text-red-700"></i>Diplomados
                        </a>
                    </div>
                </div>

                {{-- Trámites (Obtención de Grado) --}}
                <div class="relative group h-full flex items-center">
                    <a href="{{ route('tramites') }}" class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1
                              {{ request()->routeIs('tramites') ? 'text-red-500 font-bold' : '' }}">
                        Trámites <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </a>
                    <div
                        class="absolute top-full left-0 w-72 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('tramites') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-graduation-cap mr-2 text-red-700"></i>Optar el Grado de Magíster
                        </a>
                        <a href="{{ route('tramites') }}?tab=doctor" class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-user-graduate mr-2 text-red-700"></i>Optar el Grado de Doctor
                        </a>
                    </div>
                </div>

                {{-- Actualidad --}}
                <div class="relative group h-full flex items-center">
                    <span
                        class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1 cursor-pointer">
                        Actualidad <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </span>
                    <div
                        class="absolute top-full left-0 w-64 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('testimonios.index') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-quote-left mr-2 text-red-700"></i>Testimonios
                        </a>
                        <a href="https://letras.unmsm.edu.pe/categoria/noticias/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-newspaper mr-2 text-red-700"></i>Noticias FLCH
                        </a>
                        <a href="{{ route('eventos.index') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-calendar-day mr-2 text-red-700"></i>Eventos
                        </a>
                        <a href="https://letras.unmsm.edu.pe/categoria/conferencias/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-microphone mr-2 text-red-700"></i>Conferencias
                        </a>
                    </div>
                </div>

                {{-- Idiomas --}}
                <div class="relative group h-full flex items-center">
                    <span
                        class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1 cursor-pointer">
                        Idiomas <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </span>
                    <div
                        class="absolute top-full right-0 w-72 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="https://ceidletras.unmsm.edu.pe/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-language mr-2 text-red-700"></i>Centro de Idiomas (CEID)
                        </a>
                        <a href="https://letras.unmsm.edu.pe/oficina-de-examen-de-suficiencia-en-idiomas/"
                            target="_blank" class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-certificate mr-2 text-red-700"></i>Examen de Suficiencia
                        </a>
                        <a href="https://letras.unmsm.edu.pe/tarifario-centro-de-idiomas/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-money-bill mr-2 text-red-700"></i>Tarifario CEID
                        </a>
                    </div>
                </div>

                {{-- Facultad (Enlace externo) --}}
                <a href="https://letras.unmsm.edu.pe" target="_blank"
                    class="nav-item text-white font-medium hover:text-red-500 transition flex items-center gap-1">
                    Facultad <i class="fas fa-external-link-alt text-xs ml-1"></i>
                </a>

            </nav>

            {{-- Mobile Menu Button --}}
            <div class="lg:hidden" x-data="{ mobileMenuOpen: false }" x-init="
                   let scrollY = 0;
                   $watch('mobileMenuOpen', value => {
                     if(value) {
                       scrollY = window.scrollY;
                       document.body.style.position = 'fixed';
                       document.body.style.top = `-${scrollY}px`;
                       document.body.style.width = '100%';
                       document.body.style.overflow = 'hidden';
                     } else {
                       document.body.style.position = '';
                       document.body.style.top = '';
                       document.body.style.width = '';
                       document.body.style.overflow = '';
                       window.scrollTo(0, scrollY);
                     }
                   });
                 ">
                <button @click="mobileMenuOpen = true" id="mobile-menu-btn"
                    class="text-white text-2xl p-2 relative z-50 transition-colors duration-200 hover:text-red-200">
                    <i class="fas fa-bars"></i>
                </button>

                {{-- Mobile Menu Overlay --}}
                <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity ease-linear duration-300"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-black/85 backdrop-blur-sm z-[60] cursor-pointer"
                    style="display: none; pointer-events: auto;" @click="mobileMenuOpen = false">
                </div>

                {{-- Mobile Sidebar (Right Side) --}}
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-300 transform"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="fixed top-0 right-0 bottom-0 w-[320px] bg-white shadow-2xl z-[70] flex flex-col border-l-4 border-red-800 h-screen"
                    style="display: none;">

                    {{-- Sidebar Header --}}
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <img src="{{ $navSettings?->logo_path ? asset('storage/' . $navSettings->logo_path) : 'https://letras.unmsm.edu.pe/wp-content/uploads/2020/11/LOGO_LETRAS_AI.png' }}"
                                alt="{{ $navSettings?->site_name ?? 'Logo FLCH' }}" class="h-12 w-auto object-contain">
                        </div>
                        <button @click="mobileMenuOpen = false"
                            class="text-gray-400 hover:text-red-700 hover:bg-red-50 rounded-full p-2 transition-all duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    {{-- Sidebar Content --}}
                    <div class="flex-1 overflow-y-auto py-2 scroll-smooth bg-white">

                        {{-- Nosotros Group --}}
                        <div x-data="{ expanded: false }" class="border-b border-gray-50">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group">
                                <span class="flex items-center font-semibold text-base"><i
                                        class="fas fa-info-circle w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                    Nosotros</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-red-700 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="expanded" x-collapse class="bg-gray-50/80">
                                <a href="{{ route('nosotros') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Quiénes
                                    somos</a>
                                <a href="{{ route('profesores.index') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Docentes</a>
                                <a href="https://letras.unmsm.edu.pe/directorio/" @click="mobileMenuOpen = false"
                                    target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Directorio
                                    FLCH</a>
                                <a href="{{ route('directorio') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Directorio
                                    Posgrado</a>
                                <a href="https://letras.unmsm.edu.pe/unidad-de-investigacion" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Unidad
                                    de Investigación</a>
                            </div>
                        </div>

                        {{-- Admisión Group --}}
                        <div x-data="{ expanded: false }" class="border-b border-gray-50">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group">
                                <span class="flex items-center font-semibold text-base"><i
                                        class="fas fa-user-plus w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                    Admisión</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-red-700 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="expanded" x-collapse class="bg-gray-50/80">
                                <a href="{{ route('admision') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Proceso
                                    de Admisión</a>
                                <a href="https://posgrado.unmsm.edu.pe/doc/cuadro-de-vacantes-2026-i-f-1-f-1765884106-0" @click="mobileMenuOpen = false" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Cuadro
                                    de Vacantes</a>
                                <a href="https://posgrado.unmsm.edu.pe/doc/criterios-evaluacion-admision-2025" @click="mobileMenuOpen = false" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Criterios
                                    de Evaluación</a>
                                <a href="{{ route('cronograma') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Cronograma
                                    Académico</a>
                            </div>
                        </div>

                        {{-- Programas Group --}}
                        <div x-data="{ expanded: false }" class="border-b border-gray-50">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group">
                                <span class="flex items-center font-semibold text-base"><i
                                        class="fas fa-graduation-cap w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                    Programas</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-red-700 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="expanded" x-collapse class="bg-gray-50/80">
                                <a href="{{ route('programas.index') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Ver
                                    Todos</a>
                                <a href="{{ route('programas.index') }}?tipo=maestria" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Maestrías</a>
                                <a href="{{ route('programas.index') }}?tipo=doctorado" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Doctorados</a>
                                <a href="{{ route('programas.index') }}?tipo=diplomado" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700"><i class="fas fa-scroll mr-2"></i>Diplomados</a>
                            </div>
                        </div>

                        {{-- Trámites Group --}}
                        <div x-data="{ expanded: false }" class="border-b border-gray-50">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group">
                                <span class="flex items-center font-semibold text-base"><i
                                        class="fas fa-file-alt w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                    Trámites</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-red-700 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="expanded" x-collapse class="bg-gray-50/80">
                                <a href="{{ route('tramites') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Grado
                                    de Magíster</a>
                                <a href="{{ route('tramites') }}?tab=doctor" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Grado
                                    de Doctor</a>
                            </div>
                        </div>

                        {{-- Actualidad Group --}}
                        <div x-data="{ expanded: false }" class="border-b border-gray-50">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group">
                                <span class="flex items-center font-semibold text-base"><i
                                        class="fas fa-newspaper w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                    Actualidad</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-red-700 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="expanded" x-collapse class="bg-gray-50/80">
                                <a href="{{ route('testimonios.index') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Testimonios</a>
                                <a href="https://letras.unmsm.edu.pe/categoria/noticias/"
                                    @click="mobileMenuOpen = false" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Noticias</a>
                                <a href="{{ route('eventos.index') }}" @click="mobileMenuOpen = false"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Eventos</a>
                                <a href="https://letras.unmsm.edu.pe/categoria/conferencias/"
                                    @click="mobileMenuOpen = false" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Conferencias</a>
                            </div>
                        </div>

                        {{-- Idiomas Group --}}
                        <div x-data="{ expanded: false }" class="border-b border-gray-50">
                            <button @click="expanded = !expanded"
                                class="w-full flex items-center justify-between px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group">
                                <span class="flex items-center font-semibold text-base"><i
                                        class="fas fa-language w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                    Idiomas</span>
                                <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-red-700 transition-transform duration-300"
                                    :class="expanded ? 'rotate-180' : ''"></i>
                            </button>
                            <div x-show="expanded" x-collapse class="bg-gray-50/80">
                                <a href="https://ceidletras.unmsm.edu.pe/" @click="mobileMenuOpen = false"
                                    target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Centro
                                    de Idiomas</a>
                                <a href="https://letras.unmsm.edu.pe/oficina-de-examen-de-suficiencia-en-idiomas/"
                                    @click="mobileMenuOpen = false" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Examen
                                    de Suficiencia</a>
                                <a href="https://letras.unmsm.edu.pe/tarifario-centro-de-idiomas/"
                                    @click="mobileMenuOpen = false" target="_blank"
                                    class="block px-6 py-3 pl-14 text-gray-600 hover:text-red-700 hover:bg-red-50 text-sm font-medium transition-colors border-l-2 border-transparent hover:border-red-700">Tarifario</a>
                            </div>
                        </div>

                        {{-- Facultad Link --}}
                        <a href="https://letras.unmsm.edu.pe" @click="mobileMenuOpen = false" target="_blank"
                            class="w-full flex items-center px-6 py-4 text-gray-800 hover:bg-gray-50 transition-colors duration-200 group border-b border-gray-50">
                            <span class="flex items-center font-semibold text-base"><i
                                    class="fas fa-university w-6 text-center text-red-700/80 group-hover:text-red-700 mr-3 transition-colors"></i>
                                Facultad <i class="fas fa-external-link-alt text-xs ml-2 text-gray-400"></i></span>
                        </a>

                    </div>

                    {{-- Sidebar Footer --}}
                    <div class="p-6 border-t border-gray-100 bg-gray-50 text-center">
                        <div class="flex justify-center space-x-6 mb-3">
                            @if($navSettings?->facebook)<a href="{{ $navSettings->facebook }}" target="_blank"
                                class="text-gray-400 hover:text-blue-600 transform hover:scale-110 transition-all"><i
                            class="fab fa-facebook-f text-lg"></i></a>@endif
                            @if($navSettings?->instagram)<a href="{{ $navSettings->instagram }}" target="_blank"
                                class="text-gray-400 hover:text-pink-600 transform hover:scale-110 transition-all"><i
                            class="fab fa-instagram text-lg"></i></a>@endif
                            @if($navSettings?->linkedin)<a href="{{ $navSettings->linkedin }}" target="_blank"
                                class="text-gray-400 hover:text-blue-700 transform hover:scale-110 transition-all"><i
                            class="fab fa-linkedin-in text-lg"></i></a>@endif
                            @if($navSettings?->twitter)<a href="{{ $navSettings->twitter }}" target="_blank"
                                class="text-gray-400 hover:text-sky-500 transform hover:scale-110 transition-all"><i
                            class="fab fa-twitter text-lg"></i></a>@endif
                            @if($navSettings?->tiktok)<a href="{{ $navSettings->tiktok }}" target="_blank"
                                class="text-gray-400 hover:text-black transform hover:scale-110 transition-all"><i
                            class="fab fa-tiktok text-lg"></i></a>@endif
                            @if($navSettings?->youtube)<a href="{{ $navSettings->youtube }}" target="_blank"
                                class="text-gray-400 hover:text-red-600 transform hover:scale-110 transition-all"><i
                            class="fab fa-youtube text-lg"></i></a>@endif
                        </div>
                        <p class="text-xs text-gray-400 font-medium">&copy; {{ date('Y') }} Posgrado Letras UNMSM</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
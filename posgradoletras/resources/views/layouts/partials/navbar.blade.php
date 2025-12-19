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
                        alt="{{ $navSettings?->site_name ?? 'Logo Letras' }}">
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
                        <a href="{{ route('profesores.index') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-chalkboard-teacher mr-2 text-red-700"></i>Docentes
                        </a>
                        <a href="https://letras.unmsm.edu.pe/directorio/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-address-book mr-2 text-red-700"></i>Directorio FLCH
                        </a>
                        <a href="{{ route('directorio') }}"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-users mr-2 text-red-700"></i>Directorio Posgrado
                        </a>
                        <a href="https://letras.unmsm.edu.pe/unidad-de-investigacion" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-flask mr-2 text-red-700"></i>Unidad de Investigación
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
                        <a href="{{ route('tramites') }}#cronograma" class="block px-5 py-3 hover:bg-gray-50">
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
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-user-graduate mr-2 text-red-700"></i>Doctorados
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
                        <a href="https://letras.unmsm.edu.pe/categoria/eventos/" target="_blank"
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
            <div class="lg:hidden" x-data="{ mobileMenuOpen: false }">
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="nav-item text-white text-2xl p-2">
                    <i class="fas fa-bars" x-show="!mobileMenuOpen"></i>
                    <i class="fas fa-times" x-show="mobileMenuOpen"></i>
                </button>

                {{-- Mobile Menu Dropdown --}}
                <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="absolute top-full left-0 right-0 bg-[#7f1d1d] shadow-lg z-50">
                    <div class="flex flex-col py-4 px-4 space-y-2 max-h-[80vh] overflow-y-auto">
                        <a href="{{ route('nosotros') }}"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-info-circle mr-2"></i>Nosotros
                        </a>
                        <a href="{{ route('profesores.index') }}"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-chalkboard-teacher mr-2"></i>Docentes
                        </a>
                        <a href="{{ route('admision') }}"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-user-plus mr-2"></i>Admisión
                        </a>
                        <a href="{{ route('programas.index') }}"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-graduation-cap mr-2"></i>Programas
                        </a>
                        <a href="{{ route('tramites') }}"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-file-alt mr-2"></i>Trámites
                        </a>
                        <a href="{{ route('testimonios.index') }}"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-quote-left mr-2"></i>Testimonios
                        </a>
                        <a href="https://ceidletras.unmsm.edu.pe/" target="_blank"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-language mr-2"></i>Centro de Idiomas
                        </a>
                        <a href="https://letras.unmsm.edu.pe" target="_blank"
                            class="px-4 py-3 text-white hover:bg-white/10 rounded transition">
                            <i class="fas fa-university mr-2"></i>Facultad <i
                                class="fas fa-external-link-alt text-xs ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
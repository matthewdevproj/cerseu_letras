{{-- resources/views/layouts/partials/navbar.blade.php --}}

<div id="navbar-inner" class="w-full bg-transparent">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="flex justify-between items-center h-24 transition-all duration-300" id="nav-height">

            {{-- Logo y Nombre --}}
            <div class="flex-shrink-0 flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img id="header-logo"
                        src="https://letras.unmsm.edu.pe/wp-content/uploads/2020/11/LOGO_LETRAS_AI.png"
                        class="h-16 w-auto object-contain transition-all duration-300 brightness-0 invert"
                        alt="Logo Letras">


                </a>
            </div>

            {{-- Desktop Menu --}}
            <nav class="hidden lg:flex space-x-6 items-center">

                {{-- Inicio --}}
                <a href="{{ route('home') }}" class="nav-item text-white font-medium hover:text-red-500 transition
                          {{ request()->routeIs('home') ? 'text-red-500 font-bold' : '' }}">
                    Inicio
                </a>

                {{-- Facultad con Dropdown (Enlaces externos) --}}
                <div class="relative group h-full flex items-center">
                    <a href="https://letras.unmsm.edu.pe" target="_blank"
                        class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1">
                        Facultad <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </a>
                    <div
                        class="absolute top-full left-0 w-72 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="https://letras.unmsm.edu.pe/historia/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-landmark mr-2 text-red-700"></i>Historia de la Facultad
                        </a>
                        <a href="https://letras.unmsm.edu.pe/decanato/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-user-tie mr-2 text-red-700"></i>Decanato
                        </a>
                        <a href="https://letras.unmsm.edu.pe/escuelas/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-graduation-cap mr-2 text-red-700"></i>Escuelas (Pregrado)
                        </a>
                        <a href="https://letras.unmsm.edu.pe/unidad-de-investigacion/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <i class="fas fa-flask mr-2 text-red-700"></i>Investigación
                        </a>
                        <a href="https://letras.unmsm.edu.pe/documentos-de-interes/" target="_blank"
                            class="block px-5 py-3 hover:bg-gray-50">
                            <i class="fas fa-file-alt mr-2 text-red-700"></i>Documentos de Interés
                        </a>
                    </div>
                </div>

                {{-- Nosotros con Dropdown --}}
                <div class="relative group h-full flex items-center">
                    <a href="{{ route('nosotros') }}" class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1
                              {{ request()->routeIs('nosotros') ? 'text-red-500 font-bold' : '' }}">
                        Nosotros <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </a>
                    <div
                        class="absolute top-full left-0 w-60 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('nosotros') }}#historia"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">Historia</a>
                        <a href="{{ route('nosotros') }}#autoridades"
                            class="block px-5 py-3 hover:bg-gray-50">Autoridades</a>
                    </div>
                </div>

                {{-- Programas con Dropdown --}}
                <div class="relative group h-full flex items-center">
                    <a href="{{ route('programas.index') }}" class="nav-item text-white font-medium hover:text-red-500 transition py-4 flex items-center gap-1
                              {{ request()->routeIs('programas.*') ? 'text-red-500 font-bold' : '' }}">
                        Programas <i class="fas fa-angle-down text-xs mt-0.5"></i>
                    </a>
                    <div
                        class="absolute top-full left-0 w-60 bg-white shadow-xl border-t-4 border-red-700 rounded-b-md hidden group-hover:block text-gray-700 text-sm z-50">
                        <a href="{{ route('programas.index') }}?tipo=maestria"
                            class="block px-5 py-3 hover:bg-gray-50 border-b border-gray-100">Maestrías</a>
                        <a href="{{ route('programas.index') }}?tipo=doctorado"
                            class="block px-5 py-3 hover:bg-gray-50">Doctorados</a>
                    </div>
                </div>

                {{-- Profesores --}}
                <a href="{{ route('profesores.index') }}" class="nav-item text-white font-medium hover:text-red-500 transition
                          {{ request()->routeIs('profesores.*') ? 'text-red-500 font-bold' : '' }}">
                    Profesores
                </a>

                {{-- Admisión --}}
                <a href="{{ route('admision') }}" class="nav-item text-white font-medium hover:text-red-500 transition
                          {{ request()->routeIs('admision') ? 'text-red-500 font-bold' : '' }}">
                    Admisión
                </a>

                {{-- Trámites --}}
                <a href="{{ route('tramites') }}" class="nav-item text-white font-medium hover:text-red-500 transition
                          {{ request()->routeIs('tramites') ? 'text-red-500 font-bold' : '' }}">
                    Trámites
                </a>

                {{-- Idiomas con Dropdown --}}
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
                    <div class="flex flex-col py-4 px-4 space-y-2">
                        <a href="{{ route('home') }}" class="px-4 py-3 text-white hover:bg-white/10 rounded transition
                                  {{ request()->routeIs('home') ? 'bg-white/20 font-semibold' : '' }}">
                            Inicio
                        </a>
                        <a href="{{ route('nosotros') }}" class="px-4 py-3 text-white hover:bg-white/10 rounded transition
                                  {{ request()->routeIs('nosotros') ? 'bg-white/20 font-semibold' : '' }}">
                            Nosotros
                        </a>
                        <a href="{{ route('programas.index') }}" class="px-4 py-3 text-white hover:bg-white/10 rounded transition
                                  {{ request()->routeIs('programas.*') ? 'bg-white/20 font-semibold' : '' }}">
                            Programas
                        </a>
                        <a href="{{ route('profesores.index') }}" class="px-4 py-3 text-white hover:bg-white/10 rounded transition
                                  {{ request()->routeIs('profesores.*') ? 'bg-white/20 font-semibold' : '' }}">
                            Profesores
                        </a>
                        <a href="{{ route('admision') }}" class="px-4 py-3 text-white hover:bg-white/10 rounded transition
                                  {{ request()->routeIs('admision') ? 'bg-white/20 font-semibold' : '' }}">
                            Admisión
                        </a>
                        <a href="{{ route('tramites') }}" class="px-4 py-3 text-white hover:bg-white/10 rounded transition
                                  {{ request()->routeIs('tramites') ? 'bg-white/20 font-semibold' : '' }}">
                            Trámites
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
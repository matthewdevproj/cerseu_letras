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
                    {{-- A 64px de alto el logo mide ~314px de ancho y no dejaba
                         sitio al buscador ni al botón de menú en móvil (el
                         hamburguesa se salía de la pantalla). Se escala por
                         tramos. El width/height declarado respeta la proporción
                         real del archivo (1862×380) para no provocar reflow. --}}
                    <img id="header-logo"
                        src="{{ $navSettings?->logo_path ? asset('storage/' . $navSettings->logo_path) : asset('images/logo-cerseu.webp') }}"
                        class="h-10 sm:h-12 lg:h-16 w-auto object-contain transition-all duration-300 brightness-0 invert"
                        alt="{{ $navSettings?->site_name ?? 'Logo CERSEU Letras' }}" width="1234" height="310"
                        fetchpriority="high" decoding="async">
                </a>
            </div>

            {{-- Desktop Menu --}}
            <nav class="hidden lg:flex space-x-5 items-center" aria-label="Principal">
                {{-- Un único origen para las dos versiones del menú: ver el
                     comentario del menú móvil. --}}
                <x-nav-menu variante="escritorio" />
            </nav>

            {{-- Acciones a la derecha: buscador (en todos los tamaños, conforme a
                 la Obs. N.º 5) y botón del menú móvil. --}}
            <div class="flex items-center gap-1 lg:pl-3 lg:border-l lg:border-white/20">
                <x-site-search />

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
                <button @click="mobileMenuOpen = true" id="mobile-menu-btn" type="button"
                    aria-label="Abrir menú de navegación" :aria-expanded="mobileMenuOpen.toString()"
                    class="text-white text-2xl p-2 relative z-50 transition-colors duration-200 hover:text-unmsm-azul-soft focus-visible:outline focus-visible:outline-2 focus-visible:outline-white focus-visible:outline-offset-2 rounded">
                    <x-fas-bars aria-hidden="true" />
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
                <div x-show="mobileMenuOpen" x-trap.inert="mobileMenuOpen"
                    x-transition:enter="transition ease-out duration-300 transform"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transition ease-in duration-300 transform"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="fixed top-0 right-0 bottom-0 w-[320px] bg-white shadow-2xl z-[70] flex flex-col border-l-4 border-unmsm-azul h-screen"
                    role="dialog" aria-modal="true" aria-label="Menú de navegación"
                    style="display: none;">

                    {{-- Sidebar Header --}}
                    <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50/50">
                        <div class="flex items-center gap-3">
                            <img src="{{ $navSettings?->logo_path ? asset('storage/' . $navSettings->logo_path) : asset('images/logo-cerseu.webp') }}"
                                alt="{{ $navSettings?->site_name ?? 'Logo CERSEU Letras' }}" class="h-12 w-auto object-contain">
                        </div>
                        <button @click="mobileMenuOpen = false" type="button" aria-label="Cerrar menú de navegación"
                            class="text-gray-400 hover:text-unmsm-azul hover:bg-unmsm-azul/5 rounded-full p-2 transition-all duration-200 focus-visible:outline focus-visible:outline-2 focus-visible:outline-unmsm-azul focus-visible:outline-offset-2">
                            <x-fas-times class="text-xl" aria-hidden="true" />
                        </button>
                    </div>

                    {{-- Sidebar Content --}}
                    <nav aria-label="Principal (móvil)" class="flex-1 overflow-y-auto py-2 scroll-smooth bg-white">

                        {{-- El menú sale de `menu_items` (panel → Menú de
                             navegación). Antes esta lista y la de escritorio
                             estaban escritas a mano por separado y ya habían
                             divergido: aquí aparecía «Docentes», que en
                             escritorio estaba comentado, y el mismo enlace se
                             llamaba «Unidad de Investigación» abajo y «Grupos
                             de Investigación» arriba. --}}
                        <x-nav-menu variante="movil" />
                    </nav>

                    {{-- Sidebar Footer --}}
                    <div class="p-6 border-t border-gray-100 bg-gray-50 text-center">
                        <div class="flex justify-center space-x-6 mb-3">
                            @if($navSettings?->facebook)<a href="{{ $navSettings->facebook }}" target="_blank" rel="noopener noreferrer" 
                                class="text-gray-400 hover:text-blue-600 transform hover:scale-110 transition-all"><x-fab-facebook-f class="text-lg" /></a>@endif
                            @if($navSettings?->instagram)<a href="{{ $navSettings->instagram }}" target="_blank" rel="noopener noreferrer" 
                                class="text-gray-400 hover:text-pink-600 transform hover:scale-110 transition-all"><x-fab-instagram class="text-lg" /></a>@endif
                            @if($navSettings?->linkedin)<a href="{{ $navSettings->linkedin }}" target="_blank" rel="noopener noreferrer" 
                                class="text-gray-400 hover:text-blue-700 transform hover:scale-110 transition-all"><x-fab-linkedin-in class="text-lg" /></a>@endif
                            @if($navSettings?->twitter)<a href="{{ $navSettings->twitter }}" target="_blank" rel="noopener noreferrer" 
                                class="text-gray-400 hover:text-sky-500 transform hover:scale-110 transition-all"><x-fab-twitter class="text-lg" /></a>@endif
                            @if($navSettings?->tiktok)<a href="{{ $navSettings->tiktok }}" target="_blank" rel="noopener noreferrer" 
                                class="text-gray-400 hover:text-black transform hover:scale-110 transition-all"><x-fab-tiktok class="text-lg" /></a>@endif
                            @if($navSettings?->youtube)<a href="{{ $navSettings->youtube }}" target="_blank" rel="noopener noreferrer" 
                                class="text-gray-400 hover:text-red-600 transform hover:scale-110 transition-all"><x-fab-youtube class="text-lg" /></a>@endif
                        </div>
                        <p class="text-xs text-gray-400 font-medium">&copy; {{ date('Y') }} CERSEU Letras UNMSM</p>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>
</div>
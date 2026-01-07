{{-- resources/views/layouts/partials/topbar.blade.php --}}
@php
    $siteSettings = \App\Models\SiteSetting::get();
@endphp

<div id="top-bar"
    class="bg-[#1F1F20] text-white text-[13px] h-10 overflow-hidden flex items-center border-b border-white/10 w-full">
    <div class="container mx-auto px-4 lg:px-8 flex justify-between items-center h-full">
        <div class="hidden lg:flex items-center space-x-6">
            @if($siteSettings?->email)
                <a href="mailto:{{ $siteSettings->email }}" class="flex items-center hover:text-gray-300 transition gap-2">
                    <i class="fas fa-envelope"></i>
                    <span>{{ $siteSettings->email }}</span>
                </a>
                <span class="text-white/30">|</span>
            @endif
            @if($siteSettings?->telefono)
                <span class="flex items-center gap-2">
                    <i class="fas fa-phone"></i>
                    <span>{{ $siteSettings->telefono }}</span>
                </span>
                <span class="text-white/30">|</span>
            @endif
            @if($siteSettings?->web_facultad)
                <a href="{{ $siteSettings->web_facultad }}" target="_blank"
                    class="flex items-center hover:text-gray-300 transition gap-2">
                    <i class="fas fa-globe"></i>
                    <span>Web Facultad</span>
                </a>
                <span class="text-white/30">|</span>
            @endif
            @if($siteSettings?->directorio_facultad)
                <a href="{{ $siteSettings->directorio_facultad }}" target="_blank"
                    class="flex items-center hover:text-gray-300 transition gap-2">
                    <i class="fas fa-globe"></i>
                    <span>Directorio</span>
                </a>
                <span class="text-white/30">|</span>
            @endif
            <a href="{{ route('directorio') }}" class="flex items-center hover:text-gray-300 transition gap-2">
                <i class="fas fa-globe"></i>
                <span>Directorio Posgrado</span>
            </a>

        </div>

        <div class="flex lg:hidden items-center space-x-3 text-[11px]">
            @if($siteSettings?->email)
                <a href="mailto:{{ $siteSettings->email }}" class="flex items-center hover:text-gray-300 transition gap-1">
                    <i class="fas fa-envelope text-xs"></i>
                    <span>{{ $siteSettings->email }}</span>
                </a>
            @endif
        </div>


        <div class="flex items-center space-x-4 ml-auto lg:ml-0">
            @if($siteSettings?->facebook)
                <a href="{{ $siteSettings->facebook }}" target="_blank" class="hover:text-gray-300" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
            @endif
            @if($siteSettings?->instagram)
                <a href="{{ $siteSettings->instagram }}" target="_blank" class="hover:text-gray-300" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
            @endif
            @if($siteSettings?->youtube)
                <a href="{{ $siteSettings->youtube }}" target="_blank" class="hover:text-gray-300" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
            @endif
            @if($siteSettings?->linkedin)
                <a href="{{ $siteSettings->linkedin }}" target="_blank" class="hover:text-gray-300" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            @endif
            @if($siteSettings?->twitter)
                <a href="{{ $siteSettings->twitter }}" target="_blank" class="hover:text-gray-300" title="X (Twitter)">
                    <i class="fab fa-twitter"></i>
                </a>
            @endif
            @if($siteSettings?->tiktok)
                <a href="{{ $siteSettings->tiktok }}" target="_blank" class="hover:text-gray-300" title="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
            @endif
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Clases para transiciones suaves del header */
        #top-bar {
            transition: height 0.3s ease, opacity 0.3s ease;
        }

        #navbar-inner {
            transition: background-color 0.3s ease, padding 0.3s ease, box-shadow 0.3s ease, backdrop-filter 0.3s ease;
        }

        .nav-item {
            transition: color 0.3s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Pequeño delay para asegurar que Alpine y todo el DOM estén listos
            setTimeout(() => {
                const topBar = document.getElementById('top-bar');
                const navbarInner = document.getElementById('navbar-inner');
                const navHeight = document.getElementById('nav-height');
                const navItems = document.querySelectorAll('.nav-item');
                const mobileMenuBtn = document.getElementById('mobile-menu-btn');
                const logo = document.getElementById('header-logo');

                let scrolled = false;

                // Función para aplicar estilos según el estado de scroll
                function applyScrollStyles(isScrolled) {
                    if (isScrolled) {
                        // Cambios al hacer scroll
                        topBar?.classList.replace('h-10', 'h-0');
                        topBar?.classList.add('opacity-0');
                        navbarInner?.classList.remove('bg-transparent');
                        navbarInner?.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                        navItems.forEach(item => {
                            item.classList.replace('text-white', 'text-gray-800');
                        });
                        if (mobileMenuBtn) {
                            mobileMenuBtn.classList.remove('text-white');
                            mobileMenuBtn.classList.add('text-gray-800');
                        }
                        logo?.classList.remove('brightness-0', 'invert');
                        navHeight?.classList.replace('h-24', 'h-20');
                    } else {
                        // Cambios al volver arriba
                        topBar?.classList.replace('h-0', 'h-10');
                        topBar?.classList.remove('opacity-0');
                        navbarInner?.classList.add('bg-transparent');
                        navbarInner?.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-lg');
                        navItems.forEach(item => {
                            item.classList.replace('text-gray-800', 'text-white');
                        });
                        if (mobileMenuBtn) {
                            mobileMenuBtn.classList.remove('text-gray-800');
                            mobileMenuBtn.classList.add('text-white');
                        }
                        logo?.classList.add('brightness-0', 'invert');
                        navHeight?.classList.replace('h-20', 'h-24');
                    }
                }

                // Verificar posición inicial al cargar la página
                const initialScrolled = window.scrollY > 50;
                if (initialScrolled) {
                    scrolled = true;
                    applyScrollStyles(true);
                }

                // Usar throttle para optimizar performance
                let ticking = false;
                window.addEventListener('scroll', () => {
                    if (!ticking) {
                        window.requestAnimationFrame(() => {
                            const isScrolled = window.scrollY > 50;

                            if (isScrolled && !scrolled) {
                                scrolled = true;
                                applyScrollStyles(true);
                            } else if (!isScrolled && scrolled) {
                                scrolled = false;
                                applyScrollStyles(false);
                            }

                            ticking = false;
                        });
                        ticking = true;
                    }
                }, { passive: true });
            }, 100); // Esperar 100ms para que todo esté listo
        });
    </script>
@endpush

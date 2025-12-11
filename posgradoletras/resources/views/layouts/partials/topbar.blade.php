{{-- resources/views/layouts/partials/topbar.blade.php --}}

<div id="top-bar"
    class="bg-[#1F1F20] text-white text-[13px] h-10 overflow-hidden flex items-center border-b border-white/10 w-full">
    <div class="container mx-auto px-4 lg:px-8 flex justify-between items-center h-full">
        <div class="hidden lg:flex items-center space-x-6">
            <a href="mailto:posgrado.letras@unmsm.edu.pe"
                class="flex items-center hover:text-gray-300 transition gap-2">
                <i class="fas fa-envelope"></i>
                <span>posgrado.letras@unmsm.edu.pe</span>
            </a>
            <span class="text-white/30">|</span>
            <span class="flex items-center gap-2">
                <i class="fas fa-phone"></i>
                <span>982 085 037</span>
            </span>
            <span class="text-white/30">|</span>
            <a href="https://letras.unmsm.edu.pe" target="_blank"
                class="flex items-center hover:text-gray-300 transition gap-2">
                <i class="fas fa-globe"></i>
                <span>Web Facultad</span>
            </a>
            <span class="text-white/30">|</span>
            <a href="https://letras.unmsm.edu.pe/directorio/" target="_blank"
                class="flex items-center hover:text-gray-300 transition gap-2">
                <i class="fas fa-globe"></i>
                <span>Directorio</span>
            </a>
            <span class="text-white/30">|</span>
            <a href="{{ route('directorio') }}" class="flex items-center hover:text-gray-300 transition gap-2">
                <i class="fas fa-globe"></i>
                <span>Directorio Posgrado</span>
            </a>

        </div>

        <div class="flex lg:hidden items-center space-x-3 text-[11px]">
            <a href="mailto:posgrado.letras@unmsm.edu.pe"
                class="flex items-center hover:text-gray-300 transition gap-1">
                <i class="fas fa-envelope text-xs"></i>
                <span>posgrado.letras@unmsm.edu.pe</span>
            </a>
        </div>


        <div class="flex items-center space-x-4 ml-auto lg:ml-0">
            <a href="https://www.facebook.com/LetrasUNMSM" target="_blank" class="hover:text-gray-300"><i
                    class="fab fa-facebook-f"></i></a>
            <a href="#" class="hover:text-gray-300"><i class="fab fa-instagram"></i></a>
            <a href="#" class="hover:text-gray-300"><i class="fab fa-youtube"></i></a>
            <a href="#" class="hover:text-gray-300"><i class="fab fa-linkedin-in"></i></a>
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
            transition: background-color 0.3s ease, padding 0.3s ease, box-shadow 0.3s ease;
        }

        .nav-item {
            transition: color 0.3s ease;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const topBar = document.getElementById('top-bar');
            const navbarInner = document.getElementById('navbar-inner');
            const navHeight = document.getElementById('nav-height');
            const navItems = document.querySelectorAll('.nav-item');

            const logo = document.getElementById('header-logo');

            let scrolled = false;

            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    if (!scrolled) {
                        // --- AL BAJAR (SCROLL) ---
                        scrolled = true;

                        // 1. Ocultar Top Bar
                        if (topBar) {
                            topBar.classList.remove('h-10');
                            topBar.classList.add('h-0', 'opacity-0');
                        }

                        // 2. Navbar blanco con sombra
                        if (navbarInner) {
                            navbarInner.classList.remove('bg-transparent');
                            navbarInner.classList.add('bg-white', 'shadow-md');
                        }

                        // 3. Texto oscuro
                        navItems.forEach(item => {
                            item.classList.remove('text-white');
                            item.classList.add('text-gray-800');
                        });



                        // 4. Logo original (color) - Quitar filtros
                        if (logo) {
                            logo.classList.remove('brightness-0', 'invert');
                        }

                        if (navHeight) {
                            navHeight.classList.remove('h-24');
                            navHeight.classList.add('h-20');
                        }
                    }

                } else {
                    if (scrolled) {
                        // --- AL SUBIR (TOP) ---
                        scrolled = false;

                        // 1. Mostrar Top Bar
                        if (topBar) {
                            topBar.classList.add('h-10');
                            topBar.classList.remove('h-0', 'opacity-0');
                        }

                        // 2. Fondo transparente
                        if (navbarInner) {
                            navbarInner.classList.add('bg-transparent');
                            navbarInner.classList.remove('bg-white', 'shadow-md');
                        }

                        // 3. Texto blanco
                        navItems.forEach(item => {
                            item.classList.add('text-white');
                            item.classList.remove('text-gray-800');
                        });



                        // 4. Logo blanco (filtro)
                        if (logo) {
                            logo.classList.add('brightness-0', 'invert');
                        }

                        if (navHeight) {
                            navHeight.classList.add('h-24');
                            navHeight.classList.remove('h-20');
                        }
                    }
                }
            });
        });
    </script>
@endpush
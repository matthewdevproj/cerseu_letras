<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Posgrado Letras UNMSM</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --brand: #761e23;
            --brand-dark: #5a161a;
            --brand-accent: #d4a017;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
        }

        .nav-link {
            border-left: 3px solid transparent;
        }

        .nav-link.active {
            background-color: rgba(118, 30, 35, 0.08);
            border-left-color: var(--brand);
            color: var(--brand);
        }

        .nav-link.active i {
            color: var(--brand);
        }

        .nav-link:hover {
            background-color: rgba(118, 30, 35, 0.04);
        }

        .nav-link:hover i {
            color: var(--brand);
        }

        /* Stat Cards */
        .stat-card {
            border-top: 4px solid var(--card-color, var(--brand));
        }

        .stat-brand {
            --card-color: var(--brand);
            --icon-bg: rgba(118, 30, 35, 0.1);
        }

        .stat-accent {
            --card-color: var(--brand-accent);
            --icon-bg: rgba(212, 160, 23, 0.1);
        }

        .stat-dark {
            --card-color: var(--brand-dark);
            --icon-bg: rgba(90, 22, 26, 0.1);
        }

        .stat-icon {
            background-color: var(--icon-bg);
            color: var(--card-color);
        }

        /* Quick Actions */
        .quick-action-card:hover {
            border-color: var(--brand-accent);
            box-shadow: 0 5px 15px rgba(212, 160, 23, 0.15);
        }

        .quick-action-card:hover .action-icon-circle {
            background: var(--brand);
            color: white;
        }

        /* Buttons */
        .btn-brand-outline {
            border-color: var(--brand);
            color: var(--brand);
        }

        .btn-brand-outline:hover {
            background: var(--brand);
            color: white;
        }

        .btn-accent-outline {
            border-color: var(--brand-accent);
            color: #b58500;
        }

        .btn-accent-outline:hover {
            background: var(--brand-accent);
            color: white;
        }

        /* Mobile */
        @media (max-width: 1023px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Custom Utilities Compatibility */
        .bg-brand-red {
            background-color: var(--brand);
        }

        .text-brand-red {
            color: var(--brand);
        }

        .border-brand-red {
            border-color: var(--brand);
        }

        .ring-brand-red {
            --tw-ring-color: var(--brand);
        }

        .focus\:ring-brand-red:focus {
            --tw-ring-opacity: 1;
            --tw-ring-color: var(--brand);
        }

        .bg-brand-gold {
            background-color: var(--brand-accent);
        }

        .text-brand-gold {
            color: var(--brand-accent);
        }

        .border-brand-gold {
            border-color: var(--brand-accent);
        }

        .ring-brand-gold {
            --tw-ring-color: var(--brand-accent);
        }

        .text-brand-navy {
            color: #1e3a8a;
        }

        .bg-brand-navy {
            background-color: #1e3a8a;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-800">
    <!-- Overlay Mobile -->
    <div class="sidebar-overlay fixed inset-0 bg-black/40 z-40 hidden backdrop-blur-sm" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside
        class="sidebar fixed top-0 left-0 h-screen bg-white border-r border-gray-100 shadow-sm z-50 flex flex-col transition-transform duration-300"
        id="mainSidebar">
        <!-- Brand -->
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#761e23] to-[#5a161a] text-white flex items-center justify-center shadow-lg">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                    <h1 class="font-bold text-[#761e23] text-lg leading-tight">Letras</h1>
                    <p class="text-xs text-gray-500 font-medium">Posgrado Admin</p>
                </div>
            </div>
            <button class="lg:hidden absolute top-4 right-4 text-gray-400 hover:text-gray-600" id="closeSidebar">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <p class="text-xs font-bold uppercase tracking-wider text-[#d4a017] mb-2 ml-2">General</p>

            <a href="{{ route('admin.dashboard') }}"
                class="nav-link flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium transition-all {{ request()->routeIs('admin.dashboard') ? 'active' : 'text-gray-800 hover:bg-gray-50' }}">
                <i
                    class="fas fa-chart-pie w-5 text-lg {{ request()->routeIs('admin.dashboard') ? '' : 'text-gray-500' }}"></i>
                <span>Dashboard</span>
            </a>

            <p class="text-xs font-bold uppercase tracking-wider text-[#d4a017] mb-2 ml-2 mt-6">Gestión Académica</p>

            <a href="{{ route('admin.programas.index') }}"
                class="nav-link flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium transition-all {{ request()->routeIs('admin.programas.*') ? 'active' : 'text-gray-800 hover:bg-gray-50' }}">
                <i
                    class="fas fa-graduation-cap w-5 text-lg {{ request()->routeIs('admin.programas.*') ? '' : 'text-gray-500' }}"></i>
                <span>Programas</span>
            </a>

            <a href="{{ route('admin.docentes.index') }}"
                class="nav-link flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium transition-all {{ request()->routeIs('admin.docentes.*') ? 'active' : 'text-gray-800 hover:bg-gray-50' }}">
                <i
                    class="fas fa-chalkboard-teacher w-5 text-lg {{ request()->routeIs('admin.docentes.*') ? '' : 'text-gray-500' }}"></i>
                <span>Docentes</span>
            </a>

            <p class="text-xs font-bold uppercase tracking-wider text-[#d4a017] mb-2 ml-2 mt-6">Administración</p>

            <a href="{{ route('admin.testimonios.index') }}"
                class="nav-link flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium transition-all {{ request()->routeIs('admin.testimonios.*') ? 'active' : 'text-gray-800 hover:bg-gray-50' }}">
                <i
                    class="fas fa-comments w-5 text-lg {{ request()->routeIs('admin.testimonios.*') ? '' : 'text-gray-500' }}"></i>
                <span>Testimonios</span>
            </a>

            <a href="{{ route('admin.directorio.index') }}"
                class="nav-link flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium transition-all {{ request()->routeIs('admin.directorio.*') ? 'active' : 'text-gray-800 hover:bg-gray-50' }}">
                <i
                    class="fas fa-address-book w-5 text-lg {{ request()->routeIs('admin.directorio.*') ? '' : 'text-gray-500' }}"></i>
                <span>Directorio</span>
            </a>

            <a href="{{ route('admin.settings.index') }}"
                class="nav-link flex items-center gap-3 px-3 py-3 rounded-lg text-base font-medium transition-all {{ request()->routeIs('admin.settings.*') ? 'active' : 'text-gray-800 hover:bg-gray-50' }}">
                <i
                    class="fas fa-cog w-5 text-lg {{ request()->routeIs('admin.settings.*') ? '' : 'text-gray-500' }}"></i>
                <span>Configuración</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="p-4 border-t border-gray-100">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar Sesión</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-[260px] min-h-screen flex flex-col transition-all duration-300">
        <!-- Header -->
        <header
            class="sticky top-0 z-30 bg-white border-b border-gray-100 px-4 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button
                    class="lg:hidden w-10 h-10 flex items-center justify-center rounded-full border border-gray-200 shadow-sm bg-white"
                    id="sidebarToggle">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
                <div>
                    <h1 class="text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="text-sm text-gray-500">@yield('subtitle')</p>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank"
                    class="hidden md:inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-full hover:bg-gray-50 transition-colors">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Ver Web Pública</span>
                </a>

                <!-- User Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2">
                        <div class="hidden md:block text-right">
                            <p class="text-sm font-bold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">Administrador</p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-full bg-[#5a161a] text-[#d4a017] flex items-center justify-center font-bold border-2 border-[#d4a017]">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                        <p class="px-4 py-2 text-xs font-bold text-gray-400 uppercase">Mi Cuenta</p>
                        <a href="#" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-user-circle text-gray-400"></i> Perfil
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-cog text-gray-400"></i> Configuración
                        </a>
                        <hr class="my-2 border-gray-100">
                        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-4 lg:p-8 w-full">
            {{-- Alerts --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-triangle mr-3 text-red-500"></i>
                        <strong>Errores de validación:</strong>
                    </div>
                    <ul class="list-disc list-inside ml-8 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('mainSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const toggle = document.getElementById('sidebarToggle');
            const close = document.getElementById('closeSidebar');

            toggle?.addEventListener('click', () => {
                sidebar.classList.add('show');
                overlay.classList.add('show');
            });

            [close, overlay].forEach(el => {
                el?.addEventListener('click', () => {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
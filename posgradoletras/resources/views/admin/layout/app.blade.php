<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Posgrado Letras UNMSM</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap"
        rel="stylesheet">

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .font-serif {
            font-family: 'Playfair Display', serif;
        }

        /* Brand Colors */
        .bg-brand-red {
            background-color: #8B1114;
        }

        .bg-brand-red-dark {
            background-color: #560002;
        }

        .bg-brand-gold {
            background-color: #B6A350;
        }

        .bg-brand-navy {
            background-color: #1a1a2e;
        }

        .bg-brand-brown {
            background-color: #5D4037;
        }

        .text-brand-red {
            color: #8B1114;
        }

        .text-brand-gold {
            color: #B6A350;
        }

        .text-brand-navy {
            color: #1a1a2e;
        }

        .text-brand-brown {
            color: #5D4037;
        }

        .border-brand-red {
            border-color: #8B1114;
        }

        .border-brand-gold {
            border-color: #B6A350;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100 text-gray-800">
    <div class="min-h-screen bg-gray-100">

        <!-- Sidebar (Desktop Fixed) -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
            <div class="flex flex-col flex-grow bg-brand-red overflow-y-auto shadow-2xl">

                <!-- Logo Header -->
                <div class="flex items-center flex-shrink-0 px-4 py-6 border-b border-white/10">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-brand-red shadow-lg">
                            <i class="ph-bold ph-book-bookmark text-2xl"></i>
                        </div>
                        <div class="ml-3">
                            <h1 class="text-white font-serif font-bold text-lg tracking-wide">Letras Admin</h1>
                            <p class="text-brand-gold text-xs uppercase tracking-wider">Posgrado</p>
                        </div>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="flex-1 px-3 py-4 space-y-1">
                    <a href="{{ route('admin.dashboard') }}"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white shadow-inner border border-white/5' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <i
                            class="ph ph-chart-bar mr-3 text-xl {{ request()->routeIs('admin.dashboard') ? 'text-brand-gold' : 'group-hover:text-brand-gold' }} transition-colors"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.programas.index') }}"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.programas.*') ? 'bg-white/10 text-white shadow-inner border border-white/5' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <i
                            class="ph ph-graduation-cap mr-3 text-xl {{ request()->routeIs('admin.programas.*') ? 'text-brand-gold' : 'group-hover:text-brand-gold' }} transition-colors"></i>
                        Programas
                    </a>

                    <a href="{{ route('admin.docentes.index') }}"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.docentes.*') ? 'bg-white/10 text-white shadow-inner border border-white/5' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <i
                            class="ph ph-chalkboard-teacher mr-3 text-xl {{ request()->routeIs('admin.docentes.*') ? 'text-brand-gold' : 'group-hover:text-brand-gold' }} transition-colors"></i>
                        Docentes
                    </a>

                    <a href="{{ route('admin.testimonios.index') }}"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.testimonios.*') ? 'bg-white/10 text-white shadow-inner border border-white/5' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <i
                            class="ph ph-quotes mr-3 text-xl {{ request()->routeIs('admin.testimonios.*') ? 'text-brand-gold' : 'group-hover:text-brand-gold' }} transition-colors"></i>
                        Testimonios
                    </a>

                    <a href="{{ route('admin.directorio.index') }}"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.directorio.*') ? 'bg-white/10 text-white shadow-inner border border-white/5' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <i
                            class="ph ph-address-book mr-3 text-xl {{ request()->routeIs('admin.directorio.*') ? 'text-brand-gold' : 'group-hover:text-brand-gold' }} transition-colors"></i>
                        Directorio
                    </a>

                    <a href="{{ route('admin.settings.index') }}"
                        class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 
                              {{ request()->routeIs('admin.settings.*') ? 'bg-white/10 text-white shadow-inner border border-white/5' : 'text-gray-300 hover:bg-white/5 hover:text-white' }}">
                        <i
                            class="ph ph-gear mr-3 text-xl {{ request()->routeIs('admin.settings.*') ? 'text-brand-gold' : 'group-hover:text-brand-gold' }} transition-colors"></i>
                        Configuración
                    </a>

                    <div class="pt-4 mt-4 border-t border-white/10">
                        <a href="{{ route('home') }}" target="_blank"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-globe mr-3 text-xl group-hover:text-brand-gold transition-colors"></i>
                            Ver Sitio Web
                        </a>
                    </div>
                </nav>

                <!-- User Profile Bottom -->
                <div class="flex-shrink-0 border-t border-white/10 p-4 bg-black/20">
                    <div class="flex items-center mb-3">
                        <div
                            class="h-9 w-9 rounded-full bg-brand-gold flex items-center justify-center text-brand-red font-bold font-serif">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center px-3 py-2 text-xs font-medium text-red-200 hover:bg-red-900/50 rounded-lg transition-all border border-transparent hover:border-red-800/50">
                            <i class="ph ph-sign-out mr-2 text-base"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Sidebar (Hidden by default) -->
        <div x-data="{ sidebarOpen: false }" class="lg:hidden">
            <!-- Overlay -->
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/80 z-40"
                @click="sidebarOpen = false"></div>

            <!-- Mobile Sidebar Panel -->
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="fixed inset-y-0 left-0 z-50 w-64 bg-brand-red shadow-2xl">

                <!-- Close Button -->
                <div class="absolute top-4 right-4">
                    <button @click="sidebarOpen = false" class="text-white/70 hover:text-white">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>

                <!-- Same content as desktop sidebar -->
                <div class="flex flex-col h-full">
                    <div class="flex items-center flex-shrink-0 px-4 py-6 border-b border-white/10">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-brand-red shadow-lg">
                                <i class="ph-bold ph-book-bookmark text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h1 class="text-white font-serif font-bold text-lg tracking-wide">Letras Admin</h1>
                                <p class="text-brand-gold text-xs uppercase tracking-wider">Posgrado</p>
                            </div>
                        </div>
                    </div>

                    <nav class="flex-1 px-3 py-4 space-y-1">
                        <a href="{{ route('admin.dashboard') }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-chart-bar mr-3 text-xl"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.programas.index') }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-graduation-cap mr-3 text-xl"></i> Programas
                        </a>
                        <a href="{{ route('admin.docentes.index') }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-chalkboard-teacher mr-3 text-xl"></i> Docentes
                        </a>
                        <a href="{{ route('admin.testimonios.index') }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-quotes mr-3 text-xl"></i> Testimonios
                        </a>
                        <a href="{{ route('admin.directorio.index') }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-address-book mr-3 text-xl"></i> Directorio
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="group flex items-center px-3 py-3 text-sm font-medium rounded-lg text-gray-300 hover:bg-white/5 hover:text-white">
                            <i class="ph ph-gear mr-3 text-xl"></i> Configuración
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Mobile Menu Toggle stored in Alpine data -->
            <button @click="sidebarOpen = true"
                class="fixed bottom-4 left-4 z-30 lg:hidden bg-brand-red text-white p-3 rounded-full shadow-lg">
                <i class="ph ph-list text-xl"></i>
            </button>
        </div>

        <!-- Main Column -->
        <div class="lg:pl-64 flex flex-col flex-1 transition-all duration-300">

            <!-- Topbar Sticky -->
            <div class="sticky top-0 z-10 bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 items-center justify-between">
                        <!-- Mobile Hamburger (placeholder, actual button is fixed) -->
                        <div class="lg:hidden w-8"></div>

                        <!-- Breadcrumb & Actions -->
                        <div class="flex-1 flex justify-between items-center lg:ml-0 ml-4">
                            <nav class="flex" aria-label="Breadcrumb">
                                <ol class="flex items-center space-x-2">
                                    <li><span class="text-gray-400 font-serif">Admin</span></li>
                                    <li><span class="text-gray-300">/</span></li>
                                    <li><span
                                            class="text-brand-red font-semibold font-serif">@yield('title', 'Dashboard')</span>
                                    </li>
                                </ol>
                            </nav>
                            <div class="flex items-center space-x-4">
                                <button class="text-gray-400 hover:text-brand-red transition-colors relative">
                                    <i class="ph ph-bell text-xl"></i>
                                    <span
                                        class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-brand-gold ring-2 ring-white"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <main class="flex-1 py-8 bg-gray-50">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                    <!-- Alerts -->
                    @if(session('success'))
                        <div
                            class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
                            <i class="ph-fill ph-check-circle text-xl mr-3 text-green-500"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div
                            class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
                            <i class="ph-fill ph-x-circle text-xl mr-3 text-red-500"></i>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                            <div class="flex items-center mb-2">
                                <i class="ph-fill ph-warning text-xl mr-3 text-red-500"></i>
                                <strong>Errores de validación:</strong>
                            </div>
                            <ul class="list-disc list-inside ml-8 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')

                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
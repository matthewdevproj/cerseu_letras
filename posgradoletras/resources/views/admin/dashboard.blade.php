@extends('admin.layout.app')

@section('title', 'Dashboard General')

@push('styles')
    <style>
        :root {
            --primary-color: #761e23;
            --accent-color: #d4af37;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-color), var(--primary-color));
            opacity: 0;
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.15);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        .quick-action {
            transition: var(--transition);
        }

        .quick-action:hover {
            transform: translateY(-2px);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse-slow {
            animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
@endpush

@section('content')
    <div class="space-y-8">

        <!-- Welcome Card -->
        <div class="bg-gradient-to-r from-brand-red to-brand-red/90 rounded-2xl shadow-xl p-8 relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-white/20 backdrop-blur rounded-full flex items-center justify-center">
                        <i class="ph ph-hand-waving text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-serif font-bold text-white">Bienvenido, {{ auth()->user()->name }}</h1>
                        <p class="text-white/80 mt-1">Panel de gestión de contenidos</p>
                    </div>
                </div>
                <p class="text-white/70 max-w-2xl">
                    Unidad de Posgrado · Facultad de Letras y Ciencias Humanas · UNMSM
                </p>
            </div>
            <!-- Decorative elements -->
            <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute right-20 bottom-0 w-32 h-32 bg-brand-gold/20 rounded-full translate-y-1/2"></div>
        </div>

        <!-- Stats Grid (5 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

            <!-- Card 1: Total Programas -->
            <div class="stat-card p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Programas</p>
                        <p class="text-4xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_programas'] }}</p>
                        <div class="flex items-center mt-3 gap-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i class="ph-fill ph-check-circle mr-1"></i>
                                {{ $stats['programas_activos'] }} activos
                            </span>
                        </div>
                    </div>
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-red-200">
                        <i class="ph-fill ph-graduation-cap text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Maestrías -->
            <div class="stat-card p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Maestrías</p>
                        <p class="text-4xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_maestrias'] }}</p>
                        <div class="flex items-center mt-3">
                            <span class="text-xs text-gray-500">Oferta Académica</span>
                        </div>
                    </div>
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-200">
                        <i class="ph-fill ph-scroll text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Doctorados -->
            <div class="stat-card p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Doctorados</p>
                        <p class="text-4xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_doctorados'] }}</p>
                        <div class="flex items-center mt-3">
                            <span class="text-xs text-gray-500">Investigación</span>
                        </div>
                    </div>
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="ph-fill ph-medal text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4: Docentes -->
            <div class="stat-card p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Plana Docente</p>
                        <p class="text-4xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_docentes'] }}</p>
                        <div class="flex items-center mt-3 gap-2">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                <i class="ph-fill ph-users mr-1"></i>
                                {{ $stats['docentes_activos'] }} activos
                            </span>
                        </div>
                    </div>
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-orange-400 to-orange-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-orange-200">
                        <i class="ph-fill ph-chalkboard-teacher text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 5: Directorio -->
            <div class="stat-card p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Directorio</p>
                        <p class="text-4xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_directorio'] }}</p>
                        <div class="flex items-center mt-3">
                            <span class="text-xs text-gray-500">Personal Administrativo</span>
                        </div>
                    </div>
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-purple-200">
                        <i class="ph-fill ph-address-book text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Testimonios Section -->
            <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="ph ph-chat-text text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-serif font-bold text-gray-900">Testimonios</h2>
                                <p class="text-xs text-gray-500">Estado actual</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center py-6">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-green-100 to-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl font-bold text-green-600">{{ $stats['testimonios_publicados'] }}</span>
                        </div>
                        <p class="text-gray-600 font-medium">Testimonios Publicados</p>
                        <p class="text-sm text-gray-400 mt-1">De {{ $stats['total_testimonios'] }} testimonios en total</p>
                    </div>
                    <a href="{{ route('admin.testimonios.index') }}"
                        class="block w-full text-center py-3 bg-gray-50 hover:bg-gray-100 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Ver todos los testimonios <i class="ph ph-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-brand-gold/20 rounded-lg flex items-center justify-center">
                            <i class="ph ph-lightning text-brand-gold text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-serif font-bold text-gray-900">Acciones Rápidas</h2>
                            <p class="text-xs text-gray-500">Atajos frecuentes</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Action 1: Nuevo Programa -->
                        <a href="{{ route('admin.programas.create') }}"
                            class="quick-action group flex items-center p-4 bg-gradient-to-br from-red-50 to-white border border-red-100 rounded-xl hover:shadow-lg hover:border-red-200">
                            <div
                                class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-red-200 transition-colors">
                                <i class="ph ph-plus-circle text-2xl text-brand-red"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">Nuevo Programa</h3>
                                <p class="text-xs text-gray-500">Crear maestría o doctorado</p>
                            </div>
                            <i
                                class="ph-bold ph-arrow-right text-gray-300 group-hover:text-brand-red group-hover:translate-x-1 transition-all"></i>
                        </a>

                        <!-- Action 2: Nuevo Docente -->
                        <a href="{{ route('admin.docentes.create') }}"
                            class="quick-action group flex items-center p-4 bg-gradient-to-br from-amber-50 to-white border border-amber-100 rounded-xl hover:shadow-lg hover:border-amber-200">
                            <div
                                class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-amber-200 transition-colors">
                                <i class="ph ph-user-plus text-2xl text-brand-gold"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">Nuevo Docente</h3>
                                <p class="text-xs text-gray-500">Registrar profesor en plana</p>
                            </div>
                            <i
                                class="ph-bold ph-arrow-right text-gray-300 group-hover:text-brand-gold group-hover:translate-x-1 transition-all"></i>
                        </a>

                        <!-- Action 3: Nuevo Testimonio -->
                        <a href="{{ route('admin.testimonios.create') }}"
                            class="quick-action group flex items-center p-4 bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-xl hover:shadow-lg hover:border-blue-200">
                            <div
                                class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-blue-200 transition-colors">
                                <i class="ph ph-quotes text-2xl text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">Nuevo Testimonio</h3>
                                <p class="text-xs text-gray-500">Agregar experiencia de alumno</p>
                            </div>
                            <i
                                class="ph-bold ph-arrow-right text-gray-300 group-hover:text-blue-600 group-hover:translate-x-1 transition-all"></i>
                        </a>

                        <!-- Action 4: Nuevo Personal Directorio -->
                        <a href="{{ route('admin.directorio.create') }}"
                            class="quick-action group flex items-center p-4 bg-gradient-to-br from-purple-50 to-white border border-purple-100 rounded-xl hover:shadow-lg hover:border-purple-200">
                            <div
                                class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4 group-hover:bg-purple-200 transition-colors">
                                <i class="ph ph-address-book text-2xl text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-900">Nuevo Personal</h3>
                                <p class="text-xs text-gray-500">Agregar al directorio</p>
                            </div>
                            <i
                                class="ph-bold ph-arrow-right text-gray-300 group-hover:text-purple-600 group-hover:translate-x-1 transition-all"></i>
                        </a>

                    </div>
                </div>
            </div>

        </div>

        <!-- Management Links -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('admin.programas.index') }}"
                class="flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-brand-red/30 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center group-hover:bg-red-100 transition-colors">
                    <i class="ph ph-books text-brand-red"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Programas</p>
                    <p class="text-xs text-gray-400">Gestionar</p>
                </div>
            </a>
            <a href="{{ route('admin.docentes.index') }}"
                class="flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-brand-gold/30 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center group-hover:bg-amber-100 transition-colors">
                    <i class="ph ph-chalkboard-teacher text-brand-gold"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Docentes</p>
                    <p class="text-xs text-gray-400">Gestionar</p>
                </div>
            </a>
            <a href="{{ route('admin.testimonios.index') }}"
                class="flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-blue-300 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <i class="ph ph-chat-text text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Testimonios</p>
                    <p class="text-xs text-gray-400">Gestionar</p>
                </div>
            </a>
            <a href="{{ route('admin.directorio.index') }}"
                class="flex items-center gap-3 p-4 bg-white rounded-xl border border-gray-100 hover:border-purple-300 hover:shadow-md transition-all group">
                <div
                    class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <i class="ph ph-address-book text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Directorio</p>
                    <p class="text-xs text-gray-400">Gestionar</p>
                </div>
            </a>
        </div>

    </div>
@endsection
@extends('admin.layout.app')

@section('title', 'Panel de Control')
@section('subtitle', 'Estadísticas de la plataforma')

@section('content')
    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="stat-card stat-brand bg-white rounded-xl p-6 shadow-sm hover:-translate-y-1 transition-transform">
            <div class="stat-icon w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-graduation-cap text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 font-serif">{{ $stats['total_programas'] }}</h2>
            <p class="text-sm text-gray-500 mt-1">Programas publicados</p>
        </div>

        <div class="stat-card stat-accent bg-white rounded-xl p-6 shadow-sm hover:-translate-y-1 transition-transform">
            <div class="stat-icon w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-chalkboard-teacher text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 font-serif">{{ $stats['total_docentes'] }}</h2>
            <p class="text-sm text-gray-500 mt-1">Plana Docente</p>
        </div>

        <div class="stat-card stat-dark bg-white rounded-xl p-6 shadow-sm hover:-translate-y-1 transition-transform">
            <div class="stat-icon w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-quote-left text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 font-serif">{{ $stats['testimonios_publicados'] }}</h2>
            <p class="text-sm text-gray-500 mt-1">Testimonios activos</p>
        </div>

        <div class="stat-card stat-brand bg-white rounded-xl p-6 shadow-sm hover:-translate-y-1 transition-transform">
            <div class="stat-icon w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-address-book text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 font-serif">{{ $stats['total_directorio'] }}</h2>
            <p class="text-sm text-gray-500 mt-1">Personal Directorio</p>
        </div>

        <div class="stat-card stat-accent bg-white rounded-xl p-6 shadow-sm hover:-translate-y-1 transition-transform">
            <div class="stat-icon w-12 h-12 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-calendar-day text-xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 font-serif">{{ $stats['eventos_activos'] }}</h2>
            <p class="text-sm text-gray-500 mt-1">Eventos activos</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Acciones Rápidas (2/3) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm h-full">
                <div class="p-6 border-b border-gray-100">
                    <h5 class="font-bold text-lg text-[#761e23] flex items-center gap-2">
                        <i class="fas fa-rocket"></i>
                        Acciones Rápidas
                    </h5>
                    <p class="text-sm text-gray-500">Accesos directos a las funciones más usadas</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- Programas --}}
                        <div class="flex flex-col">
                            <a href="{{ route('admin.programas.index') }}"
                                class="quick-action-card flex-1 p-5 rounded-xl border border-gray-100 text-center mb-3 transition-all hover:shadow-lg">
                                <div
                                    class="action-icon-circle w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-[#761e23] transition-all">
                                    <i class="fas fa-graduation-cap text-xl"></i>
                                </div>
                                <h6 class="font-bold text-gray-800">Programas</h6>
                                <p class="text-xs text-gray-500">Maestrías, Doctorados y Diplomados</p>
                            </a>
                            <a href="{{ route('admin.programas.create') }}"
                                class="btn-brand-outline text-center py-2 px-4 rounded-lg border text-sm font-semibold transition-colors">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </a>
                        </div>

                        {{-- Docentes --}}
                        <div class="flex flex-col">
                            <a href="{{ route('admin.docentes.index') }}"
                                class="quick-action-card flex-1 p-5 rounded-xl border border-gray-100 text-center mb-3 transition-all hover:shadow-lg">
                                <div
                                    class="action-icon-circle w-14 h-14 rounded-full bg-[#d4a017]/10 flex items-center justify-center mx-auto mb-3 text-[#d4a017] transition-all">
                                    <i class="fas fa-chalkboard-teacher text-xl"></i>
                                </div>
                                <h6 class="font-bold text-gray-800">Docentes</h6>
                                <p class="text-xs text-gray-500">Plana académica</p>
                            </a>
                            <a href="{{ route('admin.docentes.create') }}"
                                class="btn-accent-outline text-center py-2 px-4 rounded-lg border text-sm font-semibold transition-colors">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </a>
                        </div>

                        {{-- Testimonios --}}
                        <div class="flex flex-col">
                            <a href="{{ route('admin.testimonios.index') }}"
                                class="quick-action-card flex-1 p-5 rounded-xl border border-gray-100 text-center mb-3 transition-all hover:shadow-lg">
                                <div
                                    class="action-icon-circle w-14 h-14 rounded-full bg-[#5a161a]/10 flex items-center justify-center mx-auto mb-3 text-[#5a161a] transition-all">
                                    <i class="fas fa-comments text-xl"></i>
                                </div>
                                <h6 class="font-bold text-gray-800">Testimonios</h6>
                                <p class="text-xs text-gray-500">Experiencias egresados</p>
                            </a>
                            <a href="{{ route('admin.testimonios.create') }}"
                                class="btn-brand-outline text-center py-2 px-4 rounded-lg border text-sm font-semibold transition-colors">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </a>
                        </div>

                        {{-- Directorio --}}
                        <div class="flex flex-col">
                            <a href="{{ route('admin.directorio.index') }}"
                                class="quick-action-card flex-1 p-5 rounded-xl border border-gray-100 text-center mb-3 transition-all hover:shadow-lg">
                                <div
                                    class="action-icon-circle w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-[#761e23] transition-all">
                                    <i class="fas fa-address-book text-xl"></i>
                                </div>
                                <h6 class="font-bold text-gray-800">Directorio</h6>
                                <p class="text-xs text-gray-500">Personal administrativo</p>
                            </a>
                            <a href="{{ route('admin.directorio.create') }}"
                                class="btn-brand-outline text-center py-2 px-4 rounded-lg border text-sm font-semibold transition-colors">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </a>
                        </div>

                        {{-- Configuración --}}
                        <div class="flex flex-col">
                            <a href="{{ route('admin.settings.index') }}"
                                class="quick-action-card flex-1 p-5 rounded-xl border border-gray-100 text-center mb-3 transition-all hover:shadow-lg">
                                <div
                                    class="action-icon-circle w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3 text-gray-500 transition-all">
                                    <i class="fas fa-cog text-xl"></i>
                                </div>
                                <h6 class="font-bold text-gray-800">Configuración</h6>
                                <p class="text-xs text-gray-500">Ajustes del sitio</p>
                            </a>
                            <a href="{{ route('admin.settings.index') }}"
                                class="text-center py-2 px-4 rounded-lg border border-gray-300 text-gray-500 text-sm font-semibold hover:bg-gray-50 transition-colors">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                        </div>

                        {{-- Eventos --}}
                        <div class="flex flex-col">
                            <a href="{{ route('admin.eventos.index') }}"
                                class="quick-action-card flex-1 p-5 rounded-xl border border-gray-100 text-center mb-3 transition-all hover:shadow-lg">
                                <div
                                    class="action-icon-circle w-14 h-14 rounded-full bg-[#d4a017]/10 flex items-center justify-center mx-auto mb-3 text-[#d4a017] transition-all">
                                    <i class="fas fa-calendar-day text-xl"></i>
                                </div>
                                <h6 class="font-bold text-gray-800">Eventos</h6>
                                <p class="text-xs text-gray-500">Actividades y afiches</p>
                            </a>
                            <a href="{{ route('admin.eventos.create') }}"
                                class="btn-accent-outline text-center py-2 px-4 rounded-lg border text-sm font-semibold transition-colors">
                                <i class="fas fa-plus mr-1"></i> Nuevo
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar (1/3) --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm h-full flex flex-col">
                <div class="p-4 border-b border-gray-100">
                    <h6 class="font-bold text-xs text-gray-500 uppercase tracking-wider">Accesos Adicionales</h6>
                </div>
                <div class="p-4 flex-1">
                    <div class="space-y-2">
                        <a href="{{ route('admin.testimonios.index') }}"
                            class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-comment-dots text-gray-400"></i>
                            <div class="flex-1">
                                <h6 class="font-bold text-sm text-gray-800">Testimonios</h6>
                                <p class="text-xs text-gray-500">Experiencias de egresados</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </a>

                        <a href="{{ route('admin.directorio.index') }}"
                            class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-address-book text-gray-400"></i>
                            <div class="flex-1">
                                <h6 class="font-bold text-sm text-gray-800">Directorio</h6>
                                <p class="text-xs text-gray-500">Personal administrativo</p>
                            </div>
                            <i class="fas fa-chevron-right text-gray-300 text-xs"></i>
                        </a>

                        <a href="{{ route('home') }}" target="_blank"
                            class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 hover:bg-gray-100 transition-colors">
                            <i class="fas fa-globe text-gray-400"></i>
                            <div class="flex-1">
                                <h6 class="font-bold text-sm text-gray-800">Ver Sitio Web</h6>
                                <p class="text-xs text-gray-500">Abrir página pública</p>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-300 text-xs"></i>
                        </a>
                    </div>

                    <hr class="my-6 border-gray-200">

                    <h6 class="font-bold text-xs text-gray-500 uppercase tracking-wider mb-4">
                        Resumen Académico
                    </h6>

                    <div class="p-3 rounded-lg mb-3 bg-[#761e23]/5 border-l-4 border-[#761e23]">
                        <p class="text-xs font-bold text-[#761e23]">Maestrías</p>
                        <p class="text-sm text-gray-600">{{ $stats['total_maestrias'] }} programas</p>
                    </div>

                    <div class="p-3 rounded-lg mb-3 bg-[#d4a017]/10 border-l-4 border-[#d4a017]">
                        <p class="text-xs font-bold text-gray-800">Doctorados</p>
                        <p class="text-sm text-gray-600">{{ $stats['total_doctorados'] }} programas</p>
                    </div>

                    <div class="p-3 rounded-lg bg-amber-50 border-l-4 border-amber-600">
                        <p class="text-xs font-bold text-amber-700">Diplomados</p>
                        <p class="text-sm text-gray-600">{{ $stats['total_diplomados'] }} programas</p>
                    </div>
                </div>
                <div class="p-4 text-center border-t border-gray-100">
                    <p class="text-xs text-gray-400">Posgrado Letras Admin v1.0</p>
                </div>
            </div>
        </div>
    </div>
@endsection
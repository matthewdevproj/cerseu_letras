@extends('admin.layout.app')

@section('title', 'Dashboard General')

@section('content')
    <div class="space-y-8">

        <!-- Welcome Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <div class="relative z-10">
                <h1 class="text-2xl font-serif font-bold text-gray-900">Bienvenido, {{ auth()->user()->name }}</h1>
                <p class="text-gray-600 mt-1 max-w-2xl">Panel de gestión de contenidos para la Unidad de Posgrado de la
                    Facultad de Letras y Ciencias Humanas.</p>
            </div>
            <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-red-50 to-transparent opacity-50"></div>
        </div>

        <!-- Stats Grid (4 Columns) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            <!-- Card 1: Total Programas -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Total Programas</p>
                        <p class="text-3xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_programas'] }}</p>
                        <div class="flex items-center mt-2">
                            <i class="ph-fill ph-check-circle text-green-500 mr-1"></i>
                            <span class="text-xs text-green-600 font-medium">{{ $stats['programas_activos'] }}
                                activos</span>
                        </div>
                    </div>
                    <div class="bg-red-50 p-3 rounded-lg text-brand-red">
                        <i class="ph-fill ph-books text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 2: Maestrías -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Maestrías</p>
                        <p class="text-3xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_maestrias'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-400">Oferta Académica</span>
                        </div>
                    </div>
                    <div class="bg-yellow-50 p-3 rounded-lg text-brand-gold">
                        <i class="ph-fill ph-scroll text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 3: Doctorados -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Doctorados</p>
                        <p class="text-3xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_doctorados'] }}</p>
                        <div class="flex items-center mt-2">
                            <span class="text-xs text-gray-400">Investigación</span>
                        </div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg text-brand-navy">
                        <i class="ph-fill ph-medal text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Card 4: Docentes -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-500">Plana Docente</p>
                        <p class="text-3xl font-serif font-bold text-gray-900 mt-2">{{ $stats['total_docentes'] }}</p>
                        <div class="flex items-center mt-2">
                            <i class="ph-fill ph-users text-brand-brown mr-1"></i>
                            <span class="text-xs text-gray-500">{{ $stats['docentes_activos'] }} activos</span>
                        </div>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-lg text-brand-brown">
                        <i class="ph-fill ph-chalkboard-teacher text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Estado de Testimonios -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-serif font-bold text-gray-900">Estado de Testimonios</h2>
                <span class="text-xs font-medium text-gray-500 bg-white border border-gray-200 px-2 py-1 rounded">Últimos 30
                    días</span>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-center py-8 text-center">
                    <div>
                        <div
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3 text-gray-400">
                            <i class="ph ph-chat-text text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">{{ $stats['testimonios_publicados'] }} testimonios publicados
                            actualmente en el portal.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Grid (Sin degradados) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Action 1: Programas -->
            <a href="{{ route('admin.programas.create') }}"
                class="group bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-red-200 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div
                            class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center mb-4 group-hover:bg-red-100 transition-colors">
                            <i class="ph ph-plus-circle text-2xl text-brand-red"></i>
                        </div>
                        <h3 class="text-lg font-bold font-serif text-gray-900 mb-1">Nuevo Programa</h3>
                        <p class="text-gray-500 text-sm">Crear maestría o doctorado</p>
                    </div>
                    <i
                        class="ph-bold ph-arrow-right text-xl text-gray-300 group-hover:text-brand-red group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Action 2: Docentes -->
            <a href="{{ route('admin.docentes.create') }}"
                class="group bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-yellow-200 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div
                            class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center mb-4 group-hover:bg-yellow-100 transition-colors">
                            <i class="ph ph-user-plus text-2xl text-brand-gold"></i>
                        </div>
                        <h3 class="text-lg font-bold font-serif text-gray-900 mb-1">Nuevo Docente</h3>
                        <p class="text-gray-500 text-sm">Registrar profesor en plana</p>
                    </div>
                    <i
                        class="ph-bold ph-arrow-right text-xl text-gray-300 group-hover:text-brand-gold group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

            <!-- Action 3: Testimonios -->
            <a href="{{ route('admin.testimonios.create') }}"
                class="group bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-blue-200 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors">
                            <i class="ph ph-quotes text-2xl text-brand-navy"></i>
                        </div>
                        <h3 class="text-lg font-bold font-serif text-gray-900 mb-1">Nuevo Testimonio</h3>
                        <p class="text-gray-500 text-sm">Agregar experiencia de alumno</p>
                    </div>
                    <i
                        class="ph-bold ph-arrow-right text-xl text-gray-300 group-hover:text-brand-navy group-hover:translate-x-1 transition-all"></i>
                </div>
            </a>

        </div>

    </div>
@endsection
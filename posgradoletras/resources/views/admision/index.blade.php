@extends('layouts.public')

@section('title', 'Admisión 2025 - Posgrado Letras UNMSM')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <h2 class="text-2xl md:text-3xl font-bold text-unmsm-guinda mb-6 border-b-2 border-unmsm-dorado/30 pb-2 font-serif">
            Proceso de Admisión 2025
        </h2>

        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Columna Principal -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Pasos para Postular -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">Pasos para postular</h3>
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda/10 text-unmsm-guinda flex items-center justify-center font-bold shrink-0">
                                1</div>
                            <div>
                                <h4 class="font-bold text-gray-800">Pago de Inscripción</h4>
                                <p class="text-sm text-gray-600">Generar ticket en SanMarket y realizar el pago en BCP o
                                    Yape.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda/10 text-unmsm-guinda flex items-center justify-center font-bold shrink-0">
                                2</div>
                            <div>
                                <h4 class="font-bold text-gray-800">Registro de Voucher</h4>
                                <p class="text-sm text-gray-600">Subir comprobante de pago a la plataforma virtual.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div
                                class="w-8 h-8 rounded-full bg-unmsm-guinda/10 text-unmsm-guinda flex items-center justify-center font-bold shrink-0">
                                3</div>
                            <div>
                                <h4 class="font-bold text-gray-800">Envío de Expediente</h4>
                                <p class="text-sm text-gray-600">Enviar PDF único con Anteproyecto y documentos al correo
                                    institucional.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Costos -->
                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="font-bold text-lg text-unmsm-guinda mb-4 font-serif">Costos de Admisión</h3>
                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-unmsm-guinda text-white p-3 text-center">
                                <h4 class="font-bold text-sm">Maestría</h4>
                            </div>
                            <div class="p-4 text-center">
                                <p class="text-xs text-gray-500 mb-1">Egresados UNMSM</p>
                                <div class="text-2xl font-bold text-unmsm-guinda">S/ 350<span
                                        class="text-sm font-normal text-gray-500">.00</span></div>
                                <p class="text-xs text-gray-500 mt-2">Externos</p>
                                <div class="text-2xl font-bold text-unmsm-guinda">S/ 450<span
                                        class="text-sm font-normal text-gray-500">.00</span></div>
                            </div>
                        </div>

                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="bg-gray-800 text-white p-3 text-center">
                                <h4 class="font-bold text-sm">Doctorado</h4>
                            </div>
                            <div class="p-4 text-center">
                                <p class="text-xs text-gray-500 mb-1">Egresados UNMSM</p>
                                <div class="text-2xl font-bold text-unmsm-guinda">S/ 400<span
                                        class="text-sm font-normal text-gray-500">.00</span></div>
                                <p class="text-xs text-gray-500 mt-2">Externos</p>
                                <div class="text-2xl font-bold text-unmsm-guinda">S/ 500<span
                                        class="text-sm font-normal text-gray-500">.00</span></div>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-4 bg-unmsm-dorado/10 border border-unmsm-dorado/30 rounded-lg p-4 flex gap-4 items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-unmsm-dorado shrink-0 mt-1 h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <h4 class="font-bold text-gray-800">Costo por Crédito</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                El valor por crédito es de <strong>S/ 120.00</strong>. Aproximadamente S/ 1,680 por
                                semestre.
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="card bg-unmsm-guinda text-white border-none sticky top-24">
                    <h3 class="font-bold mb-4 font-serif text-lg">Contacto Admisión</h3>
                    <p class="text-sm mb-4 text-white/80">¿Dudas sobre el proceso? Contáctanos.</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-dorado" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            {{ isset($contacto['email']) ? $contacto['email'] : 'posgrado-letras@unmsm.edu.pe' }}
                        </div>
                        <div class="flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-unmsm-dorado" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {{ isset($contacto['telefono']) ? $contacto['telefono'] : '982 085 037' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

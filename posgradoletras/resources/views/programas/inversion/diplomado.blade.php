{{-- Inversión económica – DIPLOMADOS (parametrizable por programa) --}}

@php
    $emailContacto = \App\Models\SiteSetting::contacto('admision');
    $inversion = $programa->inversion_economica;
@endphp

@if(!$inversion)
    <p class="text-gray-600">
        La información de inversión económica de este diplomado se encuentra en actualización.
        Para más detalles, comuníquese con la Unidad de Posgrado.
    </p>
@else
    <div class="space-y-8">

        {{-- Derecho de inscripción --}}
        @if(!empty($inversion['derecho_inscripcion']))
            <section class="space-y-4">
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                    <x-fas-user-plus class="text-unmsm-dorado" />
                    Admisión
                </h3>

                <p class="text-sm text-gray-700">
                    El pago por derecho de inscripción al diplomado es el siguiente:
                </p>

                <div class="grid sm:grid-cols-2 gap-4">
                    @if(isset($inversion['derecho_inscripcion']['bachiller_unmsm']))
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="bg-unmsm-guinda text-white text-center py-5">
                                <span class="text-3xl font-bold">S/&nbsp;{{ number_format($inversion['derecho_inscripcion']['bachiller_unmsm'], 0) }}</span>
                            </div>
                            <div class="bg-white text-center py-4 px-4">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Bachiller UNMSM, personal administrativo UNMSM, docentes de universidades nacionales y
                                    magisterio nacional
                                </p>
                            </div>
                        </div>
                    @endif
                    @if(isset($inversion['derecho_inscripcion']['otras_universidades']))
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <div class="bg-unmsm-guinda text-white text-center py-5">
                                <span class="text-3xl font-bold">S/&nbsp;{{ number_format($inversion['derecho_inscripcion']['otras_universidades'], 0) }}</span>
                            </div>
                            <div class="bg-white text-center py-4 px-4">
                                <p class="text-sm text-gray-600 leading-relaxed">
                                    Bachiller de universidad nacional o particular
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endif

        {{-- Costo total del programa --}}
        @if(!empty($inversion['costo_total']))
            <section class="space-y-4">
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                    <x-fas-calculator class="text-unmsm-dorado" />
                    Costo total del diplomado
                </h3>

                <div class="flex justify-center">
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm inline-block min-w-[220px]">
                        <div class="bg-unmsm-guinda text-white text-center py-5 px-8">
                            <span class="text-3xl font-bold">S/&nbsp;{{ number_format($inversion['costo_total'], 0) }}</span>
                        </div>
                        <div class="bg-white text-center py-3 px-4">
                            <p class="text-sm text-gray-600 font-medium">Costo total (*)</p>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Cuotas --}}
        @if(!empty($inversion['cuotas']) && is_array($inversion['cuotas']))
            <section class="space-y-6">
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                    <x-fas-calendar-alt class="text-unmsm-dorado" />
                    Cuotas de pago
                </h3>

                <div class="grid md:grid-cols-2 gap-6">
                    @foreach($inversion['cuotas'] as $cuota)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-unmsm-guinda text-white px-4 py-3">
                                <h4 class="font-semibold text-center">Cuota {{ $cuota['numero'] ?? $loop->iteration }}</h4>
                            </div>
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-gray-100">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-gray-600">Monto</td>
                                        <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                            S/&nbsp;{{ number_format($cuota['monto'] ?? 0, 0) }}</td>
                                    </tr>
                                    @if(!empty($cuota['fecha']))
                                        <tr class="bg-unmsm-guinda/5">
                                            <td class="px-4 py-3 text-gray-700 font-medium">Fecha de pago</td>
                                            <td class="px-4 py-3 text-right font-bold text-unmsm-guinda">
                                                {{ $cuota['fecha'] }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Modalidades de pago / descuentos / observaciones --}}
        @if(!empty($inversion['modalidades_pago']) || !empty($inversion['descuentos']) || !empty($inversion['observaciones']))
            <section class="bg-green-50 border border-green-200 rounded-xl p-4 md:p-5 space-y-2">
                <h3 class="text-sm font-semibold text-green-800 flex items-center gap-2">
                    <x-fas-percentage class="text-green-600" />
                    Condiciones de pago
                </h3>
                <ul class="list-disc list-inside text-sm text-green-900 space-y-1 text-justify">
                    @if(!empty($inversion['modalidades_pago']))
                        <li>
                            <span class="font-semibold">Modalidades de pago:</span>
                            {{ implode(', ', (array) $inversion['modalidades_pago']) }}.
                        </li>
                    @endif
                    @if(!empty($inversion['descuentos']))
                        <li>{{ $inversion['descuentos'] }}</li>
                    @endif
                    @if(!empty($inversion['observaciones']))
                        <li>{{ $inversion['observaciones'] }}</li>
                    @endif
                </ul>
            </section>
        @endif

        {{-- Pago del diploma --}}
        @if(!empty($inversion['costo_diploma']))
            <section class="space-y-4">
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                    <x-fas-certificate class="text-unmsm-dorado" />
                    Pago de diploma
                </h3>
                <p class="text-sm text-gray-700">
                    El costo total del diploma es de
                    <span class="font-semibold">S/&nbsp;{{ number_format($inversion['costo_diploma'], 0) }}</span>.
                </p>
            </section>
        @endif

        {{-- Informes --}}
        <section class="bg-unmsm-guinda/5 border border-unmsm-guinda/20 rounded-xl p-4 md:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                        <x-fas-info-circle class="text-unmsm-dorado" />
                        Informes
                    </h3>
                    <p class="text-sm text-gray-700">
                        Para mayor detalle sobre pagos, cronogramas y matrícula,
                        comuníquese con la Unidad de Posgrado.
                    </p>
                </div>
                <div class="space-y-1 text-sm text-right md:text-left">
                    <p>
                        <span class="font-semibold text-unmsm-guinda">Correo:&nbsp;</span>
                        <a href="mailto:{{ $emailContacto }}"
                            class="text-gray-800 underline decoration-unmsm-guinda/60 decoration-2 underline-offset-2">
                            {{ $emailContacto }}
                        </a>
                    </p>
                    <p>
                        <span class="font-semibold text-unmsm-guinda">Teléfono / WhatsApp:&nbsp;</span>
                        <a href="{{ \App\Models\SiteSetting::contacto('whatsapp') }}" target="_blank" rel="noopener noreferrer"
                            rel="noopener noreferrer"
                            class="text-gray-800 underline decoration-unmsm-guinda/60 decoration-2 underline-offset-2">
                            {{ \App\Models\SiteSetting::contacto('telefono') }}
                        </a>
                    </p>
                </div>
            </div>
        </section>

    </div>
@endif

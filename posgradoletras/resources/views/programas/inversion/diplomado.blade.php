{{-- Inversión económica – DIPLOMADOS (parametrizable por programa)

     Orden de los bloques fijado por Posgrado (Obs. N.º 2):
       1. Costo total del diplomado
       2. Modalidades de pago
       3. Pago de diploma
       4. Condiciones de pago
       5. Informes
     El derecho de inscripción se mantiene delante porque es un pago previo a
     la matrícula y no forma parte de los derechos de enseñanza. --}}

@php
    $emailContacto = \App\Models\SiteSetting::contacto('admision');
    $inversion = $programa->inversion_economica;
    $modalidades = $programa->modalidades_de_pago;

    // Las modalidades ya estructuradas sustituyen a la lista de texto libre
    // anterior; esta solo se sigue mostrando en «Condiciones de pago» mientras
    // el programa no tenga cargadas las nuevas.
    $modalidadesTexto = $modalidades === [] ? (array) ($inversion['modalidades_pago'] ?? []) : [];
    $hayCondiciones = $modalidadesTexto !== [] || !empty($inversion['descuentos']) || !empty($inversion['observaciones']);
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

        {{-- 1. Costo total del diplomado --}}
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

                <p class="text-sm text-gray-600 text-center">
                    (*) Incluye la totalidad de los derechos de enseñanza y el costo del diploma.
                </p>
            </section>
        @endif

        {{-- 2. Modalidades de pago --}}
        @if($modalidades !== [])
            <section class="space-y-4">
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                    <x-fas-calendar-alt class="text-unmsm-dorado" />
                    Modalidades de pago
                </h3>

                <p class="text-sm text-gray-700">
                    Los derechos de enseñanza pueden abonarse en cualquiera de las siguientes modalidades:
                </p>

                <div class="space-y-5">
                    @foreach($modalidades as $modalidad)
                        <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                            <div class="bg-unmsm-guinda text-white px-4 py-3">
                                <h4 class="font-semibold text-center">{{ $modalidad['nombre'] }}</h4>
                            </div>

                            {{-- Flexible por diseño: una modalidad puede tener una
                                 cuota o varias, y cada bloque se reparte el ancho
                                 disponible sin depender de un número fijo. --}}
                            <div class="flex flex-wrap gap-4 bg-white p-4">
                                @foreach($modalidad['cuotas'] as $cuota)
                                    <div class="flex-1 min-w-[200px] border border-gray-200 rounded-lg overflow-hidden">
                                        <p class="bg-gray-50 border-b border-gray-200 px-3 py-2 text-center text-xs font-bold uppercase tracking-wide text-gray-600">
                                            {{ $cuota['etiqueta'] }}
                                        </p>

                                        @if($cuota['monto'] !== null)
                                            <p class="px-3 pt-4 text-center text-2xl font-bold text-unmsm-guinda">
                                                S/&nbsp;{{ number_format($cuota['monto'], 0) }}
                                            </p>
                                        @endif

                                        {{-- La fecha va debajo del monto, tal como
                                             se pidió en el documento. --}}
                                        @if($cuota['fecha'])
                                            <p class="px-3 pb-4 pt-2 text-center text-sm text-gray-700">
                                                <span class="block text-xs uppercase tracking-wide text-gray-500">Fecha de pago</span>
                                                <span class="font-semibold">{{ $cuota['fecha'] }}</span>
                                            </p>
                                        @else
                                            <div class="pb-4"></div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- 3. Pago de diploma --}}
        @if(!empty($inversion['costo_diploma']))
            <section class="space-y-4">
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
                    <x-fas-certificate class="text-unmsm-dorado" />
                    Pago de diploma
                </h3>

                <div class="flex justify-center">
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm inline-block min-w-[220px]">
                        <div class="bg-unmsm-guinda text-white text-center py-5 px-8">
                            <span class="text-3xl font-bold">S/&nbsp;{{ number_format($inversion['costo_diploma'], 0) }}</span>
                        </div>
                        <div class="bg-white text-center py-3 px-4">
                            <p class="text-sm text-gray-600 font-medium">Costo del diploma</p>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-gray-600 text-center">
                    * El pago por derecho de diploma deberá efectuarse dentro de los cinco días hábiles posteriores
                    a la publicación de las notas finales.
                </p>
            </section>
        @endif

        {{-- 4. Condiciones de pago --}}
        @if($hayCondiciones)
            <section class="bg-green-50 border border-green-200 rounded-xl p-4 md:p-5 space-y-2">
                <h3 class="text-sm font-semibold text-green-800 flex items-center gap-2">
                    <x-fas-percentage class="text-green-600" />
                    Condiciones de pago
                </h3>
                <ul class="list-disc list-inside text-sm text-green-900 space-y-1 text-justify">
                    @if($modalidadesTexto !== [])
                        <li>
                            <span class="font-semibold">Modalidades de pago:</span>
                            {{ implode(', ', $modalidadesTexto) }}.
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

        {{-- 5. Informes --}}
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
                        <a href="{{ \App\Models\SiteSetting::contacto('whatsapp') }}" target="_blank"
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

{{-- Inversión económica – DOCTORADOS --}}

@php
    $emailContacto = \App\Models\SiteSetting::contacto('admision');

    // Importes administrables desde el panel (Programas → Inversión).
    // Se mantienen los nombres de variables que ya usaba el resto de la
    // plantilla para no tocar el marcado.
    $costoPorCredito = $programa->costo_por_credito;
    $filas = $programa->semestres_calculados;

    $semestres = [];
    foreach ($filas as $fila) {
        $semestres[$fila['numero']] = [
            'matricula' => $fila['matricula'],
            'creditos' => $fila['creditos'],
            'costoSemestre' => $fila['costo_semestre'],
            'cuotaMensual' => $fila['cuota_mensual'],
        ];
    }

    $costoTotal = $programa->costo_total ?? 0;
    $nombreSemestre = [1 => 'Primer', 2 => 'Segundo', 3 => 'Tercer', 4 => 'Cuarto', 5 => 'Quinto', 6 => 'Sexto'];
@endphp

@if (empty($semestres))
    {{-- Sin importes cargados: se avisa en lugar de mostrar ceros. --}}
    <div class="rounded-xl border border-amber-200 bg-amber-50 p-6 text-sm text-amber-800">
        <p class="font-semibold mb-1">Inversión por definir</p>
        <p>
            Aún no se ha publicado el detalle económico de este programa.
            Escríbenos a <a href="mailto:{{ $emailContacto }}" class="underline font-semibold">{{ $emailContacto }}</a>
            para recibir la información.
        </p>
    </div>
@else

<div class="space-y-8">

    {{-- Admisión / Derecho de inscripción --}}
    <section class="space-y-4">
        <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
            <x-fas-user-plus class="text-unmsm-dorado" />
            Admisión
        </h3>

        <p class="text-sm text-gray-700">
            El pago por derecho de inscripción a los programas es el siguiente:
        </p>

        <div class="grid sm:grid-cols-2 gap-4">
            {{-- Tarjeta 1: Bachiller UNMSM --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="bg-unmsm-guinda text-white text-center py-5">
                    <span class="text-3xl font-bold">S/ 350</span>
                </div>
                <div class="bg-white text-center py-4 px-4">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Bachiller UNMSM, personal administrativo UNMSM, docentes de universidades nacionales y
                        magisterio nacional
                    </p>
                </div>
            </div>

            {{-- Tarjeta 2: Bachiller universidad nacional/particular --}}
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                <div class="bg-unmsm-guinda text-white text-center py-5">
                    <span class="text-3xl font-bold">S/ 450</span>
                </div>
                <div class="bg-white text-center py-4 px-4">
                    <p class="text-sm text-gray-600 leading-relaxed">
                        Bachiller de universidad nacional o particular
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Créditos --}}
    <section class="space-y-4">
        <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
            <x-fas-coins class="text-unmsm-dorado" />
            Créditos
        </h3>

        <p class="text-sm text-gray-700 text-justify">
            Cada curso del programa tiene un valor en créditos. Es de acuerdo al
            <span class="font-semibold">número de créditos matriculados</span> que se determina el pago de un alumno por
            semestre.
        </p>

        <div class="flex justify-center">
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm inline-block min-w-[200px]">
                <div class="bg-unmsm-guinda text-white text-center py-5 px-8">
                    <span class="text-3xl font-bold">S/&nbsp;{{ number_format($costoPorCredito, 0) }}</span>
                </div>
                <div class="bg-white text-center py-3 px-4">
                    <p class="text-sm text-gray-600 font-medium">Costo por crédito (*)</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Costo total del programa --}}
    <section class="space-y-4">
        <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
            <x-fas-calculator class="text-unmsm-dorado" />
            Costo total del programa
        </h3>

        <div class="flex justify-center">
            <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm inline-block min-w-[220px]">
                <div class="bg-unmsm-guinda text-white text-center py-5 px-8">
                    <span class="text-3xl font-bold">S/&nbsp;{{ number_format($costoTotal, 0) }}</span>
                </div>
                <div class="bg-white text-center py-3 px-4">
                    <p class="text-sm text-gray-600 font-medium">Costo total (*)</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Costos por semestre --}}
    <section class="space-y-6">
        <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda flex items-center gap-2">
            <x-fas-calendar-alt class="text-unmsm-dorado" />
            Costos por semestre
        </h3>

        <div class="grid md:grid-cols-2 gap-6">
            @foreach($semestres as $num => $datos)
                <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <div class="bg-unmsm-guinda text-white px-4 py-3">
                        <h4 class="font-semibold text-center">{{ $nombreSemestre[$num] }} semestre</h4>
                    </div>

                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">Matrícula</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    S/&nbsp;{{ number_format($datos['matricula'], 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">N° de créditos</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">{{ $datos['creditos'] }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-600">Costo por semestre</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-900">
                                    S/&nbsp;{{ number_format($datos['costoSemestre'], 0) }}</td>
                            </tr>
                            <tr class="bg-unmsm-guinda/5">
                                <td class="px-4 py-3 text-gray-700 font-medium">Cuota mensual (4 cuotas)</td>
                                <td class="px-4 py-3 text-right font-bold text-unmsm-guinda">
                                    S/&nbsp;{{ number_format($datos['cuotaMensual'], 0) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Nota: cuotas y descuento --}}
    <section class="bg-green-50 border border-green-200 rounded-xl p-4 md:p-5 space-y-2">
        <h3 class="text-sm font-semibold text-green-800">
            Condiciones de pago
        </h3>
        <ul class="list-disc list-inside text-sm text-green-900 space-y-1 text-justify">
            <li>
                El costo por semestre <span class="font-semibold">se divide en cuatro cuotas</span>,
                lo que da el costo mensual.
            </li>
            <li>
                Si realiza el <span class="font-semibold">pago del semestre completo</span> en el primer mes del
                semestre,
                obtendrá un <span class="font-semibold">descuento del 10%</span>.
            </li>
            <li>
                 <span class="font-semibold">(*) El monto final está sujeto a variación de acuerdo con el régimen estudiantil!</span>
            </li>
        </ul>
    </section>

    {{-- Informes (bloque final) --}}
    <section class="bg-unmsm-guinda/5 border border-unmsm-guinda/20 rounded-xl p-4 md:p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h3 class="text-base md:text-lg font-semibold text-unmsm-guinda">
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

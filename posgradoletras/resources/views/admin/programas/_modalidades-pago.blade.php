{{-- Modalidades de pago de los derechos de enseñanza (Obs. N.º 2).

     Compartido por create y edit. Cada modalidad («Pago único», «Pago
     fraccionado»…) lleva sus propias cuotas con monto y fecha; el número de
     cuotas no está limitado a dos. Todo viaja en un único campo JSON,
     `inversion_modalidades`, que el controlador funde con el resto de la
     inversión económica. --}}

<div class="border border-gray-200 rounded-lg p-4 hover:border-brand-azul hover:shadow-sm transition-all"
    x-data="modalidadesPago({{ Illuminate\Support\Js::from($modalidades ?? []) }})">

    <label class="form-label block mb-1">
        <x-fas-calendar-alt class="text-brand-azul mr-1" /> Modalidades de pago (Talleres)
    </label>
    <p class="text-xs text-gray-400 mb-4">
        Se muestran en la ficha debajo del costo total. La fecha aparece bajo el monto de cada cuota.
        Sin modalidades cargadas, el bloque no se muestra.
    </p>

    <input type="hidden" name="inversion_modalidades" :value="payload">

    <div class="space-y-4">
        <template x-for="(modalidad, i) in modalidades" :key="modalidad.uid">
            <div class="border border-gray-200 rounded-lg p-3 bg-gray-50/60">
                <div class="flex items-center gap-2 mb-3">
                    <input type="text" x-model="modalidad.nombre" maxlength="100"
                        class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm"
                        placeholder="Nombre de la modalidad (p. ej. Pago único)">
                    <button type="button" @click="eliminar(i)"
                        class="text-red-500 hover:text-red-700" aria-label="Quitar modalidad">
                        <x-fas-trash />
                    </button>
                </div>

                <div class="space-y-2">
                    <template x-for="(cuota, j) in modalidad.cuotas" :key="j">
                        <div class="flex flex-wrap gap-2 items-center">
                            <input type="text" x-model="cuota.etiqueta" maxlength="60"
                                class="w-40 py-2 px-3 border border-gray-300 rounded-lg text-sm"
                                :placeholder="etiquetaSugerida(modalidad, j)">
                            <input type="number" min="0" x-model="cuota.monto"
                                class="w-32 py-2 px-3 border border-gray-300 rounded-lg text-sm"
                                placeholder="Monto (S/)">
                            <input type="text" x-model="cuota.fecha" maxlength="255"
                                class="flex-1 min-w-[180px] py-2 px-3 border border-gray-300 rounded-lg text-sm"
                                placeholder="Fecha de pago (p. ej. del 16 al 18 de septiembre)">
                            <button type="button" @click="eliminarCuota(modalidad, j)"
                                class="text-red-500 hover:text-red-700" aria-label="Quitar cuota">
                                <x-fas-times />
                            </button>
                        </div>
                    </template>
                </div>

                <button type="button" @click="agregarCuota(modalidad)"
                    class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-gray-400 text-gray-600 rounded-lg hover:bg-gray-100">
                    <x-fas-plus class="mr-1" /> Agregar cuota
                </button>
            </div>
        </template>
    </div>

    <button type="button" @click="agregar()"
        class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-azul text-brand-azul rounded-lg hover:bg-brand-azul hover:text-white transition-all">
        <x-fas-plus class="mr-1" /> Agregar modalidad
    </button>
</div>

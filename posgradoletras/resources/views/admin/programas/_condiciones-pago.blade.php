{{-- Condiciones de pago: lista de puntos administrable.

     Compartido por create y edit. Sustituye a los tres campos sueltos de antes
     (modalidades en texto libre, descuentos y observaciones), que limitaban el
     bloque a tres líneas con un significado fijo cada una. Viaja en un único
     campo JSON, `inversion_condiciones`, que el controlador funde con el resto
     de la inversión económica. --}}

<div class="border border-gray-200 rounded-lg p-4 hover:border-brand-red hover:shadow-sm transition-all"
    x-data="condicionesPago({{ Illuminate\Support\Js::from($condiciones ?? []) }})">

    <label class="form-label block mb-1">
        <x-fas-percentage class="text-brand-red mr-1" /> Condiciones de pago (Diplomados)
    </label>
    <p class="text-xs text-gray-400 mb-4">
        Se muestran como lista en la ficha, después del costo por matrícula.
        Sin condiciones cargadas, el bloque no aparece.
    </p>

    <input type="hidden" name="inversion_condiciones" :value="payload">

    <div class="space-y-2">
        <template x-for="(condicion, i) in condiciones" :key="condicion.uid">
            <div class="flex items-center gap-2">
                <input type="text" x-model="condicion.texto" maxlength="500"
                    class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm"
                    placeholder="Ej: Descuento del 10 % por pago adelantado del íntegro.">
                <button type="button" @click="mover(i, -1)" :disabled="i === 0"
                    class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded hover:bg-gray-200 disabled:opacity-40"
                    aria-label="Subir condición">
                    <x-fas-arrow-up />
                </button>
                <button type="button" @click="mover(i, 1)" :disabled="i === condiciones.length - 1"
                    class="w-8 h-8 flex items-center justify-center bg-gray-100 text-gray-600 rounded hover:bg-gray-200 disabled:opacity-40"
                    aria-label="Bajar condición">
                    <x-fas-arrow-down />
                </button>
                <button type="button" @click="eliminar(i)"
                    class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200"
                    aria-label="Quitar condición">
                    <x-fas-times />
                </button>
            </div>
        </template>
    </div>

    <button type="button" @click="agregar()"
        class="mt-3 inline-flex items-center px-3 py-1.5 text-sm border border-brand-red text-brand-red rounded-lg hover:bg-brand-red hover:text-white transition-all">
        <x-fas-plus class="mr-1" /> Agregar condición
    </button>
</div>

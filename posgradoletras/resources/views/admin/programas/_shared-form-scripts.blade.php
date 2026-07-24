{{-- Helpers JS compartidos entre admin/programas/create.blade.php y admin/programas/edit.blade.php --}}
{{-- Listas JSON (Objetivos, Ingresante, Graduado) --}}
<script>
    function crearItemLista(listId, value = '') {
        var list = document.getElementById(listId);
        if (!list) return;

        var itemId = listId + '-item-' + Date.now();
        var row = document.createElement('div');
        row.className = 'flex items-center gap-2';
        row.id = itemId;
        row.innerHTML = `
            <input type="text" class="flex-1 py-2 px-3 border border-gray-300 rounded-lg text-sm lista-item"
                   placeholder="Escribir item..." value="${value.replace(/"/g, '&quot;')}">
            <button type="button" onclick="eliminarItemLista('${itemId}')"
                class="w-8 h-8 flex items-center justify-center bg-red-100 text-red-600 rounded-lg hover:bg-red-200">
                <x-fas-times />
            </button>
        `;
        list.appendChild(row);
    }

    function eliminarItemLista(itemId) {
        var item = document.getElementById(itemId);
        if (item) item.remove();
    }

    function agregarObjetivo() { crearItemLista('objetivos-list'); }
    function agregarIngresante() { crearItemLista('ingresante-list'); }
    function agregarGraduado() { crearItemLista('graduado-list'); }

    function agregarCuota() {
        var list = document.getElementById('cuotas-list');
        var row = document.createElement('div');
        row.className = 'flex gap-2 items-center cuota-row';
        row.innerHTML =
            '<input type="number" min="0" placeholder="Monto (S/)" class="cuota-monto block w-32 py-2 px-3 border border-gray-300 rounded-lg">' +
            '<input type="text" placeholder="Fecha / condición de pago" class="cuota-fecha block flex-1 py-2 px-3 border border-gray-300 rounded-lg">' +
            '<button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700"><x-fas-trash /></button>';
        list.appendChild(row);
    }

    function recogerInversionEconomica() {
        var bachiller = document.getElementById('inv_derecho_bachiller').value;
        var otras = document.getElementById('inv_derecho_otras').value;
        var costoTotal = document.getElementById('inv_costo_total').value;
        var costoDiploma = document.getElementById('inv_costo_diploma').value;
        var modalidades = document.getElementById('inv_modalidades_pago').value;
        var descuentos = document.getElementById('inv_descuentos').value;
        var observaciones = document.getElementById('inv_observaciones').value;

        var tieneAlgo = bachiller || otras || costoTotal || costoDiploma || modalidades || descuentos || observaciones;

        var cuotas = [];
        document.querySelectorAll('#cuotas-list .cuota-row').forEach(function(row, index) {
            var monto = row.querySelector('.cuota-monto').value;
            var fecha = row.querySelector('.cuota-fecha').value;
            if (monto || fecha) {
                cuotas.push({ numero: index + 1, monto: monto ? parseFloat(monto) : 0, fecha: fecha || null });
                tieneAlgo = true;
            }
        });

        if (!tieneAlgo) return '';

        var data = {
            derecho_inscripcion: (bachiller || otras) ? {
                bachiller_unmsm: bachiller ? parseFloat(bachiller) : null,
                otras_universidades: otras ? parseFloat(otras) : null,
            } : null,
            costo_total: costoTotal ? parseFloat(costoTotal) : null,
            costo_diploma: costoDiploma ? parseFloat(costoDiploma) : null,
            cuotas: cuotas,
            modalidades_pago: modalidades ? modalidades.split(',').map(function(s) { return s.trim(); }).filter(Boolean) : [],
            descuentos: descuentos || null,
            observaciones: observaciones || null,
        };

        return JSON.stringify(data);
    }

    function recogerListaJSON(listId) {
        var items = [];
        document.querySelectorAll('#' + listId + ' .lista-item').forEach(function(input) {
            var val = input.value.trim();
            if (val) items.push(val);
        });
        return JSON.stringify(items);
    }
</script>

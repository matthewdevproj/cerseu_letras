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

    /**
     * Importes y textos de la inversión económica de un diplomado.
     *
     * Las cuotas ya no se recogen aquí: pasaron a «Modalidades de pago», que las
     * agrupa con su nombre y sus fechas y viaja en su propio campo
     * (`inversion_modalidades`), que el controlador funde con este objeto.
     */
    function recogerInversionEconomica() {
        var valor = function(id) {
            var el = document.getElementById(id);
            return el ? el.value.trim() : '';
        };

        var bachiller = valor('inv_derecho_bachiller');
        var otras = valor('inv_derecho_otras');
        var costoTotal = valor('inv_costo_total');
        var costoDiploma = valor('inv_costo_diploma');
        var costoMatricula = valor('inv_costo_matricula');
        var modalidades = valor('inv_modalidades_pago');
        var descuentos = valor('inv_descuentos');
        var observaciones = valor('inv_observaciones');

        if (!(bachiller || otras || costoTotal || costoDiploma || costoMatricula || modalidades || descuentos || observaciones)) {
            return '';
        }

        var data = {
            derecho_inscripcion: (bachiller || otras) ? {
                bachiller_unmsm: bachiller ? parseFloat(bachiller) : null,
                otras_universidades: otras ? parseFloat(otras) : null,
            } : null,
            costo_total: costoTotal ? parseFloat(costoTotal) : null,
            costo_diploma: costoDiploma ? parseFloat(costoDiploma) : null,
            costo_matricula: costoMatricula ? parseFloat(costoMatricula) : null,
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

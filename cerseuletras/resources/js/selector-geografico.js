/**
 * Selector de país y región del formulario de diplomados.
 *
 * Antes eran dos campos de texto libre: llegaban «peru», «Perú », «PE» y
 * regiones mal escritas, imposibles de agrupar después.
 *
 * Las listas las sirve el propio sitio (`/geografia/…`), que a su vez las trae
 * de un servicio externo y las cachea. El navegador del visitante no habla con
 * terceros.
 */

/** Rellena un `<select>` conservando el valor elegido si sigue existiendo. */
export function llenarSelect(select, opciones, { placeholder = '', valor = null } = {}) {
    if (!select) return null;

    const previo = valor ?? select.value;

    select.innerHTML = '';

    const vacia = document.createElement('option');
    vacia.value = '';
    vacia.textContent = placeholder;
    select.appendChild(vacia);

    opciones.forEach((opcion) => {
        const el = document.createElement('option');
        el.value = typeof opcion === 'string' ? opcion : opcion.codigo;
        el.textContent = typeof opcion === 'string' ? opcion : opcion.nombre;
        select.appendChild(el);
    });

    // Si el valor anterior sigue en la lista se mantiene: al volver del
    // servidor con un error de validación no se pierde lo que ya se eligió.
    const sigueExistiendo = Array.from(select.options).some((o) => o.value === previo);
    select.value = sigueExistiendo ? previo : '';

    return select.value;
}

/**
 * Renombra el campo de subdivisión según el país.
 *
 * «Región» en los 249 es impreciso: en Perú son departamentos, en Japón
 * prefecturas. Se cambia la etiqueta visible y la accesible a la vez.
 */
export function renombrarCampo(etiqueta, ...campos) {
    campos.filter(Boolean).forEach((campo) => {
        const contenedor = campo.closest('[data-campo]') ?? campo.parentElement;
        const label = contenedor?.querySelector(`label[for="${campo.id}"]`);

        if (label) {
            // El asterisco de obligatorio va en un <span> aparte: se conserva.
            const marca = label.querySelector('span');
            label.textContent = etiqueta;
            if (marca) label.append(' ', marca);
        }

        campo.setAttribute('aria-label', etiqueta);
    });
}

/**
 * Conecta los dos selectores.
 *
 * Un país sin división administrativa —50 de los 246 que las traen— deja el
 * campo como texto libre, en lugar de un desplegable vacío que no se puede
 * rellenar.
 */
export function montarSelectorGeografico({
    selectPais,
    selectRegion,
    inputRegionLibre = null,
    urlPaises = '/geografia/v2/paises',
    urlRegiones = (codigo) => `/geografia/v2/paises/${codigo}/regiones`,
    fetchImpl = globalThis.fetch,
    paisInicial = null,
    regionInicial = null,
} = {}) {
    if (!selectPais || !selectRegion) return null;

    const pedir = async (url) => {
        const res = await fetchImpl(url, { headers: { Accept: 'application/json' } });
        if (!res.ok) throw new Error(`Respuesta ${res.status}`);
        return res.json();
    };

    const mostrarCampoLibre = (libre) => {
        if (!inputRegionLibre) return;
        inputRegionLibre.closest('[data-campo]')?.classList.toggle('hidden', !libre);
        selectRegion.closest('[data-campo]')?.classList.toggle('hidden', libre);

        // Solo el campo visible viaja en el envío: si van los dos, el servidor
        // recibe `region` dos veces y se queda con el vacío.
        inputRegionLibre.disabled = !libre;
        selectRegion.disabled = libre;
    };

    async function cargarRegiones(codigo, { valor = null } = {}) {
        if (!codigo) {
            llenarSelect(selectRegion, [], { placeholder: 'Elige primero un país' });
            mostrarCampoLibre(false);
            return;
        }

        try {
            const { regiones = [], etiqueta = 'Región' } = await pedir(urlRegiones(codigo));

            renombrarCampo(etiqueta, selectRegion, inputRegionLibre);

            if (regiones.length === 0) {
                mostrarCampoLibre(true);
                return;
            }

            mostrarCampoLibre(false);
            llenarSelect(selectRegion, regiones, {
                placeholder: `Selecciona tu ${etiqueta.toLowerCase()}`,
                valor,
            });
        } catch (e) {
            // Sin conexión con el sitio, mejor dejar escribir que bloquear el
            // formulario entero.
            mostrarCampoLibre(true);
        }
    }

    async function iniciar() {
        try {
            const { paises = [] } = await pedir(urlPaises);
            const elegido = llenarSelect(selectPais, paises, {
                placeholder: 'Selecciona tu país',
                valor: paisInicial,
            });

            await cargarRegiones(elegido, { valor: regionInicial });
        } catch (e) {
            mostrarCampoLibre(true);
        }
    }

    selectPais.addEventListener('change', () => cargarRegiones(selectPais.value));

    const listo = iniciar();

    return { listo, cargarRegiones };
}

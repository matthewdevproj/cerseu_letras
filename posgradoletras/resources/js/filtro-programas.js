/**
 * Filtro de la oferta académica.
 *
 * Estaba escrito dos veces —en `home.blade.php` y en `programas/index.blade.php`—
 * con dos variantes: la portada solo filtra por tipo, y /programas añade una
 * búsqueda por texto y oculta la grilla entera cuando no hay resultados. Aquí
 * viven las dos como opciones del mismo módulo.
 */

/** ¿Coincide esta tarjeta con el filtro de tipo indicado? */
export function esVisible(tipoCarta, filtro) {
    return filtro === 'todos' || tipoCarta === filtro;
}

/** ¿Coincide el texto buscado con el título o la descripción de la tarjeta? */
export function coincideBusqueda({ title = '', desc = '' } = {}, termino) {
    const t = termino.toLowerCase().trim();
    if (!t) return true;
    return title.toLowerCase().includes(t) || desc.toLowerCase().includes(t);
}

/** Cuántas tarjetas quedan visibles: sirve para el mensaje de «sin resultados». */
export function contarVisibles(tipos, filtro) {
    return tipos.filter((t) => esVisible(t, filtro)).length;
}

/**
 * Conecta los botones de filtro (y opcionalmente un campo de búsqueda) con las
 * tarjetas.
 *
 * Un único listener por botón en lugar de `onclick` en el markup, y el mensaje
 * de vacío se sincroniza solo.
 *
 * Devuelve `null` si la página no tiene grilla, para poder llamarlo sin
 * comprobaciones desde el arranque común.
 */
export function montarFiltroProgramas({
    grid,
    botones = [],
    mensajeVacio = null,
    campoBusqueda = null,
    filtroInicial = 'diplomado',
    claseOculta = 'hidden',
    clasesActivo = ['bg-unmsm-guinda', 'text-white', 'shadow-lg', 'scale-105'],
    clasesInactivo = ['bg-white', 'text-gray-600'],
    claseInactivoExtra = 'shadow-sm',
    ocultarGridVacio = false,
} = {}) {
    if (!grid) return null;

    const cartas = Array.from(grid.querySelectorAll('.program-card'));
    let filtroActual = filtroInicial;

    function pintar() {
        botones.forEach((btn) => {
            const activo = btn.dataset.filter === filtroActual;
            if (claseInactivoExtra) btn.classList.toggle(claseInactivoExtra, !activo);
            clasesActivo.forEach((c) => btn.classList.toggle(c, activo));
            clasesInactivo.forEach((c) => btn.classList.toggle(c, !activo));
            btn.setAttribute('aria-pressed', String(activo));
        });

        const termino = campoBusqueda?.value ?? '';
        let visibles = 0;

        cartas.forEach((carta) => {
            const visible =
                esVisible(carta.dataset.type, filtroActual) && coincideBusqueda(carta.dataset, termino);
            carta.classList.toggle(claseOculta, !visible);
            if (visible) visibles++;
        });

        mensajeVacio?.classList.toggle('hidden', visibles > 0);
        // En /programas la grilla vacía dejaba un hueco bajo el mensaje.
        if (ocultarGridVacio) grid.classList.toggle('hidden', visibles === 0 && cartas.length > 0);

        return visibles;
    }

    function aplicar(filtro) {
        filtroActual = filtro;
        return pintar();
    }

    botones.forEach((btn) => btn.addEventListener('click', () => aplicar(btn.dataset.filter)));
    campoBusqueda?.addEventListener('input', pintar);
    pintar();

    return { aplicar, refrescar: pintar };
}

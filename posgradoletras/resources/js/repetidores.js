/**
 * Lógica compartida de los repetidores del panel.
 *
 * El cronograma de admisión, el editor de contenido y las tarifas por periodo
 * repetían las mismas operaciones (agregar, eliminar, reordenar) escritas tres
 * veces dentro de sus plantillas Blade, donde no había forma de probarlas.
 */

/**
 * Repetidor base: una lista ordenable de elementos.
 *
 * `uid` es solo la clave de `x-for`: sobrevive al reordenado —a diferencia del
 * índice— y no se envía al servidor.
 */
export function crearRepetidor(iniciales = [], nuevoElemento = () => ({})) {
    let uid = 0;

    return {
        elementos: (Array.isArray(iniciales) ? iniciales : []).map((e) => ({ ...e, uid: ++uid })),

        agregar() {
            this.elementos.push({ ...nuevoElemento(), uid: ++uid });
        },

        eliminar(indice) {
            if (indice < 0 || indice >= this.elementos.length) return;
            this.elementos.splice(indice, 1);
        },

        mover(indice, delta) {
            const destino = indice + delta;
            if (destino < 0 || destino >= this.elementos.length) return;
            const [item] = this.elementos.splice(indice, 1);
            this.elementos.splice(destino, 0, item);
        },
    };
}

/**
 * Etapas del cronograma de admisión de la portada.
 *
 * `iconos` es el mapa `clave → trazado SVG` que alimenta la vista previa en
 * vivo del selector de ícono; la lógica no lo usa.
 */
export function crearCronogramaAdmision(pasosIniciales = [], visibleInicial = true, iconos = {}) {
    const base = crearRepetidor(pasosIniciales, () => ({
        id: null,
        titulo: '',
        fecha_inicio: '',
        fecha_fin: '',
        detalle: '',
        publico: '',
        icono: 'documento',
        destacado: false,
        is_visible: true,
    }));

    return {
        ...base,
        visible: visibleInicial,
        iconos,

        get pasos() {
            return this.elementos;
        },

        /** Solo una etapa puede estar «en curso» a la vez. */
        marcarDestacado(indice, marcado) {
            this.elementos.forEach((p, i) => {
                p.destacado = marcado && i === indice;
            });
        },
    };
}

/** Secciones de las páginas de contenido (/tramites, /admision). */
export function crearEditorContenido(iniciales = [], grupoPorDefecto = null) {
    const base = crearRepetidor(iniciales, () => ({
        id: null,
        grupo: grupoPorDefecto,
        numeral: '',
        titulo: '',
        cuerpo: '',
        is_visible: true,
    }));

    return {
        ...base,
        get secciones() {
            return this.elementos;
        },
    };
}

/**
 * Menú de navegación: repetidor de dos niveles.
 *
 * Cada entrada de primer nivel lleva su propia lista de subentradas, que se
 * ordena y edita igual que la de arriba.
 */
export function crearMenuNavegacion(iniciales = []) {
    const nuevaEntrada = () => ({
        id: null,
        etiqueta: '',
        route_name: '',
        url: '',
        icono: '',
        nueva_pestana: false,
        is_visible: true,
        vigente_hasta: '',
    });

    const base = crearRepetidor(
        (Array.isArray(iniciales) ? iniciales : []).map((m) => ({
            ...m,
            hijos: Array.isArray(m.hijos) ? m.hijos.map((h) => ({ ...h })) : [],
        })),
        () => ({ ...nuevaEntrada(), hijos: [] })
    );

    return {
        ...base,

        get items() {
            return this.elementos;
        },

        /**
         * Etiquetas de lo que ya pasó de fecha, entradas y subentradas.
         * El servidor manda el `caducado`: es él quien conoce «hoy».
         */
        get caducados() {
            return this.elementos.flatMap((item) => [
                ...(item.caducado ? [item.etiqueta] : []),
                ...item.hijos.filter((h) => h.caducado).map((h) => h.etiqueta),
            ]);
        },

        agregarHijo(indice) {
            this.elementos[indice]?.hijos.push({ ...nuevaEntrada(), route_params: '' });
        },

        eliminarHijo(indice, indiceHijo) {
            const hijos = this.elementos[indice]?.hijos;
            if (!hijos || indiceHijo < 0 || indiceHijo >= hijos.length) return;
            hijos.splice(indiceHijo, 1);
        },

        moverHijo(indice, indiceHijo, delta) {
            const hijos = this.elementos[indice]?.hijos;
            if (!hijos) return;
            const destino = indiceHijo + delta;
            if (destino < 0 || destino >= hijos.length) return;
            const [item] = hijos.splice(indiceHijo, 1);
            hijos.splice(destino, 0, item);
        },

        /**
         * Una ruta interna y una URL externa son excluyentes: elegir ruta
         * limpia la dirección para que no queden las dos guardadas y el
         * formulario diga una cosa distinta de lo que hará el servidor.
         */
        usarRuta(entrada) {
            if (entrada.route_name) entrada.url = '';
        },

        usarUrl(entrada) {
            if (entrada.url) entrada.route_name = '';
        },
    };
}

/**
 * Tarifas por periodo de un programa.
 *
 * El total se recalcula en vivo mientras se edita: matrícula del periodo más
 * créditos por el costo unitario.
 */
export function crearInversionPeriodos(costoCredito = 0, periodos = [], etiqueta = 'Semestre') {
    return {
        costoCredito: Number(costoCredito) || 0,
        etiqueta,
        periodos: (Array.isArray(periodos) ? periodos : []).map((p) => ({
            matricula: Number(p.matricula) || 0,
            creditos: Number(p.creditos) || 0,
        })),

        agregar() {
            this.periodos.push({ matricula: 0, creditos: 0 });
        },

        eliminar(indice) {
            if (indice < 0 || indice >= this.periodos.length) return;
            this.periodos.splice(indice, 1);
        },

        subtotal(p) {
            return (p.matricula || 0) + (p.creditos || 0) * (this.costoCredito || 0);
        },

        get total() {
            return this.periodos.reduce((acc, p) => acc + this.subtotal(p), 0);
        },
    };
}

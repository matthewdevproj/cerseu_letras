/**
 * Aviso antes de abandonar un formulario con cambios sin guardar.
 *
 * Los formularios del panel son largos —el editor de contenido, la ficha de
 * programa, el menú— y cerrar la pestaña o pulsar «Cancelar» descartaba el
 * trabajo en silencio.
 *
 * Se marca el formulario con `data-avisar-sin-guardar`. El aviso desaparece al
 * enviarlo: guardar no es «salir con cambios pendientes».
 */

/** Instantánea del formulario, para comparar por valor y no por eventos. */
export function serializar(formulario) {
    if (!formulario) return '';

    return Array.from(new FormData(formulario).entries())
        // El token CSRF se regenera y haría creer que hay cambios.
        .filter(([nombre]) => nombre !== '_token')
        .map(([nombre, valor]) => `${nombre}=${valor instanceof File ? valor.name : valor}`)
        .join('&');
}

export function hayCambios(instantanea, formulario) {
    return serializar(formulario) !== instantanea;
}

/**
 * Vigila un formulario. Devuelve una función para dejar de vigilarlo.
 *
 * `alSalir` se inyecta para poder probarlo sin depender de `window`.
 */
export function vigilarFormulario(formulario, { ventana = globalThis } = {}) {
    if (!formulario) return () => {};

    let instantanea = serializar(formulario);
    let enviando = false;

    const alSalir = (evento) => {
        if (enviando || !hayCambios(instantanea, formulario)) return;

        // El navegador muestra su propio texto; lo que cuenta es prevenir.
        evento.preventDefault();
        evento.returnValue = '';
        return '';
    };

    const alEnviar = () => {
        enviando = true;
    };

    // Un botón «Cancelar» o «Volver» es una navegación normal: el aviso de
    // beforeunload no siempre llega a tiempo, así que se pregunta antes.
    const alPulsarSalida = (evento) => {
        const salida = evento.target.closest('[data-salir-sin-guardar]');
        if (!salida || enviando || !hayCambios(instantanea, formulario)) return;

        if (!ventana.confirm('Hay cambios sin guardar. ¿Seguro que quieres salir?')) {
            evento.preventDefault();
        }
    };

    ventana.addEventListener('beforeunload', alSalir);
    formulario.addEventListener('submit', alEnviar);
    document.addEventListener('click', alPulsarSalida);

    return () => {
        ventana.removeEventListener('beforeunload', alSalir);
        formulario.removeEventListener('submit', alEnviar);
        document.removeEventListener('click', alPulsarSalida);
    };
}

/** Vigila todos los formularios marcados de la página. */
export function montarAvisoSinGuardar({ ventana = globalThis } = {}) {
    const formularios = Array.from(document.querySelectorAll('[data-avisar-sin-guardar]'));

    const bajas = formularios.map((f) => vigilarFormulario(f, { ventana }));

    return () => bajas.forEach((baja) => baja());
}

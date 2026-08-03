/**
 * Editor de texto con formato para el panel.
 *
 * El contenido de /tramites, /admision y /nosotros se editaba en un `textarea`
 * que pedía HTML a mano —la etiqueta decía literalmente «Contenido (HTML)»—.
 * Un `</div>` de más rompía la maqueta de la página pública.
 *
 * Sin dependencias: un `contenteditable` sincronizado con el `textarea`
 * original, que sigue siendo lo que se envía. Si el JS no carga, queda el
 * textarea de siempre y se puede seguir trabajando.
 */

/** Etiquetas que el editor conserva; el resto se desmonta al pegar. */
const ETIQUETAS_PERMITIDAS = new Set([
    'P', 'BR', 'STRONG', 'B', 'EM', 'I', 'U',
    'UL', 'OL', 'LI', 'A', 'H3', 'H4', 'BLOCKQUOTE', 'SPAN', 'DIV',
]);

/** Atributos que sobreviven a la limpieza, por etiqueta. */
const ATRIBUTOS_PERMITIDOS = {
    A: ['href', 'target', 'rel', 'class'],
    SPAN: ['class'],
    DIV: ['class'],
    P: ['class'],
};

/**
 * Deja el HTML pegado en algo que el sitio pueda pintar.
 *
 * Pegar desde Word o desde una página trae `style`, `font`, `o:p` y clases
 * ajenas que se cuelan tal cual en la página pública. Aquí se recorta a lo que
 * el diseño entiende.
 */
export function limpiarHtml(html, { documento = document } = {}) {
    const contenedor = documento.createElement('div');
    contenedor.innerHTML = String(html ?? '');

    const recorrer = (nodo) => {
        Array.from(nodo.children).forEach((hijo) => {
            recorrer(hijo);

            if (!ETIQUETAS_PERMITIDAS.has(hijo.tagName)) {
                // Se conserva el texto y los hijos, se tira la etiqueta.
                hijo.replaceWith(...hijo.childNodes);
                return;
            }

            const permitidos = ATRIBUTOS_PERMITIDOS[hijo.tagName] ?? [];
            Array.from(hijo.attributes).forEach((attr) => {
                if (!permitidos.includes(attr.name)) hijo.removeAttribute(attr.name);
            });

            // Un enlace externo sin rel es una vía de tabnabbing.
            if (hijo.tagName === 'A' && hijo.getAttribute('target') === '_blank') {
                hijo.setAttribute('rel', 'noopener noreferrer');
            }
        });
    };

    recorrer(contenedor);

    return contenedor.innerHTML;
}

/** Quita los espacios en blanco que deja el editor entre bloques. */
export function normalizar(html) {
    return String(html ?? '')
        .replace(/<p><br\s*\/?><\/p>/gi, '')
        .replace(/&nbsp;/g, ' ')
        .replace(/\s+</g, ' <')
        .trim();
}

/** ¿El contenido está vacío a efectos prácticos? */
export function estaVacio(html) {
    return normalizar(html).replace(/<[^>]*>/g, '').trim() === '';
}

/** Órdenes de la barra de herramientas. */
export const ACCIONES = {
    negrita: { comando: 'bold', etiqueta: 'Negrita', icono: 'B' },
    cursiva: { comando: 'italic', etiqueta: 'Cursiva', icono: 'I' },
    titulo: { comando: 'formatBlock', valor: 'h3', etiqueta: 'Título', icono: 'H' },
    parrafo: { comando: 'formatBlock', valor: 'p', etiqueta: 'Párrafo', icono: '¶' },
    lista: { comando: 'insertUnorderedList', etiqueta: 'Lista', icono: '•' },
    listaNumerada: { comando: 'insertOrderedList', etiqueta: 'Lista numerada', icono: '1.' },
    enlace: { comando: 'createLink', pidePrompt: true, etiqueta: 'Enlace', icono: '🔗' },
    limpiar: { comando: 'removeFormat', etiqueta: 'Quitar formato', icono: '✕' },
};

/**
 * Conecta un `textarea` con un editor visual.
 *
 * Devuelve una función para desmontarlo. El `textarea` sigue siendo el campo
 * que se envía: no cambia nada del lado del servidor.
 */
export function montarEditor(textarea, { documento = document, ventana = globalThis } = {}) {
    if (!textarea || textarea.dataset.editorMontado === '1') return () => {};

    textarea.dataset.editorMontado = '1';

    const envoltorio = documento.createElement('div');
    envoltorio.className = 'editor-texto';

    const barra = documento.createElement('div');
    barra.className = 'editor-texto__barra';
    barra.setAttribute('role', 'toolbar');
    barra.setAttribute('aria-label', 'Formato del texto');

    const area = documento.createElement('div');
    area.className = 'editor-texto__area prose prose-sm max-w-none';
    area.contentEditable = 'true';
    area.setAttribute('role', 'textbox');
    area.setAttribute('aria-multiline', 'true');
    if (textarea.id) area.setAttribute('aria-labelledby', `${textarea.id}-etiqueta`);
    area.innerHTML = textarea.value || '';

    Object.entries(ACCIONES).forEach(([nombre, accion]) => {
        const boton = documento.createElement('button');
        boton.type = 'button';           // Dentro de un <form>, sin esto envía.
        boton.className = 'editor-texto__boton';
        boton.dataset.accion = nombre;
        boton.title = accion.etiqueta;
        boton.setAttribute('aria-label', accion.etiqueta);
        boton.textContent = accion.icono;
        barra.appendChild(boton);
    });

    textarea.parentNode.insertBefore(envoltorio, textarea);
    envoltorio.appendChild(barra);
    envoltorio.appendChild(area);
    envoltorio.appendChild(textarea);
    textarea.hidden = true;

    const sincronizar = () => {
        textarea.value = normalizar(area.innerHTML);
        // El aviso de cambios sin guardar escucha eventos del formulario.
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    };

    const alPulsarBoton = (evento) => {
        const boton = evento.target.closest('[data-accion]');
        if (!boton) return;

        evento.preventDefault();          // No robar el foco al área.
        const accion = ACCIONES[boton.dataset.accion];
        if (!accion) return;

        let valor = accion.valor ?? null;

        if (accion.pidePrompt) {
            valor = ventana.prompt('Dirección del enlace (https://…)');
            if (!valor) return;
        }

        area.focus();
        documento.execCommand(accion.comando, false, valor);
        sincronizar();
    };

    const alPegar = (evento) => {
        const html = evento.clipboardData?.getData('text/html');
        if (!html) return;               // Texto plano: que lo maneje el navegador.

        evento.preventDefault();
        documento.execCommand('insertHTML', false, limpiarHtml(html, { documento }));
        sincronizar();
    };

    barra.addEventListener('mousedown', alPulsarBoton);
    area.addEventListener('input', sincronizar);
    area.addEventListener('paste', alPegar);

    return () => {
        barra.removeEventListener('mousedown', alPulsarBoton);
        area.removeEventListener('input', sincronizar);
        area.removeEventListener('paste', alPegar);
        textarea.hidden = false;
        delete textarea.dataset.editorMontado;
        envoltorio.parentNode?.insertBefore(textarea, envoltorio);
        envoltorio.remove();
    };
}

/** Monta el editor en todos los textarea marcados de la página. */
export function montarEditores({ documento = document, ventana = globalThis } = {}) {
    const bajas = Array.from(documento.querySelectorAll('[data-editor-texto]'))
        .map((t) => montarEditor(t, { documento, ventana }));

    return () => bajas.forEach((baja) => baja());
}

// Tipografías auto-alojadas (antes Google Fonts CDN). Subconjunto «latin»,
// suficiente para español, con los mismos family-names que espera Tailwind
// ('Inter', 'Merriweather') y el panel admin ('Playfair Display').
// font-display: swap viene por defecto en @fontsource → sin texto invisible.
// El peso 300 se retiró: solo lo usaban 4 elementos y cada peso son ~23 KB de
// woff2. Esos casos caen al 400, que es imperceptible en textos de apoyo.
import '@fontsource/inter/latin-400.css';
import '@fontsource/inter/latin-500.css';
import '@fontsource/inter/latin-600.css';
import '@fontsource/inter/latin-700.css';
import '@fontsource/merriweather/latin-700.css';
import '@fontsource/playfair-display/latin-600.css';
import '@fontsource/playfair-display/latin-700.css';

import {
    crearCondicionesPago,
    crearCronogramaAdmision,
    crearEditorContenido,
    crearInversionPeriodos,
    crearMenuNavegacion,
    crearModalidadesPago,
} from './repetidores';
import { montarAvisoSinGuardar } from './aviso-sin-guardar';
import { montarEditores } from './editor-texto';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

Alpine.plugin(collapse);
Alpine.plugin(focus); // x-trap: focus-trap accesible para modal y drawer

// Hacer Alpine disponible globalmente ANTES de iniciar
window.Alpine = Alpine;

/**
 * QR bajo demanda: la librería `qrcode` (~16 kB gzip) solo se carga como un
 * chunk aparte cuando una página realmente dibuja un QR — hoy únicamente la
 * vista de Admisión de Diplomados. Así queda fuera del bundle JS global de
 * todas las demás páginas (code-splitting nativo de Vite).
 *
 * Uso:  await window.renderQRCode(canvas, texto, { width: 160, margin: 1 })
 */
window.renderQRCode = async (canvas, text, options = {}) => {
    if (!canvas || !text) return;
    const { default: QRCode } = await import('qrcode');
    return new Promise((resolve, reject) => {
        QRCode.toCanvas(canvas, text, options, (error) => {
            if (error) reject(error); else resolve(canvas);
        });
    });
};

/**
 * Scroll-reveal: cualquier elemento con [data-reveal] aparece con un fade +
 * desplazamiento leve al entrar al viewport. Respeta prefers-reduced-motion
 * mostrando todo de inmediato sin animar. Sin librería — IntersectionObserver
 * nativo, soportado en todos los navegadores relevantes para este sitio.
 */
function initScrollReveal() {
    // [data-reveal]: el elemento entero aparece con un fade + desplazamiento.
    // [data-reveal-stagger]: sus hijos aparecen uno a uno (efecto escalonado);
    //   admite un paso opcional en ms → data-reveal-stagger="90".
    const simple = document.querySelectorAll('[data-reveal]');
    const staggered = document.querySelectorAll('[data-reveal-stagger]');
    if (!simple.length && !staggered.length) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        simple.forEach((el) => el.classList.add('is-revealed'));
        staggered.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                if (el.hasAttribute('data-reveal-stagger')) {
                    const step = parseInt(el.dataset.revealStagger, 10) || 70;
                    Array.from(el.children).forEach((child, i) => {
                        child.style.transitionDelay = Math.min(i * step, 600) + 'ms';
                    });
                }
                el.classList.add('is-revealed');
                obs.unobserve(el);
            });
        },
        // threshold 0 y margen en pixeles, no en porcentaje.
        //
        // Con threshold 0.1 el umbral es una fraccion DEL ELEMENTO, no del
        // viewport, asi que cuanto mas largo es el elemento menos alcanzable
        // resulta: la grilla de /cursos mide 6143 px en escritorio (ratio
        // maximo 0.137, se revelaba tarde), 9450 px en tablet (0.076) y
        // 18428 px en movil a una columna (0.033). En los dos ultimos casos
        // el 10% era inalcanzable y los 39 cursos no aparecian nunca.
        //
        // Con threshold 0 basta con que asome un pixel, y los -80 px de
        // margen inferior evitan que se dispare por un elemento que apenas
        // roza el borde. El comportamiento deja de depender de cuantas
        // tarjetas haya dentro.
        { threshold: 0, rootMargin: '0px 0px -80px 0px' }
    );

    simple.forEach((el) => observer.observe(el));
    staggered.forEach((el) => observer.observe(el));
}

/**
 * Contadores animados: cualquier elemento con [data-count-to="N"] cuenta
 * progresivamente desde 0 hasta N al entrar al viewport. El contenido de
 * texto original (p. ej. "20+") se preserva como sufijo tras el número.
 */
function initAnimatedCounters() {
    const targets = document.querySelectorAll('[data-count-to]');
    if (!targets.length) return;

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const animate = (el) => {
        const to = parseInt(el.dataset.countTo, 10);
        const suffix = el.dataset.countSuffix || '';
        if (reduceMotion || Number.isNaN(to)) {
            el.textContent = to + suffix;
            return;
        }
        const duration = 1200;
        const start = performance.now();
        const step = (now) => {
            const progress = Math.min((now - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.round(to * eased) + suffix;
            if (progress < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    animate(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.4 }
    );

    targets.forEach((el) => observer.observe(el));
}

/**
 * Toast notifications: cualquier script en la página puede disparar
 * window.showToast('Mensaje', 'success' | 'error') para mostrar una
 * confirmación flotante y auto-descartable, sin necesidad de una librería.
 * El contenedor Alpine vive en layouts/partials/toast-container.blade.php.
 */
window.showToast = function (message, type = 'success') {
    window.dispatchEvent(new CustomEvent('toast:show', { detail: { message, type } }));
};

/*
 * Puente con las plantillas Blade.
 *
 * Alpine resuelve `x-data="nombre(...)"` contra el ámbito global, así que los
 * componentes extraídos se exponen aquí. Se registran al evaluar el módulo —no
 * dentro de `initApp`— para que existan antes de que Alpine recorra el DOM.
 * Vivir en módulos es lo que permite probarlos con Vitest, imposible mientras
 * estaban escritos dentro del HTML.
 */
window.condicionesPago = crearCondicionesPago;
window.cronogramaAdmision = crearCronogramaAdmision;
window.editorContenido = crearEditorContenido;
window.inversionPeriodos = crearInversionPeriodos;
window.menuNavegacion = crearMenuNavegacion;
window.modalidadesPago = crearModalidadesPago;

function initApp() {
    // Panel: editor con formato en lugar de pedir HTML a mano. Se monta
    // antes del aviso de cambios para que la instantánea inicial incluya ya
    // el contenido normalizado por el editor y no salte un falso positivo.
    montarEditores();

    Alpine.start();

    // Panel: avisa antes de abandonar un formulario con cambios pendientes.
    //
    // La instantánea inicial tiene que tomarse con el formulario ya en reposo,
    // y varias partes se rellenan solas después de este punto: los repetidores
    // escriben su JSON en campos ocultos vía `:value` (que Alpine no evalúa
    // hasta arrancar) y la ficha de programa genera las filas de la plana
    // docente desde su propio `DOMContentLoaded`. Si se midiera antes, esos
    // campos pasarían de vacíos a llenos por su cuenta y el aviso saltaría
    // nada más abrir la ficha, sin que nadie hubiera tocado nada.
    //
    // Por eso se espera a `load`, que llega después de todos los
    // `DOMContentLoaded`. Si la página ya terminó de cargar, se monta al vuelo.
    if (document.readyState === 'complete') {
        montarAvisoSinGuardar();
    } else {
        window.addEventListener('load', () => montarAvisoSinGuardar(), { once: true });
    }

    initScrollReveal();
    initAnimatedCounters();
}

// Optimización: solo inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

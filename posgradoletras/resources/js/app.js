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

import { crearBuscador } from './site-search';
import {
    crearCronogramaAdmision,
    crearEditorContenido,
    crearInversionPeriodos,
    crearMenuNavegacion,
    crearModalidadesPago,
} from './repetidores';
import { montarFiltroProgramas } from './filtro-programas';
import { montarAvisoSinGuardar } from './aviso-sin-guardar';
import { montarEditores } from './editor-texto';
import { montarSelectorGeografico } from './selector-geografico';

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
        { threshold: 0.1, rootMargin: '0px 0px -10% 0px' }
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
window.siteSearch = (urlSugerencias = '/buscar/sugerencias') => {
    const base = crearBuscador({ urlSugerencias });

    // El módulo se mantiene libre de DOM para poder probarlo en Vitest; lo que
    // depende de la página se envuelve aquí.
    return {
        ...base,

        mover(delta) {
            base.mover.call(this, delta);
            this.$nextTick(() => {
                document.getElementById('site-search-opt-' + this.activo)
                    ?.scrollIntoView({ block: 'nearest' });
            });
        },

        irAlActivo(event) {
            const url = base.irAlActivo.call(this, event);
            // Sin selección, el formulario navega a la página de resultados.
            if (url) window.location.href = url;
        },
    };
};
window.cronogramaAdmision = crearCronogramaAdmision;
window.editorContenido = crearEditorContenido;
window.inversionPeriodos = crearInversionPeriodos;
window.menuNavegacion = crearMenuNavegacion;
window.modalidadesPago = crearModalidadesPago;
// /programas monta su propia variante (clases y buscador propios) desde la vista.
window.montarFiltroProgramas = montarFiltroProgramas;

function initApp() {
    // Filtro de «Nuestros programas» (portada y /programas): se monta solo si
    // la sección existe en la página.
    // Panel: editor con formato en lugar de pedir HTML a mano. Se monta
    // antes del aviso de cambios para que la instantánea inicial incluya ya
    // el contenido normalizado por el editor y no salte un falso positivo.
    montarEditores();

    // Panel: avisa antes de abandonar un formulario con cambios pendientes.
    montarAvisoSinGuardar();

    // Formulario de diplomados: país y región desde listas reales. Se monta
    // solo si la página los tiene.
    const selectPais = document.getElementById('lead_pais');
    if (selectPais) {
        montarSelectorGeografico({
            selectPais,
            selectRegion: document.getElementById('lead_region'),
            inputRegionLibre: document.getElementById('lead_region_libre'),
            // ISO alpha-2, que es lo que da symfony/intl. Con el 'PER' de antes
            // el país no se preseleccionaba y la región arrancaba vacía.
            paisInicial: selectPais.dataset.valorInicial || 'PE',
            regionInicial: document.getElementById('lead_region')?.dataset.valorInicial || null,
        });
    }

    montarFiltroProgramas({
        grid: document.getElementById('programas-grid'),
        botones: Array.from(document.querySelectorAll('.filter-btn')),
        mensajeVacio: document.getElementById('programas-empty'),
    });

    Alpine.start();
    initScrollReveal();
    initAnimatedCounters();

    // Swiper solo se descarga (chunk aparte) si la página tiene carruseles.
    if (document.querySelector('.swiper')) {
        import('./carousels.js')
            .then((m) => m.initCarousels())
            .catch((e) => console.error('No se pudieron inicializar los carruseles:', e));
    }
}

// Optimización: solo inicializar cuando el DOM esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}

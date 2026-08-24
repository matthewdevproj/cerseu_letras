/**
 * Carruseles del sitio (Swiper, modular).
 *
 * Este módulo se importa de forma DIFERIDA desde app.js solo cuando la página
 * contiene un `.swiper` (hoy: la portada). Así Swiper y su CSS quedan fuera del
 * bundle global y se descargan únicamente donde hacen falta (code-splitting).
 *
 * Se importan solo los módulos usados (tree-shaking) en lugar del `swiper-bundle`
 * completo que antes llegaba por CDN.
 */
import Swiper from 'swiper';
import { Autoplay, Pagination, EffectFade } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/effect-fade';

/**
 * Carga los fondos que quedaron aplazados en el hero.
 *
 * Solo la primera diapositiva trae su imagen en el marcado; las demás esperan
 * a que el navegador esté ocioso. Antes las tres se descargaban a la vez —unos
 * 690 KB— para enseñar una.
 */
export function cargarFondosDiferidos() {
    const pendientes = document.querySelectorAll('[data-bg-diferido]');
    if (!pendientes.length) return;

    const aplicar = () => {
        pendientes.forEach((el) => {
            const webp = el.dataset.bgDiferido;
            const respaldo = el.dataset.bgRespaldo;

            // Mismo par que la primera diapositiva: WebP con JPG de reserva.
            el.style.backgroundImage = respaldo ? `url('${respaldo}')` : `url('${webp}')`;
            if (respaldo) {
                el.style.backgroundImage =
                    `image-set(url('${webp}') type('image/webp'), url('${respaldo}') type('image/jpeg'))`;
            }

            delete el.dataset.bgDiferido;
            delete el.dataset.bgRespaldo;
        });
    };

    /* Se espera a `load` y un margen extra.
       `requestIdleCallback` no servía: en una página ligera el navegador queda
       ocioso a los ~300 ms y las descargaba igual dentro de la ruta crítica.
       El autoplay tarda 5 s en pedir la segunda, así que hay tiempo de sobra. */
    const programar = () => setTimeout(aplicar, 1800);

    if (document.readyState === 'complete') {
        programar();
    } else {
        window.addEventListener('load', programar, { once: true });
    }
}

export function initCarousels() {
    cargarFondosDiferidos();

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Hero: fundido cruzado con autoplay. Con prefers-reduced-motion se
    // desactiva el autoplay y se muestra el primer slide sin movimiento.
    const hero = document.querySelector('.hero-swiper');
    if (hero) {
        new Swiper(hero, {
            modules: [Autoplay, EffectFade],
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
            speed: 2000,
            autoplay: reduceMotion ? false : { delay: 5000, disableOnInteraction: false },
        });
    }

    // Testimonios: carrusel responsivo con paginación clicable.
    const testimonios = document.querySelector('.testimonios-swiper');
    if (testimonios) {
        new Swiper(testimonios, {
            modules: [Autoplay, Pagination],
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: reduceMotion ? false : { delay: 3500, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: {
                768: { slidesPerView: 2, spaceBetween: 30 },
                1024: { slidesPerView: 3, spaceBetween: 40 },
            },
        });
    }
}

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

export function initCarousels() {
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

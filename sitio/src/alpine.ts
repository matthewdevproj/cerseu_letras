import type { Alpine } from 'alpinejs';

/**
 * Punto de entrada de Alpine.
 *
 * Astro lo llama antes de arrancarlo, y es donde se registran los datos y las
 * directivas propias. De momento solo hace falta uno: el estado compartido de
 * un acordeón, que varias secciones usan para dejar abierto un solo panel.
 */
export default (Alpine: Alpine) => {
    Alpine.data('acordeon', (inicial: number | null = null) => ({
        abierto: inicial,

        alternar(i: number) {
            // Uno a la vez: dos paneles largos abiertos obligan a desplazarse
            // para comparar, que es justo lo que un acordeon viene a evitar.
            this.abierto = this.abierto === i ? null : i;
        },

        esta(i: number) {
            return this.abierto === i;
        },
    }));
};

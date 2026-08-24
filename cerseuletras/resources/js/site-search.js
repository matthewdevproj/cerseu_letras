/**
 * Buscador global de la cabecera (componente Alpine).
 *
 * Vivía escrito dentro de la plantilla Blade, donde no había forma de probarlo.
 * Al pasarlo a un módulo se puede verificar la parte que de verdad tiene lógica:
 * el mínimo de caracteres, la cancelación de la petición anterior y la
 * navegación con teclado.
 */

/** Caracteres mínimos antes de consultar al servidor. */
export const MIN_LONGITUD = 2;

export function crearBuscador({ urlSugerencias, fetchImpl = globalThis.fetch } = {}) {
    return {
        open: false,
        q: '',
        resultados: [],
        cargando: false,
        activo: -1,
        _abort: null,

        abrir() {
            this.open = true;
            this.$nextTick?.(() => this.$refs?.input?.focus());
        },

        cerrar() {
            if (!this.open) return;
            this.open = false;
            this.activo = -1;
            this._abort?.abort();
            this.$refs?.trigger?.focus();
        },

        async buscar() {
            const termino = this.q.trim();
            this.activo = -1;

            if (termino.length < MIN_LONGITUD) {
                this._abort?.abort();
                this.resultados = [];
                this.cargando = false;
                return;
            }

            // Solo interesa la última pulsación: se cancela la anterior.
            this._abort?.abort();
            const abort = (this._abort = new AbortController());
            this.cargando = true;

            try {
                const res = await fetchImpl(
                    `${urlSugerencias}?q=${encodeURIComponent(termino)}`,
                    { signal: abort.signal, headers: { Accept: 'application/json' } }
                );
                const data = await res.json();
                this.resultados = data.resultados ?? [];
            } catch (e) {
                if (e.name !== 'AbortError') this.resultados = [];
            } finally {
                // Una petición cancelada no debe apagar el indicador: la que la
                // reemplazó sigue en curso.
                if (!abort.signal.aborted) this.cargando = false;
            }
        },

        mover(delta) {
            if (!this.resultados.length) return;
            const total = this.resultados.length;

            // Sin selección previa (`activo === -1`), ↓ debe ir al primero y ↑
            // al último. Aplicar la fórmula directamente sobre -1 mandaba al
            // segundo resultado al pulsar ↑, saltándose el último.
            const base = this.activo < 0 ? (delta > 0 ? -1 : 0) : this.activo;

            this.activo = (base + delta + total) % total;
        },

        irAlActivo(event) {
            const r = this.resultados[this.activo];
            if (this.activo >= 0 && r) {
                event?.preventDefault?.();
                return r.url;
            }
            return null;
        },
    };
}

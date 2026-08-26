/**
 * Cliente de la API de contenido de Laravel.
 *
 * Dentro de Docker la base es `http://web/api/v1` —el servicio de Nginx, no
 * `localhost`, que dentro del contenedor de Astro sería el propio contenedor—.
 * Se puede sobrescribir con CERSEU_API para construir contra otra instancia.
 */
const BASE = import.meta.env.CERSEU_API ?? 'http://web/api/v1';

export type TipoOferta = {
    slug: string;
    singular: string;
    plural: string;
    medidas: string[];
    publicados: number;
};

export type Programa = {
    slug: string;
    nombre: string;
    tipo: string | null;
    tipo_label: string | null;
    mencion: string | null;
    modalidad: string | null;
    sumilla: string | null;
    medidas: string[];
    inversion: string | null;
    estado: string;
    imagen: string | null;
    url: string;
};

async function pedir<T>(ruta: string): Promise<T> {
    const url = `${BASE}${ruta}`;
    const res = await fetch(url);

    if (!res.ok) {
        // Falla el build en vez de generar una página vacía: un sitio estático
        // publicado con secciones en blanco es peor que un despliegue detenido.
        throw new Error(`La API respondió ${res.status} en ${url}`);
    }

    const cuerpo = await res.json();
    return cuerpo.data as T;
}

export const obtenerTipos = () => pedir<TipoOferta[]>('/tipos-oferta');

export const obtenerProgramas = (tipo?: string) =>
    pedir<Programa[]>(tipo ? `/programas?tipo=${encodeURIComponent(tipo)}` : '/programas');

export const obtenerPrograma = (slug: string) =>
    pedir<Programa>(`/programas/${encodeURIComponent(slug)}`);

export type ItemMenu = {
    etiqueta: string;
    enlace: string | null;
    nueva_pestana: boolean;
    hijos: ItemMenu[];
};

export type ConfiguracionSitio = {
    nombre: string | null;
    descripcion: string | null;
    logo: string | null;
    favicon: string | null;
    contacto: {
        email: string | null;
        email_admision: string | null;
        email_tramites: string | null;
        telefono: string | null;
        anexo: string | null;
        whatsapp: string | null;
        direccion: string | null;
        horario: string | null;
    };
    redes: Record<string, string>;
};

export const obtenerConfiguracion = () => pedir<ConfiguracionSitio>('/sitio');

export const obtenerMenu = () => pedir<ItemMenu[]>('/menu');

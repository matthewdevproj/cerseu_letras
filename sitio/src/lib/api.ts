/**
 * Cliente de la API de contenido de Laravel.
 *
 * Dentro de Docker la base es `http://web/api/v1` —el servicio de Nginx, no
 * `localhost`, que dentro del contenedor de Astro sería el propio contenedor—.
 * Se puede sobrescribir con CERSEU_API para construir contra otra instancia.
 */
const BASE = import.meta.env.CERSEU_API ?? 'http://web/api/v1';

/**
 * Traduce una URL publica a una alcanzable desde el proceso de build.
 *
 * La API devuelve las URLs con el dominio publico —correcto: son las que
 * acabaran en el HTML y las que abre el navegador—. Pero <Image> descarga la
 * imagen DURANTE el build, y ahi `localhost` es el contenedor de Astro, no
 * Nginx: la descarga falla y el build entero se cae.
 *
 * Es el mismo problema que resolvio ForzarUrlPublica, visto del otro lado: al
 * separar el frontend hay dos redes distintas, y una URL solo puede ser
 * correcta en una de las dos. Aqui se traduce solo para descargar; Astro emite
 * despues su propia ruta, asi que el origen interno no llega al HTML.
 */
const ORIGEN_PUBLICO = import.meta.env.CERSEU_PUBLICO ?? 'http://localhost';
const ORIGEN_INTERNO = new URL(BASE).origin;

export function paraDescargar(url: string): string {
    if (ORIGEN_PUBLICO === ORIGEN_INTERNO) return url;
    return url.startsWith(ORIGEN_PUBLICO)
        ? ORIGEN_INTERNO + url.slice(ORIGEN_PUBLICO.length)
        : url;
}

export type TipoOferta = {
    slug: string;
    singular: string;
    plural: string;
    medidas: string[];
    publicados: number;
    hero: {
        titulo: string;
        texto: string | null;
        claim: string | null;
        imagen: string;
    };
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
    // Solo en la ficha: el listado no los trae.
    docentes?: DocentePrograma[];
};

export type DocentePrograma = {
    nombre: string;
    grado: string | null;
    rol: string | null;
    es_coordinador: boolean;
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
    /** Accesos a la Facultad, los que ocupan la barra superior. */
    facultad: {
        web: string | null;
        directorio: string | null;
    };
    portada: {
        kicker: string | null;
        titulo: string | null;
        texto: string | null;
        acciones: { texto: string; url: string }[];
        imagenes: string[];
    };
    inscripcion: {
        eyebrow: string | null;
        titulo: string | null;
        boton: { texto: string; url: string } | null;
        pasos: {
            titulo: string;
            detalle: string | null;
            fecha: string | null;
            publico: string | null;
            destacado: boolean;
            icono_path: string;
        }[];
    } | null;
    redes: Record<string, string>;
};

export const obtenerConfiguracion = () => pedir<ConfiguracionSitio>('/sitio');

export const obtenerMenu = () => pedir<ItemMenu[]>('/menu');

export type SeccionContenido = {
    grupo: string | null;
    numeral: string | null;
    titulo: string;
    /** HTML ya resuelto por Laravel: tokens de contacto e iconos incluidos. */
    cuerpo: string;
};

export type PaginaContenido = {
    slug: string;
    titulo: string | null;
    subtitulo: string | null;
    secciones: SeccionContenido[];
};

export const obtenerPagina = (slug: string) =>
    pedir<PaginaContenido>(`/paginas/${encodeURIComponent(slug)}`);

export type Docente = {
    slug: string;
    nombre: string;
    nombre_completo: string;
    grado: string | null;
    foto: string;
    /** Solo en la ficha. */
    biografia?: string | null;
    lineas_investigacion?: string | null;
    orcid?: string | null;
    cti_vitae?: string | null;
    linkedin?: string | null;
    /**
     * Lo que dicta. Es lo unico que hoy da contenido a la ficha: ninguno de
     * los docentes registrados tiene biografia todavia, pero todos ensenan
     * algo, y eso es lo que alguien busca al abrir su nombre.
     */
    programas?: { nombre: string; slug: string; tipo: string | null }[];
};

export const obtenerDocentes = () => pedir<Docente[]>('/docentes');

export const obtenerDocente = (slug: string) =>
    pedir<Docente>(`/docentes/${encodeURIComponent(slug)}`);

export type Evento = {
    titulo: string;
    descripcion: string | null;
    fecha_inicio: string | null;
    fecha_fin: string | null;
    url: string | null;
    imagen: string | null;
};

export type GrupoInformativos = {
    categoria: string;
    recursos: { titulo: string; tipo: string | null; url: string }[];
};

export type CronogramaAcademico = {
    titulo: string | null;
    descripcion: string | null;
    items: {
        seccion: string | null;
        es_encabezado: boolean;
        actividad: string;
        fecha: string | null;
    }[];
} | null;

export type Testimonio = {
    nombre: string;
    contenido: string;
    foto: string;
    programa: string | null;
};

export const obtenerTestimonios = () => pedir<Testimonio[]>('/testimonios');

export const obtenerEventos = () => pedir<Evento[]>('/eventos');
export const obtenerInformativos = () => pedir<GrupoInformativos[]>('/informativos');
export const obtenerCronograma = () => pedir<CronogramaAcademico>('/cronograma');

export type Admision = {
    tipo: string;
    titulo: string;
    subtitulo: string | null;
    pasos: { numero?: number; titulo: string; descripcion?: string }[];
    requisitos: {
        lista: string[];
        observaciones: string | null;
        notas: string | null;
        correo: string | null;
    };
    pago: {
        costo: string | null;
        descripcion: string | null;
        instrucciones: unknown[];
        observaciones: string | null;
        enlace_sanmarket: string | null;
    };
    resultados: { texto: string | null; enlace: string | null };
    convocatorias: {
        programa: string;
        convocatoria: string | null;
        inscripcion: string | null;
        limite: string | null;
        estado: string | null;
    }[];
};

export const obtenerAdmision = (tipo: string) =>
    pedir<Admision>(`/admision/${encodeURIComponent(tipo)}`);

/**
 * Una entrada del indice del buscador.
 *
 * `t` y `c` son el titulo y el cuerpo ya normalizados —minusculas y sin
 * tildes—, calculados en el servidor para que buscar «admision» encuentre
 * «Admision» sin rehacer el trabajo en cada navegador. Los nombres son cortos
 * porque se repiten en cada entrada y el indice entero viaja al cliente.
 */
export type EntradaBuscador = {
    titulo: string;
    descripcion: string;
    url: string;
    categoria: string;
    peso: number;
    t: string;
    c: string;
};

export const obtenerIndiceBuscador = () => pedir<EntradaBuscador[]>('/buscador');

/**
 * Anuncios de la portada, con los ajustes con los que se muestran.
 *
 * Van juntos porque se usan juntos: el retardo y la frecuencia se editan en el
 * panel, y separarlos obligaria al sitio a decidirlos por su cuenta.
 */
export type Anuncios = {
    items: {
        imagen: string;
        alt: string;
        link: string;
        link_texto: string;
        ancho: number | null;
        alto: number | null;
    }[];
    ajustes: {
        retardo: number;
        frecuencia: 'sesion' | 'dia' | 'siempre';
        autoAvance: boolean;
    };
};

export const obtenerAnuncios = () => pedir<Anuncios>('/anuncios');

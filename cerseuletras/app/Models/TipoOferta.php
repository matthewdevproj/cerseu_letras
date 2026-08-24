<?php

namespace App\Models;

/**
 * Los tipos de oferta del CERSEU: talleres, cursos y especializaciones.
 *
 * Comparten estructura completa —página, admisión, cronograma y formulario de
 * solicitud— y se distinguen por duración: un taller se mide en semanas, y un
 * curso o una especialización en meses. Como todo lo demás es idéntico, el
 * sitio los sirve con el mismo código y este enum concentra lo único que cambia
 * entre ellos: el rótulo, el segmento de URL y el valor de `programas.grado`.
 *
 * Añadir un tipo es añadir un case: rutas, menú, panel, filtros de la portada y
 * pruebas se generan a partir de cases().
 */
enum TipoOferta: string
{
    case Taller = 'taller';
    case Curso = 'curso';
    case Especializacion = 'especializacion';

    /** Valor de `programas.grado` que corresponde a este tipo. */
    public function grado(): string
    {
        return match ($this) {
            self::Taller => 'Taller',
            self::Curso => 'Curso',
            self::Especializacion => 'Especialización',
        };
    }

    /** Rótulo en singular, tal como se lee en la interfaz. */
    public function singular(): string
    {
        return $this->grado();
    }

    /** Rótulo en plural: también es el segmento de URL del módulo. */
    public function plural(): string
    {
        return match ($this) {
            self::Taller => 'Talleres',
            self::Curso => 'Cursos',
            self::Especializacion => 'Especializaciones',
        };
    }

    /**
     * Cómo se mide cada tipo, en pares columna → unidad.
     *
     * No comparten escala: un taller se anuncia por horas académicas, un curso
     * por sesiones y horas, y una especialización por módulos y meses. Antes
     * había una sola «unidad de duración» por tipo y las vistas pintaban
     * siempre los mismos tres chips, lo que obligaba a inventar una duración
     * para ofertas que no la tienen. Declarar aquí las medidas deja a las
     * vistas recorriendo lo que haya, sin saber de tipos.
     *
     * @return array<string, string>
     */
    public function medidas(): array
    {
        return match ($this) {
            self::Taller => ['horas_academicas' => 'horas académicas'],
            self::Curso => ['sesiones' => 'sesiones', 'horas_academicas' => 'horas académicas'],
            self::Especializacion => ['modulos' => 'módulos', 'duracion' => 'meses'],
        };
    }

    /** Segmento de URL y prefijo de los nombres de ruta. */
    public function slug(): string
    {
        return match ($this) {
            self::Taller => 'talleres',
            self::Curso => 'cursos',
            self::Especializacion => 'especializaciones',
        };
    }

    /** Prefijo de los campos `*_hero_*` en `site_settings`. */
    public function prefijoHero(): string
    {
        return $this->slug();
    }

    /**
     * Resuelve el tipo a partir del segmento de URL.
     *
     * Devuelve null en vez de lanzar para que la ruta responda 404 ante un
     * segmento desconocido, en lugar de un 500.
     */
    public static function desdeSlug(?string $slug): ?self
    {
        foreach (self::cases() as $tipo) {
            if ($tipo->slug() === $slug) {
                return $tipo;
            }
        }

        return null;
    }

    /** Los grados que corresponden a oferta corta (no a grados académicos). */
    public static function grados(): array
    {
        return array_map(fn (self $t) => $t->grado(), self::cases());
    }
}

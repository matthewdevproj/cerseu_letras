<?php

namespace App\Models;

/**
 * Los dos tipos de oferta corta del CERSEU: talleres y cursos.
 *
 * Comparten estructura completa —página, admisión, cronograma y formulario de
 * solicitud— y se distinguen por duración: un curso se mide en meses y un
 * taller en semanas. Como todo lo demás es idéntico, el sitio los sirve con el
 * mismo código y este enum concentra lo único que cambia entre ellos: el
 * rótulo, el segmento de URL y el valor de `programas.grado`.
 */
enum TipoOferta: string
{
    case Taller = 'taller';
    case Curso = 'curso';

    /** Valor de `programas.grado` que corresponde a este tipo. */
    public function grado(): string
    {
        return match ($this) {
            self::Taller => 'Taller',
            self::Curso => 'Curso',
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
        };
    }

    /**
     * Unidad en que se mide la duración.
     *
     * Es la única diferencia real entre los dos tipos: un curso dura meses y
     * un taller, semanas.
     */
    public function unidadDuracion(): string
    {
        return match ($this) {
            self::Taller => 'semanas',
            self::Curso => 'meses',
        };
    }

    /** Segmento de URL y prefijo de los nombres de ruta. */
    public function slug(): string
    {
        return match ($this) {
            self::Taller => 'talleres',
            self::Curso => 'cursos',
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
        return match ($slug) {
            'talleres' => self::Taller,
            'cursos' => self::Curso,
            default => null,
        };
    }

    /** Los grados que corresponden a oferta corta (no a grados académicos). */
    public static function grados(): array
    {
        return array_map(fn (self $t) => $t->grado(), self::cases());
    }
}

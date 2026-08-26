<?php

namespace App\Models\Concerns;

use App\Support\IndiceDeBusqueda;

/**
 * Invalida el índice del buscador global cuando el contenido cambia.
 *
 * Se engancha a los eventos del modelo (y no a cada controlador del panel) para
 * que cualquier vía de edición —panel, comandos, seeders— refresque el índice.
 */
trait InvalidatesSearchIndex
{
    protected static function bootInvalidatesSearchIndex(): void
    {
        // Delegado: la clave del cache la conoce IndiceDeBusqueda y nadie mas.
        $olvidar = fn () => IndiceDeBusqueda::olvidar();

        static::saved($olvidar);
        static::deleted($olvidar);
    }
}

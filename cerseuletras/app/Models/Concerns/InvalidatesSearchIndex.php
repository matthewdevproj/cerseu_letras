<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Cache;

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
        $olvidar = fn () => Cache::forget('search_index');

        static::saved($olvidar);
        static::deleted($olvidar);
    }
}

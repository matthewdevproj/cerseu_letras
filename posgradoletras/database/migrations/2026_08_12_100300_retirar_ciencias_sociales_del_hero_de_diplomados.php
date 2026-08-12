<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Descripción de la portada de Diplomados sin «las ciencias sociales»
     * (Obs. N.º 5).
     *
     * El texto vive en `site_settings.diplomados_hero_texto` y es editable desde
     * el panel, pero el valor cargado todavía llevaba la mención. La plantilla
     * repetía además la frase como respaldo, y ese literal se corrige aparte.
     *
     * Solo se reescribe la fila si sigue conteniendo el texto anterior: si
     * alguien ya lo redactó de otra forma desde el panel, se respeta.
     */
    private const TEXTO_ANTERIOR = 'Especializa tus conocimientos con programas diseñados para responder a los desafíos contemporáneos desde las humanidades, las ciencias sociales y las nuevas tecnologías.';

    private const TEXTO_NUEVO = 'Especializa tus conocimientos con programas diseñados para responder a los desafíos contemporáneos desde las humanidades y las nuevas tecnologías.';

    public function up(): void
    {
        $this->reemplazar(self::TEXTO_ANTERIOR, self::TEXTO_NUEVO);
    }

    public function down(): void
    {
        $this->reemplazar(self::TEXTO_NUEVO, self::TEXTO_ANTERIOR);
    }

    private function reemplazar(string $desde, string $hasta): void
    {
        if (!Schema::hasColumn('site_settings', 'diplomados_hero_texto')) {
            return;
        }

        DB::table('site_settings')
            ->where('diplomados_hero_texto', $desde)
            ->update(['diplomados_hero_texto' => $hasta]);

        // El texto se sirve desde la caché compartida del singleton.
        \App\Models\SiteSetting::clearCache();
    }
};

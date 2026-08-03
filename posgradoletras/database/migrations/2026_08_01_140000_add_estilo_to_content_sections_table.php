<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Presentación de cada sección.
     *
     * Al hacer el contenido administrable, todas las secciones pasaron a
     * pintarse con la misma caja blanca y se perdió el diseño propio de
     * algunas —en particular la del cronograma, que lleva cabecera oscura con
     * degradado e icono—. Con `estilo` cada sección recupera su presentación
     * y `subtitulo` deja de colarse dentro del cuerpo (donde su texto blanco
     * quedaba invisible sobre fondo blanco).
     */
    public function up(): void
    {
        Schema::table('content_sections', function (Blueprint $table) {
            $table->string('estilo', 20)->default('simple')->after('numeral');
            $table->string('subtitulo')->nullable()->after('titulo');
        });

        // La sección del cronograma recupera su cabecera destacada y se le
        // extrae del cuerpo el subtítulo que había quedado dentro.
        $cronograma = DB::table('content_sections')
            ->where('titulo', 'like', 'Cronograma del Proceso de Admisión%')
            ->first();

        if ($cronograma) {
            $cuerpo = (string) $cronograma->cuerpo;
            $subtitulo = null;

            if (preg_match('~^\s*<p[^>]*text-white/70[^>]*>(.*?)</p>\s*~s', $cuerpo, $m)) {
                $subtitulo = trim(strip_tags($m[1]));
                $cuerpo = trim(substr($cuerpo, strlen($m[0])));
            }

            DB::table('content_sections')->where('id', $cronograma->id)->update([
                'estilo' => 'destacado',
                'subtitulo' => $subtitulo,
                'cuerpo' => $cuerpo,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('content_sections', function (Blueprint $table) {
            $table->dropColumn(['estilo', 'subtitulo']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Retira `estilo` y `subtitulo` de las secciones de contenido.
 *
 * Se añadieron para que un componente Blade (`<x-content-section>`) pintara
 * cada sección según su presentación. Ese camino se abandonó: las vistas de
 * /tramites y /admision conservan su maquetación original y de la base de
 * datos solo sale el texto, así que el componente nunca llegó a usarse en
 * ninguna vista y estas dos columnas no las lee nadie.
 *
 * `subtitulo` sigue existiendo en `content_pages`, donde sí se edita y se usa;
 * la que se elimina aquí es la de `content_sections`, vacía en todas las filas.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('content_sections', function (Blueprint $table) {
            $table->dropColumn(['estilo', 'subtitulo']);
        });
    }

    public function down(): void
    {
        Schema::table('content_sections', function (Blueprint $table) {
            $table->string('estilo', 20)->default('simple')->after('numeral');
            $table->string('subtitulo')->nullable()->after('titulo');
        });
    }
};

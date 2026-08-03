<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Contenido editable de las páginas largas (/tramites y /admision).
     *
     * Eran ~2 000 líneas de texto dentro de las vistas: requisitos, pasos,
     * plazos y documentos que cambian con cada convocatoria y solo podían
     * corregirse tocando código. El diseño de las páginas sigue en Blade; lo
     * que pasa a la base de datos es únicamente el contenido.
     */
    public function up(): void
    {
        Schema::create('content_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();   // tramites | admision
            $table->string('titulo')->nullable();
            $table->text('subtitulo')->nullable();
            $table->timestamps();
        });

        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_page_id')->constrained('content_pages')->cascadeOnDelete();
            // Pestaña a la que pertenece (maestria/doctorado en trámites);
            // null cuando la página no usa pestañas.
            $table->string('grupo')->nullable();
            $table->string('numeral', 10)->nullable();  // I, II, III…
            $table->string('titulo');
            $table->longText('cuerpo')->nullable();     // HTML editable
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['content_page_id', 'grupo', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_sections');
        Schema::dropIfExists('content_pages');
    }
};

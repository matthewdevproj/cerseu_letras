<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Anuncios del popup de la portada.
 *
 * El componente `<x-popup-announcements>` ya existía, pero se alimentaba de un
 * array escrito dentro de `home.blade.php` — y estaba comentado, porque para
 * apagarlo había que editar la plantilla y desplegar. Ahora sale de aquí.
 *
 * `visible_desde` / `visible_hasta` evitan el problema que dejó «Criterios de
 * Evaluación 2025» un año colgado: un anuncio de convocatoria se programa y se
 * retira solo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anuncios', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');           // Interno: para reconocerlo en el panel
            $table->string('imagen');
            $table->string('alt')->nullable();
            $table->string('link')->nullable();
            $table->string('link_texto')->nullable();
            $table->date('visible_desde')->nullable();
            $table->date('visible_hasta')->nullable();
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_visible', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anuncios');
    }
};

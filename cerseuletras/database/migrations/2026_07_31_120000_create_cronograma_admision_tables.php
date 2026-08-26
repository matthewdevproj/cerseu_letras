<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sección "Cronograma de Admisión" de la portada (Obs. N.º 2): hasta ahora
     * era un array fijo dentro de home.blade.php. Pasa a ser editable desde el
     * panel para poder adaptarla a cualquier convocatoria (maestrías y
     * doctorados, diplomados, nuevos periodos) sin tocar código.
     */
    public function up(): void
    {
        Schema::create('cronograma_admisiones', function (Blueprint $table) {
            $table->id();
            $table->string('eyebrow')->nullable();       // Título superior del proceso
            $table->string('titulo')->nullable();        // Título principal de la sección
            $table->string('boton_texto')->nullable();   // Texto del botón principal
            $table->string('boton_url')->nullable();     // Enlace de redirección del botón
            $table->boolean('is_visible')->default(true); // Ocultar la sección completa
            $table->timestamps();
        });

        Schema::create('cronograma_admision_pasos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cronograma_admision_id')
                ->constrained('cronograma_admisiones')
                ->cascadeOnDelete();
            $table->string('titulo');                       // Nombre de la etapa
            $table->string('fecha_inicio')->nullable();     // Fecha de inicio (texto libre)
            $table->string('fecha_fin')->nullable();        // Fecha de cierre (texto libre)
            $table->string('detalle')->nullable();          // Texto complementario
            $table->string('publico')->nullable();          // Programa o público
            $table->string('icono')->default('documento');  // Ícono representativo
            $table->unsignedInteger('orden')->default(0);   // Orden de presentación
            $table->boolean('destacado')->default(false);   // Etapa en curso (tarjeta blanca)
            $table->boolean('is_visible')->default(true);   // Ocultar solo esta tarjeta
            $table->timestamps();

            $table->index(['cronograma_admision_id', 'orden']);
        });

        // Aqui se sembraba el cronograma de admision de la Unidad de Posgrado:
        // «Examen de conocimiento y entrevistas» los dias 6 y 7 de abril,
        // evaluacion de expediente y publicacion de resultados. El renombrado
        // del rebrand le cambio las etiquetas de publico a «Cursos» y
        // «Talleres», con lo que la portada afirmaba que los cursos del CERSEU
        // tienen examen de admision y entrevista. No los tienen.
        //
        // Se quita el sembrado entero, no solo el texto: una migracion crea
        // estructura, no contenido. Mientras esto estuvo aqui, el seeder no
        // podia corregirlo —ContenidoInicialSeeder se retira si ya existe un
        // cronograma—, de modo que una instalacion limpia seguia levantandose
        // con el cronograma de Posgrado por mucho que se arreglara el JSON.
        //
        // El contenido lo pone ahora ContenidoInicialSeeder, y se edita en
        // /admin/cronograma-admision.
    }

    public function down(): void
    {
        Schema::dropIfExists('cronograma_admision_pasos');
        Schema::dropIfExists('cronograma_admisiones');
    }
};

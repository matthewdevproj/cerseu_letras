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

        // Se siembra el contenido que la portada ya mostraba, para que la
        // migración no cambie nada de cara al visitante.
        $id = DB::table('cronograma_admisiones')->insertGetId([
            'eyebrow' => 'Proceso de Admisión 2026-I',
            'titulo' => 'Cronograma de Admisión',
            'boton_texto' => 'Iniciar Inscripción',
            'boton_url' => 'https://cerseuletras.unmsm.edu.pe/admision',
            'is_visible' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $pasos = [
            ['Inscripción de postulantes', '5 ene', '02 abr', '+ Envío de expediente', null, 'inscripcion', true],
            ['Examen de conocimiento y entrevistas', '06 de abril', null, null, 'Cursos', 'examen', false],
            ['Examen de conocimiento y entrevistas', '07 de abril', null, null, 'Talleres', 'birrete', false],
            ['Evaluación del expediente', 'Hasta el 06 de abril', null, 'Revisión de documentos', null, 'expediente', false],
            ['Publicación de Resultados', '09 de abril', null, 'Lista oficial', null, 'check', false],
        ];

        foreach ($pasos as $i => [$titulo, $inicio, $fin, $detalle, $publico, $icono, $destacado]) {
            DB::table('cronograma_admision_pasos')->insert([
                'cronograma_admision_id' => $id,
                'titulo' => $titulo,
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'detalle' => $detalle,
                'publico' => $publico,
                'icono' => $icono,
                'orden' => $i,
                'destacado' => $destacado,
                'is_visible' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cronograma_admision_pasos');
        Schema::dropIfExists('cronograma_admisiones');
    }
};

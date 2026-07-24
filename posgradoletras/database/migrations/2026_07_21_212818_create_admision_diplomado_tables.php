<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admision_diplomado_settings', function (Blueprint $table) {
            $table->id();

            // Hero
            $table->string('hero_titulo')->nullable();
            $table->string('hero_subtitulo')->nullable();
            $table->string('hero_imagen')->nullable();

            // Sección 1: Guía para el proceso de admisión
            $table->json('pasos')->nullable();

            // Sección 3: Requisitos para postular
            $table->string('requisitos_email')->nullable();
            $table->json('requisitos_lista')->nullable();
            $table->text('requisitos_observaciones')->nullable();
            $table->text('requisitos_notas')->nullable();

            // Sección 4: Pago por derecho de inscripción
            $table->string('pago_costo')->nullable();
            $table->text('pago_descripcion')->nullable();
            $table->json('pago_instrucciones')->nullable();
            $table->string('pago_link_sanmarket')->nullable();
            $table->text('pago_observaciones')->nullable();

            // Sección 5: Publicación de resultados
            $table->text('resultados_texto')->nullable();
            $table->string('resultados_enlace')->nullable();
            $table->string('resultados_pdf_url')->nullable();

            // Sección 6: Para más información
            $table->string('contacto_telefono')->nullable();
            $table->string('contacto_correo')->nullable();
            $table->text('contacto_direccion')->nullable();
            $table->string('contacto_sitio_web')->nullable();
            $table->string('contacto_qr_path')->nullable();
            $table->string('contacto_whatsapp')->nullable();

            $table->timestamps();
        });

        // Sección 2: Cronograma de Admisión (filas editables)
        Schema::create('admision_diplomado_cronograma_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admision_diplomado_setting_id')->constrained('admision_diplomado_settings')->cascadeOnDelete();
            $table->string('programa');
            $table->string('convocatoria')->nullable();
            $table->string('fecha_inscripcion')->nullable();
            $table->string('fecha_limite')->nullable();
            $table->string('estado')->default('Activo');
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admision_diplomado_cronograma_items');
        Schema::dropIfExists('admision_diplomado_settings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            // Quitar columnas
            $table->dropColumn(['presentacion', 'descripcion', 'perfil_egresado']);

            // Agregar nuevos campos JSON
            $table->json('objetivos_academicos')->nullable()->after('grado_otorga');
            $table->json('perfil_ingresante')->nullable()->after('objetivos_academicos');
            $table->json('perfil_graduado')->nullable()->after('perfil_ingresante');

            // Agregar URL de horario
            $table->string('horario_url')->nullable()->after('plan_url');
        });
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->text('presentacion')->nullable();
            $table->text('descripcion')->nullable();
            $table->text('perfil_egresado')->nullable();
            $table->dropColumn(['objetivos_academicos', 'perfil_ingresante', 'perfil_graduado', 'horario_url']);
        });
    }
};

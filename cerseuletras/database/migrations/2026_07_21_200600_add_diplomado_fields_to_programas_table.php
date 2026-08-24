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
        Schema::table('programas', function (Blueprint $table) {
            $table->string('brochure_url')->nullable()->after('horario_url');
            $table->string('admision_pdf_url')->nullable()->after('brochure_url');
            $table->unsignedInteger('horas_academicas')->nullable()->after('creditos');
            $table->string('fecha_limite_inscripcion')->nullable()->after('admision_pdf_url');
            $table->json('inversion_economica')->nullable()->after('fecha_limite_inscripcion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn([
                'brochure_url',
                'admision_pdf_url',
                'horas_academicas',
                'fecha_limite_inscripcion',
                'inversion_economica',
            ]);
        });
    }
};

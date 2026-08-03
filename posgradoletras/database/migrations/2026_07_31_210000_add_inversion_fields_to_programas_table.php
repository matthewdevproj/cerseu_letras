<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Inversión económica editable por programa.
     *
     * El costo por crédito y las matrículas por semestre estaban escritos en
     * las plantillas de maestría y doctorado (y duplicados en programas/show).
     * Son importes que cambian cada año y solo podían corregirse tocando código.
     *
     * Se siembra exactamente lo que las plantillas ya mostraban, de modo que la
     * migración no altera ni un importe de cara al visitante.
     */
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->unsignedInteger('costo_por_credito')->nullable()->after('inversion_economica');
            // [{matricula: int, creditos: int}, ...] en orden de semestre/módulo
            $table->json('semestres_inversion')->nullable()->after('costo_por_credito');
        });

        $porDefecto = [
            'Maestría' => [160, [
                ['matricula' => 310, 'creditos' => 14],
                ['matricula' => 400, 'creditos' => 16],
                ['matricula' => 400, 'creditos' => 20],
                ['matricula' => 400, 'creditos' => 22],
            ]],
            'Doctorado' => [210, [
                ['matricula' => 310, 'creditos' => 14],
                ['matricula' => 500, 'creditos' => 14],
                ['matricula' => 500, 'creditos' => 14],
                ['matricula' => 500, 'creditos' => 10],
                ['matricula' => 500, 'creditos' => 10],
                ['matricula' => 500, 'creditos' => 10],
            ]],
            'Diplomado' => [120, [
                ['matricula' => 200, 'creditos' => 12],
                ['matricula' => 200, 'creditos' => 12],
            ]],
        ];

        foreach ($porDefecto as $grado => [$costo, $semestres]) {
            DB::table('programas')->where('grado', $grado)->update([
                'costo_por_credito' => $costo,
                'semestres_inversion' => json_encode($semestres),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropColumn(['costo_por_credito', 'semestres_inversion']);
        });
    }
};

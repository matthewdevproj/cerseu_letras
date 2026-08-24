<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Denominación del responsable académico (Obs. N.º 1).
     *
     * La ficha rotulaba siempre «Coordinador del Programa», escrito a mano en
     * la tarjeta. Cuando la responsable es una mujer no había forma de
     * corregirlo sin tocar código.
     *
     * Va en el pivote y no en `programas` porque la denominación depende de la
     * persona asignada a ese programa, que es justo lo que describe la relación.
     * Se deja `null` a propósito: sin valor, la vista sigue diciendo
     * «Coordinador», de modo que los programas ya cargados no cambian.
     */
    public function up(): void
    {
        Schema::table('docente_programa', function (Blueprint $table) {
            $table->string('coordinador_denominacion', 20)->nullable()->after('es_coordinador');
        });
    }

    public function down(): void
    {
        Schema::table('docente_programa', function (Blueprint $table) {
            $table->dropColumn('coordinador_denominacion');
        });
    }
};

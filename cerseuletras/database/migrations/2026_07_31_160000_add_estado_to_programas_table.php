<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estado de publicación de cada programa.
     *
     * Hasta ahora solo existía `is_active`, un booleano que ocultaba el programa
     * de los listados pero dejaba su página accesible por URL. Con tres estados
     * se distingue lo que aún no se anuncia (404) de lo que se anuncia como
     * próxima oferta (página "Próximamente").
     *
     * Se usa `string` y no `enum` porque alterar un enum en SQLite obliga a
     * recrear la tabla; la validación de valores vive en el modelo.
     */
    public function up(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->string('estado', 20)->default('borrador')->after('is_active')->index();
        });

        // Backfill conservador: lo que estaba activo pasa a publicado; lo demás
        // a borrador (deja de ser alcanzable por URL, que es el hueco a cerrar).
        DB::table('programas')->where('is_active', 1)->update(['estado' => 'publicado']);
        DB::table('programas')->where('is_active', '!=', 1)->update(['estado' => 'borrador']);
    }

    public function down(): void
    {
        Schema::table('programas', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropColumn('estado');
        });
    }
};

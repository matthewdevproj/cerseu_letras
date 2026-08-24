<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * `directorio_posgrado` pasa a `directorio_cerseu`.
     *
     * Era el último nombre de tabla que seguía diciendo de qué unidad venía el
     * sitio. Se renombra junto con su modelo (DirectorioPosgrado →
     * DirectorioCerseu); las columnas no cambian.
     */
    public function up(): void
    {
        if (Schema::hasTable('directorio_posgrado') && ! Schema::hasTable('directorio_cerseu')) {
            Schema::rename('directorio_posgrado', 'directorio_cerseu');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('directorio_cerseu') && ! Schema::hasTable('directorio_posgrado')) {
            Schema::rename('directorio_cerseu', 'directorio_posgrado');
        }
    }
};

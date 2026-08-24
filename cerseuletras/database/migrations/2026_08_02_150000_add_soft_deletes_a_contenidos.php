<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Papelera para lo que el personal puede borrar.
 *
 * Hasta ahora un borrado era definitivo: ningún modelo usaba SoftDeletes y no
 * había forma de recuperar un programa, un documento o un evento eliminado por
 * error. Con `deleted_at` el registro sale del sitio pero sigue ahí.
 */
return new class extends Migration
{
    /** Tablas con contenido que se puede borrar desde el panel. */
    private array $tablas = [
        'programas', 'docentes', 'eventos', 'informativos',
        'documents', 'testimonios', 'directorio_posgrado',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            if (! Schema::hasTable($tabla) || Schema::hasColumn($tabla, 'deleted_at')) {
                continue;
            }

            Schema::table($tabla, fn (Blueprint $t) => $t->softDeletes());
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'deleted_at')) {
                Schema::table($tabla, fn (Blueprint $t) => $t->dropSoftDeletes());
            }
        }
    }
};

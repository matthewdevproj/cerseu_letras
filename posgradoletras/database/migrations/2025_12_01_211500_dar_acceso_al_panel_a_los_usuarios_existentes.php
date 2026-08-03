<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Deja como administradores a los usuarios que ya existían.
 *
 * La migración anterior añade `role` con valor por defecto `user`, así que al
 * aplicar esta versión sobre una instalación con usuarios reales **ninguno
 * podría entrar al panel**: el middleware exige `role = 'admin'` y
 * `is_active`. Es el fallo que aparece justo después de desplegar, cuando ya
 * no se puede arreglar desde la propia web.
 *
 * Se hace aquí y no a mano porque el sitio **no tiene registro público**: todas
 * las cuentas las crea un administrador, así que quien ya estaba dentro es
 * personal de la Unidad.
 *
 * Solo afecta a los usuarios presentes en el momento de migrar. Los que se
 * creen después nacen como `user`, y se promueven con:
 *
 *     php artisan usuario:admin correo@unmsm.edu.pe
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }

        $promovidos = DB::table('users')
            ->where('role', '!=', 'admin')
            ->orWhereNull('role')
            ->update(['role' => 'admin', 'is_active' => true]);

        if ($promovidos > 0) {
            // Queda constancia en la salida de `migrate`, que es donde alguien
            // lo va a leer si algo no cuadra después.
            echo "  Usuarios con acceso al panel: {$promovidos}." . PHP_EOL;
        }
    }

    public function down(): void
    {
        // No se revierte: dejar a todo el mundo fuera del panel sería peor que
        // el problema que esta migración resuelve.
    }
};

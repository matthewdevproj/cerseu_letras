<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Copia a un lado lo que las migraciones siguientes van a borrar.
 *
 * Entre enero y julio de 2026 el esquema cambió y varias migraciones eliminan
 * columnas con contenido: `programas.presentacion`, `.descripcion` y
 * `.perfil_egresado`; `docentes.telefono` y `.especialidad`. Sobre una base
 * recién creada da igual —están vacías—, pero al aplicar estas migraciones a
 * una instalación **con datos reales** se pierde el contenido sin aviso.
 *
 * La fecha de este archivo está puesta a propósito **antes** de la primera de
 * esas migraciones, para que se ejecute primero.
 *
 * Las tablas de respaldo **no se borran** al terminar: si el traslado a los
 * campos nuevos no encaja con la realidad del contenido, el original sigue
 * disponible. Se pueden eliminar a mano cuando la migración esté validada.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->preservar('programas', 'programas_textos_previos', [
            'presentacion', 'descripcion', 'perfil_egresado',
        ]);

        $this->preservar('docentes', 'docentes_datos_previos', [
            'telefono', 'especialidad',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('programas_textos_previos');
        Schema::dropIfExists('docentes_datos_previos');
    }

    /**
     * Guarda las columnas indicadas en una tabla aparte, junto al `id`.
     *
     * Se salta en silencio las columnas que ya no existan: en una base creada
     * desde cero con el esquema actual no hay nada que preservar, y la
     * migración debe poder correr igual.
     */
    private function preservar(string $tabla, string $respaldo, array $columnas): void
    {
        if (! Schema::hasTable($tabla) || Schema::hasTable($respaldo)) {
            return;
        }

        $presentes = array_values(array_filter(
            $columnas,
            fn ($c) => Schema::hasColumn($tabla, $c)
        ));

        if (! $presentes) {
            return;
        }

        Schema::create($respaldo, function (Blueprint $t) use ($presentes) {
            $t->unsignedBigInteger('id')->primary();
            foreach ($presentes as $columna) {
                $t->text($columna)->nullable();
            }
            $t->timestamp('preservado_en')->useCurrent();
        });

        DB::table($tabla)
            ->select(array_merge(['id'], $presentes))
            ->orderBy('id')
            ->chunk(200, function ($filas) use ($respaldo, $presentes) {
                $lote = [];

                foreach ($filas as $fila) {
                    $registro = ['id' => $fila->id];
                    foreach ($presentes as $columna) {
                        $registro[$columna] = $fila->{$columna};
                    }

                    // Solo interesa lo que tenga algo que perder.
                    if (collect($registro)->except('id')->filter()->isNotEmpty()) {
                        $lote[] = $registro;
                    }
                }

                if ($lote) {
                    DB::table($respaldo)->insert($lote);
                }
            });
    }
};
